@extends('housingTheme.layouts.app')
@section('title', 'Search Existing Applicant')
@section('page-header', 'Legacy Applicant Details Check')



@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-search me-2"></i> Search Legacy Applicant</h3>
                            <p>Search by Physical Application No or Computer Serial No</p>
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

                <div class="form-section">
                    <form method="POST" action="{{ route('existing-applicant.search.submit') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="search_type" id="search_physical" 
                                        value="physical_app_no" checked>
                                    <label class="form-check-label" for="search_physical">
                                        Physical Application No.
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="search_type" id="search_computer" 
                                        value="computer_serial_no">
                                    <label class="form-check-label" for="search_computer">
                                        Computer Serial No.
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="search_value" name="search_value" 
                                        placeholder="Enter Physical Application Number or Computer serial No" required>
                                    <label for="search_value">Enter Physical Application Number or Computer serial No</label>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-search me-2"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

