<?php

namespace App\Services\Crm;

use Illuminate\Support\Facades\Schema;

/**
 * What stands between this website and the CRM record data, in words an
 * admin can act on. Shown on Admin > Users and by `php artisan crm:check`.
 */
class CrmStatus
{
    /** Null when the website is set up; otherwise the first thing to fix. */
    public static function problem(): ?string
    {
        if (!CrmSigner::configured()) {
            return 'This website is not connected to the CRM yet: CRM_PRIVATE_KEY is missing from the .env file (see docs/CRM-INTEGRATION.md). Until it is set, no CRM customers or records appear.';
        }

        if (!self::migrated()) {
            return 'The CRM columns are missing from the users table: run "php artisan migrate".';
        }

        if ($error = CrmClient::lastError()) {
            return 'The CRM could not be reached - ' . $error;
        }

        return null;
    }

    public static function migrated(): bool
    {
        try {
            return Schema::hasColumn('users', 'crm_email');
        } catch (\Throwable) {
            return false;
        }
    }
}
