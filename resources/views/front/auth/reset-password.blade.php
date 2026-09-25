@extends('front.layouts.app')

@section('title', 'Set a New Password | SBK Global Auto Trading')
@section('content')
@include('front.auth.partials.auth-style')

<section class="sbk-auth">
    <div class="sbk-auth-hero">
        <div class="container">
            <h1>Set Your Password</h1>
            <p>Choose a password of at least 8 characters. You will be signed in straight away.</p>
        </div>
    </div>
    <div class="container sbk-auth-body">
        @if($errors->any())
            <div class="sbk-auth-alert sbk-auth-alert--error">{{ $errors->first() }}</div>
        @endif
        <div class="sbk-auth-card">
            <div class="sbk-auth-head">
                <span>Account Access</span>
                <h2>New password</h2>
            </div>
            <form class="sbk-auth-form" method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="sbk-field">
                    <label for="email">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required>
                </div>
                <div class="sbk-field">
                    <label for="password">New Password</label>
                    <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" autofocus>
                </div>
                <div class="sbk-field">
                    <label for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
                </div>
                <div class="sbk-auth-row">
                    <button class="sbk-auth-submit" type="submit">Save password</button>
                    <a class="sbk-auth-link" href="{{ route('login') }}">Back to login</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
