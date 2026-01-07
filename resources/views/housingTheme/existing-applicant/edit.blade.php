@extends('housingTheme.layouts.app')
@section('title', 'Edit Existing Applicant')
@section('page-header', 'Edit Existing Applicant')

@section('content')
@include('housingTheme.components.form-wrapper', [
    'title' => 'Edit Existing Applicant',
    'description' => 'Update legacy/physical applicant information',
    'backRoute' => 'existing-applicant.index',
    'backText' => 'Back to List',
    'icon' => 'fa-edit',
    'isEdit' => true,
    'showInfoAlert' => true,
])

@push('form-content')
@if(isset($applicant))
    <form method="POST" action="{{ route('existing-applicant.update', encrypt($applicant['online_application_id'] ?? '')) }}" 
        id="editApplicantForm" enctype="multipart/form-data" onsubmit="return validate_existing_applicant_form()">
        @csrf
        @method('PUT')
        
        @include('housingTheme.existing-applicant.form', ['applicant' => $applicant])

        @include('housingTheme.components.form-footer', [
            'backRoute' => 'existing-applicant.index',
            'submitText' => 'Update Applicant',
        ])
    </form>
@else
    <div class="alert alert-warning">
        Applicant data not found.
    </div>
@endif
@endpush

@endsection

@push('scripts')
<script src="{{ asset('/assets/housingTheme/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('/assets/housingTheme/jquery/jquery-ui.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('/assets/housingTheme/css/jquery-ui.css') }}">

@include('housingTheme.existing-applicant._form_scripts_edit')
@endpush

