{{--
    Reusable List Wrapper Component
    
    Usage:
    @extends('housingTheme.layouts.app')
    @section('title', 'Item List')
    
    @section('content')
        @include('housingTheme.components.list-wrapper', [
            'title' => 'Item List',
            'description' => 'Manage items',
            'icon' => 'fa-users',
            'addRoute' => 'route.name',
            'addText' => 'Add New',
            'searchRoute' => 'route.name',
        ])
            {{-- List content here --}}
        @endcomponent
    @endsection
--}}

<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div>
                            <h3>
                                <i class="fa {{ $icon ?? 'fa-list' }} me-2"></i> 
                                {{ $title ?? 'List' }}
                            </h3>
                            <p>{{ $description ?? '' }}</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            @if(isset($searchRoute))
                                <a href="{{ route($searchRoute) }}" class="btn btn-light me-2">
                                    <i class="fa fa-search me-2"></i> Search
                                </a>
                            @endif
                            @if(isset($addRoute))
                                <a href="{{ route($addRoute) }}" class="btn btn-light">
                                    <i class="fa fa-plus me-2"></i> {{ $addText ?? 'Add New' }}
                                </a>
                            @endif
                            @if(isset($customActions))
                                @foreach($customActions as $action)
                                    <a href="{{ isset($action['route']) ? route($action['route'], $action['params'] ?? []) : ($action['url'] ?? '#') }}" 
                                       class="btn {{ $action['class'] ?? 'btn-light' }} {{ isset($action['route']) || isset($action['url']) ? '' : 'ms-2' }}">
                                        @if(isset($action['icon']))
                                            <i class="fa {{ $action['icon'] }} me-2"></i>
                                        @endif
                                        {{ $action['text'] ?? '' }}
                                    </a>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                @if(isset($searchFormRoute) || isset($searchRoute))
                    <div class="search-filter-box">
                        <form method="GET" action="{{ isset($searchFormRoute) ? route($searchFormRoute) : route($searchRoute ?? '') }}" class="row g-3">
                            <div class="col-md-10">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="search" name="search" 
                                        placeholder="Search" value="{{ $filters['search'] ?? request('search', '') }}">
                                    <label for="search">
                                        <i class="fa fa-search me-2"></i>{{ $searchLabel ?? 'Search' }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-primary w-100" type="submit">
                                    <i class="fa fa-search"></i> Search
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                {{ $slot }}
            </div>
        </div>
    </div>
</div>

