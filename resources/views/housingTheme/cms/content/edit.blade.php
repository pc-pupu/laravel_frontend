@extends('housingTheme.layouts.app')
@section('title', 'Edit CMS Content')
@section('page-header', 'Edit CMS Content')


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

                <div id="cmsFormError" class="alert alert-danger d-none"></div>

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form id="cmsContentForm" method="POST" action="{{ route('cms-content.update', $content['housing_cms_id']) }}" enctype="multipart/form-data" novalidate>
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

@include('housingTheme.cms.content._form-validation-script')

