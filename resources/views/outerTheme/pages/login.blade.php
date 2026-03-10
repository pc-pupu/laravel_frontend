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

        <form method="POST" action="{{ route('login') }}" id="login-form" autocomplete="off">
            @csrf
            <input type="hidden" name="password_encrypted" id="password_encrypted" value="">
            <div class="mb-3 mt-3">
                <input type="text" class="form-control input-form-custom" id="username" placeholder="Enter user name" name="username" value="{{ old('username') }}" autocomplete="off" maxlength="255" required>
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3 position-relative">
                <input type="password" class="form-control input-form-custom pe-5" id="pass" placeholder="Enter password" name="pass" autocomplete="new-password" maxlength="255" required>
                <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-secondary text-decoration-none p-0 me-2" id="togglePassword" aria-label="Show password" title="Show password">
                    <img id="eyeIcon" src="{{ asset('/assets/outerTheme/images/view.png') }}" alt="Show password" width="20" height="20">
                    <img id="eyeOffIcon" src="{{ asset('/assets/outerTheme/images/hide.png') }}" alt="Hide password" width="20" height="20" class="d-none">
                </button>
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
                <input type="text" name="captcha" class="form-control mt-2" placeholder="Enter captcha" maxlength="10" autocomplete="off">
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
    // Audit: Encrypt password with RSA before submit - never send plain text over wire
    var loginPublicKey = null;
    fetch('{{ rtrim(config("services.api.base_url", url("/api")), "/") }}/auth/public-key')
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.public_key) loginPublicKey = d.public_key;
        })
        .catch(function() {});

    function pemToBinary(pem) {
        var b64 = pem.replace(/-----BEGIN PUBLIC KEY-----|-----END PUBLIC KEY-----|\s/g, '');
        var binary = atob(b64);
        var bytes = new Uint8Array(binary.length);
        for (var i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
        return bytes;
    }

    function encryptPassword(plaintext) {
        return pemToBinary(loginPublicKey).then(function(binary) {
            return crypto.subtle.importKey(
                'spki', binary,
                { name: 'RSA-OAEP', hash: 'SHA-1' },
                false, ['encrypt']
            );
        }).then(function(key) {
            var data = new TextEncoder().encode(plaintext);
            return crypto.subtle.encrypt({ name: 'RSA-OAEP' }, key, data);
        }).then(function(encrypted) {
            return btoa(String.fromCharCode.apply(null, new Uint8Array(encrypted)));
        });
    }

    document.getElementById('login-form').addEventListener('submit', function(e) {
        var form = this;
        var passInput = document.getElementById('pass');
        var encInput = document.getElementById('password_encrypted');
        if (!passInput.value || !loginPublicKey) {
            if (!loginPublicKey) return; // Allow submit without encrypt if key fetch failed (fallback)
        } else {
            e.preventDefault();
            encryptPassword(passInput.value).then(function(encrypted) {
                encInput.value = encrypted;
                passInput.removeAttribute('required');
                passInput.value = '';
                passInput.name = '';
                form.submit();
            }).catch(function() { form.submit(); });
        }
    });

    document.getElementById('reload').addEventListener('click', function () {
        document.getElementById('captcha-img').src = '{{ captcha_src('flat') }}' + '?' + Date.now();
    });

    document.getElementById('togglePassword').addEventListener('click', function () {
        var passInput = document.getElementById('pass');
        var eyeIcon = document.getElementById('eyeIcon');
        var eyeOffIcon = document.getElementById('eyeOffIcon');
        if (passInput.type === 'password') {
            passInput.type = 'text';
            eyeIcon.classList.add('d-none');
            eyeOffIcon.classList.remove('d-none');
            this.setAttribute('aria-label', 'Hide password');
            this.setAttribute('title', 'Hide password');
        } else {
            passInput.type = 'password';
            eyeIcon.classList.remove('d-none');
            eyeOffIcon.classList.add('d-none');
            this.setAttribute('aria-label', 'Show password');
            this.setAttribute('title', 'Show password');
        }
    });
</script>
@endsection
