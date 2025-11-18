<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF token for fetchProxy --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel - e-Allotment of Rental Housing Estates')</title>

    {{-- CSS --}}
    <link href="{{ asset('/assets/outerTheme/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/outerTheme/css/font.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
<div class="admin-wrapper">

    {{-- Dynamic Sidebar --}}
    @include('admin.partials.admin-sidebar')

    {{-- Main Content --}}
    <main class="admin-main">

        {{-- Header --}}
        <header class="admin-header">
            <div class="admin-header-content">
                <h1>@yield('page-title', 'Dashboard')</h1>

                <div class="admin-user-info">
                    <span>{{ session('user')['name'] ?? 'Admin' }}</span>
                </div>
            </div>
        </header>

        {{-- Main Content --}}
        <div class="admin-content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')

        </div>

    </main>
</div>


{{-- ============================ --}}
{{-- JAVASCRIPT --}}
{{-- ============================ --}}

{{-- jQuery & Bootstrap --}}
<script src="{{ asset('/assets/outerTheme/js/jquery.min.js') }}"></script>
<script src="{{ asset('/assets/outerTheme/js/bootstrap.bundle.min.js') }}"></script>

{{-- Must load FIRST - core helpers --}}
<script src="{{ asset('/assets/admin/js/admin.js') }}"></script>

{{-- Page-specific scripts --}}
@yield('scripts')

</body>
</html>
