@extends('housingTheme.layouts.app')
@section('title', 'View Existing Occupant')
@section('page-header', 'View Existing Occupant Details')


@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-eye me-2"></i> Existing Occupant Details</h3>
                            <p>{{ $occupant['applicant_name'] ?? 'Occupant Information' }}</p>
                        </div>
                        <div>
                            <a href="{{ route('existing-occupant.edit', encrypt($occupant['online_application_id'] ?? '')) }}" 
                                class="btn btn-light me-2">
                                <i class="fa fa-edit me-2"></i> Edit
                            </a>
                            <a href="{{ route('existing-occupant.index') }}" class="btn btn-light">
                                <i class="fa fa-arrow-left me-2"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                @if(isset($occupant))
                    {{-- Flat Information --}}
                    <div class="info-section">
                        <h5 class="mb-3"><i class="fa fa-home me-2"></i> Flat Information</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-label">Estate Name</div>
                                <div class="info-value">{{ $occupant['estate_name'] ?? '-' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Flat Type</div>
                                <div class="info-value">{{ $occupant['flat_type'] ?? '-' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Block Name</div>
                                <div class="info-value">{{ $occupant['block_name'] ?? '-' }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-label">Flat No.</div>
                                <div class="info-value">{{ $occupant['flat_no'] ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Personal Information --}}
                    <div class="info-section">
                        <h5 class="mb-3"><i class="fa fa-user me-2"></i> Personal Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-label">Applicant Name</div>
                                <div class="info-value">{{ $occupant['applicant_name'] ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Father/Husband Name</div>
                                <div class="info-value">{{ $occupant['guardian_name'] ?? '-' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Date of Birth</div>
                                <div class="info-value">{{ $occupant['date_of_birth'] ? \Carbon\Carbon::parse($occupant['date_of_birth'])->format('d/m/Y') : '-' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Gender</div>
                                <div class="info-value">{{ $occupant['gender'] == 'M' ? 'Male' : 'Female' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Mobile No</div>
                                <div class="info-value">{{ $occupant['mobile_no'] ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $occupant['email'] ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Official Information --}}
                    <div class="info-section">
                        <h5 class="mb-3"><i class="fa fa-briefcase me-2"></i> Official Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-label">HRMS ID</div>
                                <div class="info-value">{{ $occupant['hrms_id'] ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Designation</div>
                                <div class="info-value">{{ $occupant['applicant_designation'] ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Place of Posting</div>
                                <div class="info-value">{{ $occupant['applicant_posting_place'] ?? '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Headquarter</div>
                                <div class="info-value">{{ $occupant['applicant_headquarter'] ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- License Information --}}
                    <div class="info-section">
                        <h5 class="mb-3"><i class="fa fa-file-text me-2"></i> License Information</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-label">License No.</div>
                                <div class="info-value">{{ $occupant['existing_occupant_license_no'] ?? '-' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">License Issue Date</div>
                                <div class="info-value">{{ $occupant['license_issue_date'] ? \Carbon\Carbon::parse($occupant['license_issue_date'])->format('d/m/Y') : '-' }}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">License Expiry Date</div>
                                <div class="info-value">{{ $occupant['license_expiry_date'] ? \Carbon\Carbon::parse($occupant['license_expiry_date'])->format('d/m/Y') : '-' }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Occupant Status</div>
                                <div class="info-value">
                                    <span class="badge bg-{{ $occupant['authorised_or_not'] == 'authorised' ? 'success' : 'warning' }}">
                                        {{ ucfirst($occupant['authorised_or_not'] ?? '-') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        Occupant data not found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

