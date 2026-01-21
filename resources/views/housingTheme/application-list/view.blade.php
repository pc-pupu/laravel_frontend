@extends('housingTheme.layouts.app')

@section('title', 'Application Details')

@section('content')
    @php
                use Illuminate\Support\Facades\DB;

                $app = $application;
                $commonData = $application;
                $applicantData = $application['applicant_personal_info'] ?? null;
                $preferences = $application['estate_preferences'] ?? [];
                $statusDesc = $application['status_description'] ?? null;
                $flatDetails = $application['allotment_flat_details'] ?? null;
                $documents = $application['documents'] ?? null;
                // print_r($application);die;

                // Determine entity type
                // $entityType = '';
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
            $entityType = '';
            if (isset($app['application_no'])) {
                if (strpos($app['application_no'], 'NA') !== false) {
                    $entityType = 'New Allotment';
                } elseif (strpos($app['application_no'], 'VS') !== false) {
                    $entityType = 'Vertical Shifting';
                } elseif (strpos($app['application_no'], 'CS') !== false) {
                    $entityType = 'Category Shifting';
                } elseif (strpos($app['application_no'], 'PA') !== false) {
                    $entityType = 'Physical Application';
                } elseif (strpos($app['application_no'], 'EO') !== false) {
                    $entityType = 'Existing Occupant Application';
                }
            }

                // Determine heading based on entity type and status
                $statusShortCode = $statusDesc['short_code'] ?? null;
                $heading = 'Information for Allotment';
                if ($entityType == 'New Allotment') {
                    if ($statusShortCode == 'existing_occupant') {
                        $heading = 'Possession Details';
                    } else {
                        $heading = 'Information for Allotment';
                    }
                } else if (in_array($entityType, ['Vertical Shifting', 'Category Shifting'])) {
                    $heading = 'Possession Details';
                } else if (in_array($entityType, ['New Licence', 'VS Licence', 'CS Licence', 'Renew Licence'])) {
                    $heading = 'Allotment Details';
                } else if (empty($entityType) && $statusShortCode == 'existing_occupant') {
                    $heading = 'Possession Details';
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
                    $applicantData['permanent_city_town_village'] ?? '',
                    !empty($applicantData['permanent_post_office']) ? 'P.O- ' . $applicantData['permanent_post_office'] : '',
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
                        $applicantData['present_city_town_village'] ?? '',
                        !empty($applicantData['present_post_office']) ? 'P.O- ' . $applicantData['present_post_office'] : '',
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

                // Check if supporting document should be shown
                $showSupportingDoc = false;
                if ($documents && !empty($documents->extra_doc_path)) {
                    $allotmentCategory = $app['na_allotment_category'] ?? $app['allotment_category'] ?? 'General';
                    $applicationNoPrefix = substr($app['application_no'] ?? '', 0, 2);
                    if ($allotmentCategory != 'General' && !in_array($applicationNoPrefix, ['VS', 'CS'])) {
                        // Get status_id to check if <= 8
                        $statusId = DB::table('housing_allotment_status_master')
                            ->where('short_code', $app['application_status'] ?? '')
                            ->value('status_id');
                        if ($statusId && $statusId <= 8) {
                            $showSupportingDoc = true;
                        }
                    }
                }
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
                            <a href="{{ route('application-list.index') }}" class="btn btn-light">
                                <i class="fa fa-arrow-left me-2"></i> Back to List
                            </a>
                        </div>
                    </div>

                    @include('housingTheme.partials.alerts')

                    <div class="table-bottom row mb-3">
                        <div class="col-md-2">
                            @php
                                $filename = str_replace(' ', '', $entityType);
                                $encryptedId = \App\Helpers\UrlEncryptionHelper::encryptUrl($app['online_application_id'] ?? 0);
                                $encryptedStatus = \App\Helpers\UrlEncryptionHelper::encryptUrl($app['application_status'] ?? '');
                            @endphp
                            <a href="{{ route('application_detail_pdf', ['id' => $encryptedId, 'status' => $encryptedStatus]) }}" 
                               target="_blank" 
                               class="btn bg-primary px-6 rounded-pill text-white fw-bolder mb-2">
                                <i class="fa fa-download me-2"></i> Download Details
                            </a>
                        </div>

                        @if($showSupportingDoc && !empty($documents->extra_doc_path))
                        <div class="col-md-4">
                            <a href="{{ asset('storage/' . $documents->extra_doc_path) }}" 
                               target="_blank" 
                               class="btn bg-primary px-6 rounded-pill text-white fw-bolder mb-2">
                                <i class="fa fa-download me-2"></i> Download Supporting Document
                            </a>
                        </div>
                        @endif

                        <div class="col-md-2">
                            @if(in_array($app['application_status'] ?? '', ['license_generate', 'flat_possession_taken']))
                                @php
                                    $licenseId = \App\Helpers\UrlEncryptionHelper::encryptUrl($app['online_application_id'] ?? 0);
                                @endphp
                                <a href="{{ route('download_licence_pdf', ['id' => $licenseId]) }}" 
                                   target="_blank" 
                                   class="btn bg-primary px-6 rounded-pill text-white fw-bolder mb-2">
                                    <i class="fa fa-download me-2"></i> Download License
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="table-bottom">
                        <table class="table table-list">
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px;font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Application Status</th>
                            </tr>
                            <tr>
                                <th style="background-color:#00000000" width="50%">Application Type</th>
                                <td width="50%">
                                    @if(empty($entityType) && $statusShortCode == 'existing_occupant')
                                        Application for Existing Occupant
                                    @else
                                        Application for {{ $entityType }}
                                    @endif
                                </td>
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
                                <td>{{ $statusDesc['status_description'] ?? 'N/A' }}</td>
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

                            @if(!empty($preferences))
                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px;font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">Applicant's Housing Estate Preference</th>
                            </tr>
                            @foreach($preferences as $index => $pref)
                                @php
                                    $prefLabel = ['First', 'Second', 'Third', 'Fourth', 'Fifth'][$index] ?? 'Preference';
                                @endphp
                                <tr>
                                    <th style="background-color:#00000000">{{ $prefLabel }} Preference</th>
                                    <td>{{ $pref['estate_name'] ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                            @endif

                            <tr>
                                <th colspan="2" style="background: none repeat scroll 0 0 #473a39;color:white;text-align: center;font-size: 18px;line-height: 24px;font-weight: normal;font-family: 'Dosis',Arial,Verdana,serif;" class="first">{{ $heading }}</th>
                            </tr>

                            @if($entityType == 'New Allotment')
                                @if($statusShortCode == 'existing_occupant' && $flatDetails)
                                    <tr>
                                        <th style="background-color:#00000000">Possession Date</th>
                                        <td>{{ !empty($flatDetails['possession_date']) ? $formatDate($flatDetails['possession_date']) : 'Not Available' }}</td>
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
                                        <th style="background-color:#00000000">Flat No.</th>
                                        <td>{{ $flatDetails['flat_no'] ?? 'N/A' }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <th style="background-color:#00000000">Allotment Category</th>
                                        <td>{{ $app['na_allotment_category'] ?? $app['allotment_category'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th style="background-color:#00000000">Flat Type</th>
                                        <td>{{ $app['flat_type'] ?? 'N/A' }}</td>
                                    </tr>
                                @endif
                            @elseif(in_array($entityType, ['Vertical Shifting', 'Category Shifting']) && $flatDetails)
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
                                    <th style="background-color:#00000000">Flat No.</th>
                                    <td>{{ $flatDetails['flat_no'] ?? 'N/A' }}</td>
                                </tr>
                            @elseif(empty($entityType) && $statusShortCode == 'existing_occupant' && $flatDetails)
                                <tr>
                                    <th style="background-color:#00000000">Possession Date</th>
                                    <td>{{ !empty($flatDetails['possession_date']) ? $formatDate($flatDetails['possession_date']) : 'Not Available' }}</td>
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
                                    <th style="background-color:#00000000">Flat No.</th>
                                    <td>{{ $flatDetails['flat_no'] ?? 'N/A' }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

