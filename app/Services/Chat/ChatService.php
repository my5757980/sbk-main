<?php

namespace App\Services\Chat;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

/**
 * The live chat - everything the pages and the API share.
 *
 * A port of the SBK chat's includes/lib.php (MohsinRaza2000/SBK-Chat), the chat
 * on sbkautotrading.com: the same rules, function for function, running on THIS
 * website's own database and people. Nobody is copied from anywhere:
 *
 *   STAFF     an active admin-panel account whose role has "Chat desk"
 *             (chat.desk), or the system administrator. A staff member signed in
 *             on the website's front is their admin account, matched by e-mail.
 *   CUSTOMER  a website user (role customer). Signed in, they walk straight in.
 *   VISITOR   nobody signed in: name, e-mail and telephone once, and a cookie so
 *             they are the same person tomorrow.
 *
 * The chat is on the same domain as the website, so the session already says
 * who is here - the old site's signed handover between two domains is gone.
 *
 * Times are UTC 'Y-m-d H:i:s', written and compared by PHP (chat.js reads them
 * as UTC), so MySQL and SQLite behave the same.
 */
class ChatService
{
    public const GUEST_COOKIE = 'sbk_guest';

    private bool $staffLoaded = false;
    private ?array $staffRow = null;
    private bool $guestLoaded = false;
    private ?array $guestRow = null;
    /** @var array<int, array|null> staff by admins.id */
    private array $people = [];

    /* ================================================================ basics */

    public static function now(): string
    {
        return gmdate('Y-m-d H:i:s');
    }

    public static function ago(int $seconds): string
    {
        return gmdate('Y-m-d H:i:s', time() - $seconds);
    }

    /** Only what a browser should be handed back. */
    public static function json($value, int $code = 200): JsonResponse
    {
        return response()->json($value, $code, [
            'Cache-Control' => 'no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** Answer now and stop - the old jsonOut() that exited. */
    public static function stop($value, int $code = 200): never
    {
        throw new HttpResponseException(self::json($value, $code));
    }

    /* =================================================================== staff */

    /** One member of staff (admins.id), shaped as the old wpUser(), or null. */
    public function person(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }
        if (array_key_exists($id, $this->people)) {
            return $this->people[$id];
        }
        $admin = Admin::with('role.permissions')->find($id);

        return $this->people[$id] = $admin ? $this->personOf($admin) : null;
    }

    /**
     * Is this person the desk, may a customer see them, do they see everything?
     *
     * The role decides, through Roles & Permissions: "Chat desk" (chat.desk)
     * and "See every conversation" (chat.view_all). Then the per-person "Show in
     * the chat" setting (admins.chat_visible) can overrule who a CUSTOMER sees,
     * as the old profile tick box did - 'yes' always, 'no' never. It never takes
     * the desk away from anybody: that is a different question.
     *
     * The system administrator reads every conversation and, like the old site's
     * Admin account, is not in a customer's list unless set to 'yes'.
     */
    private function personOf(Admin $a): array
    {
        $perms = $a->role ? $a->role->permissions->pluck('name')->all() : [];
        $system = (int) $a->is_system_admin === 1;
        $active = (int) $a->is_active === 1;
        $flag = (string) ($a->chat_visible ?? '');
        $desk = $active && ($system || in_array('chat.desk', $perms, true) || $flag === 'yes');
        $listed = $active && ($flag === 'yes' || ($flag !== 'no' && $desk && !$system));
        $name = trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? ''));

        return [
            'id' => (int) $a->id,
            'name' => $name !== '' ? $name : (string) $a->email,
            'has_name' => true,
            'email' => (string) $a->email,
            'phone' => (string) ($a->phone ?? ''),
            'role_label' => $a->role ? (string) $a->role->name : ($system ? 'Administrator' : ''),
            'is_staff' => $desk,
            'is_listed' => $listed,
            'is_admin' => $active && ($system || in_array('chat.view_all', $perms, true)),
        ];
    }

    /** A website account that is staff rather than a customer. */
    private static function isStaffAccount(?User $user): bool
    {
        return $user && ($user->role ?? 'customer') !== 'customer';
    }

    /**
     * The member of staff at this screen, or null.
     *
     * Checked on every request: somebody whose role loses the desk, or whose
     * account is switched off, loses it on their very next poll.
     */
    public function staff(): ?array
    {
        if ($this->staffLoaded) {
            return $this->staffRow;
        }
        $this->staffLoaded = true;

        $admin = auth('admin')->user();
        if (!$admin) {
            $user = auth('web')->user();
            if (self::isStaffAccount($user)) {
                $admin = Admin::where('email', $user->email)->first();
            }
        }
        $p = $admin ? $this->person((int) $admin->id) : null;

        return $this->staffRow = ($p && $p['is_staff']) ? $p : null;
    }

    /** May this member of staff read every conversation? ("See every conversation") */
    public function staffSeesEverything(): bool
    {
        $me = $this->staff();

        return (bool) ($me && $me['is_admin']);
    }

