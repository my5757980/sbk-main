<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Services\Crm\CrmClient;
use App\Services\Crm\CrmCustomerSync;
use App\Services\Crm\CrmFileCache;
use App\Services\Crm\CrmPasswords;
use App\Services\Crm\CrmRecord;
use App\Services\Crm\CrmSigner;
use App\Services\Crm\CrmStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

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
            'CRM sync done: %d customers - %d new accounts, %d linked, %d up to date, %d waiting for email verification.',
            $stats['customers'],
            $stats['created'],
            $stats['linked'],
            $stats['updated'],
            $stats['pending']
        ));
    }

    /** Links an existing account whose email matches a CRM customer (admin vouches for it). */
    public function link($id, CrmCustomerSync $sync)
    {
        $user = User::findOrFail($id);

        if (!$user->crm_pending_email || $user->crm_email) {
            return back()->with('error', 'This account has no CRM customer waiting to be linked.');
        }

        $user->forceFill(['crm_email' => $user->crm_pending_email, 'crm_pending_email' => null])->save();
        $sync->run();

        return back()->with('success', 'The account is now linked to its CRM record.');
    }

    /** Emails the customer a link to set their own password. */
    public function sendReset($id)
    {
        $user = User::findOrFail($id);

        try {
            $status = Password::broker('users')->sendResetLink(['email' => $user->email]);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'The email could not be sent - please check the website mail settings.');
        }

        // Flash messages are printed into a script by the admin layout, so they carry no user data.
        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'A password setup link was emailed to the customer.')
            : back()->with('error', 'The email could not be sent: ' . __($status));
    }

    /**
     * A new random login password for one customer, shown once on their page
     * for staff to send them. The customer's previous password stops working.
     */
    public function generatePassword($id)
    {
        $user = User::findOrFail($id);

        if (($user->role ?? 'customer') !== 'customer') {
            return back()->with('error', 'Passwords can only be generated here for customer accounts.');
        }

        $password = CrmPasswords::assign($user);

        return redirect()->route('admin.users.show', $user->id)
            ->with('generated_password', $password);
    }

    /**
     * A separate random password for every CRM customer who has none yet, listed
     * once for staff to hand out. Customers who already received one, or set
     * their own, are left alone, so pressing it twice changes nothing.
     */
    public function generatePasswords()
    {
        $rows = [];
        foreach (CrmPasswords::awaitingPassword()->orderBy('first_name')->get() as $user) {
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
        $user = User::findOrFail($id);
        abort_unless($user->hasCrmRecord() && CrmRecord::isId($file), 404);

        $record = $crm->customerRecord($user->crm_email);
        $found = $record ? CrmRecord::findFile($record, $file, false) : null;
        abort_unless($found, 404);

        return $cache->fileResponse($found, $request->boolean('download')) ?? abort(502, 'The CRM could not be reached.');
    }

    public function pdf(Request $request, $id, string $invoice, CrmClient $crm, CrmFileCache $cache)
    {
        $user = User::findOrFail($id);
        abort_unless($user->hasCrmRecord() && CrmRecord::isId($invoice), 404);

        $record = $crm->customerRecord($user->crm_email);
        $found = $record ? CrmRecord::findInvoiceWithPdf($record, $invoice, false) : null;
        abort_unless($found, 404);

        return $cache->pdfResponse($found, $request->boolean('download')) ?? abort(502, 'The CRM could not be reached.');
    }
}
