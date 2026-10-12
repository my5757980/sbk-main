<?php

namespace Tests\Feature;

use App\Services\App\AppPush;
use App\Services\Chat\ChatService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request as Sent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The SBK app's notifications (App\Services\App\AppPush), with Google's
 * answers faked: a phone registers, the desk writes or rings, the right
 * message goes to Firebase for that customer only, and a phone that no longer
 * has the app is forgotten. Rolled back after each test.
 */
class AppPushTest extends TestCase
{
    use DatabaseTransactions;

    private string $keyFile;
    private array $guest;
    private int $thread;
    private int $staffId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->keyFile = tempnam(sys_get_temp_dir(), 'fcm');
        // A throwaway key made for this run only - it opens nothing anywhere. The
        // empty config file lets OpenSSL make keys where no openssl.cnf is installed (Windows).
        $cnf = tempnam(sys_get_temp_dir(), 'cnf');
        $pem = '';
        openssl_pkey_export(openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA, 'config' => $cnf]), $pem, null, ['config' => $cnf]);
        @unlink($cnf);
        file_put_contents($this->keyFile, json_encode(['type' => 'service_account', 'client_email' => 'test@sbk-chat.iam.gserviceaccount.com', 'private_key' => $pem]));
        config(['services.fcm.service_account' => $this->keyFile, 'services.fcm.project' => 'sbk-chat']);
        $this->resetReady();
        cache()->forget('app-push:fcm-token');

        $this->staffId = (int) DB::table('admins')->value('id');
        $token = strtolower(preg_replace('/[^a-f0-9]/', 'a', bin2hex(random_bytes(20))));
        $id = DB::table('sbk_chat_guests')->insertGetId(['token' => $token, 'user_id' => null, 'name' => 'Push Tester',
            'email' => 'push-' . Str::random(6) . '@local.test', 'phone' => '+1 555 0100', 'ip' => '127.0.0.1', 'user_agent' => 'test',
            'last_seen' => ChatService::now(), 'created_at' => ChatService::now()]);
        $this->guest = ['id' => $id, 'token' => $token];
        $this->thread = (int) DB::table('sbk_chat_threads')->insertGetId(['guest_id' => $id, 'staff_id' => $this->staffId,
            'created_at' => ChatService::now(), 'last_message_at' => ChatService::now()]);
    }

    protected function tearDown(): void
    {
        @unlink($this->keyFile);
        $this->resetReady();
        parent::tearDown();
    }

    private function resetReady(): void
    {
        $r = new \ReflectionProperty(AppPush::class, 'ready');
        $r->setAccessible(true);
        $r->setValue(null, null);
    }

    private function fakeGoogle(bool $unregistered = false): void
    {
        Http::fake([
            'oauth2.googleapis.com/*' => Http::response(['access_token' => 'fake-access'], 200),
            'fcm.googleapis.com/*' => $unregistered
                ? Http::response(['error' => ['status' => 'NOT_FOUND', 'details' => [['errorCode' => 'UNREGISTERED']]]], 404)
                : Http::response(['name' => 'projects/sbk-chat/messages/1'], 200),
        ]);
    }

    private function register(string $phoneToken): void
    {
        $this->withHeader('X-Chat-Guest', $this->guest['token'])
            ->postJson('/api/app/chat/push', ['token' => $phoneToken])->assertOk()->assertJson(['stored' => true]);
    }

    public function test_the_desk_writes_and_the_customer_s_phone_is_told(): void
    {
        $this->fakeGoogle();
        $this->register('phone-token-1');
        app(ChatService::class)->addMessage($this->thread, false, $this->staffId, 'text', 'Your car is ready to ship');
        $this->app->terminate();

        Http::assertSent(function (Sent $r) {
            if (!str_contains($r->url(), 'fcm.googleapis.com/v1/projects/sbk-chat/messages:send')) {
                return false;
            }
            $m = $r['message'];

            return $m['token'] === 'phone-token-1'
                && $m['data']['message'] === 'Your car is ready to ship'
                && $m['data']['tag'] === 't' . $this->thread
                && $m['data']['channelId'] === 'messages'
                && json_decode($m['data']['body'], true)['thread'] === $this->thread;
        });
    }

    public function test_the_customer_s_own_message_tells_nobody(): void
    {
        $this->fakeGoogle();
        $this->register('phone-token-2');
        app(ChatService::class)->addMessage($this->thread, true, null, 'text', 'Hello');
        $this->app->terminate();
        Http::assertNotSent(fn (Sent $r) => str_contains($r->url(), 'fcm.googleapis.com'));
    }

    public function test_a_ring_from_the_desk_is_a_call_notification(): void
    {
        $this->fakeGoogle();
        $this->register('phone-token-3');
        $call = DB::table('sbk_chat_calls')->insertGetId(['thread_id' => $this->thread, 'from_guest' => 0, 'kind' => 'video',
            'offer' => 'sdp', 'state' => 'ringing', 'created_at' => ChatService::now(), 'guest_seen' => ChatService::now(), 'staff_seen' => ChatService::now()]);
        AppPush::ringing($call);
        $this->app->terminate();
        Http::assertSent(fn (Sent $r) => str_contains($r->url(), 'fcm.googleapis.com')
            && $r['message']['data']['message'] === 'Incoming video call' && $r['message']['data']['channelId'] === 'calls');
    }

    public function test_a_phone_without_the_app_is_forgotten(): void
    {
        $this->fakeGoogle(true);
        $this->register('phone-token-gone');
        app(ChatService::class)->addMessage($this->thread, false, $this->staffId, 'text', 'Anyone there?');
        $this->app->terminate();
        $this->assertSame(0, DB::table('sbk_app_push')->where('token', 'phone-token-gone')->count());
    }

    public function test_forget_and_no_key_mean_no_notifications(): void
    {
        $this->fakeGoogle();
        $this->register('phone-token-4');
        $this->withHeader('X-Chat-Guest', $this->guest['token'])->postJson('/api/app/chat/push', ['token' => 'phone-token-4', 'do' => 'forget'])->assertOk();
        $this->assertSame(0, DB::table('sbk_app_push')->where('token', 'phone-token-4')->count());

        $this->register('phone-token-5');
        config(['services.fcm.service_account' => null]);
        $this->resetReady();
        app(ChatService::class)->addMessage($this->thread, false, $this->staffId, 'text', 'Quiet');
        $this->app->terminate();
        Http::assertNotSent(fn (Sent $r) => str_contains($r->url(), 'fcm.googleapis.com'));
    }
}
