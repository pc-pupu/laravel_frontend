<!-- Top header section start -->
<section class="top-header">
    <div class="container">
        <div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6 text-end"><a href="#" target="_self">Skip to main content</a></div>
        </div>
    </div>
</section>
<!-- Top header section end -->
<!-- Top header logo section start -->
<section class="top-header-logo-bg">
    <div class="container">
        <div class="row">
            <div class="col-md-6"><img src="{{ asset('assets/outerTheme/images/e-allotment-rhe-logo2.jpg') }}" alt="Housing Department"
                    title="Housing Department"></div>
            <div class="col-md-6 text-end mt-5">
                <a href="{{url('/login')}}" target="_self" class="btn-admin-login mx-2">Official Login</a>
                <!-- <a href="{{url('/hrms-applicant-login')}}" target="_self" class="btn-admin-login">Applicant Login</a> -->
                {{-- <a href="{{url('/hrms-login')}}" target="_self" class="btn-admin-login">Applicant Login</a> 
                 <a href="{{url('/hrms-ddo-login')}}" target="_self" class="btn-admin-login">DDO Login</a> --}}
            </div>
        </div>
    </div>
</section>
<!-- Top header logo section end -->
<!-- Top header section start -->
<section class="top-header-menu-bg">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <nav class="navbar navbar-expand-sm bg-dark# navbar-dark#">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapsibleNavbar">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="collapsibleNavbar">
                        <ul class="navbar-nav ">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('/') }}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " href="{{ url('about-us') }}">About Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " href="{{ url('faq') }}">FAQ</a>
                            </li> 
                            <li class="nav-item">
                                <a class="nav-link " href="{{ url('contact-us') }}">Contact Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " href="{{ url('notice') }}">Notice</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " href="{{ url('user-manual') }}">User Manual / SOP</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</section>
<!-- Top header section end -->