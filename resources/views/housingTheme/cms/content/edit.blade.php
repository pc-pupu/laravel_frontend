@extends('housingTheme.layouts.app')
@section('title', 'Edit CMS Content')
@section('page-header', 'Edit CMS Content')

@push('styles')
<style>
    .cms-wrapper {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f2ff 100%);
        min-height: calc(100vh - 200px);
        padding: 1.5rem 0;
    }
    .cms-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(73, 128, 247, 0.12);
        padding: 2rem;
        border: 1px solid rgba(73, 128, 247, 0.1);
    }
    .cms-header {
        background: linear-gradient(135deg, #4980f7 0%, #19bbd3 100%);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(73, 128, 247, 0.3);
    }
    .cms-header h3 {
        margin: 0;
        font-weight: 600;
        font-size: 1.75rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .cms-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.95;
        font-size: 0.95rem;
    }
    .form-section {
        background: linear-gradient(135deg, #f8faff 0%, #ffffff 100%);
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(73, 128, 247, 0.15);
        box-shadow: 0 2px 10px rgba(73, 128, 247, 0.08);
    }
    .form-floating > label {
        color: #4980f7;
        font-weight: 500;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4980f7;
        box-shadow: 0 0 0 0.2rem rgba(73, 128, 247, 0.25);
    }
    .required::after {
        content: " *";
        color: #e74c3c;
        font-weight: bold;
    }
    .btn-submit {
        background: linear-gradient(135deg, #4980f7 0%, #19bbd3 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(73, 128, 247, 0.3);
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(73, 128, 247, 0.4);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-edit me-2"></i> Edit CMS Content</h3>
                            <p>{{ $content['content_title'] ?? 'Update selected record' }}</p>
                        </div>
                        <a href="{{ route('cms-content.index') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to List
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('cms-content.update', $content['housing_cms_id']) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-section">
                        @include('housingTheme.cms.content._form', ['nextOrder' => $content['order_no'] ?? 1])
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-end gap-3">
                        <a href="{{ route('cms-content.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-submit">
                            <i class="fa fa-save me-2"></i>Update Content
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

