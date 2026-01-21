@extends('housingTheme.layouts.app')

@section('title', 'Application Detail')

@section('content')
   
    <div class="cms-wrapper">
        <div class="row">
            <div class="col-md-12">
                <div class="cms-card">
                    <div class="cms-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3><i class="fa fa-file-alt me-2"></i> Application Detail</h3>
                                <p class="mb-0">Complete application information</p>
                            </div>
                            <a href="{{ route('application-status-check.view-list', ['id' => $id, 'status' => $status]) }}" class="btn btn-light">
                                <i class="fa fa-arrow-left me-2"></i> Back
                            </a>
                        </div>
                    </div>

                    @include('housingTheme.partials.alerts')

                    <div class="table-responsive">
                        <table class="table table-list">
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px; font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Application Information</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000" width="50%">Application Type:</th>
                                <td width="50%">Applied for <b>{{ $application['application_type'] ?? 'N/A' }}</b></td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000" width="50%">Application Number:</th>
                                <td width="50%">{{ $application['application_no'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Date of Application:</th>
                                <td>{{ isset($application['date_of_application']) ? date('d/m/Y', strtotime($application['date_of_application'])) : 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Application Status:</th>
                                <td>{{ $application['status_description'] ?? 'N/A' }}</td>
                            </tr>

                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px; font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant Personal Information (According to Service Book)</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Applicant's Name:</th>
                                <td>{{ $application['applicant_name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Father's / Husband's Name:</th>
                                <td>{{ $application['guardian_name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Permanent Address:</th>
                                <td>{{ $application['permanent_address'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Present Address:</th>
                                <td>{{ $application['present_address'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Gender:</th>
                                <td>{{ $application['gender'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Date of Birth:</th>
                                <td>
                                    @if(isset($application['date_of_birth']))
                                        {{ date('d/m/Y', strtotime($application['date_of_birth'])) }}
                                    @else
                                        Not Available
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Mobile Number:</th>
                                <td>{{ $application['office_phone_no'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Email ID:</th>
                                <td>{{ $application['mail'] ?? 'Not Available' }}</td>
                            </tr>

                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px; font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant Official Information</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Employee HRMS ID:</th>
                                <td>{{ $application['hrms_id'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Designation:</th>
                                <td>{{ $application['applicant_designation'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Office Headquarter:</th>
                                <td>{{ $application['applicant_headquarter'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Place of Posting:</th>
                                <td>{{ $application['applicant_posting_place'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Pay Band:</th>
                                <td>{{ $application['pay_band_display'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Basic Pay:</th>
                                <td>{{ $application['pay_in_the_pay_band'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Date of Joining:</th>
                                <td>
                                    @if(isset($application['date_of_joining']))
                                        {{ date('d/m/Y', strtotime($application['date_of_joining'])) }}
                                    @else
                                        Not Available
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Date of Retirement (According to Service Book):</th>
                                <td>
                                    @if(isset($application['date_of_retirement']))
                                        {{ date('d/m/Y', strtotime($application['date_of_retirement'])) }}
                                    @else
                                        Not Available
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px; font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant Office Name and Address</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Name of the office:</th>
                                <td>{{ $application['office_name'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Office Address:</th>
                                <td>{{ $application['office_address'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Office Phone Number:</th>
                                <td>{{ $application['office_phone_no'] ?? 'Not Available' }}</td>
                            </tr>

                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px; font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant DDO Information</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">DDO District:</th>
                                <td>{{ $application['district_name'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">DDO Designation:</th>
                                <td>{{ $application['ddo_designation'] ?? 'Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">DDO Address:</th>
                                <td>{{ $application['ddo_address'] ?? 'Not Available' }}</td>
                            </tr>

                            @if(isset($estatePreferences) && is_array($estatePreferences) && count($estatePreferences) > 0)
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px; font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant's Housing Estate Preference</th>
                            </tr>
                            @foreach($estatePreferences as $index => $pref)
                                @php
                                    $prefName = is_object($pref) ? ($pref->estate_name ?? 'N/A') : ($pref['estate_name'] ?? 'N/A');
                                    $prefNumber = ['First', 'Second', 'Third', 'Fourth', 'Fifth'][$index] ?? 'N/A';
                                @endphp
                                <tr>
                                    <th style="background-color:#00000000">{{ $prefNumber }} Preference:</th>
                                    <td>{{ $prefName }}</td>
                                </tr>
                            @endforeach
                            @endif

                            @if($allotmentDetails)
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px; font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Information for Allotment</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Allotment Reason:</th>
                                <td>{{ $application['allotment_category'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Estate Name:</th>
                                <td>{{ $allotmentDetails['estate_name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Estate Address:</th>
                                <td>{{ $allotmentDetails['estate_address'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Flat Type:</th>
                                <td>{{ $allotmentDetails['flat_type'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Floor:</th>
                                <td>{{ $allotmentDetails['floor'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Flat Number:</th>
                                <td>{{ $allotmentDetails['flat_no'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Block Name:</th>
                                <td>{{ $allotmentDetails['block_name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Possession Date:</th>
                                <td>{{ $allotmentDetails['possession_date'] ?? 'N/A' }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

