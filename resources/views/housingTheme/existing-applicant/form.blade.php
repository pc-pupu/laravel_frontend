@php
    $applicant = $applicant ?? [];
    $isEdit = isset($applicant['online_application_id']) || isset($applicant['housing_applicant_id']);
    $dob = $applicant['dob'] ?? ($applicant['date_of_birth'] ?? old('dob', ''));
    $doj = $applicant['doj'] ?? ($applicant['date_of_joining'] ?? old('doj', ''));
    $dor = $applicant['dor'] ?? ($applicant['date_of_retirement'] ?? old('dor', ''));
    $doa = $applicant['doa'] ?? ($applicant['date_of_application'] ?? old('doa', ''));
    $payBandType = 'new';
    if (isset($applicant['pay_band_id'])) {
        $payBandType = $applicant['pay_band_id'] <= 5 ? 'new' : 'old';
    }
@endphp

{{-- Hidden fields for update --}}
@if($isEdit)
    <input type="hidden" name="app_uid" value="{{ $applicant['uid'] ?? '' }}">
    <input type="hidden" name="housing_applicant_id" value="{{ $applicant['housing_applicant_id'] ?? '' }}">
    <input type="hidden" name="housing_official_detail_id" value="{{ $applicant['applicant_official_detail_id'] ?? '' }}">
    <input type="hidden" name="housing_online_application_id" value="{{ $applicant['online_application_id'] ?? '' }}">
@endif

{{-- Personal Information --}}
@include('commonForm.personal-info', ['data' => $applicant])

{{-- Permanent Address --}}
@include('commonForm.address-fields', ['data' => $applicant, 'addressType' => 'permanent'])

{{-- Present Address --}}
@include('commonForm.address-fields', ['data' => $applicant, 'addressType' => 'present'])

