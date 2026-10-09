<?php

namespace App\Http\Controllers\Admin;

use App\Models\CrmLead;
use App\Models\User;
use App\Services\Crm\CrmClient;
use App\Services\Crm\CrmCustomerSync;
use App\Services\Crm\CrmFileCache;
use App\Services\Crm\CrmPasswords;
use App\Services\Crm\CrmRecord;
use App\Services\Crm\CrmSigner;
use App\Services\Crm\CrmStatus;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * "CRM Global": sends the admin to the CRM with a signed one-minute link
     * for their own email. The CRM signs in its active user with that email
     * straight to the dashboard, or shows its login page when there is none.
     */
    public function sso(CrmSigner $signer)
    {
        $crmUrl = config('services.crm.url');

        if (!CrmSigner::configured()) {
            return redirect()->away($crmUrl . '/login');
        }

        $token = $signer->ssoToken(auth('admin')->user()->email);

        return redirect()->away($crmUrl . '/api/auth/sso?token=' . urlencode($token));
    }

    /**
     * "Auction": the same one-minute signed link for the auction's admin panel
     * (auction portal sso.php). It signs in the auction staff account with this
     * admin's email, or shows the auction's staff login page when there is none.
     */
    public function auctionSso(CrmSigner $signer)
    {
        $auctionUrl = config('services.auction.url');

        if (!CrmSigner::configured()) {
            return redirect()->away($auctionUrl . '/admin/login.php');
        }

        $token = $signer->auctionSsoToken(auth('admin')->user()->email);

        return redirect()->away($auctionUrl . '/sso.php?token=' . urlencode($token));
    }

    public function sync(CrmCustomerSync $sync)
    {
        if (!CrmSigner::configured() || !CrmStatus::migrated()) {
            return back()->with('error', 'The website is not connected to the CRM yet - see the red note on this page.');
        }

        $stats = $sync->run();

        if ($stats === null) {
            return back()->with('error', 'The CRM could not be reached - see the red note on this page.');
        }

        return back()->with('success', sprintf(
            'CRM sync done: %d customers - %d new accounts, %d linked, %d up to date, %d waiting for email verification; %d without an email in the CRM.',
            $stats['customers'],
            $stats['created'],
            $stats['linked'],
            $stats['updated'],
            $stats['pending'],
            $stats['without_email']
        ));
    }

    /** A CRM customer with no email (see CrmLead): their whole CRM record, for staff. */
    public function lead(Request $request, string $lead, CrmClient $crm)
    {
        $data = $this->visibleLead($lead);
        $record = $crm->leadRecord($data->lead_id, $request->boolean('refresh'));
        $crmView = [
            'record' => $record ? CrmRecord::forAudience($record, false) : null,
            'unavailable' => $record === null,
            'fileUrl' => fn ($file, $download = false) => route('admin.users.show.crm-lead-file', ['lead' => $data->lead_id, 'file' => $file['id']] + ($download ? ['download' => 1] : [])),
            'pdfUrl' => fn ($invoice, $download = false) => route('admin.users.show.crm-lead-pdf', ['lead' => $data->lead_id, 'invoice' => $invoice['id']] + ($download ? ['download' => 1] : [])),
            'audience' => 'admin',
        ];

        return view('admin.users.crm-lead', ['data' => $data, 'crm' => $crmView]);
    }

    public function leadFile(Request $request, string $lead, string $file, CrmClient $crm, CrmFileCache $cache)
    {
        $data = $this->visibleLead($lead);
        abort_unless(CrmRecord::isId($file), 404);

        $record = $crm->leadRecord($data->lead_id);
        $found = $record ? CrmRecord::findFile($record, $file, false) : null;
        abort_unless($found, 404);

        return $cache->fileResponse($found, $request->boolean('download')) ?? abort(502, 'The CRM could not be reached.');
    }

    public function leadPdf(Request $request, string $lead, string $invoice, CrmClient $crm, CrmFileCache $cache)
    {
        $data = $this->visibleLead($lead);
        abort_unless(CrmRecord::isId($invoice), 404);

        $record = $crm->leadRecord($data->lead_id);
        $found = $record ? CrmRecord::findInvoiceWithPdf($record, $invoice, false) : null;
        abort_unless($found, 404);

        return $cache->pdfResponse($found, $request->boolean('download')) ?? abort(502, 'The CRM could not be reached.');
    }

    /** A no-email CRM customer this admin may see (their own, or everyone with "See all customers"), or 404. */
    private function visibleLead(string $lead): CrmLead
    {
        abort_unless(CrmLead::ready() && CrmRecord::isId($lead), 404);

        return CrmLead::visibleTo(auth('admin')->user())->where('lead_id', $lead)->firstOrFail();
    }

    /** Links an existing account whose email matches a CRM customer (admin vouches for it). */
    public function link($id, CrmCustomerSync $sync)
    {
        $user = $this->visibleUser($id);

        if (!$user->crm_pending_email || $user->crm_email) {
            return back()->with('error', 'This account has no CRM customer waiting to be linked.');
        }

        $user->forceFill(['crm_email' => $user->crm_pending_email, 'crm_pending_email' => null])->save();
        $sync->run();

        return back()->with('success', 'The account is now linked to its CRM record.');
    }

    /**
     * A separate random password for every CRM customer who has none yet, listed
     * once for staff to hand out. Customers who already received one, or set
     * their own, are left alone, so pressing it twice changes nothing.
     */
    public function generatePasswords()
    {
        $rows = [];
        foreach (CrmPasswords::awaitingPassword()->visibleTo(auth('admin')->user())->orderBy('first_name')->get() as $user) {
            $rows[] = [
                'id' => $user->id,
                'name' => $user->name ?: $user->email,
                'email' => $user->email,
                'phone' => $user->phone,
                'password' => CrmPasswords::assign($user),
            ];
        }

        return redirect()->route('admin.users.edit.crm-passwords-result')->with('generated_passwords', $rows);
    }

    /** The one-time list from generatePasswords(); gone after a refresh. */
    public function generatedPasswords()
    {
        if (!session()->has('generated_passwords')) {
            return redirect()->route('admin.users.index');
        }

        return view('admin.users.crm-passwords', ['rows' => session('generated_passwords')]);
    }

    public function file(Request $request, $id, string $file, CrmClient $crm, CrmFileCache $cache)
    {
        $user = $this->visibleUser($id);
        abort_unless($user->hasCrmRecord() && CrmRecord::isId($file), 404);

        $record = $crm->customerRecord($user->crm_email);
        $found = $record ? CrmRecord::findFile($record, $file, false) : null;
        abort_unless($found, 404);

        return $cache->fileResponse($found, $request->boolean('download')) ?? abort(502, 'The CRM could not be reached.');
    }

    public function pdf(Request $request, $id, string $invoice, CrmClient $crm, CrmFileCache $cache)
    {
        $user = $this->visibleUser($id);
        abort_unless($user->hasCrmRecord() && CrmRecord::isId($invoice), 404);

        $record = $crm->customerRecord($user->crm_email);
        $found = $record ? CrmRecord::findInvoiceWithPdf($record, $invoice, false) : null;
        abort_unless($found, 404);

        return $cache->pdfResponse($found, $request->boolean('download')) ?? abort(502, 'The CRM could not be reached.');
    }

    /** A user this admin may see (their own customers, or everyone with "See all customers"), or 404. */
    private function visibleUser($id): User
    {
        return User::visibleTo(auth('admin')->user())->findOrFail($id);
    }
}
