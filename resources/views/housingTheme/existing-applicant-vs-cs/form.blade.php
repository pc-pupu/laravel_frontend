@php
    // Normalize data - handle both create ($applicantData) and edit ($application) formats
    $data = $application ?? $applicantData ?? [];
    $isEdit = isset($application) || (isset($applicantData) && isset($applicantData['online_application_id']));
    
    // Normalize field names
    $normalizedData = [
        'applicant_name' => $data['applicant_name'] ?? old('applicant_name', ''),
        'guardian_name' => $data['guardian_name'] ?? old('applicant_father_name', 'NA'),
        'mobile_no' => $data['mobile_no'] ?? $data['mobile'] ?? old('mobile', '9999999999'),
        'email' => $data['email'] ?? old('email', ''),
        'dob' => $data['dob'] ?? $data['date_of_birth'] ?? old('dob', $isEdit ? '01/01/1900' : '01/01/1945'),
        'gender' => $data['gender'] ?? old('gender', 'M'),
        'hrms_id' => $data['hrms_id'] ?? old('hrms_id', ''),
        'applicant_designation' => $data['applicant_designation'] ?? $data['app_designation'] ?? old('app_designation', 'NA'),
        'applicant_posting_place' => $data['applicant_posting_place'] ?? old('app_posting_place', 'NA'),
        'applicant_headquarter' => $data['applicant_headquarter'] ?? old('app_headquarter', 'NA'),
        'doj' => $data['doj'] ?? $data['date_of_joining'] ?? old('doj', $isEdit ? '01/01/1900' : '01/01/1945'),
        'dor' => $data['dor'] ?? $data['date_of_retirement'] ?? old('dor', ''),
        'office_name' => $data['office_name'] ?? old('office_name', 'NA'),
        'office_street' => $data['office_street'] ?? old('office_street', 'NA'),
        'office_city_town_village' => $data['office_city_town_village'] ?? $data['office_city'] ?? old('office_city', 'NA'),
        'office_post_office' => $data['office_post_office'] ?? old('office_post_office', 'NA'),
        'office_pin_code' => $data['office_pin_code'] ?? $data['office_pincode'] ?? old('office_pincode', '700001'),
        'office_phone_no' => $data['office_phone_no'] ?? old('office_phone_no', '033-22222222'),
        'ddo_address' => $data['ddo_address'] ?? old('ddo_address', ''),
        'permanent_street' => $data['permanent_street'] ?? old('permanent_street', 'NA'),
        'permanent_city_town_village' => $data['permanent_city_town_village'] ?? old('permanent_city_town_village', 'NA'),
        'permanent_post_office' => $data['permanent_post_office'] ?? old('permanent_post_office', 'NA'),
        'permanent_pincode' => $data['permanent_pincode'] ?? old('permanent_pincode', '700001'),
        'present_street' => $data['present_street'] ?? old('present_street', 'NA'),
        'present_city_town_village' => $data['present_city_town_village'] ?? old('present_city_town_village', 'NA'),
        'present_post_office' => $data['present_post_office'] ?? old('present_post_office', 'NA'),
        'present_pincode' => $data['present_pincode'] ?? old('present_pincode', '700001'),
        'license_no' => $data['license_no'] ?? old('license_no', 'NA'),
        'physical_application_no' => $data['physical_application_no'] ?? old('physical_application_no', 'NA'),
        'possession_date' => $data['possession_date'] ?? old('possession_date', ''),
        'housing_applicant_id' => $data['housing_applicant_id'] ?? '',
        'uid' => $data['uid'] ?? '',
    ];
    
    // Determine application type from application_no if editing
    $applicationType = '';
    if ($isEdit && isset($data['application_no'])) {
        $applicationType = (strpos($data['application_no'], 'VS') === 0) ? 'VS' : 'CS';
    }
    $applicationType = old('application_type', $applicationType);
    
    $applicationDate = $data['date_of_application'] ?? old('application_date', '');
    
    // Pay band type
    $payBandType = 'old';
    if (isset($data['pay_band_id']) && $data['pay_band_id'] <= 5) {
        $payBandType = 'new';
    }
    $payBandType = old('pay_type_new', $payBandType);
@endphp

{{-- Application Type Information --}}
<div class="form-section">
    <h5><i class="fa fa-file-alt me-2"></i> Application Type Information</h5>
    
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <select class="form-select" id="application_type" name="application_type" required>
                    <option value="" {{ empty($applicationType) ? 'selected' : '' }}>- Select -</option>
                    <option value="VS" {{ $applicationType == 'VS' ? 'selected' : '' }}>Floor Shifting</option>
                    <option value="CS" {{ $applicationType == 'CS' ? 'selected' : '' }}>Category Shifting</option>
                </select>
                <label for="application_type" class="required">Application Type</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="application_date" name="application_date" 
                    placeholder="DD/MM/YYYY" value="{{ $applicationDate }}" required autocomplete="off"
                    {{ $isEdit ? 'readonly' : '' }}>
                <label for="application_date" class="required">Date of Application</label>
            </div>
        </div>
    </div>
