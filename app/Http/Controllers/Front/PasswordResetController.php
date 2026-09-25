<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * "Forgot password" for customers. Accounts made from the CRM start with a
 * random password; the owner sets theirs through the emailed link, which
 * also proves they own the email (and completes a pending CRM link).
 */
class PasswordResetController extends Controller
{
    public function showForgot()
    {
        return view('front.auth.forgot-password');
    }

    public function sendLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        try {
            Password::broker('users')->sendResetLink(['email' => strtolower(trim($request->email))]);
        } catch (\Throwable $e) {
            Log::error('Password reset email failed', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'We could not send the email right now. Please try again later or contact SBK.');
        }

        // The same answer either way, so the form cannot be used to find accounts.
        return back()->with('success', 'If an account exists for that email, a link to set a new password is on its way.');
    }

    public function showReset(Request $request, string $token)
    {
        return view('front.auth.reset-password', ['token' => $token, 'email' => $request->query('email', '')]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ]);

                // The emailed link proves the address belongs to this person.
                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                }
                if (!$user->crm_email && $user->crm_pending_email && strtolower($user->email) === $user->crm_pending_email) {
                    $user->crm_email = $user->crm_pending_email;
                    $user->crm_pending_email = null;
                }

                $user->save();
                event(new PasswordReset($user));
                Auth::login($user);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
        }

        $request->session()->regenerate();

        return redirect()
            ->route('profile.index', $request->user()->hasCrmRecord() ? ['tab' => 'invoices'] : [])
            ->with('success', 'Your password has been set. Welcome to your SBK account.');
    }
}
