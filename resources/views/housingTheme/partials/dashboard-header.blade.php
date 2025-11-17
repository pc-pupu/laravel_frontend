@php
    $user = session('user');
@endphp

<div class="d-flex flex-row justify-content-between">
    <div class="title">
        <h3 class="title-lg">@yield('page-header')</h3>
        <!-- <ul class="breadcrumb text-muted fs-6 fw-normal ms-1">
         <li class="breadcrumb-item text-muted"><a href="dashboard.html" class="text-muted text-hover-primary">Home</a></li>
         <li class="breadcrumb-item text-dark">Dashboards</li>
      </ul> -->
    </div>
    @if($user)
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown"
            aria-expanded="false">
            <img src="{{ asset('/themes/dashboard-theme/images/profile_icon.png') }}" alt="" width="50" height="50"
                class="rounded-circle me-2">
            <div class="user-name">
                <strong>{{ $user['name'] ?? 'User' }}</strong></br>
                <small><b>Email:</b> {{ $user['mail'] ?? $user['email'] ?? 'N/A' }}</small>
            </div>
        </a>
        <ul class="dropdown-menu text-small shadow" style="">
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('logout') }}"onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
                <!-- <a class="dropdown-item" href="#">Sign out</a> -->
            </li>
        </ul>
    </div>
    @endif
</div>
