@extends('housingTheme.layouts.app')

@section('title', 'Allotment Details (New Allotment)')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-info-circle me-2"></i> Allotment Details (New Allotment)</h3>
                            <p class="mb-0">View your allotment details and accept or reject the offer</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                @if($allotment)
                    @php
                        $currentDate = date('Y-m-d');
                        $allotmentDate = $allotment['allotment_approve_or_reject_date'] ?? null;
                        $finalDate = $allotmentDate ? date("Y-m-d", strtotime("+15 days", strtotime($allotmentDate))) : null;
                        $canAcceptReject = $canAcceptReject && ($currentDate <= $finalDate) && 
                                           ($allotment['accept_reject_status'] != 'Cancel') &&
                                           ($allotment['accept_reject_status'] == null || $allotment['status'] == 'offer_letter_extended');
                    @endphp

                    @if($canAcceptReject && ($allotment['accept_reject_status'] == null || $allotment['status'] == 'offer_letter_extended'))
                        <div class="mb-4">
                            <a href="{{ route('view-allotment-details.update-status', [
                                'encrypted_app_id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($allotment['online_application_id']),
                                'encrypted_status' => \App\Helpers\UrlEncryptionHelper::encryptUrl('Accept')
                            ]) }}" 
                               class="btn btn-success me-3"
                               onclick="return confirm('Are you sure you want to accept your allotment?')">
                                <i class="fa fa-check me-2"></i> Accept Offer
                            </a>
                            <a href="{{ route('view-allotment-details.update-status', [
                                'encrypted_app_id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($allotment['online_application_id']),
                                'encrypted_status' => \App\Helpers\UrlEncryptionHelper::encryptUrl('Reject')
                            ]) }}" 
                               class="btn btn-danger"
                               onclick="return confirm('Are you sure you want to reject your allotment?')">
                                <i class="fa fa-times me-2"></i> Reject Offer
                            </a>
                        </div>
                    @elseif($allotment['accept_reject_status'] == 'Accept')
                        <div class="mb-4">
                            <a href="#" class="btn btn-primary" target="_blank">
                                <i class="fa fa-file-pdf me-2"></i> Download Offer Letter
                            </a>
                        </div>
                    @endif

                    @if($documents)
                        <div class="mb-4">
                            <h5>Uploaded Documents</h5>
                            <ul class="list-unstyled">
                                @if(!empty($documents['license_application_signed_form']))
                                    <li class="mb-2">
                                        <a href="{{ asset('storage/documents/' . auth()->user()->uid . '/' . $documents['license_application_signed_form']) }}" 
                                           target="_blank" 
                                           class="text-decoration-none">
                                            <i class="fa fa-download me-2"></i> Uploaded Licence Application Signed Form
                                        </a>
                                    </li>
                                @endif
                                @if(!empty($documents['declaration_signed_form']))
                                    <li class="mb-2">
                                        <a href="{{ asset('storage/documents/' . auth()->user()->uid . '/' . $documents['declaration_signed_form']) }}" 
                                           target="_blank" 
                                           class="text-decoration-none">
                                            <i class="fa fa-download me-2"></i> Uploaded Declaration Signed Form
                                        </a>
                                    </li>
                                @endif
                                @if(!empty($documents['current_pay_slip']))
                                    <li class="mb-2">
                                        <a href="{{ asset('storage/documents/' . auth()->user()->uid . '/' . $documents['current_pay_slip']) }}" 
                                           target="_blank" 
                                           class="text-decoration-none">
                                            <i class="fa fa-download me-2"></i> Uploaded Current Pay Slip
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-list table-striped">
                            <thead>
                                <tr>
                                    <th width="20%">Allotment No</th>
                                    <th width="10%">Allotment Date</th>
                                    <th width="15%">District</th>
                                    <th width="5%">Flat Type</th>
                                    <th width="5%">Block No.</th>
                                    <th width="5%">Flat No.</th>
                                    <th width="5%">Floor</th>
                                    <th width="25%">Alloted Estate Name & Address</th>
                                    <th width="25%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $allotment['allotment_no'] }}</td>
                                    <td>{{ $allotment['allotment_date'] ? date('d/m/Y', strtotime($allotment['allotment_date'])) : '-' }}</td>
                                    <td>{{ $allotment['district_name'] }}</td>
                                    <td>{{ ($allotment['status'] == 'allotted') ? '--' : $allotment['flat_type'] }}</td>
                                    <td>{{ ($allotment['status'] == 'allotted') ? '--' : $allotment['block_name'] }}</td>
                                    <td>{{ ($allotment['status'] == 'allotted') ? '--' : $allotment['flat_no'] }}</td>
                                    <td>{{ ($allotment['status'] == 'allotted') ? '--' : $allotment['floor'] }}</td>
                                    <td>
                                        @if($allotment['status'] == 'allotted')
                                            --
                                        @else
                                            {{ $allotment['estate_name'] }},<br>{{ $allotment['estate_address'] }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(empty($allotment['accept_reject_status']))
                                            In Progress
                                        @elseif($allotment['accept_reject_status'] == 'Accept')
                                            You {{ $allotment['accept_reject_status'] }}ed the Offer of Allotment.
                                        @elseif($allotment['accept_reject_status'] == 'Reject')
                                            You {{ $allotment['accept_reject_status'] }}ed the Offer of Allotment.
                                        @elseif($allotment['accept_reject_status'] == 'Cancel')
                                            Your Offer of Allotment has been {{ $allotment['accept_reject_status'] }}led because you have not accept Offer of Allotment within one month from the Date of Allotment.
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i> No allotment details found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

