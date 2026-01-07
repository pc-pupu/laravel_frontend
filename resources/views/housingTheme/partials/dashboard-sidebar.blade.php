
<div class="sidebar d-flex flex-column p-3">

    <a href="{{ route('dashboard') }}" class="d-flex flex-column align-items-center mb-5 text-center text-white">
        <img src="{{ asset('/assets/dashboard-theme/images/wb-logo.png') }}" class="img-fluid mb-3" width="120">
        
        <div class="dashboard-logo">
            <div class="fs-5 fw-semibold lh-1">e-Allotment of Rental Housing Estate</div>
            <small>Housing Department <br/> Government of West Bengal</small>
        </div>
    </a>

@php
    $menus = session('sidebar_menus', []);
    $currentRoute = request()->route()->getName();
    $currentUrl = request()->url();
    $currentPath = trim(request()->path(), '/');
    $currentFullUrl = request()->fullUrl();
@endphp
@php
    $isUserTaggingPage = request()->is('user-tagging') || request()->is('user_tagging');
@endphp


<ul class="nav nav-pills flex-column mb-auto cus-dashboard">

    {{-- DASHBOARD - Available for all logged in users --}}
    @if(!$isUserTaggingPage)
        <li class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa fa-home me-2"></i> Dashboard
            </a>
        </li>
    @endif

    {{-- DYNAMIC MENUS --}}
    @if($isUserTaggingPage)

        <li class="nav-item">
            <a class="nav-link text-white mt-2" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="fa fa-sign-out"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </li>

    @else
        @foreach ($menus as $menu)

            @php
                $children = $menu['children'] ?? [];
                $hasSub = count($children) > 0;
                $menuId = "menu-" . $menu['sidebar_menu_id'];

                // Parent URL
                $menuUrl = '#';
                // Prioritize route_params over url field to ensure encryption
                if (!empty($menu['route_name']) && Route::has($menu['route_name'])) {
                    try {
                        $routeParams = $menu['route_params'] ?? [];
                        
                        // Handle array of parameter sets - use first set
                        if (is_array($routeParams) && isset($routeParams[0]) && is_array($routeParams[0])) {
                            $routeParams = $routeParams[0];
                        }
                        
                        // Build URL manually with encrypted parameters
                        if (!empty($routeParams) && is_array($routeParams)) {
                            $route = Route::getRoutes()->getByName($menu['route_name']);
                            if ($route) {
                                $uri = $route->uri();
                                
                                // Replace each parameter placeholder with encrypted value
                                foreach ($routeParams as $key => $value) {
                                    $encryptedValue = \App\Helpers\UrlEncryptionHelper::encryptUrl($value);
                                    $uri = str_replace('{' . $key . '}', $encryptedValue, $uri);
                                }
                                
                                $menuUrl = url($uri);
                            } else {
                                // Fallback to route helper if route not found
                                $encryptedParams = [];
                                foreach ($routeParams as $key => $value) {
                                    $encryptedParams[$key] = \App\Helpers\UrlEncryptionHelper::encryptUrl($value);
                                }
                                $menuUrl = route($menu['route_name'], $encryptedParams);
                            }
                        } else {
                            // No parameters, use route helper directly or fallback to url field
                            if (!empty($menu['url'])) {
                                $menuUrl = url($menu['url']);
                            } else {
                                $menuUrl = route($menu['route_name']);
                            }
                        }
                    } catch (\Exception $e) {
                        // Fallback to url field if route generation fails
                        if (!empty($menu['url'])) {
                            $menuUrl = url($menu['url']);
                        } else {
                            $menuUrl = '#';
                        }
                    }
                } elseif (!empty($menu['url'])) {
                    // Only use url field if route_name doesn't exist
                    $menuUrl = url($menu['url']);
                }


                // Parent Active?
                $isParentActive = false;

                if (!empty($menu['route_name']) && $currentRoute === $menu['route_name']) {
                    // If route name matches, also check URL to ensure exact match
                    if (!empty($menu['url'])) {
                        $menuPath = trim(parse_url($menu['url'], PHP_URL_PATH), '/');
                        if ($currentPath === $menuPath) {
                            $isParentActive = true;
                        }
                    } else {
                        $isParentActive = true;
                    }
                }

                foreach ($children as $child) {
                    $childIsActive = false;
                    if (!empty($child['route_name']) && $currentRoute === $child['route_name']) {
                        // If route name matches, also check URL to ensure exact match
                        if (!empty($child['url'])) {
                            $childPath = trim(parse_url($child['url'], PHP_URL_PATH), '/');
                            if ($currentPath === $childPath) {
                                $childIsActive = true;
                                $isParentActive = true;
                            }
                        } else {
                            $childIsActive = true;
                            $isParentActive = true;
                        }
                    } elseif (!empty($child['url'])) {
                        // Fallback: check URL directly
                        $childPath = trim(parse_url($child['url'], PHP_URL_PATH), '/');
                        if ($currentPath === $childPath) {
                            $childIsActive = true;
                            $isParentActive = true;
                        }
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

                                // Prioritize route_params over url field to ensure encryption
                                if (!empty($child['route_name']) && Route::has($child['route_name'])) {
                                    try {
                                        $routeParams = $child['route_params'] ?? [];
                                        
                                        // Handle array of parameter sets - use first set
                                        if (is_array($routeParams) && isset($routeParams[0]) && is_array($routeParams[0])) {
                                            $routeParams = $routeParams[0];
                                        }
                                        
                                        // Build URL manually with encrypted parameters
                                        if (!empty($routeParams) && is_array($routeParams)) {
                                            $route = Route::getRoutes()->getByName($child['route_name']);
                                            if ($route) {
                                                $uri = $route->uri();
                                                
                                                // Replace each parameter placeholder with encrypted value
                                                foreach ($routeParams as $key => $value) {
                                                    $encryptedValue = \App\Helpers\UrlEncryptionHelper::encryptUrl($value);
                                                    $uri = str_replace('{' . $key . '}', $encryptedValue, $uri);
                                                }
                                                
                                                $childUrl = url($uri);
                                            } else {
                                                // Fallback to route helper if route not found
                                                $encryptedParams = [];
                                                foreach ($routeParams as $key => $value) {
                                                    $encryptedParams[$key] = \App\Helpers\UrlEncryptionHelper::encryptUrl($value);
                                                }
                                                $childUrl = route($child['route_name'], $encryptedParams);
                                            }
                                        } else {
                                            // No parameters, use route helper directly or fallback to url field
                                            if (!empty($child['url'])) {
                                                $childUrl = url($child['url']);
                                            } else {
                                                $childUrl = route($child['route_name']);
                                            }
                                        }
                                    } catch (\Exception $e) {
                                        // Fallback to url field if route generation fails
                                        if (!empty($child['url'])) {
                                            $childUrl = url($child['url']);
                                        } else {
                                            $childUrl = '#';
                                        }
                                    }
                                } elseif (!empty($child['url'])) {
                                    // Only use url field if route_name doesn't exist
                                    $childUrl = url($child['url']);
                                }


                                // Check if child is active - must match route name AND URL path
                                $childActive = false;
                                
                                // First check if route name matches
                                if (!empty($child['route_name']) && $currentRoute === $child['route_name']) {
                                    // Route name matches, now check URL path
                                    $generatedChildPath = trim(parse_url($childUrl, PHP_URL_PATH), '/');
                                    
                                    // Compare current path with generated child URL path
                                    if ($currentPath === $generatedChildPath) {
                                        $childActive = true;
                                    } elseif (!empty($child['url'])) {
                                        // Also check database URL path as fallback
                                        $childUrlPath = trim(parse_url($child['url'], PHP_URL_PATH), '/');
                                        if ($currentPath === $childUrlPath) {
                                            $childActive = true;
                                        }
                                    } elseif (!empty($child['route_params'])) {
                                        // If no URL but has route_params, try to match
                                        try {
                                            $expectedUrl = route($child['route_name'], $child['route_params']);
                                            $expectedPath = trim(parse_url($expectedUrl, PHP_URL_PATH), '/');
                                            if ($currentPath === $expectedPath) {
                                                $childActive = true;
                                            }
                                        } catch (\Exception $e) {
                                            // If route generation fails, just check route name
                                            $childActive = true;
                                        }
                                    } else {
                                        // If no URL and no route_params, just check route name
                                        $childActive = true;
                                    }
                                } elseif (!empty($child['url'])) {
                                    // Fallback: check URL directly if route name doesn't match
                                    $childUrlPath = trim(parse_url($child['url'], PHP_URL_PATH), '/');
                                    if ($currentPath === $childUrlPath) {
                                        $childActive = true;
                                    } else {
                                        // Also check generated URL
                                        $generatedChildPath = trim(parse_url($childUrl, PHP_URL_PATH), '/');
                                        if ($currentPath === $generatedChildPath) {
                                            $childActive = true;
                                        }
                                    }
                                }

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
    @endif
</ul>

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


