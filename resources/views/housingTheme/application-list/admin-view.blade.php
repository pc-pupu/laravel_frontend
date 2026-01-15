@extends('housingTheme.layouts.app')

@section('title', 'Application Details')

@section('content')
@php use Illuminate\Support\Facades\Crypt; @endphp
@php
    use Illuminate\Support\Facades\DB;
    use App\Helpers\UrlEncryptionHelper;
    
    $app = $application;
    $commonData = $application;
    $applicantData = $application['applicant_personal_info'] ?? null;
    $preferences = $application['estate_preferences'] ?? [];
    $statusDesc = $application['status_description'] ?? null;
    $flatDetails = $application['allotment_flat_details'] ?? null;
    $documents = $application['extra_doc_path'] ?? null;
    
    // Determine entity type added by Subham dt.05-01-2026
    $entityType = '';
    if (isset($app['application_no'])) {
      if (strpos($app['application_no'], 'NA') !== false) {
          $entityType = 'New Application';
      } elseif (strpos($app['application_no'], 'VS') !== false) {
          $entityType = 'Floor Shifting';
      } elseif (strpos($app['application_no'], 'CS') !== false) {
          $entityType = 'Category Shifting';
      } elseif (strpos($app['application_no'], 'PA') !== false) {
          $entityType = 'Physical Application';
      }elseif (strpos($app['application_no'], 'EO') !== false) {
          $entityType = 'Existing Occupant Application';
      }  
    }
    // if (isset($app['application_type'])) {
    //     switch($app['application_type']) {
    //         case 'new-apply':
    //             $entityType = 'New Allotment';
    //             break;
    //         case 'vs':
    //             $entityType = 'Vertical Shifting';
    //             break;
    //         case 'cs':
    //             $entityType = 'Category Shifting';
    //             break;
    //         case 'license':
    //             $entityType = 'New Licence';
    //             break;
    //         default:
    //             $entityType = ucfirst($app['application_type'] ?? 'Application');
    //     }
    // }
    
    // Determine heading based on entity type
    $heading = 'Information for Allotment';
    if ($entityType == 'New Allotment') {
        $heading = 'Information for Allotment';
    } else if (in_array($entityType, ['Vertical Shifting', 'Category Shifting'])) {
        $heading = 'Existing Flat Details With Possession Date';
    } else if (in_array($entityType, ['New Licence', 'VS Licence', 'CS Licence', 'Renew Licence'])) {
        $heading = 'Allotment Details';
    }
    
    // Format date helper
    $formatDate = function($date) {
        if (empty($date) || $date == '0000-00-00') return 'N/A';
        return implode('/', array_reverse(explode('-', $date)));
    };
    
    // Get district name helper
    $getDistrictName = function($districtCode) {
        if (empty($districtCode)) return '';
        try {
            $district = DB::table('housing_district')
                ->where('district_code', $districtCode)
                ->value('district_name');
            return $district ?? '';
        } catch (\Exception $e) {
            return '';
        }
    };
    
    // Format permanent address
    $permanentDistrictName = $getDistrictName($applicantData['permanent_district'] ?? '');
    $permanentAddressParts = array_filter([
        $applicantData['permanent_street'] ?? '',
        !empty($applicantData['permanent_post_office']) ? 'P.O - ' . $applicantData['permanent_post_office'] : '',
        $applicantData['permanent_city_town_village'] ?? '',
        $permanentDistrictName,
        !empty($applicantData['permanent_pincode']) ? '-' . $applicantData['permanent_pincode'] : '',
    ]);
    $permanentAddress = !empty($permanentAddressParts) ? implode(', ', $permanentAddressParts) : 'Not Available';
    
    // Format present address
    if (($applicantData['permanent_present_same'] ?? 0) == 1) {
        $presentAddress = $permanentAddress;
    } else {
        $presentDistrictName = $getDistrictName($applicantData['present_district'] ?? '');
        $presentAddressParts = array_filter([
            $applicantData['present_street'] ?? '',
            !empty($applicantData['present_post_office']) ? 'P.O - ' . $applicantData['present_post_office'] : '',
            $applicantData['present_city_town_village'] ?? '',
            $presentDistrictName,
            !empty($applicantData['present_pincode']) ? '-' . $applicantData['present_pincode'] : '',
        ]);
        $presentAddress = !empty($presentAddressParts) ? implode(', ', $presentAddressParts) : 'Not Available';
    }
    
    // Format office address
    $officeDistrictName = $getDistrictName($app['office_district'] ?? '');
    $officeAddress = implode(', ', array_filter([
        $app['office_street'] ?? '',
        'P.O - ' . ($app['office_post_office'] ?? ''),
        $app['office_city_town_village'] ?? '',
        $officeDistrictName,
        '-' . ($app['office_pin_code'] ?? ''),
    ]));
    
    // Pay band string
    $payBandStr = '';
    if (!empty($app['scale_from']) || !empty($app['scale_to'])) {
        if (($app['scale_from'] ?? 0) == 0 && ($app['scale_to'] ?? 0) != 0) {
            $payBandStr = ($app['flat_type'] ?? '') . ' (Below Rs ' . $app['scale_to'] . '/-)';
        } else if (($app['scale_from'] ?? 0) != 0 && ($app['scale_to'] ?? 0) == 0) {
            $payBandStr = ($app['flat_type'] ?? '') . ' (Rs ' . $app['scale_from'] . '/- and above)';
        } else {
            $payBandStr = ($app['flat_type'] ?? '') . ' (Rs ' . ($app['scale_from'] ?? 0) . '/- to Rs ' . ($app['scale_to'] ?? 0) . '/-)';
        }
    }
    
    // Status description based on pageStatus
    $statusDescription = '';
    if ($pageStatus == 'verified-list') {
        $statusDescription = $statusDesc['status_description'] ?? 'N/A';
    } else if ($pageStatus == 'action-list') {
        $statusDescription = $statusDesc['status_description'] ?? 'N/A';
    }
    
    // Check if supporting document should be shown
    $showSupportingDoc = false;
    $supportingDocPath = '';
    if ($documents) {
        $allotmentCategory = $app['na_allotment_category'] ?? $app['allotment_category'] ?? 'General';
        if ($allotmentCategory != 'General') {
            $showSupportingDoc = true;
            $supportingDocPath = $documents;
        }
    }
    
    // Check for VS/CS license download
    $showCurrentLicense = false;
    $currentLicensePath = '';
    $customFileName = '';
    if ($entityType == 'Vertical Shifting' && !empty($app['uri_vs'])) {
        $showCurrentLicense = true;
        $currentLicensePath = $app['uri_vs'];
        $customFileName = str_replace(' ', '', $app['application_no'] ?? '') . '_Current_Licence';
    } else if ($entityType == 'Category Shifting' && !empty($app['uri_cs'])) {
        $showCurrentLicense = true;
        $currentLicensePath = $app['uri_cs'];
        $customFileName = str_replace(' ', '', $app['application_no'] ?? '') . '_Current_Licence';
    }
    
    // Check if rejection form should be shown
    $showRejectionForm = false;
    $statusShortCode = $statusDesc['short_code'] ?? '';
    $rejectedStatuses = ['housing_sup_reject_1', 'housing_official_reject', 'housing_sup_reject_2', 'housingapprover_reject1', 'housingapprover_reject2'];
    if (in_array($statusShortCode, $rejectedStatuses) && $pageStatus == 'action-list') {
        $showRejectionForm = true;
    }
    
    // Get entity from application_no
    $entity = '';
    if (!empty($app['application_no'])) {
        $parts = explode('-', $app['application_no']);
        $entity = $parts[0] ?? '';
    }
    
    $encryptedId = UrlEncryptionHelper::encryptUrl($app['online_application_id'] ?? '');
    $encryptedStatus = UrlEncryptionHelper::encryptUrl($status ?? '');
    $encryptedEntity = UrlEncryptionHelper::encryptUrl($entity);
    $encryptedComputerSerialNo = !empty($app['computer_serial_no']) ? UrlEncryptionHelper::encryptUrl($app['computer_serial_no']) : '';
    $encryptedStatusShortCode = !empty($statusDesc['short_code']) ? UrlEncryptionHelper::encryptUrl($statusDesc['short_code']) : '';
