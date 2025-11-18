{{-- Admin Panel Sidebar with Static + Dynamic Menus --}}
<aside class="admin-sidebar">
    <div class="admin-sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="admin-logo">
            <img src="{{ asset('/assets/outerTheme/images/admin-logo-rhe.png') }}" alt="Admin Logo">
            <span>Admin Panel</span>
        </a>
    </div>

    <nav class="admin-nav">
        <ul>
            {{-- Original Static Admin Menus --}}
            <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="{{ route('admin.roles') }}" class="{{ request()->routeIs('admin.roles') ? 'active' : '' }}"><i class="fas fa-user-tag"></i> Roles</a></li>
            <li><a href="{{ route('admin.permissions') }}" class="{{ request()->routeIs('admin.permissions') ? 'active' : '' }}"><i class="fas fa-key"></i> Permissions</a></li>
            <li><a href="{{ route('admin.error-logs') }}" class="{{ request()->routeIs('admin.error-logs') ? 'active' : '' }}"><i class="fas fa-exclamation-triangle"></i> Error Logs</a></li>
            <li><a href="{{ route('admin.sidebar-menus') }}" class="{{ request()->routeIs('admin.sidebar-menus') ? 'active' : '' }}"><i class="fas fa-bars"></i> Sidebar Menus</a></li>

            {{-- Divider before dynamic menus --}}
            @if(!empty($sidebarMenus) && count($sidebarMenus) > 0)
                <li style="border-top: 2px solid rgba(255, 255, 255, 0.1); margin-top: 10px; padding-top: 10px;"></li>
            @endif

            {{-- Dynamic Menus from Database --}}
            @php
                $currentRoute = request()->route()->getName();
                $currentUrl = request()->url();
            @endphp

            @foreach($sidebarMenus ?? [] as $menu)
                @php
                    $isActive = false;
                    $menuUrl = null;
                    
                    // Determine menu URL
                    if (!empty($menu['route_name'])) {
                        try {
                            $menuUrl = route($menu['route_name']);
                            $isActive = $currentRoute === $menu['route_name'];
                        } catch (\Exception $e) {
                            // Route doesn't exist, try URL
                            if (!empty($menu['url'])) {
                                $menuUrl = $menu['url'];
                                $isActive = $currentUrl === url($menu['url']);
                            }
                        }
                    } elseif (!empty($menu['url'])) {
                        $menuUrl = $menu['url'];
                        $isActive = $currentUrl === url($menu['url']);
                    }

                    $hasSubmenu = isset($menu['has_submenu']) && $menu['has_submenu'] && !empty($menu['children']);
                    $menuId = 'admin-menu-' . $menu['sidebar_menu_id'];
                @endphp

                <li class="{{ $hasSubmenu ? 'has-submenu' : '' }}">
                    @if($hasSubmenu)
                        <a href="#" class="{{ $isActive ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#{{ $menuId }}" aria-expanded="false">
                            @if(!empty($menu['icon_class']))
                                <i class="{{ $menu['icon_class'] }}"></i>
                            @else
                                <i class="fas fa-circle"></i>
                            @endif
                            {{ $menu['menu_name'] }}
                            <i class="fas fa-chevron-down float-end" style="font-size: 0.75rem; margin-top: 0.25rem;"></i>
                        </a>
                        <ul class="collapse submenu" id="{{ $menuId }}">
                            @foreach($menu['children'] as $child)
                                @php
                                    $childIsActive = false;
                                    $childUrl = null;
                                    
                                    if (!empty($child['route_name'])) {
                                        try {
                                            $childUrl = route($child['route_name']);
                                            $childIsActive = $currentRoute === $child['route_name'];
                                        } catch (\Exception $e) {
                                            if (!empty($child['url'])) {
                                                $childUrl = $child['url'];
                                                $childIsActive = $currentUrl === url($child['url']);
                                            }
                                        }
                                    } elseif (!empty($child['url'])) {
                                        $childUrl = $child['url'];
                                        $childIsActive = $currentUrl === url($child['url']);
                                    }
                                @endphp
                                <li>
                                    <a href="{{ $childUrl ?? '#' }}" class="{{ $childIsActive ? 'active' : '' }}">
                                        @if(!empty($child['icon_class']))
                                            <i class="{{ $child['icon_class'] }}"></i>
                                        @else
                                            <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                        @endif
                                        {{ $child['menu_name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{ $menuUrl ?? '#' }}" class="{{ $isActive ? 'active' : '' }}">
                            @if(!empty($menu['icon_class']))
                                <i class="{{ $menu['icon_class'] }}"></i>
                            @else
                                <i class="fas fa-circle"></i>
                            @endif
                            {{ $menu['menu_name'] }}
                        </a>
                    @endif
                </li>
            @endforeach

            {{-- Always show these links at the bottom --}}
            <li style="border-top: 2px solid rgba(255, 255, 255, 0.1); margin-top: 10px; padding-top: 10px;"><a href="{{ route('homepage') }}" target="_blank"><i class="fas fa-external-link-alt"></i> View Site</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </form>
            </li>
        </ul>
    </nav>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-expand submenu if current page is a child
    const activeSubmenu = document.querySelector('.submenu a.active');
    if (activeSubmenu) {
        const submenu = activeSubmenu.closest('.submenu');
        const parentLink = submenu.previousElementSibling;
        if (submenu && parentLink) {
            submenu.classList.add('show');
            parentLink.setAttribute('aria-expanded', 'true');
            parentLink.classList.add('active');
        }
    }
});
</script>

