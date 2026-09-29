<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * One agent a customer belongs to, by the agent's email (lower-case): the CRM
 * lead's owner (source "crm", kept in step by CrmCustomerSync) or whoever added
 * the customer in the admin panel (source "panel").
 */
class CustomerAgent extends Model
{
    public const SOURCE_CRM = 'crm';
    public const SOURCE_PANEL = 'panel';

    protected $guarded = [];

    private static ?bool $ready = null;

    /** False until the migration has run on this server. */
    public static function ready(): bool
    {
        return self::$ready ??= Schema::hasTable('customer_agents');
    }

    public static function normalize(?string $email): string
    {
        return strtolower(trim((string) $email));
    }

    /** Records that $user belongs to the agent with $email (no-op if already so). */
    public static function assign(User $user, ?string $email, string $source): void
    {
        $email = self::normalize($email);
        if (!self::ready() || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        self::firstOrCreate(['user_id' => $user->id, 'agent_email' => $email], ['source' => $source]);
    }
}
