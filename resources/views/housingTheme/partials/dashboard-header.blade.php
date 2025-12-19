@php
    $user = session('user');
@endphp

<style>
    .header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
    }

    .header-title h3 {
        font-size: 28px;
        font-weight: 700;
        color: #4d4dd8;
        margin: 0;
    }

    .profile-wrapper img {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
    }

    

    .user-text {
        line-height: 1.1;
        margin-left: 8px;
    }
    .profile-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.dropdown-container {
    position: relative;
}

/* dropdown hidden normally */
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%; /* directly below user-text */
    right: 0;
    border-radius: 8px;
    min-width: 160px;
    margin-top: 0; /* NO GAP */
    z-index: 99999;
}

/* show dropdown when hovering text OR dropdown */
.dropdown-container:hover .dropdown-menu {
    display: block;
}


</style>


<div class="header-bar">
    <div class="header-title">
        <h3>@yield('page-header')</h3>
        <div><small>Home / @yield('page-header')</small></div>
    </div>

    @if($user)
    <div class="profile-wrapper">

    <img src="{{ asset('/assets/dashboard-theme/images/profile_icon.png') }}" 
         width="42" height="42" class="rounded-circle">

    <div class="dropdown-container ms-2">

        <div class="user-text">
            <strong>{{ $user['name'] ?? 'User' }}</strong><br>
            <small>{{ $user['mail'] ?? $user['email'] ?? 'N/A' }}</small>
        </div>

        <ul class="dropdown-menu shadow">
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li>
                <a class="dropdown-item"
                   href="{{ route('logout') }}"
                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>

    </div>

</div>

    @endif
</div>

