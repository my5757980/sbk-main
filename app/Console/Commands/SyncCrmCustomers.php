<?php

namespace App\Console\Commands;

use App\Services\Crm\CrmCustomerSync;
use Illuminate\Console\Command;

class SyncCrmCustomers extends Command
{
    protected $signature = 'crm:sync-customers';

    protected $description = 'Create or link a website account for every CRM customer (runs every 5 minutes)';

    public function handle(CrmCustomerSync $sync): int
    {
        $stats = $sync->run();

        if ($stats === null) {
            $this->error('The CRM could not be reached (check CRM_URL and CRM_PRIVATE_KEY).');

            return self::FAILURE;
        }

        $this->info(sprintf(
            '%d CRM customers: %d created, %d linked, %d updated, %d waiting for the owner to verify their email; %d without an email in the CRM.',
            $stats['customers'],
            $stats['created'],
            $stats['linked'],
            $stats['updated'],
            $stats['pending'],
            $stats['without_email']
        ));

        return self::SUCCESS;
    }
}
