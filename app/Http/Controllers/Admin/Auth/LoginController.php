<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\Admin;
use App\Models\User;
use App\Http\Controllers\Admin\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /*
         * Logged in user should not be able to visit login page, unless logged out
         *
         * It's redirection is managed from App > Http > Middleware > RedirectIfAuthenticated
         */
        $this->middleware('guest:admin')->except('logout');
        /*parent::__construct();*/
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }

    protected function authenticated(Request $request, $user)
    {
        // A Users row left on a renamed role's old slug is put right on sign-in.
        if ($staff = User::where('email', $user->email)->first()) {
            Admin::roleFor($staff);
        }

        return redirect()->route($user->firstAccessibleAdminRoute());
    }

    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {

        $this->validateLogin($request);

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        if ($this->syncUserAdminAccount($request) && $this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // With the right password, say what is actually in the way; with a wrong
        // one, only the usual "do not match" (nothing about the account).
        if ($reason = $this->blockedReason($request)) {
            throw ValidationException::withMessages([$this->username() => [$reason]]);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /** Why an account whose password IS right still cannot use the admin panel. */
    private function blockedReason(Request $request): ?string
    {
        $email = $request->{$this->username()};
        $user = User::where('email', $email)->first();
        $admin = Admin::where('email', $email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ((int) $user->status !== 1) {
                return 'This account is switched off. An administrator can turn it on in Users.';
            }
            if (in_array($user->role, [null, '', 'customer'], true)) {
                return 'This is a customer account. Customers sign in on the website, not in the admin panel.';
            }
            if (!Admin::roleFor($user)) {
                return 'The role of this account no longer exists. An administrator can choose its role in Users - Edit.';
            }
            if ($admin && (int) $admin->is_system_admin === 1) {
                return 'This email is the login of the main administrator - sign in with the password of that administrator.';
            }
        }

        if ($admin && (int) $admin->is_active !== 1 && Hash::check($request->password, (string) $admin->password)) {
            return 'This admin account is switched off. An administrator can turn it on.';
        }

        return null;
    }

    protected function credentials(Request $request)
    {
        // return $request->only($this->username(), 'password');
        return [
            'email'     => $request->{$this->username()},
            'password'  => $request->password,
            'is_active' => 1
        ];
    }

    private function syncUserAdminAccount(Request $request): bool
    {
        $user = User::where('email', $request->{$this->username()})->first();

        if (!$user || (int) $user->status !== 1 || !Hash::check($request->password, $user->password)) {
            return false;
        }

        $adminRole = Admin::roleFor($user);

        if (!$adminRole) {
            return false;
        }

        $admin = Admin::where('email', $user->email)->first();

        if ($admin && (int) $admin->is_system_admin === 1) {
            return true;
        }

        $payload = Admin::profileFrom($user) + [
            'password' => $user->password,
            'is_active' => 1,
            'admin_role_id' => $adminRole->id,
        ];

        if ($admin) {
            $admin->update($payload);
        } else {
            Admin::create($payload);
        }

        return true;
    }

    public function logout(Request $request)
    {
        /*
         * Here, admin guard is being called by $this->guard()
         */
        $this->guard()->logout();

        // $request->session()->invalidate();

        return redirect()->route('admin.auth.login');
    }

    /**
     * Handle forgot password
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function forgotPassword(Request $request)
    {
        if (auth()->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.forgot-password');
    }
}