</div>

{{-- Personal Information --}}
@include('commonForm.personal-info', ['data' => $normalizedData])

{{-- Permanent Address --}}
@include('commonForm.address-fields', ['data' => $normalizedData, 'addressType' => 'permanent'])

{{-- Present Address --}}
@include('commonForm.address-fields', ['data' => $normalizedData, 'addressType' => 'present'])

{{-- Hidden fields --}}
<input type="hidden" name="housing_applicant_id" id="housing_applicant_id" value="{{ $normalizedData['housing_applicant_id'] }}">
<input type="hidden" name="housing_hrms_id" id="housing_hrms_id" value="{{ $normalizedData['hrms_id'] }}">
<input type="hidden" name="housing_hidden_uid" id="housing_hidden_uid" value="{{ $normalizedData['uid'] }}">
@if($isEdit)
    <input type="hidden" name="housing_hidden_uid_or_draft_id" value="{{ $normalizedData['uid'] }}">
@endif

{{-- Official Information --}}
<div class="form-section">
    <h5><i class="fa fa-briefcase me-2"></i> Applicant's Official Information</h5>
    
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="hrms_id" name="hrms_id" 
                    placeholder="Employee HRMS ID" maxlength="10" value="{{ $normalizedData['hrms_id'] }}" 
                    {{ !empty($normalizedData['hrms_id']) ? 'readonly' : '' }}>
                <label for="hrms_id">Employee HRMS ID</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="app_designation" name="app_designation" 
                    placeholder="Designation" value="{{ $normalizedData['applicant_designation'] }}" 
                    oninput="this.value=this.value.toUpperCase()">
                <label for="app_designation">Designation</label>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label required">Select Pay Type</label>
                <div class="d-flex">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="pay_type_new" id="pay_type_new_new" value="new" 
                            {{ $payBandType == 'new' ? 'checked' : '' }}>
                        <label class="form-check-label" for="pay_type_new_new">New</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="pay_type_new" id="pay_type_new_old" value="old" 
                            {{ $payBandType == 'old' ? 'checked' : '' }}>
                        <label class="form-check-label" for="pay_type_new_old">Old</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" id="pay_band_replace_div">
            <div class="form-floating">
                @php
                    $currentPayBand = $data['pay_band_id'] ?? old('pay_band', '');
                @endphp
                <select class="form-select" id="pay_band" name="pay_band" required>
                    <option value="" {{ empty($currentPayBand) ? 'selected' : '' }}>- Select Pay Band First -</option>
                </select>
                <label for="pay_band" class="required">Pay Band</label>
            </div>
        </div>
        <div class="col-md-4" id="flat_type_display_div">
            <div class="form-floating">
                <input type="text" class="form-control" id="flat_type_display" name="flat_type_display" 
                    placeholder="Flat Type" readonly required>
                <label for="flat_type_display" class="required">Flat Type</label>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="pay_in" name="pay_in" 
                    placeholder="Basic Pay" value="{{ $data['pay_in_the_pay_band'] ?? old('pay_in', '1') }}">
                <label for="pay_in">Basic Pay</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="app_posting_place" name="app_posting_place" 
                    placeholder="Place of Posting" value="{{ $normalizedData['applicant_posting_place'] }}" 
                    oninput="this.value=this.value.toUpperCase()">
                <label for="app_posting_place">Place of Posting</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="app_headquarter" name="app_headquarter" 
                    placeholder="Headquarter" value="{{ $normalizedData['applicant_headquarter'] }}" 
                    oninput="this.value=this.value.toUpperCase()">
                <label for="app_headquarter">Headquarter</label>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="doj" name="doj" 
                    placeholder="DD/MM/YYYY" value="{{ $normalizedData['doj'] }}" autocomplete="off"
                    {{ $isEdit ? 'readonly' : '' }}>
                <label for="doj">Date of Joining</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="dor" name="dor" 
                    placeholder="DD/MM/YYYY" value="{{ $normalizedData['dor'] }}" required autocomplete="off"
                    {{ $isEdit ? 'readonly' : '' }}>
                <label for="dor" class="required">Date of Retirement (According to Service Book)</label>
            </div>
        </div>
    </div>
</div>

