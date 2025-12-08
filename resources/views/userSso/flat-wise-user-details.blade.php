@extends('housingTheme.layouts.app')

@section('title', 'User Tagging Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4">User Tagging Details</h3>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @php
                $tagData = $data['tag_data'] ?? null;
                $draftData = $data['draft_data'] ?? null;
                $flatInfo = $data['flat_info'] ?? null;
            @endphp

            {{-- Comparison Table --}}
            <div class="card mb-4">
                <div class="card-body flatWiseUser">
                    <table class="table table-bordered">
                        <thead class="flatWiseUser table-primary ">
                            <tr>
                                <th>Parameters</th>
                                <th>Applicant Information</th>
                                <th>Departmental Information</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Flat Information</strong></td>
                                <td colspan="2" class="text-center">
                                    Estate: {{ $flatInfo->estate_name ?? 'N/A' }}, 
                                    Block: {{ $flatInfo->block_name ?? 'N/A' }}, 
                                    Floor: {{ $flatInfo->floor ?? 'N/A' }}, 
                                    Flat Type: {{ $flatInfo->flat_type ?? 'N/A' }}, 
                                    Flat No.: {{ $flatInfo->flat_no ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Applicant Name</td>
                                <td class="text-center">{{ $tagData['applicant_name'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $draftData['applicant_name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>HRMS ID</td>
                                <td class="text-center">{{ $tagData['hrms_id'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $draftData['hrms_id'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Contact Number</td>
                                <td class="text-center">{{ $tagData['mobile_no'] ?? 'N/A' }}</td>
                                <td class="text-center">{{ $draftData['mobile_no'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>License Information</td>
                                <td class="text-center">{{ $tagData['license_no'] ?? 'Not Available' }}</td>
                                <td class="text-center">{{ $draftData['license_no'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <td>License Issue Date</td>
                                <td class="text-center">{{ $tagData['license_issue_date'] ? \Carbon\Carbon::parse($tagData['license_issue_date'])->format('d/m/Y') : 'Not Available' }}</td>
                                <td class="text-center">{{ $draftData['license_issue_date'] ? \Carbon\Carbon::parse($draftData['license_issue_date'])->format('d/m/Y') : 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <td>License Expiry Date</td>
                                <td class="text-center">{{ $tagData['license_expiry_date'] ? \Carbon\Carbon::parse($tagData['license_expiry_date'])->format('d/m/Y') : 'Not Available' }}</td>
                                <td class="text-center">{{ $draftData['license_expiry_date'] ? \Carbon\Carbon::parse($draftData['license_expiry_date'])->format('d/m/Y') : 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <td>Authorised / Unauthorised</td>
                                <td class="text-center">---</td>
                                <td class="text-center">{{ ucwords($draftData['authorised_or_not'] ?? 'Not Available') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Action Form --}}
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Action Taken</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user-tagging.update-status', $encryptedFlatId) }}">
                        @csrf
                        
                        <input type="hidden" name="flat_id" value="{{ $flatId }}">
                        <input type="hidden" name="housing_user_tagging_id" value="{{ $tagData['housing_user_tagging_id'] ?? '' }}">
                        <input type="hidden" name="applicant_name" value="{{ $tagData['applicant_name'] ?? '' }}">
                        <input type="hidden" name="mobile_no" value="{{ $tagData['mobile_no'] ?? '' }}">
                        <input type="hidden" name="hrms_id" value="{{ $tagData['hrms_id'] ?? '' }}">
                        <input type="hidden" name="user_id" value="{{ $tagData['uid'] ?? '' }}">
                        <input type="hidden" name="housing_existing_occupant_draft_id" value="{{ $draftData['housing_existing_occupant_draft_id'] ?? '' }}">
                        <input type="hidden" name="license_no" value="{{ $draftData['license_no'] ?? $tagData['license_no'] ?? '' }}">
                        <input type="hidden" name="license_issue_date" value="{{ $draftData['license_issue_date'] ?? $tagData['license_issue_date'] ?? '' }}">
                        <input type="hidden" name="license_expiry_date" value="{{ $draftData['license_expiry_date'] ?? $tagData['license_expiry_date'] ?? '' }}">
                        <input type="hidden" name="authorised_or_not" value="{{ $draftData['authorised_or_not'] ?? '' }}">
                        <input type="hidden" name="draft_ddo_id" value="{{ $draftData['ddo_id'] ?? 1263 }}">
                        <input type="hidden" name="draft_pay_band_id" value="{{ $draftData['pay_band_id'] ?? 10 }}">

                        <div class="row justify-content-center">
                            <div class="col-md-6 mb-3">
                                <label for="action" class="form-label">Action *</label>
                                <select class="form-select" id="action" name="action" required>
                                    <option value="">-Select-</option>
                                    <option value="tagged">Approved</option>
                                    <option value="reject">Reject</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-6 mb-3">
                                <label for="remarks" class="form-label">Remarks *</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="4" required></textarea>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-6 text-center">
                                <button type="submit" class="btn btn-secondary px-4">Save</button>
                                <a href="{{ route('user-tagging.flat-wise-user-info') }}" class="btn btn-info px-4">Back</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

