<?php

namespace App\Services\Crm;

use App\Models\CustomerAgent;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Gives every CRM customer (anyone with an invoice and an email in the CRM) a
 * website account linked to their CRM record by email.
 *
 * Linking rules - a customer's invoices, photos and documents must never
 * reach someone who merely typed their email into the public sign-up form:
 *  - no website account with that email: one is created (role customer, a
 *    random password; the customer sets their own through "Forgot password");
 *  - an account created by this sync, or one whose email has been proved (a
 *    completed password reset sets email_verified_at): linked;
 *  - any other existing account: left unlinked and marked "pending"; it links
 *    itself once the owner completes a password reset, or an admin links it.
 * A password or role is never changed, and profile fields are only filled
 * where the website has nothing yet.
 */
class CrmCustomerSync
{
    public const LAST_RUN_KEY = 'crm:last-sync';

    public function __construct(private CrmClient $crm)
    {
    }

    /** @return array<string,int>|null counts per outcome, or null if the CRM could not be reached */
    public function run(): ?array
    {
        Cache::forever(self::LAST_RUN_KEY . ':attempt', now()->toIso8601String());

        $customers = $this->crm->customers();
        if ($customers === null) {
            return null;
        }

        $stats = ['customers' => 0, 'created' => 0, 'linked' => 0, 'updated' => 0, 'pending' => 0];

        foreach ($customers as $customer) {
            $email = strtolower(trim((string) ($customer['email'] ?? '')));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $stats['customers']++;
            $stats[$this->syncOne($email, $customer)]++;
        }

        Cache::forever(self::LAST_RUN_KEY, now()->toIso8601String());

        return $stats;
    }

    /** Run at most once per $minutes (used when an admin opens the Users list). */
    public function runIfStale(int $minutes = 5): ?array
    {
        $lastAttempt = Cache::get(self::LAST_RUN_KEY . ':attempt');
        if ($lastAttempt && now()->diffInSeconds($lastAttempt) < $minutes * 60) {
            return null;
        }

        return $this->run();
    }

    public static function lastRun(): ?string
    {
        return Cache::get(self::LAST_RUN_KEY);
    }

    private function syncOne(string $email, array $customer): string
    {
        $summary = [
            'name' => (string) ($customer['name'] ?? ''),
            'invoices' => (int) ($customer['invoiceCount'] ?? 0),
            'total' => (float) ($customer['total'] ?? 0),
            'paid' => (float) ($customer['paid'] ?? 0),
            'balance' => (float) ($customer['balance'] ?? 0),
            'last_invoice_at' => $customer['lastInvoiceAt'] ?? null,
        ];

        $user = User::where('crm_email', $email)->first();
        $outcome = 'updated';

        if (!$user) {
            $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

            if ($user) {
                // An account already tied to another CRM customer is never re-pointed
                // (someone could otherwise edit their email into a stranger's).
                $linkedElsewhere = $user->crm_email && $user->crm_email !== $email;
                $trusted = !$linkedElsewhere && ($user->crm_source || $user->email_verified_at);

                if (!$trusted) {
                    if (!$linkedElsewhere && $user->crm_pending_email !== $email) {
                        $user->forceFill(['crm_pending_email' => $email])->save();
                    }

                    return 'pending';
                }

                $outcome = 'linked';
            } else {
                [$firstName, $lastName] = $this->splitName(
                    ($customer['name'] ?? '') ?: (($customer['contactPerson'] ?? '') ?: 'SBK Customer')
                );
                $user = new User();
                $user->forceFill([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => '',
                    'password' => Hash::make(Str::random(40)),
                    'status' => 1,
                    'role' => 'customer',
                    'crm_source' => true,
                ]);
                $outcome = 'created';
            }
        }

        $user->forceFill([
            'crm_email' => $email,
            'crm_pending_email' => null,
            'crm_summary' => $summary,
            'crm_synced_at' => now(),
            'phone' => $user->phone ?: Str::limit((string) ($customer['phone'] ?? ''), 50, ''),
            'country' => $user->country ?: (($customer['country'] ?? '') ?: null),
        ])->save();

        // The agent(s) whose leads these are, so each agent sees their own
        // customers. A CRM that does not send agentEmails leaves them as they are.
        if (isset($customer['agentEmails']) && is_array($customer['agentEmails'])) {
            $this->syncAgents($user, $customer['agentEmails']);
        }

        return $outcome;
    }

    /** The customer's "crm" owners become exactly the CRM's lead owners; "panel" owners stay. */
    private function syncAgents(User $user, array $emails): void
    {
        if (!CustomerAgent::ready()) {
            return;
        }

        $emails = collect($emails)
            ->map(fn ($email) => CustomerAgent::normalize(is_string($email) ? $email : ''))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();

        CustomerAgent::where('user_id', $user->id)
            ->where('source', CustomerAgent::SOURCE_CRM)
            ->whereNotIn('agent_email', $emails->all())
            ->delete();

        foreach ($emails as $email) {
            CustomerAgent::assign($user, $email, CustomerAgent::SOURCE_CRM);
        }
    }

    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [$parts[0] ?? $name, $parts[1] ?? ''];
    }
}
