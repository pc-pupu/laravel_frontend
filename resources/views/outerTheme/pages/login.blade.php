@extends('outerTheme.layouts.login') {{-- if you have a main layout --}}

@section('title', 'Admin Login - e-Allotment of Rental Housing Estates')

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
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->has('login_error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first('login_error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3 mt-3">
                <input type="username" class="form-control input-form-custom" id="username" placeholder="Enter user name" name="username" value="{{ old('username') }}" required>
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <input type="password" class="form-control input-form-custom" id="pass" placeholder="Enter password" name="pass" required>
                @error('pass')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="captcha">Captcha</label>
                <div class="mb-3 mt-3">
                    <img src="{{ captcha_src('flat') }}" id="captcha-img" alt="captcha">
                    <button type="button" class="btn btn-sm btn-secondary" id="reload">
                        ↻ Reload
                    </button>
                </div>
                <input type="text" name="captcha" class="form-control mt-2" placeholder="Enter captcha">
                @error('captcha')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>




            <div class="clearfix mb-3"></div>
            <div class="d-grid mb-3">
                <button type="submit" class="admin-login-btn btn-block custom-btn-rounded">Submit</button>
            </div>
            <div class="clearfix">
                <div class="float-start"><a href="hrms-login" class="admin-signin-link">HRMS Login</a></div>
                <div class="float-end"><a href="forgot-password" class="admin-forgot-link">Forgot Password ?</a></div>
            </div>
        </form>    
    </div>
</section>
@endsection

@section('scripts')
<script type="text/javascript">
    document.getElementById('reload').addEventListener('click', function () {
        document.getElementById('captcha-img').src = '{{ captcha_src('flat') }}' + '?' + Date.now();
    });    

</script>
@endsection
