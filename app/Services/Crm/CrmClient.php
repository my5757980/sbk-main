<?php

namespace App\Services\Crm;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Read-only access to the CRM's customer records (/api/external/*).
 *
 * The CRM stays the one source of truth: records are read live and cached
 * for a minute. Any failure returns null, so a page can say "temporarily
 * unavailable" instead of breaking; after a failure the CRM is not asked
 * again for 30 seconds, so a CRM outage never makes every page wait.
 */
class CrmClient
{
    private const DOWN_KEY = 'crm:down';

    public function __construct(private CrmSigner $signer)
    {
    }

    public function configured(): bool
    {
        return CrmSigner::configured();
    }

    /**
     * Every CRM customer with a (non-rejected) invoice and an email, with
     * totals. Null if the CRM could not be reached.
     */
    public function customers(): ?array
    {
        $data = $this->getJson('/api/external/customers');

        return is_array($data['customers'] ?? null) ? $data['customers'] : null;
    }

    /**
     * One customer's full record: ['customer' => [...]|null, 'invoices' => [...]].
     * Null if the CRM could not be reached.
     */
    public function customerRecord(string $email, bool $fresh = false): ?array
    {
        $email = strtolower(trim($email));
        $key = 'crm:record:' . sha1($email);

        if ($fresh) {
            Cache::forget($key);
        } elseif (is_array($cached = Cache::get($key))) {
            return $cached;
        }

        $data = $this->getJson('/api/external/customer', ['email' => $email]);
        if (!is_array($data) || !array_key_exists('invoices', $data)) {
            return null;
        }

        Cache::put($key, $data, now()->addSeconds(max(5, (int) config('services.crm.cache_seconds', 60))));

        return $data;
    }

    /** Bytes of a car photo/document or an invoice PDF, or null. */
    public function download(string $path): ?string
    {
        $response = $this->send($path, [], max(30, (int) config('services.crm.timeout', 10)));

        return $response && $response->successful() ? $response->body() : null;
    }

    private function getJson(string $path, array $query = []): ?array
    {
        $response = $this->send($path, $query);
        if (!$response || !$response->successful()) {
            return null;
        }

        $json = $response->json();

        return is_array($json) ? $json : null;
    }

    private function send(string $path, array $query = [], ?int $timeout = null): ?Response
    {
        if (!$this->configured() || Cache::has(self::DOWN_KEY)) {
            return null;
        }

        $queryString = http_build_query($query);
        $url = config('services.crm.url') . $path . ($queryString !== '' ? '?' . $queryString : '');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'SBK-Website ' . $this->signer->apiToken('GET', $path, $queryString),
                'Accept' => 'application/json',
            ])
                ->timeout($timeout ?? (int) config('services.crm.timeout', 10))
                ->get($url);
        } catch (\Throwable $e) {
            Log::warning('CRM request failed', ['path' => $path, 'error' => $e->getMessage()]);
            Cache::put(self::DOWN_KEY, true, now()->addSeconds(30));

            return null;
        }

        if ($response->serverError()) {
            Log::warning('CRM request failed', ['path' => $path, 'status' => $response->status()]);
            Cache::put(self::DOWN_KEY, true, now()->addSeconds(30));
        } elseif ($response->status() === 401) {
            Log::error('CRM refused the website signature - check CRM_PRIVATE_KEY against the CRM public key.', ['path' => $path]);
        }

        return $response;
    }
}
