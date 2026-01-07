{{--
    Reusable Form Footer Component
    
    Usage:
    @include('housingTheme.components.form-footer', [
        'backRoute' => 'route.name',
        'cancelText' => 'Cancel',
        'submitText' => 'Submit',
        'submitButtonClass' => 'btn-submit',
    ])
--}}

<div class="mt-4 d-flex justify-content-end gap-3">
    @if(isset($backRoute))
        <a href="{{ route($backRoute) }}" class="btn btn-outline-secondary px-4">
            {{ $cancelText ?? 'Cancel' }}
        </a>
    @endif
    <button type="submit" class="btn {{ $submitButtonClass ?? 'btn-submit' }} px-{{ $submitButtonPadding ?? '4' }}">
        <i class="fa fa-save me-2"></i>{{ $submitText ?? 'Submit' }}
    </button>
</div>