    /**
     * The people a customer may write to: the desk, online first, then by name.
     * Built by ASKING for staff, never by removing customers from everybody.
     */
    public function staffDirectory(): array
    {
        $people = [];
        foreach (Admin::with('role.permissions')->where('is_active', 1)->get() as $a) {
            $p = $this->people[(int) $a->id] = $this->personOf($a);
            if (!$p['is_listed']) {
                continue;
            }
            $people[$p['id']] = [
                'id' => $p['id'],
                'name' => $p['name'],
                'username' => $p['email'],
                'role_label' => $p['role_label'],
                'online' => false,
            ];
        }
        if (!$people) {
            return [];
        }

        $online = DB::table('sbk_chat_presence')
            ->where('who', 'staff')
            ->whereIn('who_id', array_keys($people))
            ->where('last_ping', '>', self::ago((int) config('chat.online_seconds', 45)))
            ->pluck('who_id');
        foreach ($online as $id) {
            if (isset($people[(int) $id])) {
                $people[(int) $id]['online'] = true;
            }
        }

        $out = array_values($people);
        usort($out, function ($a, $b) {
            if ($a['online'] !== $b['online']) {
                return $a['online'] ? -1 : 1;
            }

            return strcasecmp($a['name'], $b['name']);
        });

        return $out;
    }

    /** Staff names for a list of ids, in one question. */
    public function staffNames(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', array_filter($ids))));
        if (!$ids) {
            return [];
        }
        $out = [];
        foreach (Admin::whereIn('id', $ids)->get(['id', 'first_name', 'last_name', 'email']) as $a) {
            $name = trim(($a->first_name ?? '') . ' ' . ($a->last_name ?? ''));
            $out[(int) $a->id] = $name !== '' ? $name : (string) $a->email;
        }

        return $out;
    }

    /* ================================================================ customers */

    /** The customer or visitor at this screen, or null. An account beats a cookie. */
    public function guest(): ?array
    {
        if ($this->guestLoaded) {
            return $this->guestRow;
        }
        $this->guestLoaded = true;

        $user = auth('web')->user();
        if ($user && !self::isStaffAccount($user)) {
            $row = DB::table('sbk_chat_guests')->where('user_id', $user->id)->first();

            return $this->guestRow = $row ? (array) $row : $this->guestForUser($user);
        }

        $tok = (string) request()->cookie(self::GUEST_COOKIE, '');
        if (!preg_match('/^[a-f0-9]{40}$/', $tok)) {
            return $this->guestRow = null;
        }
        $row = DB::table('sbk_chat_guests')->where('token', $tok)->first();

        return $this->guestRow = $row ? (array) $row : null;
    }

    /** Is this "name" really just the front of somebody's e-mail (or their login)? */
    public static function looksLikeLogin($name, $login, $email): bool
    {
        $n = strtolower(trim((string) $name));
        if ($n === '' || str_contains($n, '@')) {
            return true;
        }
        $pre = strtolower((string) strstr((string) $email, '@', true));

        return $n === strtolower((string) $login) || ($pre !== '' && $n === $pre);
    }

