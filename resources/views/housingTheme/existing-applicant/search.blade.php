@extends('housingTheme.layouts.app')
@section('title', 'Search Existing Applicant')
@section('page-header', 'Legacy Applicant Details Check')

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
    .cms-header h3 {
        margin: 0;
        font-weight: 600;
        font-size: 1.75rem;
    }
    .form-section {
        background: linear-gradient(135deg, #f8faff 0%, #ffffff 100%);
        padding: 2rem;
        border-radius: 12px;
        border: 1px solid rgba(73, 128, 247, 0.15);
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

