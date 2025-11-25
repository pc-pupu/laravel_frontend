@extends('housingTheme.layouts.app')
@section('title', 'Add Existing Applicant')
@section('page-header', 'Add Existing Applicant (Legacy/Physical Applicant)')



@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-plus-circle me-2"></i> Add New Existing Applicant</h3>
                            <p>Create new legacy/physical applicant entry (waiting list applicant)</p>
                        </div>
                        <a href="{{ route('existing-applicant.index') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to List
                        </a>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="alert alert-info">
                    <i class="fa fa-info-circle me-2"></i>
                    {{-- <strong>Note:</strong> This form is for entering legacy/physical applicants. 
                    The full form implementation will be completed based on the Drupal existing_applicant_form.inc structure. --}}
                </div>

                <form method="POST" action="{{ route('existing-applicant.store') }}" id="existingApplicantForm" 
                    enctype="multipart/form-data" onsubmit="return validate_existing_applicant_form()">
                    @csrf
                    
                    @include('housingTheme.existing-applicant._form_fields')

                    <div class="mt-4 d-flex justify-content-end gap-3">
                        <a href="{{ route('existing-applicant.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-submit">
                            <i class="fa fa-save me-2"></i>Submit
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

@include('housingTheme.existing-applicant._form_scripts')
@endpush

