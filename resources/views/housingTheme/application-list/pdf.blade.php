<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Application Details - {{ $application['application_no'] ?? 'N/A' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #473a39;
            padding-bottom: 10px;
        }
        .header h2 {
            color: #473a39;
            margin: 0;
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #473a39;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: normal;
            font-size: 14px;
        }
        table td, table th {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .section-header {
            background-color: #473a39 !important;
            color: white !important;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }
        .label {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 40%;
        }
        .value {
            width: 60%;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
@php
    use Illuminate\Support\Facades\DB;
    
    $app = $application;
    $applicantData = $application['applicant_personal_info'] ?? null;
    $preferences = $application['estate_preferences'] ?? [];
    $statusDesc = $application['status_description'] ?? null;
    $flatDetails = $application['allotment_flat_details'] ?? null;
    
    // Determine entity type
    $entityType = '';
    if (isset($app['application_type'])) {
        switch($app['application_type']) {
            case 'new-apply':
                $entityType = 'New Allotment';
                break;
            case 'vs':
                $entityType = 'Vertical Shifting';
                break;
            case 'cs':
                $entityType = 'Category Shifting';
                break;
            case 'license':
                $entityType = 'New Licence';
                break;
            default:
                $entityType = ucfirst($app['application_type'] ?? 'Application');
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
@endphp

<div class="header">
    <h2>Application Details</h2>
</div>

<table>
    <tr>
        <th colspan="2" class="section-header">Application Status</th>
    </tr>
    <tr>
        <td class="label">Application Type</td>
        <td class="value">
            @if(empty($entityType) && $statusShortCode == 'existing_occupant')
                Application for Existing Occupant
            @else
                Application for {{ $entityType }}
            @endif
        </td>
    </tr>
    <tr>
        <td class="label">Application No.</td>
        <td class="value">{{ $app['application_no'] ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Date of Application</td>
        <td class="value">{{ $formatDate($app['date_of_application'] ?? '') }}</td>
    </tr>
    <tr>
        <td class="label">Application Status</td>
        <td class="value">{{ $statusDesc['status_description'] ?? 'N/A' }}</td>
    </tr>

    <tr>
        <th colspan="2" class="section-header">Applicant Personal Information(According to Service Book)</th>
    </tr>
    <tr>
        <td class="label">Applicant's Name</td>
        <td class="value">{{ !empty($applicantData['applicant_name']) ? $applicantData['applicant_name'] : 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Father's / Husband's Name</td>
        <td class="value">{{ !empty($applicantData['guardian_name']) ? $applicantData['guardian_name'] : 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Permanent Address</td>
        <td class="value">{{ $permanentAddress }}</td>
    </tr>
    <tr>
        <td class="label">Present Address</td>
        <td class="value">{{ $presentAddress }}</td>
    </tr>
    <tr>
        <td class="label">Gender</td>
        <td class="value">{{ ($applicantData['gender'] ?? '') == 'M' ? 'Male' : 'Female' }}</td>
    </tr>
    <tr>
        <td class="label">Date of Birth(According to Service Book)</td>
        <td class="value">{{ $formatDate($applicantData['date_of_birth'] ?? '') }}</td>
    </tr>
    <tr>
        <td class="label">Mobile No</td>
        <td class="value">{{ !empty($applicantData['mobile_no']) ? $applicantData['mobile_no'] : 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Email ID</td>
        <td class="value">{{ $applicantData['email'] ?? 'N/A' }}</td>
    </tr>

    <tr>
        <th colspan="2" class="section-header">Applicant Official Information</th>
    </tr>
    <tr>
        <td class="label">Employee HRMS ID</td>
        <td class="value">{{ $app['hrms_id'] ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Designation</td>
        <td class="value">{{ !empty($app['applicant_designation']) ? $app['applicant_designation'] : 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Office Headquarter</td>
        <td class="value">{{ !empty($app['applicant_headquarter']) ? $app['applicant_headquarter'] : 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Place of Posting</td>
        <td class="value">{{ !empty($app['applicant_posting_place']) ? $app['applicant_posting_place'] : 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Pay Band</td>
        <td class="value">{{ !empty($payBandStr) ? $payBandStr : 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Basic Pay</td>
        <td class="value">{{ !empty($app['pay_in_the_pay_band']) ? $app['pay_in_the_pay_band'] : 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Date of Joining</td>
        <td class="value">{{ $formatDate($app['date_of_joining'] ?? '') }}</td>
    </tr>
    <tr>
        <td class="label">Date of Retirement(According to Service Book)</td>
        <td class="value">{{ $formatDate($app['date_of_retirement'] ?? '') }}</td>
    </tr>

    <tr>
        <th colspan="2" class="section-header">Applicant Office Name and Address</th>
    </tr>
    <tr>
        <td class="label">Name of the Office</td>
        <td class="value">{{ $app['office_name'] ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">Office Address</td>
        <td class="value">{{ $officeAddress }}</td>
    </tr>
    <tr>
        <td class="label">Office Phone No.</td>
        <td class="value">{{ $app['office_phone_no'] ?? 'N/A' }}</td>
    </tr>

    <tr>
        <th colspan="2" class="section-header">Applicant DDO Information</th>
    </tr>
    <tr>
        <td class="label">DDO District</td>
        <td class="value">{{ $app['district_name'] ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">DDO Designation</td>
        <td class="value">{{ $app['ddo_designation'] ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="label">DDO Address</td>
        <td class="value">{{ $app['ddo_address'] ?? 'N/A' }}</td>
    </tr>

    @if(!empty($preferences))
    <tr>
        <th colspan="2" class="section-header">Applicant's Housing Estate Preference</th>
    </tr>
    @foreach($preferences as $index => $pref)
        @php
            $prefLabel = ['First', 'Second', 'Third', 'Fourth', 'Fifth'][$index] ?? 'Preference';
        @endphp
        <tr>
            <td class="label">{{ $prefLabel }} Preference</td>
            <td class="value">{{ $pref['estate_name'] ?? 'N/A' }}</td>
        </tr>
    @endforeach
    @endif

    <tr>
        <th colspan="2" class="section-header">{{ $heading }}</th>
    </tr>

    @if($entityType == 'New Allotment')
        @if($statusShortCode == 'existing_occupant' && $flatDetails)
            <tr>
                <td class="label">Possession Date</td>
                <td class="value">{{ !empty($flatDetails['possession_date']) ? $formatDate($flatDetails['possession_date']) : 'Not Available' }}</td>
            </tr>
            <tr>
                <td class="label">Estate Name</td>
                <td class="value">{{ $flatDetails['estate_name'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Block Name</td>
                <td class="value">{{ $flatDetails['block_name'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Flat No.</td>
                <td class="value">{{ $flatDetails['flat_no'] ?? 'N/A' }}</td>
            </tr>
        @else
            <tr>
                <td class="label">Allotment Category</td>
                <td class="value">{{ $app['na_allotment_category'] ?? $app['allotment_category'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label">Flat Type</td>
                <td class="value">{{ $app['flat_type'] ?? 'N/A' }}</td>
            </tr>
        @endif
    @elseif(in_array($entityType, ['Vertical Shifting', 'Category Shifting']) && $flatDetails)
        <tr>
            <td class="label">Possession Date</td>
            <td class="value">{{ $formatDate($flatDetails['possession_date'] ?? '') }}</td>
        </tr>
        <tr>
            <td class="label">Estate Name</td>
            <td class="value">{{ $flatDetails['estate_name'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Block Name</td>
            <td class="value">{{ $flatDetails['block_name'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Flat No.</td>
            <td class="value">{{ $flatDetails['flat_no'] ?? 'N/A' }}</td>
        </tr>
    @elseif(empty($entityType) && $statusShortCode == 'existing_occupant' && $flatDetails)
        <tr>
            <td class="label">Possession Date</td>
            <td class="value">{{ !empty($flatDetails['possession_date']) ? $formatDate($flatDetails['possession_date']) : 'Not Available' }}</td>
        </tr>
        <tr>
            <td class="label">Estate Name</td>
            <td class="value">{{ $flatDetails['estate_name'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Block Name</td>
            <td class="value">{{ $flatDetails['block_name'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Flat No.</td>
            <td class="value">{{ $flatDetails['flat_no'] ?? 'N/A' }}</td>
        </tr>
    @endif
</table>

<div class="footer">
    <p>Generated on: {{ date('d/m/Y H:i:s') }}</p>
    <p>This is a system generated document.</p>
</div>

</body>
</html>

