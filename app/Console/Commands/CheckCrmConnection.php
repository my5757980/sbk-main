<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Crm\CrmClient;
use App\Services\Crm\CrmCustomerSync;
use App\Services\Crm\CrmSigner;
use App\Services\Crm\CrmStatus;
use Illuminate\Console\Command;

/**
 * One command that says whether the CRM link works and, if not, why:
 * php artisan crm:check
 */
class CheckCrmConnection extends Command
{
    protected $signature = 'crm:check';

    protected $description = 'Check the connection to the SBK CRM and say what to fix';

    public function handle(CrmClient $crm, CrmCustomerSync $sync): int
    {
        $this->line('CRM URL ............ ' . config('services.crm.url'));

        if (!CrmSigner::configured()) {
            $this->error('Private key ........ MISSING - put CRM_PRIVATE_KEY in .env (docs/CRM-INTEGRATION.md), then run: php artisan config:clear');

            return self::FAILURE;
        }
        try {
            app(CrmSigner::class)->ssoToken('check@example.com');
            $this->info('Private key ........ OK');
        } catch (\Throwable $e) {
            $this->error('Private key ........ INVALID - ' . $e->getMessage());

            return self::FAILURE;
        }

        if (!CrmStatus::migrated()) {
            $this->error('Database ........... the CRM columns are missing - run: php artisan migrate');

            return self::FAILURE;
        }
        $this->info('Database ........... OK');

        $customers = $crm->customers();
        if ($customers === null) {
            $error = CrmClient::lastError() ?? 'no answer';
            $this->error('CRM connection ..... FAILED - ' . $error);
            if (str_contains($error, 'cURL error 60') || str_contains($error, 'SSL certificate')) {
                $this->line('  PHP could not verify the CRM\'s HTTPS certificate. On Windows/XAMPP this usually means PHP');
                $this->line('  has no CA bundle: download https://curl.se/ca/cacert.pem, point curl.cainfo and');
                $this->line('  openssl.cafile to it in php.ini, then restart PHP.');
            }

            return self::FAILURE;
        }
        $this->info('CRM connection ..... OK - ' . count($customers) . ' CRM customers with an invoice and an email');

        $stats = $sync->run();
        if ($stats !== null) {
            $this->info(sprintf('Account sync ....... OK - %d created, %d linked, %d updated, %d waiting for email verification; %d without an email in the CRM',
                $stats['created'], $stats['linked'], $stats['updated'], $stats['pending'], $stats['without_email']));
        }
        $this->info('Linked accounts .... ' . User::whereNotNull('crm_email')->count() . ' (Admin > Users shows them with a red CRM badge)');

        return self::SUCCESS;
    }
}
