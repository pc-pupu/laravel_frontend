<div class="sidebar d-flex flex-column p-3">
    <a href="{{ route('dashboard') }}" class="d-flex flex-column align-items-center mb-5 text-center">
        <img src="{{ asset('/themes/dashboard-theme/images/wb-logo.png') }}" class="img-fluid" alt="e-Allotment of Rental Housing Estate">
        <div class="dashboard-logo">
            <div class="fs-5 fw-semibold lh-1">e-Allotment of Rental Housing Estate</div>
            <small>Housing Department <br/> Government of West Bengal</small>
        </div>
    </a>
    <ul class="nav nav-pills flex-column mb-auto cus-dashboard">
        @php
            $currentRoute = request()->route()->getName();
            $currentUrl = request()->url();
        @endphp

        @forelse($sidebarMenus ?? [] as $menu)
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
                $menuId = 'menu-' . $menu['sidebar_menu_id'];
            @endphp

            <li class="nav-item {{ $hasSubmenu ? 'has-submenu' : '' }}">
                @if($hasSubmenu)
                    <a class="nav-link {{ $isActive ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#{{ $menuId }}" aria-expanded="false">
                        @if(!empty($menu['icon_class']))
                            <i class="{{ $menu['icon_class'] }}"></i>
                        @endif
                        {{ $menu['menu_name'] }}
                        <i class="fa fa-angle-down fa-lg float-end mt-1" aria-hidden="true"></i>
                    </a>
                    <ul class="submenu collapse" id="{{ $menuId }}">
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
                                <a class="nav-link {{ $childIsActive ? 'active' : '' }}" href="{{ $childUrl ?? '#' }}">
                                    @if(!empty($child['icon_class']))
                                        <i class="{{ $child['icon_class'] }}"></i>
                                    @endif
                                    {{ $child['menu_name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <a href="{{ $menuUrl ?? '#' }}" class="nav-link {{ $isActive ? 'active' : '' }}">
                        @if(!empty($menu['icon_class']))
                            <i class="{{ $menu['icon_class'] }}"></i>
                        @endif
                        {{ $menu['menu_name'] }}
                    </a>
                @endif
            </li>
        @empty
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa fa-tachometer fa-lg" aria-hidden="true"></i>
                    Dashboard
                </a>
            </li>
        @endforelse
    </ul>
    
    <!-- <button type="button" class="btn btn-outline-light border-dashed"><img src="./images/complaint_icon.png" /><br/>Complaint Management</button> -->
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-expand submenu if current page is a child
    const activeSubmenu = document.querySelector('.submenu .nav-link.active');
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
