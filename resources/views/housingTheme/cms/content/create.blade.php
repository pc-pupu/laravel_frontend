@extends('housingTheme.layouts.app')
@section('title', 'Add CMS Content')
@section('page-header', 'Add CMS Content')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-plus-circle me-2"></i> Add New CMS Content</h3>
                            <p>Create new CMS entries similar to Drupal housing forms.</p>
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

                <form id="cmsContentForm" method="POST" action="{{ route('cms-content.store') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    <div class="form-section">
                        @include('housingTheme.cms.content._form', ['content' => null, 'nextOrder' => $nextOrder ?? 1])
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-end gap-3">
                        <a href="{{ route('cms-content.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-submit">
                            <i class="fa fa-save me-2"></i>Save Content
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@include('housingTheme.cms.content._form-validation-script')

