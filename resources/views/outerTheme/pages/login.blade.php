@extends('outerTheme.layouts.login') {{-- if you have a main layout --}}

@section('content')
<section class="admin-login-bg text-center">
    <div class="admin-login-top-bg">
        <div>
            <a href="#" target="_self">
                <img src="{{ asset('/assets/outerTheme/images/admin-logo-rhe.png')}}" class="" alt="e-Allotment of Rental Housing Estates" title="e-Allotment of Rental Housing Estates">
            </a>
        </div>
        <h2 class="mt-4">Admin Sign in to Continue</h2>
    </div>
    <div class="admin-login-white-bg text-start">
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        <a href="/password" target="_self">Forgot Password?</a>
    </div>
</section>
@endsection