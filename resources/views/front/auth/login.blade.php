@extends('front.layouts.app')

@php $assetBase = asset('assets/front'); @endphp

@section('title', 'Login | SBK Global Auto Trading')
@section('content')
<style>
.sbk-auth{background:#fff;color:#111}.sbk-auth .container{width:min(1120px,calc(100% - 36px));margin:auto}.sbk-auth-hero{padding:72px 0;background:#08090b;color:#fff}.sbk-auth-hero h1{margin:0;color:#fff;font-size:clamp(38px,5vw,62px);line-height:1}.sbk-auth-hero p{max-width:620px;margin:14px 0 0;color:rgba(255,255,255,.75);line-height:1.7}.sbk-auth-body{padding:56px 0 90px}.sbk-auth-card{max-width:520px;border:1px solid #e7e8eb;border-radius:10px;background:#fff;box-shadow:0 20px 55px rgba(17,18,20,.09);overflow:hidden}.sbk-auth-head{padding:26px 30px;background:#111216;color:#fff}.sbk-auth-head span{color:#ff4a52;font-size:11px;font-weight:900;letter-spacing:1.5px;text-transform:uppercase}.sbk-auth-head h2{margin:8px 0 0;color:#fff;font-size:30px}.sbk-auth-form{display:grid;gap:17px;padding:30px}.sbk-field{display:grid;gap:8px}.sbk-field label{font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.7px}.sbk-field input{height:48px;width:100%;border:1px solid #dcdde1;border-radius:5px;padding:0 13px;font:inherit}.sbk-field input:focus{border-color:#d71920;outline:none;box-shadow:0 0 0 3px rgba(215,25,32,.09)}.sbk-auth-row{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}.sbk-auth-submit{min-height:50px;border:0;border-radius:6px;background:#d71920;color:#fff;padding:0 28px;font-size:12px;font-weight:900;text-transform:uppercase;box-shadow:0 14px 26px rgba(215,25,32,.22);cursor:pointer}.sbk-auth-link{color:#d71920;font-weight:800;text-decoration:none}.sbk-auth-alert{margin-bottom:18px;padding:14px 16px;border-radius:7px}.sbk-auth-alert--error{background:#fff1f1;color:#9a1111;border:1px solid #ffcaca}.sbk-auth-alert--success{background:#edf9f1;color:#126a31;border:1px solid #bde7ca}@media(max-width:620px){.sbk-auth .container{width:calc(100% - 28px)}.sbk-auth-body{padding:34px 0 66px}.sbk-auth-form{padding:24px}.sbk-auth-head h2{font-size:25px}.sbk-auth-submit{width:100%}}
</style>

<section class="sbk-auth">
    <div class="sbk-auth-hero">
        <div class="container">
            <h1>Customer Login</h1>
            <p>Access your SBK profile, saved details, and complete vehicle order records.</p>
        </div>
    </div>
    <div class="container sbk-auth-body">
        @if(session('success'))
            <div class="sbk-auth-alert sbk-auth-alert--success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="sbk-auth-alert sbk-auth-alert--error">Please check your email and password.</div>
        @endif
        <div class="sbk-auth-card">
            <div class="sbk-auth-head">
                <span>Account Access</span>
                <h2>Login to your profile</h2>
            </div>
            <form class="sbk-auth-form" method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="sbk-field">
                    <label for="email">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="sbk-field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required>
                </div>
                <label style="display:flex;gap:9px;align-items:center;font-size:13px;color:#555">
                    <input type="checkbox" name="remember" value="1"> Remember me
                </label>
                <div class="sbk-auth-row">
                    <button class="sbk-auth-submit" type="submit">Login</button>
                    <a class="sbk-auth-link" href="{{ route('register') }}">Create account</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
