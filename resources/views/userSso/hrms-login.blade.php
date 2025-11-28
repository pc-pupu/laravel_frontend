@extends('userSso.layouts.app')

@section('title', 'HRMS Login')

@section('content')
<section class="slider hrms-login-bg cus-responsive" style="overflow-x: hidden; overflow-y: inherit;">
    {{-- <div class="overlay-1"></div>
    <div class="overlay-2"></div> --}}
    
    <div class="p-3 text-center position-absolute start-0 end-0">
        <div class="container">
            <a href="{{ route('homepage') }}">
                <div class="logo">
                    <img src="{{ asset('/assets/outerTheme/images/wb-logo.png') }}" class="img-fluid mh-100" alt="West Bengal Logo" />
                </div>
                <div class="logo-text services">
                    <h1 class="poppins-regular">e-Allotment of Rental Housing Estate</h1>
                    <h4 class="poppins-medium abt-dept-heading">Housing Department | Government of West Bengal</h4>
                </div>
            </a>
            
            <div class="d-flex justify-content-center h-100 end-0 mb-4">
                <div class="search">
                    <form method="POST" action="{{ route('user-sso.hrms-login-submit') }}" id="hrmsLoginForm">
                        @csrf
                        <div id="hrms" class="login-panel">
                            <input type="text" 
                                   class="search_input" 
                                   id="hrms_id" 
                                   name="hrms_id" 
                                   placeholder="Enter HRMS ID" 
                                   required 
                                   autocomplete="off"
                                   value="{{ old('hrms_id') }}">
                            <button type="submit" class="search_icon btn">
                                <i class="fa fa-sign-in"></i>
                            </button>
                        </div>
                        @error('hrms_id')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

