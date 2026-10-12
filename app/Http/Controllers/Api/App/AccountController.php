<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Models\CheckoutOrder;
use App\Models\User;
use App\Services\App\AppPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

/**
 * The customer's account in the SBK app - ONE account with the website: the
 * same users table, the same password, the same rules as the website's
 * Front\AuthController, PasswordResetController and ProfileController. The
 * app holds a Sanctum token instead of a session cookie.
 */
class AccountController extends Controller
{
    /** Create Account (AuthController@register). */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:192'],
            'email' => ['required', 'email', 'max:192', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:120'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        [$firstName, $lastName] = self::splitName($validated['name']);

        $user = User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'country' => $validated['country'],
            'password' => Hash::make($validated['password']),
            'status' => 1,
            'role' => 'customer',
        ]);

        return response()->json(self::signedIn($user, $request), 201);
    }

    /** Login (AuthController@login): the website's email and password. */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided login details are incorrect.',
                'errors' => ['email' => ['The provided login details are incorrect.']],
            ], 422);
        }

        return response()->json(self::signedIn($user, $request));
    }

    /** Forgot password (PasswordResetController@sendLink): the website e-mails its own link. */
    public function forgot(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        try {
            Password::broker('users')->sendResetLink(['email' => strtolower(trim($request->email))]);
        } catch (\Throwable $e) {
            Log::error('Password reset email failed (app)', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'We could not send the email right now. Please try again later or contact SBK.'], 503);
        }

        // The same answer either way, so the form cannot be used to find accounts.
        return response()->json(['message' => 'If an account exists for that email, a link to set a new password is on its way.']);
    }

    /** Logout: this phone's token only. */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['ok' => true]);
    }

    public function me(Request $request)
    {
        return response()->json(['user' => AppPresenter::user($request->user())]);
    }

    /** Edit profile (ProfileController@update), picture included. */
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

        [$firstName, $lastName] = self::splitName($validated['name']);

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

        // A new email is unproven until a password-reset link sent to it is used.
        if ($emailChanged) {
            $user->forceFill(['email_verified_at' => null])->save();
        }

        return response()->json(['message' => 'Profile has been updated.', 'user' => AppPresenter::user($user->fresh())]);
    }

    /** Change password (ProfileController@updatePassword). */
    public function password(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password:sanctum'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update(['password' => Hash::make($validated['password'])]);

        return response()->json(['message' => 'Password has been changed.']);
    }

    /** Website orders (the Orders tab of My Profile). */
    public function orders(Request $request)
    {
        $orders = CheckoutOrder::with('items.product.brand', 'items.product.vehicleModel')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json(['orders' => $orders->map([AppPresenter::class, 'order'])->values()]);
    }

    /** A fresh token for this phone, and who it belongs to. */
    public static function signedIn(User $user, Request $request): array
    {
        $device = substr(trim((string) $request->input('device_name', 'phone')), 0, 60) ?: 'phone';

        return [
            'token' => $user->createToken('sbk-app: ' . $device)->plainTextToken,
            'user' => AppPresenter::user($user),
        ];
    }

    public static function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [$parts[0] ?? $name, $parts[1] ?? ''];
    }
}
