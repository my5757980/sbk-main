<?php

namespace App\Services\Crm;

use RuntimeException;

/**
 * Signs what the CRM accepts from this website: the "CRM Global" sign-in
 * links and reads of the CRM's /api/external/* records - and the "Auction"
 * sign-in links the auction portal accepts (its sso.php, same key pair).
 *
 * RSA-SHA256 with this website's private key (config services.crm); the CRM
 * verifies with the public half (official-crm src/lib/website-link.ts).
 * A token is v1.<base64url JSON>.<base64url signature over "v1.<JSON part>">.
 */
class CrmSigner
{
    private const ISSUER = 'sbk-website';
    private const AUDIENCE = 'sbk-crm';

    public static function configured(): bool
    {
        return self::privateKeyPem() !== null;
    }

    /** A sign-in link for the CRM user with this email: one use, one minute. */
    public function ssoToken(string $email): string
    {
        $now = time();

        return $this->sign([
            'iss' => self::ISSUER,
            'aud' => self::AUDIENCE,
            'purpose' => 'sso',
            'sub' => strtolower(trim($email)),
            'iat' => $now,
            'exp' => $now + 60,
            'nonce' => bin2hex(random_bytes(16)),
        ]);
    }

    /**
     * The same for the auction's admin panel (auction portal sso.php): its own
     * audience, so a CRM link can never be used on the auction or the other way round.
     */
    public function auctionSsoToken(string $email): string
    {
        $now = time();

        return $this->sign([
            'iss' => self::ISSUER,
            'aud' => 'sbk-auction',
            'purpose' => 'sso',
            'sub' => strtolower(trim($email)),
            'iat' => $now,
            'exp' => $now + 60,
            'nonce' => bin2hex(random_bytes(16)),
        ]);
    }

    /** Authorises one read: bound to its method, path and query, two minutes. */
    public function apiToken(string $method, string $path, string $query = ''): string
    {
        $now = time();

        return $this->sign([
            'iss' => self::ISSUER,
            'aud' => self::AUDIENCE,
            'purpose' => 'api',
            'iat' => $now,
            'exp' => $now + 120,
            'nonce' => bin2hex(random_bytes(16)),
            'm' => strtoupper($method),
            'p' => $path,
            'q' => $query,
        ]);
    }

    private function sign(array $claims): string
    {
        $pem = self::privateKeyPem();
        if ($pem === null) {
            throw new RuntimeException('CRM_PRIVATE_KEY is not set.');
        }

        $key = openssl_pkey_get_private($pem);
        if ($key === false) {
            throw new RuntimeException('CRM_PRIVATE_KEY is not a valid private key.');
        }

        $payload = self::base64url(json_encode($claims, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $signature = '';
        if (!openssl_sign('v1.' . $payload, $signature, $key, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Could not sign the CRM token.');
        }

        return 'v1.' . $payload . '.' . self::base64url($signature);
    }

    private static function privateKeyPem(): ?string
    {
        $value = trim((string) config('services.crm.private_key'));
        if ($value !== '') {
            if (str_contains($value, 'BEGIN')) {
                return str_replace('\n', "\n", $value);
            }
            $decoded = base64_decode($value, true);

            return $decoded !== false && str_contains($decoded, 'BEGIN') ? $decoded : null;
        }

        $path = trim((string) config('services.crm.private_key_path'));
        if ($path !== '') {
            $isAbsolute = str_starts_with($path, '/') || preg_match('/^[A-Za-z]:[\\\\\/]/', $path);
            $full = $isAbsolute ? $path : base_path($path);
            if (is_readable($full)) {
                $pem = file_get_contents($full);

                return $pem !== false && $pem !== '' ? $pem : null;
            }
        }

        return null;
    }

    private static function base64url(string $bytes): string
    {
        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
