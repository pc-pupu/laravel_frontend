@extends('housingTheme.layouts.app')
@section('title', 'Form For Existing Applicant VS/CS')
@section('page-header', 'Form For Existing Applicant VS/CS')


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

                <form method="POST" action="{{ route('existing-applicant-vs-cs.store', ['uid' => $uid])}}" id="vsCsForm" enctype="multipart/form-data">
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
<script src="{{ asset('/assets/housingTheme/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('/assets/housingTheme/jquery/jquery-ui.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('/assets/housingTheme/css/jquery-ui.css') }}">
@include('housingTheme.existing-applicant-vs-cs._form_scripts')
@endpush

