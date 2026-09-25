@extends('front.layouts.app')

@section('title', 'Forgot Password | SBK Global Auto Trading')
@section('content')
@include('front.auth.partials.auth-style')

<section class="sbk-auth">
    <div class="sbk-auth-hero">
        <div class="container">
            <h1>Set Your Password</h1>
            <p>Enter the email address your SBK account uses. We will email you a secure link to set a new password.</p>
        </div>
    </div>
    <div class="container sbk-auth-body">
        @if(session('success'))
            <div class="sbk-auth-alert sbk-auth-alert--success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="sbk-auth-alert sbk-auth-alert--error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="sbk-auth-alert sbk-auth-alert--error">{{ $errors->first() }}</div>
        @endif
        <div class="sbk-auth-card">
            <div class="sbk-auth-head">
                <span>Account Access</span>
                <h2>Forgot your password?</h2>
            </div>
            <form class="sbk-auth-form" method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="sbk-field">
                    <label for="email">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="sbk-auth-row">
                    <button class="sbk-auth-submit" type="submit">Email me a link</button>
                    <a class="sbk-auth-link" href="{{ route('login') }}">Back to login</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
