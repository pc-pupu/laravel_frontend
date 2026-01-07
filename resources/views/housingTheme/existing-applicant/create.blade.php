@extends('housingTheme.layouts.app')
@section('title', 'Add Existing Applicant')
@section('page-header', 'Add Existing Applicant (Legacy/Physical Applicant)')

@section('content')
@include('housingTheme.components.form-wrapper', [
    'title' => 'Add New Existing Applicant',
    'description' => 'Create new legacy/physical applicant entry (waiting list applicant)',
    'backRoute' => 'existing-applicant.index',
    'backText' => 'Back to List',
    'icon' => 'fa-plus-circle',
    'showInfoAlert' => true,
])

@push('form-content')
<form method="POST" action="{{ route('existing-applicant.store') }}" id="existingApplicantForm" 
    enctype="multipart/form-data" onsubmit="return validate_existing_applicant_form()">
    @csrf
    
    @include('housingTheme.existing-applicant.form', ['applicant' => []])

    @include('housingTheme.components.form-footer', [
        'backRoute' => 'existing-applicant.index',
        'submitText' => 'Submit',
    ])
</form>
@endpush

@endsection

@push('scripts')
<script src="{{ asset('/assets/housingTheme/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('/assets/housingTheme/jquery/jquery-ui.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('/assets/housingTheme/css/jquery-ui.css') }}">

@include('housingTheme.existing-applicant._form_scripts')
@endpush

