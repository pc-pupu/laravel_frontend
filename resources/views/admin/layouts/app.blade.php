<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - e-Allotment of Rental Housing Estates')</title>
    
    <link href="{{ asset('/assets/outerTheme/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/outerTheme/css/font.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/admin/css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="admin-logo">
                    <img src="{{ asset('/assets/outerTheme/images/admin-logo-rhe.png') }}" alt="Admin Logo">
                    <span>Admin Panel</span>
                </a>
            </div>
            <nav class="admin-nav">
                <ul>
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.roles') }}" class="{{ request()->routeIs('admin.roles') ? 'active' : '' }}">
                            <i class="fas fa-user-tag"></i> Roles
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.permissions') }}" class="{{ request()->routeIs('admin.permissions') ? 'active' : '' }}">
                            <i class="fas fa-key"></i> Permissions
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.error-logs') }}" class="{{ request()->routeIs('admin.error-logs') ? 'active' : '' }}">
                            <i class="fas fa-exclamation-triangle"></i> Error Logs
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('homepage') }}" target="_blank">
                            <i class="fas fa-external-link-alt"></i> View Site
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Top Header -->
            <header class="admin-header">
                <div class="admin-header-content">
                    <h1>@yield('page-title', 'Dashboard')</h1>
                    <div class="admin-user-info">
                        <span>{{ session('user')['name'] ?? 'Admin' }}</span>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <div class="admin-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Hidden input to pass token to JavaScript -->
                <input type="hidden" id="session-api-token" value="{{ session('api_token') ?? ($api_token ?? '') }}">
                
                @if(config('app.debug'))
                <!-- Debug: Token info (only in debug mode) -->
                <div class="alert alert-info" style="font-size: 12px; padding: 5px; margin-bottom: 10px;">
                    <strong>Debug:</strong> 
                    Session Token: {{ session('api_token') ? 'Set (' . strlen(session('api_token')) . ' chars)' : 'Not set' }} | 
                    Controller Token: {{ isset($api_token) && $api_token ? 'Set (' . strlen($api_token) . ' chars)' : 'Not set' }}
                </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // API Configuration
        window.API_BASE_URL = '{{ config("services.api.base_url", "http://localhost:8000/api") }}';
        
        // Get token from cookie or localStorage
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }
        
        // Set initial token from session/controller (before DOM ready)
        window.API_TOKEN = '{{ session("api_token") ?? ($api_token ?? "") }}';
        
        // Wait for DOM to be ready before getting token
        document.addEventListener('DOMContentLoaded', function() {
            // Try to get token from multiple sources
            const tokenFromCookie = getCookie('api_token');
            const tokenFromStorage = localStorage.getItem('api_token');
            const tokenFromSession = '{{ session("api_token") ?? ($api_token ?? "") }}';
            const hiddenInput = document.getElementById('session-api-token');
            const tokenFromHiddenInput = hiddenInput ? hiddenInput.value.trim() : '';
            
            // Use the first available token (prioritize hidden input, then cookie, then storage, then session)
            const finalToken = tokenFromHiddenInput || tokenFromCookie || tokenFromStorage || tokenFromSession || window.API_TOKEN || '';
            window.API_TOKEN = finalToken;
            
            // Store token in localStorage for JavaScript access (if we have one)
            if (window.API_TOKEN && window.API_TOKEN.trim() !== '') {
                localStorage.setItem('api_token', window.API_TOKEN);
                console.log('API Token loaded successfully. Length:', window.API_TOKEN.length);
            } else {
                console.error('API Token not found!');
                console.log('Token sources:', {
                    cookie: tokenFromCookie ? 'Found (' + tokenFromCookie.substring(0, 10) + '...)' : 'Not found',
                    storage: tokenFromStorage ? 'Found (' + tokenFromStorage.substring(0, 10) + '...)' : 'Not found',
                    session: tokenFromSession ? 'Found (' + tokenFromSession.substring(0, 10) + '...)' : 'Not found',
                    hiddenInput: tokenFromHiddenInput ? 'Found (' + tokenFromHiddenInput.substring(0, 10) + '...)' : 'Not found',
                    window: window.API_TOKEN ? 'Found (' + window.API_TOKEN.substring(0, 10) + '...)' : 'Not found'
                });
                
                // Show error and redirect to login after 3 seconds
                const errorMsg = document.createElement('div');
                errorMsg.className = 'alert alert-danger';
                errorMsg.innerHTML = '<strong>Authentication Error:</strong> API token not found. Redirecting to login page in 3 seconds...';
                const content = document.querySelector('.admin-content');
                if (content) {
                    content.insertBefore(errorMsg, content.firstChild);
                }
                
                setTimeout(function() {
                    window.location.href = '{{ route("login") }}';
                }, 3000);
            }
        });
    </script>
    <script src="{{ asset('/assets/admin/js/token-debug.js') }}"></script>
    <script src="{{ asset('/assets/outerTheme/js/jquery.min.js') }}"></script>
    <script src="{{ asset('/assets/outerTheme/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/assets/admin/js/admin.js') }}"></script>
    @yield('scripts')
    
    @if(config('app.debug'))
    <script>
        // Debug: Log token info on page load
        setTimeout(function() {
            console.log('=== Token Debug Info ===');
            if (window.debugToken) {
                window.debugToken();
            }
            console.log('Current API_TOKEN:', window.API_TOKEN ? window.API_TOKEN.substring(0, 20) + '...' : 'NOT SET');
        }, 500);
    </script>
    @endif
</body>
</html>