{{-- Official Information Section --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-briefcase me-2"></i> Applicant's Official Information</h5>
    
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="hrms_id" name="hrms_id" 
                    value="{{ $applicant['hrms_id'] ?? old('hrms_id', '') }}" placeholder="HRMS ID" maxlength="10">
                <label for="hrms_id">Employee HRMS ID</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="app_designation" name="app_designation" 
                    value="{{ $applicant['applicant_designation'] ?? old('app_designation', '') }}" 
                    placeholder="Designation" required oninput="this.value=this.value.toUpperCase()">
                <label for="app_designation" class="required">Designation</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="app_posting_place" name="app_posting_place" 
                    value="{{ $applicant['applicant_posting_place'] ?? old('app_posting_place', 'NA') }}" 
                    placeholder="Place of Posting" required oninput="this.value=this.value.toUpperCase()">
                <label for="app_posting_place" class="required">Place of Posting</label>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="required mb-2 d-block">Pay Band Type</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="pay_band_type" id="pay_band_type_old" value="old" required
                    {{ old('pay_band_type', $payBandType) == 'old' ? 'checked' : '' }}>
                <label class="form-check-label" for="pay_band_type_old">Old</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="pay_band_type" id="pay_band_type_new" value="new" required
                    {{ old('pay_band_type', $payBandType) == 'new' ? 'checked' : '' }}>
                <label class="form-check-label" for="pay_band_type_new">New</label>
            </div>
        </div>
        <div class="col-md-4" id="pay-band-wrapper">
            <div class="form-floating">
                @php
                    $currentPayBand = $applicant['pay_band_id'] ?? old('pay_band', '');
                @endphp
                <select class="form-select" id="pay_band" name="pay_band" required>
                    <option value="" {{ empty($currentPayBand) ? 'selected' : '' }}>- Select -</option>
                </select>
                <label for="pay_band" class="required">Basic Pay Range</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="pay_in" name="pay_in" 
                    value="{{ $applicant['pay_in_the_pay_band'] ?? old('pay_in', '') }}" 
                    placeholder="Basic Pay" required>
                <label for="pay_in" class="required">Basic Pay</label>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="app_headquarter" name="app_headquarter" 
                    value="{{ $applicant['applicant_headquarter'] ?? old('app_headquarter', '') }}" 
                    placeholder="Headquarter" oninput="this.value=this.value.toUpperCase()">
                <label for="app_headquarter">Headquarter</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="doj" name="doj" 
                    value="{{ $doj }}" placeholder="DD/MM/YYYY" autocomplete="off" maxlength="10"
                    {{ $isEdit && isset($applicant['doj']) ? 'readonly' : '' }}>
                <label for="doj">Date of Joining</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="dor" name="dor" 
                    value="{{ $dor }}" placeholder="DD/MM/YYYY" required autocomplete="off" maxlength="10"
                    {{ $isEdit && isset($applicant['dor']) ? 'readonly' : '' }}>
                <label for="dor" class="required">Date of Retirement (DD/MM/YYYY)</label>
            </div>
        </div>
    </div>
</div>

{{-- Office Address Section --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-building me-2"></i> Name and Address of the Office</h5>
    
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_name" name="office_name" 
                    value="{{ $applicant['office_name'] ?? old('office_name', '') }}" 
                    placeholder="Office Name" required oninput="this.value=this.value.toUpperCase()">
                <label for="office_name" class="required">Name of the Office</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_street" name="office_street" 
                    value="{{ $applicant['office_street'] ?? old('office_street', '') }}" 
                    placeholder="Address" required oninput="this.value=this.value.toUpperCase()">
                <label for="office_street" class="required">Address</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_city" name="office_city" 
                    value="{{ $applicant['office_city_town_village'] ?? $applicant['office_city'] ?? old('office_city', '') }}" 
                    placeholder="City/Town/Village" required oninput="this.value=this.value.toUpperCase()">
                <label for="office_city" class="required">City / Town / Village</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_post_office" name="office_post_office" 
                    value="{{ $applicant['office_post_office'] ?? old('office_post_office', '') }}" 
                    placeholder="Post Office" oninput="this.value=this.value.toUpperCase()">
                <label for="office_post_office">Post Office</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                @php
                    $currentOfficeDistrict = $applicant['office_district'] ?? $applicant['office_district_code'] ?? old('office_district', '');
                @endphp
                <select class="form-select" id="office_district" name="office_district" required>
                    <option value="" {{ empty($currentOfficeDistrict) ? 'selected' : '' }}>- Select -</option>
                </select>
                <label for="office_district" class="required">District</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="office_pincode" name="office_pincode" 
                    value="{{ $applicant['office_pin_code'] ?? $applicant['office_pincode'] ?? old('office_pincode', '') }}" 
                    placeholder="Pincode" required maxlength="6">
                <label for="office_pincode" class="required">Pincode</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="office_phone_no" name="office_phone_no" 
                    value="{{ $applicant['office_phone_no'] ?? old('office_phone_no', '') }}" 
                    placeholder="Phone No" maxlength="15">
                <label for="office_phone_no">Phone No. (With STD Code)</label>
            </div>
        </div>
    </div>
</div>

{{-- DDO Details Section --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-id-card me-2"></i> DDO with full address</h5>
    
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="form-floating">
                @php
                    $ddoDistrictName = $isEdit ? 'district' : 'ddo_district';
                    $currentDdoDistrict = $applicant['ddo_district'] ?? $applicant['ddo_district_code'] ?? old($ddoDistrictName, '');
                @endphp
                <select class="form-select" id="{{ $ddoDistrictName }}" name="{{ $ddoDistrictName }}">
                    <option value="" {{ empty($currentDdoDistrict) ? 'selected' : '' }}>- Select -</option>
                </select>
                <label for="{{ $ddoDistrictName }}">DDO District</label>
            </div>
        </div>
        <div class="col-md-6" id="replace_designation">
            <div class="form-floating">
                @php
                    $currentDdoDesignation = $applicant['ddo_id'] ?? old('designation', '');
                @endphp
                <select class="form-select" id="designation" name="designation">
                    <option value="" {{ empty($currentDdoDesignation) ? 'selected' : '' }}>- Select -</option>
                </select>
                <label for="designation">DDO Designation</label>
            </div>
        </div>
        <div class="col-md-12" id="replace_ddo_address" style="display: none;">
            <div class="form-floating">
                <textarea class="form-control" id="ddo_address" name="ddo_address" 
                    placeholder="DDO Address" readonly style="height: 100px">{{ $applicant['ddo_address'] ?? old('ddo_address', '') }}</textarea>
                <label for="ddo_address">DDO Address</label>
            </div>
        </div>
    </div>
</div>

{{-- Allotment Category Section --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-file-alt me-2"></i> Allotment Category</h5>
    
    <div class="row g-3 mb-3">
        <div class="col-md-6" id="replace_rhe_flat_type">
            <div class="form-floating">
                <input type="text" class="form-control" id="rhe_flat_type" name="rhe_flat_type" 
                    value="{{ $applicant['rhe_flat_type'] ?? old('rhe_flat_type', '') }}" 
                    placeholder="Flat Type" required readonly>
                <label for="rhe_flat_type" class="required">Flat TYPE</label>
            </div>
        </div>
        <div class="col-md-6" id="replace_allotment_category">
            <div class="form-floating">
                @php
                    $currentReason = $applicant['reason'] ?? $applicant['allotment_reason_id'] ?? old('reason', '');
                @endphp
                <select class="form-select" id="reason" name="reason" required>
                    <option value="" {{ empty($currentReason) ? 'selected' : '' }}>- Select -</option>
                </select>
                <label for="reason" class="required">Allotment Category</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="doa" name="doa" 
                    value="{{ $doa }}" placeholder="DD/MM/YYYY" required autocomplete="off" maxlength="10"
                    {{ $isEdit && isset($applicant['doa']) ? 'readonly' : '' }}>
                <label for="doa" class="required">Date of Application</label>
            </div>
        </div>
        <div class="col-md-4" id="computr_serial_no">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="computer_serial_no" name="computer_serial_no" 
                    value="{{ $applicant['computer_serial_no'] ?? old('computer_serial_no', '') }}" 
                    placeholder="Computer Serial No" required {{ $isEdit ? 'readonly' : '' }}>
                <label for="computer_serial_no" class="required">Computer Serial No.</label>
            </div>
        </div>
        @if(!$isEdit)
        <div class="col-md-4" id="confirm_computr_serial_no">
            <div class="form-floating">
                <input type="password" class="form-control numeric_positive" id="confirm_computer_serial_no" name="confirm_computer_serial_no" 
                    placeholder="Confirm Computer Serial No" required>
                <label for="confirm_computer_serial_no" class="required">Confirm Computer Serial No.</label>
            </div>
        </div>
        @else
        <input type="hidden" name="confirm_computer_serial_no" value="{{ $applicant['computer_serial_no'] ?? '' }}">
        @endif
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="physical_application_no" name="physical_application_no" 
                    value="{{ $applicant['physical_application_no'] ?? old('physical_application_no', '') }}" 
                    placeholder="Physical Application No" required>
                <label for="physical_application_no" class="required">Physical Application Number</label>
            </div>
        </div>
        <div class="col-md-12">
            <div id="comp_ser_no_message" class="text-danger"></div>
        </div>
    </div>
</div>

{{-- Document Upload Section --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-upload me-2"></i> Document Upload</h5>
    
    <div class="row g-3">
        <div class="col-md-12" id="extra_doc">
            <div class="form-floating">
                <input type="file" class="form-control" id="extra_doc" name="extra_doc" accept=".pdf">
                <label for="extra_doc">Upload Allotment Reason Supporting Document</label>
            </div>
            <small class="text-muted"><b>Allowed Extension: pdf<br>Maximum File Size: 1 MB</b></small>
        </div>
    </div>
</div>

{{-- Remarks Section --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-comment me-2"></i> Remarks</h5>
    
    <div class="row g-3">
        <div class="col-md-12">
            <div class="form-floating">
                <textarea class="form-control" id="remarks" name="remarks" 
                    placeholder="Remarks" style="height: 100px">{{ $applicant['remarks'] ?? old('remarks', '') }}</textarea>
                <label for="remarks">Remarks</label>
            </div>
        </div>
    </div>
</div>

