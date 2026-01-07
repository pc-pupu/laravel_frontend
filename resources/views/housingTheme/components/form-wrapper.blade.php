{{--
    Reusable Form Wrapper Component
    
    Usage:
    @extends('housingTheme.layouts.app')
    @section('title', 'Add Item')
    
    @section('content')
        @include('housingTheme.components.form-wrapper', [
            'title' => 'Add New Item',
            'description' => 'Create new item entry',
            'backRoute' => 'route.name',
            'backText' => 'Back to List',
            'icon' => 'fa-plus-circle',
            'isEdit' => false,
        ])
            {{-- Form content here --}}
            <input type="text" name="name">
        @endcomponent
        
        @push('scripts')
            <script>...</script>
        @endpush
    @endsection
--}}

<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3>
                                <i class="fa {{ $icon ?? ($isEdit ?? false ? 'fa-edit' : 'fa-plus-circle') }} me-2"></i> 
                                {{ $title ?? ($isEdit ?? false ? 'Edit' : 'Add New') }}
                            </h3>
                            <p class="mb-0">{{ $description ?? '' }}</p>
                        </div>
                        @if(isset($backRoute))
                            <a href="{{ route($backRoute) }}" class="btn btn-light">
                                <i class="fa fa-arrow-left me-2"></i> {{ $backText ?? 'Back' }}
                            </a>
                        @endif
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(isset($showInfoAlert) && $showInfoAlert)
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>
                        {{ $infoAlertText ?? '' }}
                    </div>
                @endif

                @if(isset($showWarning) && $showWarning)
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        {{ $warningText ?? '' }}
                    </div>
                @endif

                @stack('form-content')
            </div>
        </div>
    </div>
</div>

