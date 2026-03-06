@extends('housingTheme.layouts.app')

@section('title', 'View Application Details')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-eye me-2"></i> Application Details</h3>
                        </div>
                        <a href="javascript:window.close();" class="btn btn-light">
                            <i class="fa fa-times me-2"></i> Close
                        </a>
                    </div>
                </div>

                @if(isset($application))
                    <div class="table-bottom">
                        <table class="table table-list">
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #0a8bd6dc;color:white;text-align: center;font-size: 18px;line-height: 24px; font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Application Information</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000" width="50%">Application Number:</th>
                                <td width="50%">{{ $application['application_no'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Date of Application:</th>
                                <td>{{ $application['date_of_application'] ? date('d/m/Y', strtotime($application['date_of_application'])) : 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000" width="50%">Allotment Reason:</th>
                                <td width="50%">{{ $application['allotment_category'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000" width="50%">Applied for flat type:</th>
                                <td width="50%">{{ $application['flat_type'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #0a8bd6dc;color:white;text-align: center;font-size: 18px;line-height: 24px; font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant Personal Information (According to Service Book)</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Applicant's Name:</th>
                                <td>{{ $application['applicant_name'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Father's/ Husband's Name:</th>
                                <td>{{ $application['guardian_name'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Present Address</th>
                                <td>{{ $application['present_address'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Permanent Address</th>
                                <td>{{ $application['permanent_address'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Gender:</th>
                                <td>{{ $application['gender'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Date of Birth(According to Service Book):</th>
                                <td>{{ $application['date_of_birth'] ? date('d/m/Y', strtotime($application['date_of_birth'])) : 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Mobile No:</th>
                                <td>{{ $application['mobile_no'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Email ID:</th>
                                <td>{{ $application['email'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #0a8bd6dc;color:white;text-align: center;font-size: 18px;line-height: 24px; font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant Official Information</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Employee HRMS ID:</th>
                                <td>{{ $application['hrms_id'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Designation:</th>
                                <td>{{ $application['applicant_designation'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Office Headquarter:</th>
                                <td>{{ $application['applicant_headquarter'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Place of Posting:</th>
                                <td>{{ $application['applicant_posting_place'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Basic Pay:</th>
                                <td>{{ $application['pay_in_the_pay_band'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Date of Joining:</th>
                                <td>{{ $application['date_of_joining'] ? date('d/m/Y', strtotime($application['date_of_joining'])) : 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Date of Retirement(According to Service Book):</th>
                                <td>{{ $application['date_of_retirement'] ? date('d/m/Y', strtotime($application['date_of_retirement'])) : 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #0a8bd6dc;color:white;text-align: center;font-size: 18px;line-height: 24px; font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant Office Name and Address</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Name of the Office:</th>
                                <td>{{ $application['office_name'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Office Address:</th>
                                <td>{{ $application['office_address'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Office Phone No.:</th>
                                <td>{{ $application['office_phone_no'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #0a8bd6dc;color:white;text-align: center;font-size: 18px;line-height: 24px; font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant DDO Information</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">DDO District.:</th>
                                <td>{{ $application['district_name'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">DDO Designation:</th>
                                <td>{{ $application['ddo_designation'] ?? 'Data Not Available' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">DDO Address :</th>
                                <td>{{ $application['ddo_address'] ?? 'Data Not Available' }}</td>
                            </tr>
                        </table>
                    </div>
                @else
                    <div class="alert alert-danger">
                        Application details not found.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
