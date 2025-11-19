@extends('housingTheme.layouts.app')
@section('title', 'Edit Existing Applicant')
@section('page-header', 'Edit Existing Applicant')

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
        margin-bottom: 1.5rem;
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
                    <form method="POST" action="#" id="editApplicantForm" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <div class="form-section">
                            <h5 class="mb-3"><i class="fa fa-user me-2"></i> Personal Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="applicant_name" name="applicant_name" 
                                            value="{{ $applicant['applicant_name'] ?? '' }}" required>
                                        <label for="applicant_name" class="required">Applicant's Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="guardian_name" name="guardian_name" 
                                            value="{{ $applicant['guardian_name'] ?? '' }}" required>
                                        <label for="guardian_name" class="required">Father / Husband Name</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-3">
                            <a href="{{ route('existing-applicant.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editApplicantForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Form submission');
        });
    }
});
</script>
@endpush

