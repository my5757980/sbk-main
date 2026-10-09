<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\CheckoutOrder;
use App\Services\Crm\CrmClient;
use App\Services\Crm\CrmRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(Request $request, CrmClient $crmClient)
    {
        $user = $request->user();
        $orders = CheckoutOrder::with('items.product.brand', 'items.product.vehicleModel')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // The customer's purchases recorded in the SBK CRM (My Invoices tab).
        $crm = null;
        if ($user->hasCrmRecord()) {
            $record = $crmClient->customerRecord($user->crm_email);
            $crm = [
                'record' => $record ? CrmRecord::forAudience($record, true) : null,
                'unavailable' => $record === null,
                'fileUrl' => fn ($file, $download = false) => route('profile.crm.file', ['file' => $file['id']] + ($download ? ['download' => 1] : [])),
                'pdfUrl' => fn ($invoice, $download = false) => route('profile.crm.pdf', ['invoice' => $invoice['id']] + ($download ? ['download' => 1] : [])),
                'audience' => 'customer',
            ];
        }

        return view('front.profile.index', compact('user', 'orders', 'crm'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:192'],
            'email' => ['required', 'email', 'max:192', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:120'],
            'profile_picture' => ['nullable', 'image', 'max:4096'],
        ]);

        [$firstName, $lastName] = $this->splitName($validated['name']);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && file_exists(uploadsDir('front') . $user->profile_picture)) {
                unlink(uploadsDir('front') . $user->profile_picture);
            }

            $file = $request->file('profile_picture');
            $filename = 'profile-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(uploadsDir('front'), $filename);
            $validated['profile_picture'] = $filename;
        }

        $emailChanged = strtolower($validated['email']) !== strtolower($user->email);

        $user->update([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'country' => $validated['country'],
            'profile_picture' => $validated['profile_picture'] ?? $user->profile_picture,
        ]);

        // A new email is unproven until a password-reset link sent to it is used;
        // until then it cannot pick up another CRM customer's records.
        if ($emailChanged) {
            $user->forceFill(['email_verified_at' => null])->save();
        }

        return redirect()->route('profile.index', ['tab' => 'edit'])->with('success', 'Profile has been updated.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.index', ['tab' => 'edit'])->with('success', 'Password has been changed.');
    }

    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [$parts[0] ?? $name, $parts[1] ?? ''];
    }
}
