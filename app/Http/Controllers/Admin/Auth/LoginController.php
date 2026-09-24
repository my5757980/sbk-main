<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Models\Admin;
use App\Models\AdminRole;
use App\Models\User;
use App\Http\Controllers\Admin\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

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

        return $this->sendFailedLoginResponse($request);
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

        $adminRole = AdminRole::where('slug', $user->role)->first();

        if (!$adminRole) {
            return false;
        }

        $admin = Admin::where('email', $user->email)->first();

        if ($admin && (int) $admin->is_system_admin === 1) {
            return true;
        }

        $payload = [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'email' => $user->email,
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
