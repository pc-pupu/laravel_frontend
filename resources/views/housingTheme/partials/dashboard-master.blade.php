<!doctype html>
<html lang="en">
    <head>
       <meta charset="utf-8">
       <meta name="viewport" content="width=device-width, initial-scale=1">
       <title>Welcome to e-Allotment of Rental Housing Estate | e-Allotment of Rental Housing Estate</title>

       <link rel="stylesheet" href="{{ asset('/assets/housingTheme/bootstrap/css/bootstrap.min.css') }}">
       <link rel="stylesheet" href="{{ asset('/assets/housingTheme/bootstrap/css/bootstrap-icons.min.css') }}">
       <link rel="stylesheet" href="{{ asset('/assets/housingTheme/css/style.css') }}">
       <link rel="stylesheet" href="{{ asset('/assets/housingTheme/css/font-awesome.css') }}">
       <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
        
    </head>
    <body>
        <div class="dashboard">
            @include('layouts.dashboard-sidebar')
            <div id="content-wrapper" class="content-wrapper">
                <div class="main-content w-100 p-4 pb-0">
                    @include('sweetalert::alert')
                    @include('layouts.dashboard-header')
                    @yield('dashboard-body')
                    @include('layouts.dashboard-footer')
                </div>
            </div>
        </div>
        {{-- <script src="{{ asset('/jquery/jquery.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('/bootstrap-5.3.3/js/bootstrap.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('/bootstrap-5.3.3/js/bootstrap.bundle.min.js') }}"></script> --}}
        {{-- <script src="{{ asset(assets/dashboard-theme/js/custome.js') }}"></script> --}}

        <script src="{{ asset('/assets/housingTheme/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('/assets/housingTheme/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('/assets/housingTheme/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('/assets/dashboard-theme/js/custome.js') }}"></script>
    </body>
</html>




