<?php

namespace App\Services\Crm;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * A separate random login password for each customer, for staff to hand out
 * (WhatsApp, phone, email). Only the hash is stored: the plain password is
 * returned once, to the admin who generated it.
 */
class CrmPasswords
{
    // No 0/O, 1/l/I - easy to read out and type from a phone.
    private const ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';

    /** e.g. "Kp7mQ-x3RtA": ten random characters, about 57 bits. */
    public static function make(): string
    {
        $chars = '';
        for ($i = 0; $i < 10; $i++) {
            $chars .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
        }

        return substr($chars, 0, 5) . '-' . substr($chars, 5);
    }

    /** Gives the customer a new password and returns it (the only copy). */
    public static function assign(User $user): string
    {
        $password = self::make();
        self::set($user, $password);

        return $password;
    }

    /**
     * Sets the password staff chose or generated. It is marked as handed out,
     * so "Generate passwords" never replaces it later.
     */
    public static function set(User $user, string $password): void
    {
        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
            'crm_password_generated_at' => now(),
        ])->save();
    }

    /**
     * CRM customers who can log in with nothing yet: created from the CRM,
     * never given a password here, and never set one themselves.
     */
    public static function awaitingPassword()
    {
        return User::whereNotNull('crm_email')
            ->where('crm_source', true)
            ->whereNull('crm_password_generated_at')
            ->whereNull('email_verified_at')
            ->where('role', 'customer');
    }
}