{{-- Office Address --}}
<div class="form-section">
    <h5><i class="fa fa-building me-2"></i> Name and Address of the Office</h5>
    
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_name" name="office_name" 
                    placeholder="Name of the Office" value="{{ $normalizedData['office_name'] }}" 
                    oninput="this.value=this.value.toUpperCase()">
                <label for="office_name">Name of the Office</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_street" name="office_street" 
                    placeholder="Address" value="{{ $normalizedData['office_street'] }}" 
                    oninput="this.value=this.value.toUpperCase()">
                <label for="office_street">Address</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_city" name="office_city" 
                    placeholder="City/Town/Village" value="{{ $normalizedData['office_city_town_village'] }}" 
                    oninput="this.value=this.value.toUpperCase()">
                <label for="office_city">City / Town / Village</label>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_post_office" name="office_post_office" 
                    placeholder="Post Office" value="{{ $normalizedData['office_post_office'] }}" 
                    oninput="this.value=this.value.toUpperCase()">
                <label for="office_post_office">Post Office</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                @php
                    $currentOfficeDistrict = $data['office_district'] ?? $data['office_district_code'] ?? old('office_district', '');
                @endphp
                <select class="form-select" id="office_district" name="office_district">
                    <option value="" {{ empty($currentOfficeDistrict) ? 'selected' : '' }}>- Select -</option>
                </select>
                <label for="office_district">District</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="office_pincode" name="office_pincode" 
                    placeholder="Pincode" maxlength="6" value="{{ $normalizedData['office_pin_code'] }}">
                <label for="office_pincode">Pincode</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="office_phone_no" name="office_phone_no" 
                    placeholder="Phone No" maxlength="15" value="{{ $normalizedData['office_phone_no'] }}">
                <label for="office_phone_no">Phone No. (With STD Code)</label>
            </div>
        </div>
    </div>
</div>

{{-- DDO Details --}}
<div class="form-section">
    <h5><i class="fa fa-user-tie me-2"></i> DDO with full address</h5>
    
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="form-floating">
                @php
                    $currentDdoDistrict = $data['ddo_district'] ?? $data['ddo_district_code'] ?? old('district', '');
                @endphp
                <select class="form-select" id="district" name="district">
                    <option value="" {{ empty($currentDdoDistrict) ? 'selected' : '' }}>- Select DDO District -</option>
                </select>
                <label for="district">DDO District</label>
            </div>
        </div>
        <div class="col-md-6" id="replace_designation">
            <div class="form-floating">
                @php
                    $currentDdoDesignation = $data['ddo_id'] ?? old('designation', '');
                @endphp
                <select class="form-select" id="designation" name="designation">
                    <option value="" {{ empty($currentDdoDesignation) ? 'selected' : '' }}>- Select DDO Designation -</option>
                </select>
                <label for="designation">DDO Designation</label>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-12" id="replace_ddo_address">
            <div class="form-floating">
                <textarea class="form-control" id="ddo_address" name="ddo_address" 
                    placeholder="DDO Address" readonly style="height: 100px;">{{ $normalizedData['ddo_address'] }}</textarea>
                <label for="ddo_address">DDO Address</label>
            </div>
        </div>
    </div>
</div>

{{-- Current Occupancy Details --}}
<div class="form-section">
    <h5><i class="fa fa-home me-2"></i> Current Occupancy Details</h5>
    
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="form-floating">
                @php
                    $currentEstate = $data['occupation_estate'] ?? $data['estate_id'] ?? old('occupation_estate', '');
                @endphp
                <select class="form-select" id="occupation_estate" name="occupation_estate" required>
                    <option value="" {{ empty($currentEstate) ? 'selected' : '' }}>- Select Housing -</option>
                </select>
                <label for="occupation_estate" class="required">Select Housing</label>
            </div>
        </div>
        <div class="col-md-6" id="block_replace">
            <div class="form-floating">
                @php
                    $currentBlock = $data['occupation_block'] ?? $data['block_id'] ?? old('occupation_block', '');
                @endphp
                <select class="form-select" id="occupation_block" name="occupation_block" required>
                    <option value="" {{ empty($currentBlock) ? 'selected' : '' }}>- Select Block -</option>
                </select>
                <label for="occupation_block" class="required">Select Block</label>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6" id="flat_no_replace">
            <div class="form-floating">
                @php
                    $currentFlat = $data['occupation_flat'] ?? $data['flat_id'] ?? old('occupation_flat', '');
                @endphp
                <select class="form-select" id="occupation_flat" name="occupation_flat" required>
                    <option value="" {{ empty($currentFlat) ? 'selected' : '' }}>- Select Flat No -</option>
                </select>
                <label for="occupation_flat" class="required">Flat No</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="possession_date" name="possession_date" 
                    placeholder="DD/MM/YYYY" value="{{ $normalizedData['possession_date'] }}" required readonly>
                <label for="possession_date" class="required">Date of Possession</label>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="license_no" name="license_no" 
                    placeholder="License Number" value="{{ $normalizedData['license_no'] }}" 
                    oninput="this.value=this.value.toUpperCase()">
                <label for="license_no">License Number</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="physical_application_no" name="physical_application_no" 
                    placeholder="Physical Application Number" value="{{ $normalizedData['physical_application_no'] }}">
                <label for="physical_application_no">Physical Application Number</label>
            </div>
        </div>
    </div>
</div>

