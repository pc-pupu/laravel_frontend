@extends('housingTheme.layouts.app')
@section('title', 'View Existing Applicant')
@section('page-header', 'View Existing Applicant Details')

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
    .info-table {
        width: 100%;
    }
    .info-table th {
        background: #473a39;
        color: white;
        padding: 1rem;
        text-align: center;
        font-size: 18px;
        font-weight: normal;
    }
    .info-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e0e0e0;
    }
    .info-table td:first-child {
        width: 50%;
        background-color: #f8f9fa;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3><i class="fa fa-user me-2"></i> Applicant Details</h3>
                    <div>
                        <a href="{{ route('existing-applicant.edit', request()->route('id')) }}" class="btn btn-primary me-2">
                            <i class="fa fa-edit me-2"></i> Edit
                        </a>
                        <a href="{{ route('existing-applicant.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left me-2"></i> Back to List
                        </a>
                    </div>
                </div>

                @if(isset($applicant))
                    <div class="table-responsive">
                        <table class="table info-table">
                            <tr>
                                <th colspan="2">Application Information</th>
                            </tr>
                            <tr>
                                <td>Application Number</td>
                                <td>{{ $applicant['application_no'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <td>Date of Application</td>
                                <td>{{ $applicant['date_of_application'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <td>Physical Application No</td>
                                <td>{{ $applicant['physical_application_no'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <td>Computer Serial No</td>
                                <td>{{ $applicant['computer_serial_no'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th colspan="2">Personal Information</th>
                            </tr>
                            <tr>
                                <td>Applicant's Name</td>
                                <td>{{ $applicant['applicant_name'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <td>Father / Husband Name</td>
                                <td>{{ $applicant['guardian_name'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <td>Mobile No</td>
                                <td>{{ $applicant['mobile_no'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <td>Email ID</td>
                                <td>{{ $applicant['email'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <td>Gender</td>
                                <td>{{ $applicant['gender'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <td>Date of Birth</td>
                                <td>{{ $applicant['date_of_birth'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th colspan="2">Official Information</th>
                            </tr>
                            <tr>
                                <td>HRMS ID</td>
                                <td>{{ $applicant['hrms_id'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <td>Designation</td>
                                <td>{{ $applicant['applicant_designation'] ?? 'Not Available' }}</td>
                            </tr>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        Applicant data not found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