@endphp

<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-file-alt me-2"></i> Application Details</h3>
                            <p class="mb-0">View application information</p>
                        </div>
                        {{-- <a href="javascript:history.back()" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back
                        </a> --}}
                    </div>
                </div>
                
                <div class="cms-body">
                    <div class="table-bottom">
                        <table class="table table-list">
                            @if($showSupportingDoc || $showCurrentLicense)
                            <tr>
                                <td colspan="2">
                                    @if($showSupportingDoc)
                                        @if(!empty($supportingDocPath))
                                            <a href="{{ route('supporting-doc.download', ['path' => Crypt::encryptString($supportingDocPath)]) }}" class="btn bg-dark px-5 rounded-pill text-white fw-bolder">
                                                <i class="fa fa-download me-2"></i> Download Supporting Document
                                            </a>
                                        @else
                                            <span class="text-danger">Supporting Document Not Uploaded</span>
                                        @endif
                                    @endif
                                    
                                    @if($showCurrentLicense)
                                        @if(!empty($currentLicensePath))
                                            <a href="{{ asset('storage/' . $currentLicensePath) }}" download="{{ $customFileName }}" class="btn bg-dark px-4 rounded-pill text-white fw-bolder">
                                                <i class="fa fa-download me-2"></i> Download Current Licence
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endif
                            
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px;font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Application Status</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000" width="50%">Application Type</th>
                                <td width="50%">Application for {{ $entityType }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000" width="50%">Application No.</th>
                                <td width="50%">{{ $app['application_no'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Date of Application</th>
                                <td>{{ $formatDate($app['date_of_application'] ?? '') }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Application Status</th>
                                <td>{{ $statusDesc['status_description'] }}</td> {{-- <td> Modified by Subham dt.05-01-2025 </td>  --}}
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Date of Last Action</th>
                                <td>
                                    @if(!empty($app['date_of_verified']))
                                        {{ date('d/m/Y g:i a', strtotime($app['date_of_verified'])) }}
                                    @else
                                        Not Verified
                                    @endif
                                </td>
                            </tr>
                            
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px;font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant Personal Information(According to Service Book)</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Applicant's Name</th>
                                <td>{{ !empty($applicantData['applicant_name']) ? $applicantData['applicant_name'] : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Father's / Husband's Name</th>
                                <td>{{ !empty($applicantData['guardian_name']) ? $applicantData['guardian_name'] : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Permanent Address</th>
                                <td>{{ $permanentAddress }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Present Address</th>
                                <td>{{ $presentAddress }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Gender</th>
                                <td>{{ ($applicantData['gender'] ?? '') == 'M' ? 'Male' : 'Female' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Date of Birth(According to Service Book)</th>
                                <td>{{ $formatDate($applicantData['date_of_birth'] ?? '') }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Mobile No</th>
                                <td>{{ !empty($applicantData['mobile_no']) ? $applicantData['mobile_no'] : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Email ID</th>
                                <td>{{ $applicantData['email'] ?? 'N/A' }}</td>
                            </tr>
                            
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px;font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant Official Information</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Employee HRMS ID</th>
                                <td>{{ $app['hrms_id'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Designation</th>
                                <td>{{ !empty($app['applicant_designation']) ? $app['applicant_designation'] : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Office Headquarter</th>
                                <td>{{ !empty($app['applicant_headquarter']) ? $app['applicant_headquarter'] : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Place of Posting</th>
                                <td>{{ !empty($app['applicant_posting_place']) ? $app['applicant_posting_place'] : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Pay Band</th>
                                <td>{{ !empty($payBandStr) ? $payBandStr : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Basic Pay</th>
                                <td>{{ !empty($app['pay_in_the_pay_band']) ? $app['pay_in_the_pay_band'] : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Date of Joining</th>
                                <td>{{ $formatDate($app['date_of_joining'] ?? '') }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Date of Retirement(According to Service Book)</th>
                                <td>{{ $formatDate($app['date_of_retirement'] ?? '') }}</td>
                            </tr>
                            
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px;font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant Office Name and Address</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Name of the Office</th>
                                <td>{{ $app['office_name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Office Address</th>
                                <td>{{ $officeAddress }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Office Phone No.</th>
                                <td>{{ $app['office_phone_no'] ?? 'N/A' }}</td>
                            </tr>
                            
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px;font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant DDO Information</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">DDO District</th>
                                <td>{{ $app['district_name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">DDO Designation</th>
                                <td>{{ $app['ddo_designation'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">DDO Address</th>
                                <td>{{ $app['ddo_address'] ?? 'N/A' }}</td>
                            </tr>
                            
                            @if($entityType == 'New Allotment')
                                <!-- New Allotment - no special section needed -->
                            @elseif(in_array($entityType, ['Vertical Shifting', 'Category Shifting']) && $flatDetails)
                                <tr>
                                    <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px;font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">{{ $heading }}</th>
                                </tr>
                                <tr>
                                    <th style="background-color:#00000000">Possession Date</th>
                                    <td>{{ $formatDate($flatDetails['possession_date'] ?? '') }}</td>
                                </tr>
                                <tr>
                                    <th style="background-color:#00000000">Estate Name</th>
                                    <td>{{ $flatDetails['estate_name'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th style="background-color:#00000000">Block Name</th>
                                    <td>{{ $flatDetails['block_name'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th style="background-color:#00000000">Flat No</th>
                                    <td>{{ $flatDetails['flat_no'] ?? 'N/A' }}</td>
                                </tr>
                            @elseif(in_array($entityType, ['New Licence', 'VS Licence', 'CS Licence', 'Renew Licence']))
                                <tr>
                                    <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px;font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">{{ $heading }}</th>
                                </tr>
                                <!-- Add allotment details for license types if needed -->
                            @endif
                            
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px;font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Allotment Information</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Estate Name</th>
                                <td>{{ $flatDetails['estate_name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Block Name</th>
                                <td>{{ $flatDetails['block_name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Flat Type</th>
                                <td>{{ $flatDetails['flat_type'] ?? ($app['flat_type'] ?? 'N/A') }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Flat No</th>
                                <td>{{ $flatDetails['flat_no'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Floor</th>
                                <td>{{ $flatDetails['floor'] ?? 'N/A' }}</td>
                            </tr>
                            
                            @if(!empty($app['remarks']))
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px;font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Application Rejection Remarks</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000">Rejection Remarks</th>
                                <td>{{ $app['remarks'] }}</td>
                            </tr>
                            @endif
                        </table>
                        
                        @if($showRejectionForm)
                        <table class="table table-list">
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #cb3232;color:white;text-align: center;font-size: 18px;line-height: 24px;font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Remarks for Rejection</th>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center">
                                    <form action="{{ route('reject-application') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="online_application_id" value="{{ $encryptedId }}">
                                        <input type="hidden" name="rejected_status" value="{{ $encryptedStatusShortCode }}">
                                        <input type="hidden" name="status" value="{{ $encryptedStatus }}">
                                        <input type="hidden" name="entity" value="{{ $encryptedEntity }}">
                                        @if(!empty($encryptedComputerSerialNo))
                                        <input type="hidden" name="computer_serial_no" value="{{ $encryptedComputerSerialNo }}">
                                        @endif
                                        <textarea name="reject_remarks" id="reject_remarks" class="form-control" rows="4" required></textarea>
                                        <button type="submit" class="btn bg-success btn-sm px-5 mt-4 rounded-pill text-white fw-bolder" value="Submit" onclick="return confirm('Are you sure you want to reject?')">
                                            Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

