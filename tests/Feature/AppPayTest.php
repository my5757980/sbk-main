<?php

namespace Tests\Feature;

use App\Models\CheckoutOrder;
use App\Models\Product;
use App\Models\User;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The SBK app's PayPal / card payment (Api\App\PayController), with PayPal's
 * answers faked: start -> the payment page -> create -> capture -> the order,
 * exactly as the website's checkout writes it. Rolled back after each test.
 */
class AppPayTest extends TestCase
{
    use DatabaseTransactions;

    private Product $car;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.paypal.client_id' => 'test-client', 'services.paypal.secret' => 'test-secret', 'services.paypal.mode' => 'sandbox']);

        $brand = VehicleBrand::create(['name' => 'Testbrand', 'slug' => 'testbrand-' . Str::random(6), 'status' => 'published', 'sort_order' => 0]);
        $model = VehicleModel::create(['vehicle_brand_id' => $brand->id, 'name' => 'Testmodel', 'slug' => 'testmodel-' . Str::random(6), 'status' => 'published', 'sort_order' => 0]);
        $this->car = Product::create([
            'vehicle_brand_id' => $brand->id, 'vehicle_model_id' => $model->id, 'title' => 'Test Car 2020',
            'slug' => 'test-car-' . Str::random(8), 'price' => 8200, 'status' => 'published', 'sort_order' => 0, 'is_featured' => 0,
        ]);
    }

    private function fakePaypal(string $captured = '820.00', string $status = 'COMPLETED'): void
    {
        Http::fake([
            '*/v1/oauth2/token' => Http::response(['access_token' => 'fake-token'], 200),
            '*/v2/checkout/orders/*/capture' => Http::response([
                'status' => $status,
                'purchase_units' => [['payments' => ['captures' => [['amount' => ['value' => $captured]]]]]],
            ], 201),
            '*/v2/checkout/orders' => Http::response(['id' => 'PAYPAL-ORDER-1'], 201),
        ]);
    }

    private function form(array $extra = []): array
    {
        return array_merge([
            'items' => [$this->car->slug],
            'name' => 'Pay Tester', 'email' => 'pay-' . Str::random(8) . '@local.test',
            'phone' => '+254 700 000000', 'country' => 'Kenya', 'city' => 'Mombasa',
            'payment_percentage' => 10,
        ], $extra);
    }

    public function test_a_signed_in_customer_pays_and_the_order_is_written(): void
    {
        $this->fakePaypal();
        $user = User::create(['first_name' => 'Pay', 'last_name' => 'Tester', 'email' => 'payer-' . Str::random(6) . '@local.test',
            'phone' => '1', 'country' => 'X', 'password' => bcrypt('Secret#2026'), 'status' => 1, 'role' => 'customer']);
        $token = $user->createToken('test')->plainTextToken;

        $start = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/app/checkout/pay', $this->form(['email' => $user->email]))
            ->assertOk()->json();
        $this->assertSame(820.0, (float) $start['payNow']);
        $key = basename($start['url']);

        $this->get('/app/pay/' . $key)->assertOk()->assertSee('Pay with PayPal or card')->assertSee('$820.00');
        $this->postJson("/api/app/pay/{$key}/create")->assertOk()->assertJson(['id' => 'PAYPAL-ORDER-1']);

        $done = $this->postJson("/api/app/pay/{$key}/capture", ['orderID' => 'PAYPAL-ORDER-1'])->assertOk()->json();
        $this->assertStringStartsWith('sbkapp://pay-result?status=paid&order=SBK-', $done['redirect']);

        $order = CheckoutOrder::where('paypal_order_id', 'PAYPAL-ORDER-1')->firstOrFail();
        $this->assertSame($user->id, (int) $order->user_id);
        $this->assertSame('PayPal', $order->payment_method);
        $this->assertSame('COMPLETED', $order->payment_status);
        $this->assertSame('processing', $order->status);
        $this->assertEquals(820, (float) $order->paid_amount);
        $this->assertEquals(7380, (float) $order->remaining_amount);
        $this->assertSame(1, $order->items()->count());

        // The key is used once.
        $this->postJson("/api/app/pay/{$key}/capture", ['orderID' => 'PAYPAL-ORDER-1'])->assertStatus(410);
        $this->assertSame(1, CheckoutOrder::where('paypal_order_id', 'PAYPAL-ORDER-1')->count());
    }

    public function test_a_new_customer_gets_an_account_and_the_order(): void
    {
        $this->fakePaypal('8200.00');
        $form = $this->form(['payment_percentage' => 100, 'password' => 'Secret#2026', 'password_confirmation' => 'Secret#2026']);
        $start = $this->postJson('/api/app/checkout/pay', $form)->assertOk()->json();
        $this->assertNotEmpty($start['signedIn']['token']);
        $key = basename($start['url']);
        $this->postJson("/api/app/pay/{$key}/create")->assertOk();
        $this->postJson("/api/app/pay/{$key}/capture", ['orderID' => 'PAYPAL-ORDER-1'])->assertOk();

        $user = User::where('email', $form['email'])->firstOrFail();
        $order = CheckoutOrder::where('user_id', $user->id)->firstOrFail();
        $this->assertSame('paid', $order->status);
        $this->assertEquals(0, (float) $order->remaining_amount);
    }

    public function test_the_website_rules_still_apply(): void
    {
        $this->fakePaypal();
        // A new email without a password, as on the website, is refused.
        $this->postJson('/api/app/checkout/pay', $this->form())->assertStatus(422)->assertJsonValidationErrors('password');
        // At $3,000 or less the least anyone pays now is 25%.
        $cheap = Product::create(['vehicle_brand_id' => $this->car->vehicle_brand_id, 'vehicle_model_id' => $this->car->vehicle_model_id,
            'title' => 'Cheap Car', 'slug' => 'cheap-car-' . Str::random(8), 'price' => 2000, 'status' => 'published', 'sort_order' => 0, 'is_featured' => 0]);
        $start = $this->postJson('/api/app/checkout/pay', $this->form(['items' => [$cheap->slug], 'password' => 'Secret#2026', 'password_confirmation' => 'Secret#2026']))
            ->assertOk()->json();
        $this->assertSame(500.0, (float) $start['payNow']);
    }

    public function test_a_short_capture_writes_no_order(): void
    {
        $this->fakePaypal('10.00');
        $start = $this->postJson('/api/app/checkout/pay', $this->form(['password' => 'Secret#2026', 'password_confirmation' => 'Secret#2026']))->json();
        $key = basename($start['url']);
        $this->postJson("/api/app/pay/{$key}/capture", ['orderID' => 'PAYPAL-ORDER-1'])->assertStatus(422);
        $this->assertSame(0, CheckoutOrder::where('paypal_order_id', 'PAYPAL-ORDER-1')->count());
    }

    public function test_without_paypal_settings_the_website_s_words(): void
    {
        config(['services.paypal.client_id' => null]);
        $this->postJson('/api/app/checkout/pay', $this->form())->assertStatus(422)->assertJson(['message' => 'PayPal is not configured yet.']);
        $this->get('/app/pay/' . Str::random(40))->assertOk()->assertSee('This payment page has expired');
    }
}
