@extends('housingTheme.layouts.app')

@section('title', 'View Allotment Information')

@section('content')
    <div class="cms-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="cms-card">
                    <div class="cms-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3><i class="fa fa-list me-2"></i> View Allotment Information</h3>
                                <p class="mb-0">Application details</p>
                            </div>
                            <a href="{{ route('application-status-check.index') }}" class="btn btn-light">
                                <i class="fa fa-arrow-left me-2"></i> Back to Search
                            </a>
                        </div>
                    </div>

                    @include('housingTheme.partials.alerts')

                    <div class="table-responsive">
                        <table class="table table-list table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Application Number</th>
                                    <th>Status</th>
                                    <th width="40%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $application['applicant_name'] ?? '-' }}</td>
                                    <td>{{ $application['application_no'] ?? '-' }}</td>
                                    <td>{{ $application['status_description'] ?? '-' }}</td>
                                    <td>
                                        @php
                                            $userRole = $userRole ?? null;

                                            $hasLicense = !empty($licenseNo);
                                            $hasPossession = !empty($possessionDate);
                                            $hasRelease = !empty($releaseDate);
                                        @endphp

                                        @if($userRole == 7)

                                            {{-- Role 7: Housing Official --}}
                                            @if($hasLicense && $hasPossession && $hasRelease)
                                                <a href="{{ route('application-status-check.view-detail', ['id' => $id, 'status' => $status]) }}" 
                                                   class="btn fa fa-eye bg-primary btn-sm px-4 rounded-pill text-white fw-bolder me-2">
                                                    View
                                                </a>
                                                <span class="btn bg-success btn-sm px-4 fa fa-check rounded-pill text-white fw-bolder me-2">
                                                    Possession Date Updated
                                                </span>
                                                <span class="btn bg-secondary fa fa-check btn-sm px-4 rounded-pill text-white fw-bolder">
                                                    Release Date Updated
                                                </span>
                                            @elseif($hasLicense && $hasPossession)
                                                <a href="{{ route('application-status-check.view-detail', ['id' => $id, 'status' => $status]) }}" 
                                                   class="btn fa fa-eye bg-primary btn-sm px-4 rounded-pill text-white fw-bolder me-2">
                                                    View
                                                </a>
                                                <span class="btn bg-success btn-sm px-4 fa fa-check rounded-pill text-white fw-bolder me-2">
                                                    Possession Date Updated
                                                </span>
                                                <a href="{{ route('application-status-check.add-release', ['id' => $id, 'status' => $status]) }}" 
                                                   class="btn fa fa-plus bg-secondary btn-sm px-4 rounded-pill text-white fw-bolder">
                                                    Add Release Date
                                                </a>
                                            @elseif(($hasLicense || !$hasLicense) && in_array($application['status'] ?? '', ['offer_letter_cancel', 'license_cancel']))
                                                @php
                                                    // Fetch extension count from API
                                                    $extensionCount = 0;
                                                    try {
                                                        $extensionType = ($application['status'] ?? '') == 'offer_letter_cancel' ? 'offer-letter' : 'license';
                                                        // This would need to be fetched via AJAX or passed from controller
                                                    } catch (\Exception $e) {
                                                        $extensionCount = 0;
                                                    }
                                                    $canExtend = $extensionCount < 2;
                                                @endphp
                                                @if(($application['status'] ?? '') == 'offer_letter_cancel' && $canExtend)
                                                    <a href="{{ route('application-status-check.view-detail', ['id' => $id, 'status' => $status]) }}" 
                                                       class="btn fa fa-eye bg-primary btn-sm px-4 rounded-pill text-white fw-bolder me-2">
                                                        View
                                                    </a>
                                                    <a href="{{ route('application-status-check.offer-letter-extension', [
                                                        'id' => $id,
                                                        'status' => $status,
                                                        'uid' => \App\Helpers\UrlEncryptionHelper::encryptUrl($application['uid'] ?? ''),
                                                        'official_detail_id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($application['applicant_official_detail_id'] ?? ''),
                                                        'date_of_verified' => \App\Helpers\UrlEncryptionHelper::encryptUrl($application['date_of_verified'] ?? '')
                                                    ]) }}" 
                                                       class="btn fa fa-history bg-primary btn-sm px-4 rounded-pill text-white fw-bolder">
                                                        Extend Offer Letter
                                                    </a>
                                                @else
                                                    <a href="{{ route('application-status-check.view-detail', ['id' => $id, 'status' => $status]) }}" 
                                                       class="btn fa fa-eye bg-primary btn-sm px-4 rounded-pill text-white fw-bolder">
                                                        View
                                                    </a>
                                                @endif
                                            @elseif($hasLicense)
                                                <a href="{{ route('application-status-check.view-detail', ['id' => $id, 'status' => $status]) }}" 
                                                   class="btn fa fa-eye bg-primary btn-sm px-4 rounded-pill text-white fw-bolder me-2">
                                                    View
                                                </a>
                                                <a href="{{ route('application-status-check.add-possession', ['id' => $id, 'status' => $status]) }}" 
                                                   class="btn bg-success btn-sm px-4 fa fa-plus rounded-pill text-white fw-bolder">
                                                    Add possession Details
                                                </a>
                                            @else
                                                    @php echo $hasLicense; @endphp
                                                <a href="{{ route('application-status-check.view-detail', ['id' => $id, 'status' => $status]) }}" 
                                                   class="btn fa fa-eye bg-primary btn-sm px-4 rounded-pill text-white fw-bolder">
                                                    View
                                                </a>
                                            @endif
                                        @elseif(in_array($userRole, [6, 10, 13]))
                                            {{-- Role 6, 10, 13: Other officials --}}
                                            <a href="{{ route('application-status-check.view-detail', ['id' => $id, 'status' => $status]) }}" 
                                               class="btn fa fa-eye bg-primary btn-sm px-4 rounded-pill text-white fw-bolder">
                                                View
                                            </a>
                                        @else
                                            <a href="{{ route('application-status-check.view-detail', ['id' => $id, 'status' => $status]) }}" 
                                               class="btn fa fa-eye bg-primary btn-sm px-4 rounded-pill text-white fw-bolder">
                                                View
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

