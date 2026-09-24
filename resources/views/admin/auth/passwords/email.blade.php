@extends('admin.auth.layouts.master')

@section('content')
<div class="header-navbar-shadow"></div>
<div class="content-wrapper">
    <div class="content-header row">
    </div>
    <div class="content-body">
        <section class="row flexbox-container">
            <div class="col-xl-7 col-md-9 col-10 d-flex justify-content-center px-0">
                <div class="card bg-authentication rounded-0 mb-0">
                    <div class="row m-0">
                        <div class="col-lg-6 d-lg-block d-none text-center align-self-center">
                            <img src="{!! asset('assets/admin/app-assets/images/pages/forgot-password.png') !!}" alt="branding logo">
                        </div>
                        <div class="col-lg-6 col-12 p-0">
                            <div class="card rounded-0 mb-0 px-2 py-1">
                                <div class="card-header pb-1">
                                    <div class="card-title">
                                        <h4 class="mb-0">Recover your password</h4>
                                    </div>
                                </div>
                                <p class="px-2 mb-0">Please enter your email address and we'll send you instructions on how to reset your password.</p>
                                <div class="card-content">
                                    <div class="card-body">
                                        <form method="post" action="{{ route('admin.password.email') }}">
                                            @csrf
                                            <div class="form-label-group">
                                                <input type="email" id="inputEmail" class="form-control" placeholder="Email" name="email">
                                                <label for="inputEmail">Email</label>
                                            </div>
                                        
                                            <div class="float-md-left d-block mb-1">
                                                <a href="{{ route('admin.auth.login')  }}" class="btn btn-outline-primary btn-block px-75">Back to Login</a>
                                            </div>
                                            <div class="float-md-right d-block mb-1">
                                                <button type="submit" class="btn btn-primary btn-block px-75">Recover Password</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