    /**
     * The chat's row for a customer signed in to the website, made or refreshed.
     *
     * Matched on the account first and the e-mail second, so somebody who talked
     * to the desk as a visitor and has since made an account keeps the
     * conversation they already had.
     */
    public function guestForUser(User $u): array
    {
        $email = strtolower(trim((string) $u->email));
        $real = trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? ''));
        $hasName = $real !== '' && !self::looksLikeLogin($real, '', $email);
        $name = $real !== '' ? $real : (string) strstr($email, '@', true);

        $row = DB::table('sbk_chat_guests')->where('user_id', $u->id)->first();
        if (!$row && $email !== '') {
            $row = DB::table('sbk_chat_guests')->where('email', $email)
                ->where(fn ($q) => $q->whereNull('user_id')->orWhere('user_id', 0))
                ->orderByDesc('id')->first();
        }
        $row = $row ? (array) $row : null;

        /* A name the customer TYPED into the chat survives this, which runs on
           every heartbeat - unless the website has a real one. */
        if ($row && !self::looksLikeLogin($row['name'], '', $email)
            && (!$hasName || self::looksLikeLogin($name, '', $email))) {
            $name = (string) $row['name'];
        }

        /* The telephone, if the chat has none usable and the website has one.
           Never the other way round: a number typed INTO the chat is newer. */
        $sitePhone = preg_replace('/\D+/', '', (string) ($u->phone ?? ''));
        $havePhone = $row ? preg_replace('/\D+/', '', (string) $row['phone']) : '';
        $phone = (strlen($havePhone) < 7 && strlen($sitePhone) >= 7)
            ? trim((string) $u->phone)
            : ($row ? (string) $row['phone'] : '');

        $fields = [
            'user_id' => (int) $u->id,
            'name' => mb_substr($name, 0, 120),
            'email' => mb_substr($email, 0, 160),
            'phone' => mb_substr($phone, 0, 40),
            'ip' => substr((string) request()->ip(), 0, 45),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
            'last_seen' => self::now(),
        ];

        if ($row) {
            DB::table('sbk_chat_guests')->where('id', $row['id'])->update($fields);

            return array_merge($row, $fields);
        }

        $fields['token'] = bin2hex(random_bytes(20));
        $fields['created_at'] = self::now();
        $fields['id'] = DB::table('sbk_chat_guests')->insertGetId($fields);

        return $fields;
    }

    /**
     * Take the three boxes and let a visitor in. An e-mail seen before is the same
     * person, so clearing cookies does not lose them their conversation.
     */
    public function guestSignIn($name, $email, $phone): array
    {
        $name = mb_substr(trim(preg_replace('/\s+/u', ' ', (string) $name)), 0, 120);
        $email = mb_substr(strtolower(trim((string) $email)), 0, 160);
        $phone = mb_substr(trim((string) $phone), 0, 40);
        $ip = substr((string) request()->ip(), 0, 45);
        $ua = substr((string) request()->userAgent(), 0, 255);

        $row = DB::table('sbk_chat_guests')->where('email', $email)->orderByDesc('id')->first();
        if ($row) {
            $row = (array) $row;
            DB::table('sbk_chat_guests')->where('id', $row['id'])->update([
                'name' => $name, 'phone' => $phone, 'ip' => $ip, 'user_agent' => $ua, 'last_seen' => self::now(),
            ]);
            $row['name'] = $name;
            $row['phone'] = $phone;
        } else {
            $row = [
                'token' => bin2hex(random_bytes(20)), 'user_id' => null, 'name' => $name, 'email' => $email,
                'phone' => $phone, 'ip' => $ip, 'user_agent' => $ua, 'last_seen' => self::now(), 'created_at' => self::now(),
            ];
            $row['id'] = DB::table('sbk_chat_guests')->insertGetId($row);
        }

        // A year, so a customer who comes back next month is not asked again.
        Cookie::queue(Cookie::make(self::GUEST_COOKIE, $row['token'], 525600, '/', null,
            request()->isSecure(), true, false, 'lax'));
        $this->guestLoaded = true;

        return $this->guestRow = $row;
    }

    /**
     * Must this signed-in customer still give their name and telephone? Nobody
     * reaches the desk without name, e-mail and phone - the owner's rule.
     */
    public static function guestNeedsDetails(?array $g): bool
    {
        if (!$g || empty($g['user_id'])) {
            return false;
        }
        $name = trim((string) ($g['name'] ?? ''));

        return strlen(preg_replace('/\D+/', '', (string) ($g['phone'] ?? ''))) < 7
            || mb_strlen($name) < 2 || str_contains($name, '@');
    }

    public function guestSaveDetails(int $id, $name, $phone): void
    {
        DB::table('sbk_chat_guests')->where('id', $id)->update([
            'name' => mb_substr(trim(preg_replace('/\s+/u', ' ', (string) $name)), 0, 120),
            'phone' => mb_substr(trim((string) $phone), 0, 40),
            'last_seen' => self::now(),
        ]);
        $this->guestLoaded = false;
    }

    /** Nothing from the desk until a signed-in customer has given all three. */
    public static function refuseUntilDetails(?array $g, ?array $s): void
    {
        if ($g && !$s && self::guestNeedsDetails($g)) {
            self::stop(['ok' => false, 'signedout' => true, 'details' => true], 401);
        }
    }

    /** Forget the visitor cookie (leaving the chat). */
    public function forgetGuestCookie(): void
    {
        Cookie::queue(Cookie::forget(self::GUEST_COOKIE));
    }

    /* ================================================================ presence */

    /** Online means signed in to the WEBSITE: every page sends the beat. */
    public function touchPresence(string $who, int $id, string $where = 'chat'): void
    {
        DB::table('sbk_chat_presence')->upsert(
            [['who' => $who, 'who_id' => $id, 'last_ping' => self::now(), 'where_at' => $where]],
            ['who', 'who_id'],
            ['last_ping', 'where_at']
        );
    }

    public function forgetPresence(string $who, int $id): void
    {
        DB::table('sbk_chat_presence')->where('who', $who)->where('who_id', $id)->delete();
    }

    /** Two initials for the round avatar. */
    public static function initials($name): string
    {
        $parts = preg_split('/\s+/u', trim((string) $name));
        $a = mb_substr($parts[0] ?? '', 0, 1);
        $b = count($parts) > 1 ? mb_substr($parts[count($parts) - 1], 0, 1) : '';

        return mb_strtoupper($a . $b) ?: '?';
    }

    /** A stable colour per person. */
    public static function avatarHue($seed): int
    {
        return (int) (hexdec(substr(md5((string) $seed), 0, 4)) % 360);
    }

    /* ================================================================= threads */

    /** The conversation between this customer and this member of staff, made if new. */
    public function threadFor(int $guestId, int $staffId): array
    {
        $row = DB::table('sbk_chat_threads')->where('guest_id', $guestId)->where('staff_id', $staffId)->first();
        if ($row) {
            return (array) $row;
        }
        DB::table('sbk_chat_threads')->insertOrIgnore([
            'guest_id' => $guestId, 'staff_id' => $staffId, 'created_at' => self::now(),
        ]);

        return (array) DB::table('sbk_chat_threads')->where('guest_id', $guestId)->where('staff_id', $staffId)->first();
    }

    /**
     * May whoever is asking read this thread? The customer whose thread it is,
     * the member of staff it is with, or someone who sees every conversation.
     */
    public function mayReadThread(int $threadId): ?array
    {
        $t = DB::table('sbk_chat_threads')->where('id', $threadId)->first();
        if (!$t) {
            return null;
        }
        $t = (array) $t;
        $g = $this->guest();
        if ($g && (int) $t['guest_id'] === (int) $g['id']) {
            return $t;
        }
        $s = $this->staff();
        if ($s && ((int) $t['staff_id'] === (int) $s['id'] || $this->staffSeesEverything())) {
            return $t;
        }

        return null;
    }

    /** What a list shows instead of the message itself. */
    public static function previewOf($kind, $body): string
    {
        if ($kind === 'image') {
            return '📷 Photo';
        }
        if ($kind === 'voice') {
            return '🎤 Voice note';
        }
        if ($kind === 'file') {
            return '📎 File';
        }
        if ($kind === 'call') {
            // body is "voice|ended|192" - kind of call, how it ended, seconds.
            $b = explode('|', (string) $body);
            $what = (($b[0] ?? '') === 'video') ? 'Video call' : 'Voice call';
            if (($b[1] ?? '') === 'missed') {
                return '📞 Missed ' . strtolower($what);
            }
            if (($b[1] ?? '') === 'declined') {
                return '📞 ' . $what . ' declined';
            }

            return '📞 ' . $what;
        }

        return mb_substr((string) $body, 0, 150);
    }

    /** Write a message and move the thread with it (preview + unread kept on the thread). */
    public function addMessage(int $threadId, bool $fromGuest, ?int $staffId, string $kind, string $body,
                               ?string $mediaPath = null, ?string $mediaMime = null, ?int $mediaBytes = null,
                               ?int $mediaSecs = null, bool $bumpUnread = true): int
    {
        $id = DB::table('sbk_chat_messages')->insertGetId([
            'thread_id' => $threadId,
            'from_guest' => $fromGuest ? 1 : 0,
            'staff_id' => $staffId,
            'kind' => $kind,
            'body' => $body,
            'media_path' => $mediaPath,
            'media_mime' => $mediaMime,
            'media_bytes' => $mediaBytes,
            'media_secs' => $mediaSecs,
            'created_at' => self::now(),
        ]);

        /* A call that was answered is not news to the person who answered it -
           only a MISSED call should light their bell. */
        $col = $fromGuest ? 'staff_unread' : 'guest_unread';
        DB::table('sbk_chat_threads')->where('id', $threadId)->update([
            'last_message_at' => self::now(),
            'last_preview' => mb_substr(self::previewOf($kind, $body), 0, 160),
            $col => DB::raw($col . ' + ' . ($bumpUnread ? 1 : 0)),
        ]);

        // The customer's phone hears about the desk's message (the SBK app).
        \App\Services\App\AppPush::afterMessage($threadId, $fromGuest, $staffId, $kind, $body, $bumpUnread);

        return (int) $id;
    }

    /** One message, shaped for the browser. */
    public function messageOut(array $m, bool $viewerIsGuest): array
    {
        return [
            'id' => (int) $m['id'],
            'thread' => (int) $m['thread_id'],
            'mine' => $viewerIsGuest === ((int) $m['from_guest'] === 1),
            'kind' => $m['kind'],
            'body' => (string) $m['body'],
            'media' => $m['media_path'] ? route('chat.media', ['message' => (int) $m['id']]) : null,
            'mime' => $m['media_mime'],
            'secs' => $m['media_secs'] !== null ? (int) $m['media_secs'] : null,
            'at' => $m['created_at'],
            'delivered' => !empty($m['delivered_at']),
            'read' => !empty($m['read_at']),
        ];
    }

    public function messageRow(int $id): array
    {
        return (array) DB::table('sbk_chat_messages')->where('id', $id)->first();
    }

    /** Everything in a thread after $after. Fetching is what marks it delivered. */
    public function messagesSince(int $threadId, int $after, bool $viewerIsGuest, int $limit = 300): array
    {
        $out = [];
        $rows = DB::table('sbk_chat_messages')->where('thread_id', $threadId)->where('id', '>', $after)
            ->orderBy('id')->limit($limit)->get();
        foreach ($rows as $m) {
            $out[] = $this->messageOut((array) $m, $viewerIsGuest);
        }

        // The other side's messages have now arrived on this screen.
        DB::table('sbk_chat_messages')->where('thread_id', $threadId)
            ->where('from_guest', $viewerIsGuest ? 0 : 1)->whereNull('delivered_at')
            ->update(['delivered_at' => self::now()]);

        return $out;
    }

    /** The reader has the thread open, so what they can see is read. */
    public function markRead(int $threadId, bool $viewerIsGuest): void
    {
        $now = self::now();
        DB::table('sbk_chat_messages')->where('thread_id', $threadId)
            ->where('from_guest', $viewerIsGuest ? 0 : 1)->whereNull('read_at')
            ->update(['read_at' => $now, 'delivered_at' => DB::raw('COALESCE(delivered_at, ' . DB::getPdo()->quote($now) . ')')]);
        DB::table('sbk_chat_threads')->where('id', $threadId)->update([$viewerIsGuest ? 'guest_unread' : 'staff_unread' => 0]);
    }

    /** The ticks on the viewer's own last 50 messages in a thread. */
    public function receipts(int $threadId, bool $viewerIsGuest): array
    {
        $rc = [];
        $rows = DB::table('sbk_chat_messages')->where('thread_id', $threadId)->where('from_guest', $viewerIsGuest ? 1 : 0)
            ->orderByDesc('id')->limit(50)->get(['id', 'delivered_at', 'read_at']);
        foreach ($rows as $row) {
            $rc[] = ['id' => (int) $row->id, 'd' => !empty($row->delivered_at), 'r' => !empty($row->read_at)];
        }

        return $rc;
    }

    /**
     * The threads on a member of staff's screen. $all: every conversation, with
     * the name of the member of staff each one belongs to.
     */
    public function staffThreads(int $staffId, bool $all = false): array
    {
        $q = DB::table('sbk_chat_threads as t')
            ->join('sbk_chat_guests as g', 'g.id', '=', 't.guest_id')
            ->leftJoin('sbk_chat_presence as gp', function ($j) {
                $j->on('gp.who_id', '=', 't.guest_id')->where('gp.who', '=', 'guest');
            })
            ->select('t.*', 'g.name as guest_name', 'g.email as guest_email', 'g.phone as guest_phone', 'gp.last_ping as guest_ping');
        if (!$all) {
            $q->where('t.staff_id', $staffId);
        }
        $rows = $q->orderByRaw('t.last_message_at IS NULL')->orderByDesc('t.last_message_at')->orderByDesc('t.id')
            ->limit(300)->get();

        $cut = self::ago((int) config('chat.online_seconds', 45));
        $out = [];
        foreach ($rows as $row) {
            $row = (array) $row;
            $row['guest_online'] = !empty($row['guest_ping']) && $row['guest_ping'] > $cut;
            $out[] = $row;
        }
        if ($all && $out) {
            $names = $this->staffNames(array_column($out, 'staff_id'));
            foreach ($out as &$row) {
                $row['staff_name'] = $names[(int) $row['staff_id']] ?? '';
            }
            unset($row);
        }

        return $out;
    }

    /** The newest message waiting for this person, for the pop-up card: {id, thread, from, text}. */
    public function latestWaiting(string $who, int $id): ?array
    {
        if ($who === 'staff') {
            $row = DB::table('sbk_chat_messages as m')
                ->join('sbk_chat_threads as t', 't.id', '=', 'm.thread_id')
                ->join('sbk_chat_guests as g', 'g.id', '=', 't.guest_id')
                ->where('t.staff_id', $id)->where('m.from_guest', 1)->whereNull('m.read_at')
                ->orderByDesc('m.id')->first(['m.id', 'm.thread_id', 'm.kind', 'm.body', 'g.name as who']);
        } else {
            $row = DB::table('sbk_chat_messages as m')
                ->join('sbk_chat_threads as t', 't.id', '=', 'm.thread_id')
                ->where('t.guest_id', $id)->where('m.from_guest', 0)->whereNull('m.read_at')
                ->orderByDesc('m.id')->first(['m.id', 'm.thread_id', 'm.kind', 'm.body', 'm.staff_id as who']);
        }
        if (!$row) {
            return null;
        }
        $from = (string) $row->who;
        if ($who !== 'staff') {
            $u = $this->person((int) $row->who);
            $from = $u ? $u['name'] : 'SBK';
        }

        return [
            'id' => (int) $row->id,
            'thread' => (int) $row->thread_id,
            'from' => $from,
            'text' => self::previewOf($row->kind, $row->body),
        ];
    }

    /** How many messages are waiting for this person, for the bell. */
    public function unreadFor(string $who, int $id): int
    {
        return $who === 'staff'
            ? (int) DB::table('sbk_chat_threads')->where('staff_id', $id)->sum('staff_unread')
            : (int) DB::table('sbk_chat_threads')->where('guest_id', $id)->sum('guest_unread');
    }

    /* =================================================================== calls
       Voice and video, browser to browser. Only the handshake lives here. Who
       may be in a call is exactly who may be in the conversation it belongs to;
       someone who reads every conversation still cannot ring into another's. */

    public function callRow(int $callId): ?array
    {
        $row = DB::table('sbk_chat_calls as c')->join('sbk_chat_threads as t', 't.id', '=', 'c.thread_id')
            ->where('c.id', $callId)->first(['c.*', 't.guest_id', 't.staff_id']);

        return $row ? (array) $row : null;
    }

    public static function isCallParty(array $call, bool $viewerIsGuest, int $meId): bool
    {
        return $viewerIsGuest ? ((int) $call['guest_id'] === $meId) : ((int) $call['staff_id'] === $meId);
    }

    /** Did the viewer RECEIVE this call (rather than make it)? */
    public static function callIsIncoming(array $call, bool $viewerIsGuest): bool
    {
        return ((int) $call['from_guest'] === 1) !== $viewerIsGuest;
    }

    /** Close a call, once, and leave a line in the conversation saying so. */
    public function finishCall(array $call, string $state): bool
    {
        $changed = DB::table('sbk_chat_calls')->where('id', $call['id'])->whereIn('state', ['ringing', 'accepted'])
            ->update(['state' => $state, 'ended_at' => self::now()]);
        if ($changed < 1) {
            return false;       // somebody else closed it first; one log line per call
        }

        $secs = 0;
        if ($state === 'ended' && !empty($call['answered_at'])) {
            $secs = max(0, time() - strtotime($call['answered_at'] . ' UTC'));
        }
        $fromGuest = (int) $call['from_guest'] === 1;
        $this->addMessage((int) $call['thread_id'], $fromGuest, $fromGuest ? null : (int) $call['staff_id'],
            'call', $call['kind'] . '|' . $state . '|' . $secs, null, null, null, null, $state === 'missed');

        return true;
    }

    /** Ringing too long is missed; answered but one side gone quiet is over. */
    public function sweepCalls(): void
    {
        $ring = self::ago((int) config('chat.ring_seconds', 45));
        $stale = self::ago((int) config('chat.call_stale_seconds', 25));
        $rows = DB::table('sbk_chat_calls as c')->join('sbk_chat_threads as t', 't.id', '=', 'c.thread_id')
            ->where(function ($q) use ($ring, $stale) {
                $q->where(fn ($r) => $r->where('c.state', 'ringing')->where('c.created_at', '<', $ring))
                  ->orWhere(fn ($a) => $a->where('c.state', 'accepted')->where(function ($s) use ($stale) {
                      $s->whereRaw('COALESCE(c.guest_seen, c.answered_at) < ?', [$stale])
                        ->orWhereRaw('COALESCE(c.staff_seen, c.answered_at) < ?', [$stale]);
                  }));
            })
            ->limit(20)->get(['c.*', 't.guest_id', 't.staff_id']);
        foreach ($rows as $c) {
            $c = (array) $c;
            $this->finishCall($c, $c['state'] === 'ringing' ? 'missed' : 'ended');
        }
    }

    /** The call this person is in, or being rung by, right now - or null. */
    public function liveCallFor(bool $viewerIsGuest, int $meId): ?array
    {
        $row = DB::table('sbk_chat_calls as c')->join('sbk_chat_threads as t', 't.id', '=', 'c.thread_id')
            ->where($viewerIsGuest ? 't.guest_id' : 't.staff_id', $meId)
            ->whereIn('c.state', ['ringing', 'accepted'])
            ->orderByDesc('c.id')->first(['c.*', 't.guest_id', 't.staff_id']);

        return $row ? (array) $row : null;
    }

    /** Stamp that this side's browser is still here, for the stale check. */
    public function touchCall(array $call, bool $viewerIsGuest): void
    {
        DB::table('sbk_chat_calls')->where('id', $call['id'])->update([$viewerIsGuest ? 'guest_seen' : 'staff_seen' => self::now()]);
    }

    public function callPeerName(array $call, bool $viewerIsGuest): string
    {
        if ($viewerIsGuest) {
            $u = $this->person((int) $call['staff_id']);

            return $u ? $u['name'] : 'SBK';
        }
        $name = DB::table('sbk_chat_guests')->where('id', $call['guest_id'])->value('name');

        return $name !== null ? (string) $name : 'Customer';
    }

    /** A call, shaped for the viewer. Never carries the session descriptions. */
    public function callOut(array $call, bool $viewerIsGuest): array
    {
        return [
            'id' => (int) $call['id'],
            'thread' => (int) $call['thread_id'],
            'kind' => $call['kind'],
            'state' => $call['state'],
            'incoming' => self::callIsIncoming($call, $viewerIsGuest),
            'peer' => $this->callPeerName($call, $viewerIsGuest),
            'peerId' => $viewerIsGuest ? (int) $call['staff_id'] : (int) $call['guest_id'],
        ];
    }

    /* ================================================================== groups
       The desk talking to itself. Every member is staff; a customer never sees
       one. A group message lives in sbk_chat_messages with thread_id = 0 and
       group_id set - every thread query filters on a real thread_id. */

    public function groupRow(int $id): ?array
    {
        $row = DB::table('sbk_chat_groups')->where('id', $id)->first();

        return $row ? (array) $row : null;
    }

    public function inGroup(int $groupId, int $staffId): bool
    {
        return DB::table('sbk_chat_group_members')->where('group_id', $groupId)->where('staff_id', $staffId)->exists();
    }

    /** Membership and nothing else: seeing every conversation is not an invitation to a room. */
    public function mayReadGroup(int $groupId): ?array
    {
        $s = $this->staff();
        if (!$s || $groupId <= 0) {
            return null;
        }
        $g = $this->groupRow($groupId);

        return ($g && $this->inGroup($groupId, (int) $s['id'])) ? $g : null;
    }

    public function ownsGroup(int $groupId, int $staffId): bool
    {
        return (int) DB::table('sbk_chat_group_members')->where('group_id', $groupId)->where('staff_id', $staffId)
            ->value('is_owner') === 1;
    }

    /** The groups this member of staff is in, newest first. */
    public function groupsFor(int $staffId): array
    {
        $rows = DB::table('sbk_chat_groups as g')
            ->join('sbk_chat_group_members as m', function ($j) use ($staffId) {
                $j->on('m.group_id', '=', 'g.id')->where('m.staff_id', '=', $staffId);
            })
            ->select('g.id', 'g.name', 'g.created_by', 'g.last_message_at', 'g.last_preview', 'm.unread', 'm.is_owner')
            ->selectSub(DB::table('sbk_chat_group_members as x')->selectRaw('COUNT(*)')->whereColumn('x.group_id', 'g.id'), 'members')
            ->orderByRaw('g.last_message_at IS NULL')->orderByDesc('g.last_message_at')->orderByDesc('g.id')
            ->limit(200)->get();

        return $rows->map(fn ($r) => (array) $r)->all();
    }

    /** Who is in a group - the owner first, then in joining order. */
    public function groupMemberIds(int $groupId): array
    {
        return DB::table('sbk_chat_group_members')->where('group_id', $groupId)
            ->orderByDesc('is_owner')->orderBy('joined_at')->orderBy('staff_id')
            ->pluck('staff_id')->map(fn ($v) => (int) $v)->all();
    }

    /** Make a group. Everybody else must be staff right now. @return array {id, skipped} */
    public function createGroup($name, int $ownerId, array $memberIds): array
    {
        $name = trim(preg_replace('/\s+/u', ' ', (string) $name));
        $name = mb_substr($name === '' ? 'New group' : $name, 0, 120);

        $gid = (int) DB::table('sbk_chat_groups')->insertGetId([
            'name' => $name, 'created_by' => $ownerId, 'created_at' => self::now(),
        ]);
        DB::table('sbk_chat_group_members')->insertOrIgnore([
            'group_id' => $gid, 'staff_id' => $ownerId, 'is_owner' => 1, 'joined_at' => self::now(),
        ]);

        $skipped = 0;
        foreach (array_unique(array_map('intval', $memberIds)) as $id) {
            if ($id <= 0 || $id === $ownerId) {
                continue;
            }
            $who = $this->person($id);
            if (!$who || !$who['is_staff']) {
                $skipped++;
                continue;
            }
            DB::table('sbk_chat_group_members')->insertOrIgnore([
                'group_id' => $gid, 'staff_id' => $id, 'joined_at' => self::now(),
            ]);
        }

        return ['id' => $gid, 'skipped' => $skipped];
    }

    public function renameGroup(int $groupId, $name): bool
    {
        $name = trim(preg_replace('/\s+/u', ' ', (string) $name));
        if ($name === '') {
            return false;
        }
        DB::table('sbk_chat_groups')->where('id', $groupId)->update(['name' => mb_substr($name, 0, 120)]);

        return true;
    }

    public function addGroupMembers(int $groupId, array $ids): int
    {
        $added = 0;
        foreach (array_unique(array_map('intval', $ids)) as $id) {
            if ($id <= 0 || $this->inGroup($groupId, $id)) {
                continue;
            }
            $who = $this->person($id);
            if (!$who || !$who['is_staff']) {
                continue;
            }
            $added += DB::table('sbk_chat_group_members')->insertOrIgnore([
                'group_id' => $groupId, 'staff_id' => $id, 'joined_at' => self::now(),
            ]) > 0 ? 1 : 0;
        }

        return $added;
    }

    /**
     * Take somebody out - or let them walk out. An owner leaving hands the group
     * to whoever has been in it longest; the last one out deletes it.
     */
    public function removeGroupMember(int $groupId, int $staffId): string
    {
        $wasOwner = $this->ownsGroup($groupId, $staffId);
        DB::table('sbk_chat_group_members')->where('group_id', $groupId)->where('staff_id', $staffId)->delete();

        $left = $this->groupMemberIds($groupId);
        if (!$left) {
            DB::table('sbk_chat_messages')->where('group_id', $groupId)->delete();
            DB::table('sbk_chat_groups')->where('id', $groupId)->delete();

            return 'gone';
        }
        if ($wasOwner) {
            DB::table('sbk_chat_group_members')->where('group_id', $groupId)->where('staff_id', $left[0])->update(['is_owner' => 1]);
        }

        return 'ok';
    }

    /** Write into a group; everyone EXCEPT the writer gets one more waiting. */
    public function addGroupMessage(int $groupId, int $staffId, string $kind, string $body,
                                    ?string $mediaPath = null, ?string $mediaMime = null,
                                    ?int $mediaBytes = null, ?int $mediaSecs = null): int
    {
        $id = (int) DB::table('sbk_chat_messages')->insertGetId([
            'thread_id' => 0,
            'group_id' => $groupId,
            'from_guest' => 0,
            'staff_id' => $staffId,
            'kind' => $kind,
            'body' => $body,
            'media_path' => $mediaPath,
            'media_mime' => $mediaMime,
            'media_bytes' => $mediaBytes,
            'media_secs' => $mediaSecs,
            'created_at' => self::now(),
        ]);
        DB::table('sbk_chat_groups')->where('id', $groupId)->update([
            'last_message_at' => self::now(),
            'last_preview' => mb_substr(self::previewOf($kind, $body), 0, 160),
        ]);
        DB::table('sbk_chat_group_members')->where('group_id', $groupId)->where('staff_id', '<>', $staffId)
            ->increment('unread');

        return $id;
    }

    /** One group message, shaped for the browser - with who wrote it. */
    public function groupMessageOut(array $m, int $meId, array $names): array
    {
        $from = (int) $m['staff_id'];
        $name = (string) ($names[$from] ?? 'Someone');

        return [
            'id' => (int) $m['id'],
            'group' => (int) $m['group_id'],
            'mine' => $from === $meId,
            'kind' => $m['kind'],
            'body' => (string) $m['body'],
            'media' => $m['media_path'] ? route('chat.media', ['message' => (int) $m['id']]) : null,
            'mime' => $m['media_mime'],
            'secs' => $m['media_secs'] !== null ? (int) $m['media_secs'] : null,
            'at' => $m['created_at'],
            'who' => $name,
            'hue' => self::avatarHue($from . $name),
            'ini' => self::initials($name),
            'delivered' => true,
            'read' => false,
        ];
    }

    public function groupMessagesSince(int $groupId, int $after, int $meId, int $limit = 300): array
    {
        $rows = DB::table('sbk_chat_messages')->where('group_id', $groupId)->where('id', '>', $after)
            ->orderBy('id')->limit($limit)->get()->map(fn ($r) => (array) $r)->all();
        if (!$rows) {
            return [];
        }
        $names = $this->staffNames(array_column($rows, 'staff_id'));

        return array_map(fn ($row) => $this->groupMessageOut($row, $meId, $names), $rows);
    }

    /** Blue when EVERY other member has read it: the lowest last_read_id among them. */
    public function groupReceipts(int $groupId, int $meId, int $limit = 50): array
    {
        $others = DB::table('sbk_chat_group_members')->where('group_id', $groupId)->where('staff_id', '<>', $meId);
        $alone = !(clone $others)->exists();
        $min = $alone ? -1 : (int) (clone $others)->min('last_read_id');

        $out = [];
        foreach (DB::table('sbk_chat_messages')->where('group_id', $groupId)->where('staff_id', $meId)
                     ->orderByDesc('id')->limit($limit)->pluck('id') as $id) {
            $out[] = ['id' => (int) $id, 'd' => true, 'r' => !$alone && (int) $id <= $min];
        }

        return $out;
    }

    /** Somebody is looking at a group: nothing in it is waiting for them any more. */
    public function markGroupRead(int $groupId, int $staffId): void
    {
        $top = (int) DB::table('sbk_chat_messages')->where('group_id', $groupId)->max('id');
        DB::table('sbk_chat_group_members')->where('group_id', $groupId)->where('staff_id', $staffId)->update([
            'unread' => 0,
            'last_read_id' => DB::raw('CASE WHEN last_read_id < ' . $top . ' THEN ' . $top . ' ELSE last_read_id END'),
        ]);
    }

    public function groupUnreadFor(int $staffId): int
    {
        return (int) DB::table('sbk_chat_group_members')->where('staff_id', $staffId)->sum('unread');
    }

    /* =================================================================== media */

    /** The folder pictures and voice notes live in - outside public/. */
    public static function mediaDir(): string
    {
        return rtrim((string) config('chat.media_dir', storage_path('app/chat-media')), '/\\');
    }
}
