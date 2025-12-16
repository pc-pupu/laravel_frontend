@extends('housingTheme.layouts.app')
@section('title', 'Edit Existing Applicant')
@section('page-header', 'Edit Existing Applicant')



@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-edit me-2"></i> Edit Existing Applicant</h3>
                            <p>Update legacy/physical applicant information</p>
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
                    {{-- <strong>Note:</strong> Edit form implementation will be completed based on Drupal existing_applicant_edit_form logic. --}}
                </div>

                @if(isset($applicant))
                    <form method="POST" action="{{ route('existing-applicant.update', encrypt($applicant['online_application_id'] ?? '')) }}" 
                        id="editApplicantForm" enctype="multipart/form-data" onsubmit="return validate_existing_applicant_form()">
                        @csrf
                        @method('PUT')
                        
                        @include('housingTheme.existing-applicant.form', ['applicant' => $applicant])

                        <div class="mt-4 d-flex justify-content-end gap-3">
                            <a href="{{ route('existing-applicant.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-submit">
                                <i class="fa fa-save me-2"></i>Update Applicant
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning">
                        Applicant data not found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('/assets/housingTheme/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('/assets/housingTheme/jquery/jquery-ui.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('/assets/housingTheme/css/jquery-ui.css') }}">

@include('housingTheme.existing-applicant._form_scripts_edit')
@endpush

