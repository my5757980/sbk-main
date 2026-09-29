@extends('admin.auth.layouts.master')

@section('content')
<style>
    body.blank-page {
        background: #07080a !important;
    }

    body.blank-page .app-content.content {
        background:
            linear-gradient(115deg, rgba(7, 8, 10, .97), rgba(7, 8, 10, .82) 52%, rgba(120, 10, 16, .88)),
            url("{!! asset('assets/front/img/hero-prius.webp') !!}") center/cover no-repeat;
        min-height: 100vh;
    }

    .sbk-admin-login {
        min-height: 100vh;
        padding: 32px 18px;
    }

    .sbk-admin-login .sbk-login-card {
        width: min(980px, 100%);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 14px;
        background: rgba(12, 13, 16, .92);
        box-shadow: 0 30px 90px rgba(0, 0, 0, .38);
        overflow: hidden;
    }

    .sbk-login-brand {
        position: relative;
        min-height: 540px;
        background: linear-gradient(150deg, #111216, #050506);
        overflow: hidden;
    }

    .sbk-login-brand:before {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 22% 18%, rgba(215, 25, 32, .34), transparent 31%);
    }

    .sbk-login-brand:after {
        content: "SBK";
        position: absolute;
        right: -24px;
        bottom: -34px;
        color: rgba(255, 255, 255, .055);
        font: 900 160px/1 Arial, sans-serif;
        letter-spacing: 0;
    }

    .sbk-login-brand-inner {
        position: relative;
        z-index: 1;
        display: flex;
        height: 100%;
        min-height: 540px;
        flex-direction: column;
        justify-content: center;
        padding: 52px;
        color: #fff;
    }

    .sbk-login-brand img {
        max-width: 260px;
        max-height: 120px;
        object-fit: contain;
        object-position: left center;
        margin-bottom: 34px;
        filter: drop-shadow(0 16px 34px rgba(0, 0, 0, .38));
    }

    .sbk-login-kicker {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #ff4a52;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 1.4px;
        text-transform: uppercase;
    }

    .sbk-login-kicker:before {
        content: "";
        width: 38px;
        height: 3px;
        border-radius: 6px;
        background: #d71920;
    }

    .sbk-login-brand h1 {
        margin: 16px 0 14px;
        color: #fff;
        font-size: clamp(34px, 4vw, 54px);
        line-height: .98;
        font-weight: 900;
    }

    .sbk-login-brand p {
        max-width: 390px;
        margin: 0;
        color: rgba(255, 255, 255, .72);
        font-size: 14px;
        line-height: 1.8;
    }

    .sbk-login-panel {
        min-height: 540px;
        background: #fff;
    }

    .sbk-login-panel .card {
        min-height: 540px;
        border: 0;
        background: #fff;
        box-shadow: none;
    }

    .sbk-login-panel .card-header {
        padding-top: 52px;
        border: 0;
        background: #fff;
    }

    .sbk-login-panel h4 {
        color: #111216;
        font-size: 30px;
        font-weight: 900;
    }

    .sbk-login-panel p {
        color: #69707a;
        line-height: 1.7;
    }

    .sbk-login-panel .form-control {
        min-height: 48px;
        border: 1px solid #dfe2e8;
        border-radius: 7px;
    }

    .sbk-login-panel .form-control:focus {
        border-color: #d71920;
        box-shadow: 0 0 0 .2rem rgba(215, 25, 32, .12);
    }

    .sbk-login-panel .form-control-position i {
        color: #d71920;
    }

    .sbk-login-panel .btn-primary {
        min-width: 142px;
        min-height: 46px;
        border: 0;
        border-radius: 7px;
        background: #d71920 !important;
        box-shadow: 0 12px 26px rgba(215, 25, 32, .24);
        font-weight: 800;
        text-transform: uppercase;
    }

    .sbk-login-panel .btn-primary:hover,
    .sbk-login-panel .btn-primary:focus {
        background: #a90c12 !important;
    }

    @media (max-width: 991.98px) {
        .sbk-admin-login {
            align-items: center;
        }

        .sbk-login-panel,
        .sbk-login-panel .card {
            min-height: auto;
        }
    }

</style>
<!-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> -->
<div class="content-overlay"></div>
<div class="header-navbar-shadow"></div>
<div class="content-wrapper">
    <div class="content-header row">
    </div>
    <div class="content-body">
        <section class="row flexbox-container sbk-admin-login">
            <div class="col-12 d-flex justify-content-center">
                <div class="card bg-authentication rounded-0 mb-0 sbk-login-card">
                    <div class="row m-0">
                        <div class="col-lg-6 d-lg-block d-none p-0">
                            <div class="sbk-login-brand">
                                <div class="sbk-login-brand-inner">
                                    @if($siteSettings->logo_path)
                                        <img src="{!! $siteSettings->logo_path !!}" alt="branding logo">
                                    @else
                                        <img src="{!! asset('assets/front/img/logo.png') !!}" alt="branding logo">
                                    @endif
                                    <span class="sbk-login-kicker">Admin Console</span>
                                    <h1>{!! $siteSettings->site_title !!}</h1>
                                    <p>Manage inventory, orders, content, and customer activity from one secure dashboard.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12 p-0 sbk-login-panel">
                            <div class="card rounded-0 mb-0 px-2">
                                <div class="card-header pb-1">
                                    <div class="card-title">
                                        <h4 class="mb-0">Login</h4>
                                    </div>
                                </div>
                                <p class="px-2">Welcome to {!! $siteSettings->site_title !!} Admin, please login to your account.</p>
                                <div class="card-content">
                                    <div class="card-body pt-1">
                                        <form method="POST" action="{{ route('admin.auth.login') }}">
                                            @csrf
                                            <fieldset class="form-label-group form-group position-relative has-icon-left">
                                                <input type="email" class="form-control" id="user-name" placeholder="Username" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                                <div class="form-control-position">
                                                    <i class="feather icon-user"></i>
                                                </div>
                                                <label for="user-name">Email</label>
                                            </fieldset>

                                            <fieldset class="form-label-group position-relative has-icon-left">
                                                <input type="password" class="form-control" id="user-password" placeholder="Password" name="password" required autocomplete="current-password">
                                                <div class="form-control-position">
                                                    <i class="feather icon-lock"></i>
                                                </div>
                                                <label for="user-password">Password</label>
                                            </fieldset>
                                            <div class="form-group d-flex justify-content-between align-items-center">
                                                <div class="text-left">
                                                    <fieldset class="checkbox">
                                                        <div class="vs-checkbox-con vs-checkbox-primary">
                                                            <input type="checkbox">
                                                            <span class="vs-checkbox">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                            <span class="">Remember me</span>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                {{-- <div class="text-right"><a href="{{ route('admin.password.request')  }}" class="card-link">Forgot Password?</a></div> --}}
                                            </div>
                                            <button type="submit" class="btn btn-primary float-right btn-inline">Login</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="login-footer">
                                    <div class="divider"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</div>
@endsection
