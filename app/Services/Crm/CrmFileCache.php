<?php

namespace App\Services\Crm;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Car photos, documents and invoice PDFs from the CRM, fetched once and then
 * served from this website's own disk (storage/app/crm-cache). Callers must
 * already have checked that the file belongs to the record being viewed.
 */
class CrmFileCache
{
    public function __construct(private CrmClient $crm)
    {
    }

    public function fileResponse(array $file, bool $download = false): ?Response
    {
        $id = (string) ($file['id'] ?? '');
        if (!CrmRecord::isId($id)) {
            return null;
        }

        return $this->respond(
            'crm-cache/files/' . $id,
            '/api/external/files/' . $id,
            (string) ($file['mimetype'] ?? 'application/octet-stream'),
            (string) ($file['filename'] ?? $id),
            $download
        );
    }

    public function pdfResponse(array $invoice, bool $download = false): ?Response
    {
        $id = (string) ($invoice['id'] ?? '');
        if (!CrmRecord::isId($id)) {
            return null;
        }

        // A re-uploaded PDF gets a new cache entry.
        $version = substr(sha1((string) ($invoice['pdf']['uploadedAt'] ?? '')), 0, 12);

        return $this->respond(
            'crm-cache/pdf/' . $id . '-' . $version . '.pdf',
            '/api/external/invoices/' . $id . '/pdf',
            'application/pdf',
            (string) ($invoice['pdf']['filename'] ?? (($invoice['number'] ?? 'invoice') . '.pdf')),
            $download
        );
    }

    private function respond(string $cachePath, string $crmPath, string $mimetype, string $filename, bool $download): ?Response
    {
        $disk = Storage::disk('local');

        if (!$disk->exists($cachePath)) {
            $bytes = $this->crm->download($crmPath);
            if ($bytes === null) {
                return null;
            }
            $disk->put($cachePath, $bytes);
        }

        return response()->file($disk->path($cachePath), [
            'Content-Type' => $mimetype,
            'Content-Disposition' => self::disposition($download ? 'attachment' : 'inline', $filename),
            'Cache-Control' => 'private, max-age=86400',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private static function disposition(string $type, string $filename): string
    {
        $ascii = preg_replace('/[^\x20-\x7e]|["\\\\]/', '_', $filename) ?: 'file';

        return $type . '; filename="' . $ascii . '"; filename*=UTF-8\'\'' . rawurlencode($filename);
    }
}
