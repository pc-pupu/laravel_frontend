@extends('housingTheme.layouts.app')
@section('title', 'Form For Existing Applicant VS/CS')
@section('page-header', 'Form For Existing Applicant VS/CS')

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
    .form-section {
        margin-bottom: 2.5rem;
        padding: 1.5rem;
        background: #f8faff;
        border-radius: 12px;
        border: 1px solid rgba(73, 128, 247, 0.15);
    }
    .form-section h5 {
        color: #4980f7;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(73, 128, 247, 0.2);
    }
    .required::after {
        content: " *";
        color: red;
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
                            <h3><i class="fa fa-file-alt me-2"></i> Form For Existing Applicant VS/CS</h3>
                            <p class="mb-0">Floor Shifting (VS) / Category Shifting (CS) Application</p>
                        </div>
                        <a href="{{ route('existing-applicant-vs-cs.flat-wise-form') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back
                        </a>
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

                <form method="POST" action="{{ route('existing-applicant-vs-cs.store') }}" id="vsCsForm" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="housing_hidden_uid_or_draft_id" value="{{ $uid }}">

                    @include('housingTheme.existing-applicant-vs-cs._form_fields', ['applicantData' => $applicantData ?? null])

                    <div class="mt-4 d-flex justify-content-end gap-3">
                        <a href="{{ route('existing-applicant-vs-cs.flat-wise-form') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fa fa-save me-2"></i>Apply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">

@include('housingTheme.existing-applicant-vs-cs._form_scripts')
@endpush

