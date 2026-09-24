@extends('admin.auth.layouts.master')

@section('content')
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
        <section class="row flexbox-container">
            <div class="col-xl-8 col-11 d-flex justify-content-center">
                <div class="card bg-authentication rounded-0 mb-0" style="width: 100%">
                    <div class="row m-0">
                        <div class="col-lg-6 d-lg-block d-none text-center align-self-center px-1 py-0">
                            @if($siteSettings->logo_path)
                                <img style="height: 100px;width: 100px;" src="{!! $siteSettings->logo_path !!}" alt="branding logo">
                            @else
                                <img src="{!! asset('assets/admin/app-assets/images/pages/login.png') !!}" alt="branding logo">
                            @endif
                        </div>
                        <div class="col-lg-6 col-12 p-0">
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
