<?php

namespace App\Services\Crm;

/**
 * Helpers over a CRM customer record as returned by CrmClient::customerRecord().
 *
 * Customers never see rejected invoices; staff see everything. Files and PDFs
 * are only ever served when they are part of the record being viewed, so a
 * guessed id belonging to someone else is simply "not found".
 */
class CrmRecord
{
    /** The record as the given audience may see it, with its totals. */
    public static function forAudience(array $record, bool $isCustomer): array
    {
        $invoices = array_values(array_filter(
            $record['invoices'] ?? [],
            fn ($invoice) => !$isCustomer || ($invoice['status'] ?? '') !== 'rejected'
        ));

        $countable = array_filter($invoices, fn ($invoice) => ($invoice['status'] ?? '') !== 'rejected');
        $total = array_sum(array_map(fn ($invoice) => (float) ($invoice['cnfPrice'] ?? 0), $countable));
        $paid = array_sum(array_map(fn ($invoice) => (float) ($invoice['paid'] ?? 0), $countable));

        return [
            'customer' => $record['customer'] ?? null,
            'invoices' => $invoices,
            'totals' => [
                'invoices' => count($countable),
                'total' => $total,
                'paid' => $paid,
                'balance' => $total - $paid,
                'photos' => count(self::files($invoices, true)),
                'documents' => count(self::files($invoices, false)),
            ],
        ];
    }

    public static function findFile(array $record, string $fileId, bool $isCustomer): ?array
    {
        foreach (self::forAudience($record, $isCustomer)['invoices'] as $invoice) {
            foreach ($invoice['units'] ?? [] as $unit) {
                foreach ($unit['files'] ?? [] as $file) {
                    if (($file['id'] ?? null) === $fileId) {
                        return $file;
                    }
                }
            }
        }

        return null;
    }

    public static function findInvoiceWithPdf(array $record, string $invoiceId, bool $isCustomer): ?array
    {
        foreach (self::forAudience($record, $isCustomer)['invoices'] as $invoice) {
            if (($invoice['id'] ?? null) === $invoiceId && !empty($invoice['pdf'])) {
                return $invoice;
            }
        }

        return null;
    }

    /** CRM ids are short hex strings; anything else never reaches a path. */
    public static function isId(string $id): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_-]{6,64}$/', $id);
    }

    private static function files(array $invoices, bool $images): array
    {
        $out = [];
        foreach ($invoices as $invoice) {
            foreach ($invoice['units'] ?? [] as $unit) {
                foreach ($unit['files'] ?? [] as $file) {
                    if ((bool) ($file['isImage'] ?? false) === $images) {
                        $out[] = $file;
                    }
                }
            }
        }

        return $out;
    }
}
