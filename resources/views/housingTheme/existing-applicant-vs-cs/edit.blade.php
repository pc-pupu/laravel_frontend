@extends('housingTheme.layouts.app')
@section('title', 'Edit VS/CS Application')
@section('page-header', 'Edit VS/CS Application')


@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-edit me-2"></i> Edit VS/CS Application</h3>
                            <p class="mb-0">Update Floor Shifting (VS) / Category Shifting (CS) Application</p>
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

                @if(isset($application))
                    <form method="POST" action="{{ route('existing-applicant-vs-cs.update', $id) }}" id="vsCsForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @include('housingTheme.existing-applicant-vs-cs._form_fields_edit', ['application' => $application])

                        <div class="mt-4 d-flex justify-content-end gap-3">
                            <a href="{{ route('existing-applicant-vs-cs.flat-wise-form') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fa fa-save me-2"></i>Update
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning">
                        Application data not found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">

@include('housingTheme.existing-applicant-vs-cs._form_scripts_edit')
@endpush

