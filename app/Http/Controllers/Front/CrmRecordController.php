<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\Crm\CrmClient;
use App\Services\Crm\CrmFileCache;
use App\Services\Crm\CrmRecord;
use Illuminate\Http\Request;

/**
 * A logged-in customer's own car photos, documents and invoice PDFs from the
 * CRM. Anything outside their own record - including rejected invoices - is
 * "not found".
 */
class CrmRecordController extends Controller
{
    public function file(Request $request, string $file, CrmClient $crm, CrmFileCache $cache)
    {
        $user = $request->user();
        abort_unless($user->hasCrmRecord() && CrmRecord::isId($file), 404);

        $record = $crm->customerRecord($user->crm_email);
        $found = $record ? CrmRecord::findFile($record, $file, true) : null;
        abort_unless($found, 404);

        return $cache->fileResponse($found, $request->boolean('download')) ?? abort(502, 'Please try again in a minute.');
    }

    public function pdf(Request $request, string $invoice, CrmClient $crm, CrmFileCache $cache)
    {
        $user = $request->user();
        abort_unless($user->hasCrmRecord() && CrmRecord::isId($invoice), 404);

        $record = $crm->customerRecord($user->crm_email);
        $found = $record ? CrmRecord::findInvoiceWithPdf($record, $invoice, true) : null;
        abort_unless($found, 404);

        return $cache->pdfResponse($found, $request->boolean('download')) ?? abort(502, 'Please try again in a minute.');
    }
}
