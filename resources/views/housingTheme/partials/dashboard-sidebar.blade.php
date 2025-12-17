
<div class="sidebar d-flex flex-column p-3">

    <a href="{{ route('dashboard') }}" class="d-flex flex-column align-items-center mb-5 text-center text-white">
        <img src="{{ asset('/themes/dashboard-theme/images/wb-logo.png') }}" class="img-fluid mb-3" width="120">
        
        <div class="dashboard-logo">
            <div class="fs-5 fw-semibold lh-1">e-Allotment of Rental Housing Estate</div>
            <small>Housing Department <br/> Government of West Bengal</small>
        </div>
    </a>

@php
    $menus = session('sidebar_menus', []);
    $currentRoute = request()->route()->getName();
    $currentUrl = request()->url();
@endphp

<ul class="nav nav-pills flex-column mb-auto cus-dashboard">

    {{-- DASHBOARD --}}
    {{-- <li class="nav-item">
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa fa-home me-2"></i> Dashboard
        </a>
    </li> --}}

    {{-- DYNAMIC MENUS --}}
    @foreach ($menus as $menu)

        @php
            $children = $menu['children'] ?? [];
            $hasSub = count($children) > 0;
            $menuId = "menu-" . $menu['sidebar_menu_id'];

            // Parent URL
            $menuUrl = '#';
            if (!empty($menu['url'])) {
                $menuUrl = url($menu['url']);
            } elseif (!empty($menu['route_name']) && Route::has($menu['route_name'])) {
                try {
                    $menuUrl = route($menu['route_name']);
                } catch (\Illuminate\Routing\Exceptions\UrlGenerationException $e) {
                    $menuUrl = url($menu['url'] ?? '#');
                }
            }

            // Parent Active?
            $isParentActive = false;

            if (!empty($menu['route_name']) && $currentRoute === $menu['route_name']) {
                $isParentActive = true;
            }

            foreach ($children as $child) {
                if (!empty($child['route_name']) && $currentRoute === $child['route_name']) {
                    $isParentActive = true;
                }
                if (!empty($child['url']) && $currentUrl === url($child['url'])) {
                    $isParentActive = true;
                }
            }
        @endphp

        <li class="nav-item {{ $hasSub ? 'has-submenu' : '' }}">

            {{-- MAIN MENU --}}
            @if ($hasSub)
                <a href="#"
                   class="nav-link {{ $isParentActive ? 'active' : '' }}"
                   data-bs-toggle="collapse"
                   data-bs-target="#{{ $menuId }}">

                    @if (!empty($menu['icon_class']))
                        <i class="{{ $menu['icon_class'] }}"></i>
                    @endif

                    {{ $menu['menu_name'] }}

                    <i class="fa fa-angle-down fa-lg float-end mt-1"></i>
                </a>

                {{-- SUBMENU --}}
                <ul class="submenu collapse {{ $isParentActive ? 'show' : '' }}" id="{{ $menuId }}">
                    @foreach ($children as $child)

                        @php
                            $childUrl = '#';
                            
                            // If URL is provided, use it (especially for routes with required parameters)
                            if (!empty($child['url'])) {
                                $childUrl = url($child['url']);
                            } 
                            // Otherwise, try to generate route if route_name exists and route is registered
                            elseif (!empty($child['route_name']) && Route::has($child['route_name'])) {
                                try {
                                    // Try to generate route - if it requires parameters, this will fail
                                    $childUrl = route($child['route_name']);
                                } catch (\Illuminate\Routing\Exceptions\UrlGenerationException $e) {
                                    // If route requires parameters, fall back to URL or use default
                                    $childUrl = url($child['url'] ?? '#');
                                }
                            }

                            $childActive =
                                (!empty($child['route_name']) && $currentRoute === $child['route_name']) ||
                                (!empty($child['url']) && $currentUrl === url($child['url']));
                        @endphp

                        <li>
                            <a href="{{ $childUrl }}"
                               class="nav-link {{ $childActive ? 'active' : '' }}">
                                @if(!empty($child['icon_class']))
                                    <i class="{{ $child['icon_class'] }}"></i>
                                @endif
                                {{ $child['menu_name'] }}
                            </a>
                        </li>

                    @endforeach
                </ul>

            @else

                {{-- SINGLE ITEM MENU --}}
                <a href="{{ $menuUrl }}"
                   class="nav-link {{ $isParentActive ? 'active' : '' }}">
                    @if(!empty($menu['icon_class']))
                        <i class="{{ $menu['icon_class'] }}"></i>
                    @endif
                    {{ $menu['menu_name'] }}
                </a>

            @endif

        </li>

    @endforeach

</ul>


    

    <div class="sidebar-bottom mt-auto">
        <a href="#" class="nav-link text-white">
            <i class="fa fa-key"></i> Change Password
        </a>
        {{-- <a href="{{ route('logout') }}" class="nav-link text-white mt-2">
            <i class="fa fa-sign-out"></i> Logout
        </a> --}}
        <a class="nav-link text-white mt-2" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            <i class="fa fa-sign-out"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

</div>



<script>
document.addEventListener('DOMContentLoaded', function () {

    const parentLinks = document.querySelectorAll(".has-submenu > .nav-link");

    parentLinks.forEach(link => {

        link.addEventListener("click", function (e) {
            e.preventDefault();

            const targetSelector = this.getAttribute("data-bs-target");
            const target = document.querySelector(targetSelector);

            if (!target) return;

            const isOpen = target.classList.contains('show');

            // 1️⃣ CLOSE ALL OTHER MENUS FIRST
            document.querySelectorAll(".submenu.show").forEach(openSub => {
                if (openSub !== target) {
                    const inst = bootstrap.Collapse.getInstance(openSub) 
                                || new bootstrap.Collapse(openSub, { toggle: false });
                    inst.hide();

                    // remove active from other parent links
                    const parentLink = openSub.previousElementSibling;
                    if (parentLink) {
                        parentLink.classList.remove('active');
                        parentLink.setAttribute('aria-expanded', 'false');
                    }
                }
            });

            // 2️⃣ TOGGLE CURRENT MENU (ALLOW CLOSE EVEN IF CHILD ACTIVE)
            const instance = bootstrap.Collapse.getInstance(target) 
                            || new bootstrap.Collapse(target, { toggle: false });

            if (isOpen) {
                instance.hide();
                this.classList.remove('active');
                this.setAttribute('aria-expanded', 'false');
            } else {
                instance.show();
                this.classList.add('active');
                this.setAttribute('aria-expanded', 'true');
            }

        });

    });

    // 3️⃣ AUTO-EXPAND if submenu has active item
    document.querySelectorAll('.submenu').forEach(sub => {
        if (sub.querySelector('.nav-link.active')) {
            const inst = bootstrap.Collapse.getInstance(sub) 
                        || new bootstrap.Collapse(sub, { toggle: false });
            inst.show();

            const parentLink = sub.previousElementSibling;
            if (parentLink) {
                parentLink.classList.add('active');
                parentLink.setAttribute('aria-expanded', 'true');
            }
        }
    });

});
</script>


