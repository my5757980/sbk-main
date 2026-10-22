<?php

namespace Tests\Feature;

use App\Services\Crm\CrmRecord;
use Tests\TestCase;

class CrmRecordViewTest extends TestCase
{
    public function test_invoice_summary_renders_for_both_audiences(): void
    {
        foreach (['admin', 'customer'] as $audience) {
            foreach ([[1000, 250, 25], [0, 0, 0], [1000, 1200, 100]] as [$total, $paid, $percent]) {
                $record = CrmRecord::forAudience([
                    'customer' => ['name' => 'CRM Test Customer', 'email' => 'crm-test@example.com'],
                    'invoices' => [[
                        'id' => 'invoice123', 'number' => 'TEST-001', 'unit' => 'Test Vehicle',
                        'status' => 'approved', 'cnfPrice' => $total, 'paid' => $paid,
                    ]],
                ], $audience === 'customer');

                $view = $this->view('partials.crm.record', ['crm' => $this->crm($record, $audience)]);
                $view->assertSee('TEST-001')->assertSee($percent . '% paid')
                    ->assertSee('$' . number_format($total))->assertSee('$' . number_format($paid));
                if ($audience === 'admin') {
                    $view->assertSee('CRM Test Customer')->assertSee('crm-test@example.com');
                }
            }
        }
    }

    public function test_empty_and_unavailable_records_render(): void
    {
        $this->view('partials.crm.record', ['crm' => $this->crm(CrmRecord::forAudience([], false))])
            ->assertSee('There are no invoices in the CRM for this account yet.');
        $this->view('partials.crm.record', ['crm' => $this->crm(null)])
            ->assertSee('The CRM record could not be loaded right now.');
    }

    private function crm(?array $record, string $audience = 'admin'): array
    {
        return [
            'record' => $record,
            'unavailable' => $record === null,
            'audience' => $audience,
            'fileUrl' => fn ($file, $download = false) => '/test-file',
            'pdfUrl' => fn ($invoice, $download = false) => '/test-pdf',
        ];
    }
}
