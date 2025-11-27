{{-- Application Type Information --}}
<div class="form-section">
    <h5><i class="fa fa-file-alt me-2"></i> Application Type Information</h5>
    
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <select class="form-select" id="application_type" name="application_type" required>
                    <option value="">- Select -</option>
                    <option value="VS">Floor Shifting</option>
                    <option value="CS">Category Shifting</option>
                </select>
                <label for="application_type" class="required">Application Type</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="application_date" name="application_date" 
                    placeholder="DD/MM/YYYY" required autocomplete="off">
                <label for="application_date" class="required">Date of Application</label>
            </div>
        </div>
    </div>
</div>

{{-- Personal Information --}}
<div class="form-section">
    <h5><i class="fa fa-user me-2"></i> Personal Information (According to Service Book)</h5>
    
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="applicant_name" name="applicant_name" 
                    placeholder="Applicant Name" value="{{ $applicantData['applicant_name'] ?? old('applicant_name') }}" required oninput="this.value=this.value.toUpperCase()">
                <label for="applicant_name" class="required">Applicant's Name</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="applicant_father_name" name="applicant_father_name" 
                    placeholder="Father/Husband Name" value="{{ $applicantData['guardian_name'] ?? old('applicant_father_name', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="applicant_father_name">Father / Husband Name</label>
            </div>
        </div>
    </div>

    {{-- Permanent Address --}}
    <h6 class="mt-4 mb-3"><i class="fa fa-map-marker-alt me-2"></i> Permanent Address</h6>
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="permanent_street" name="permanent_street" 
                    placeholder="Address" value="{{ old('permanent_street', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="permanent_street">Address</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="permanent_city_town_village" name="permanent_city_town_village" 
                    placeholder="City/Town/Village" value="{{ old('permanent_city_town_village', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="permanent_city_town_village">City / Town / Village</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="permanent_post_office" name="permanent_post_office" 
                    placeholder="Post Office" value="{{ old('permanent_post_office', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="permanent_post_office">Post Office</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <select class="form-select" id="permanent_district" name="permanent_district">
                    <option value="">- Select -</option>
                </select>
                <label for="permanent_district">District</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="permanent_pincode" name="permanent_pincode" 
                    placeholder="Pincode" maxlength="6" value="{{ old('permanent_pincode', '700001') }}">
                <label for="permanent_pincode">Pincode</label>
            </div>
        </div>
    </div>

    {{-- Present Address --}}
    <h6 class="mt-4 mb-3"><i class="fa fa-map-marker-alt me-2"></i> Present Address</h6>
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="present_street" name="present_street" 
                    placeholder="Address" value="{{ old('present_street', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="present_street">Address</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="present_city_town_village" name="present_city_town_village" 
                    placeholder="City/Town/Village" value="{{ old('present_city_town_village', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="present_city_town_village">City / Town / Village</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="present_post_office" name="present_post_office" 
                    placeholder="Post Office" value="{{ old('present_post_office', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="present_post_office">Post Office</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <select class="form-select" id="present_district" name="present_district">
                    <option value="">- Select -</option>
                </select>
                <label for="present_district">District</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="present_pincode" name="present_pincode" 
                    placeholder="Pincode" maxlength="6" value="{{ old('present_pincode', '700001') }}">
                <label for="present_pincode">Pincode</label>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="mobile" name="mobile" 
                    placeholder="Mobile No" maxlength="10" value="{{ old('mobile', '9999999999') }}">
                <label for="mobile">Mobile No</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="email" name="email" 
                    placeholder="Email ID" value="{{ old('email') }}" oninput="this.value=this.value.toLowerCase()">
                <label for="email">Email ID</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="dob" name="dob" 
                    placeholder="DD/MM/YYYY" value="{{ old('dob', $applicantData['dob'] ?? '01/01/1945') }}" autocomplete="off">
                <label for="dob">Date of Birth (According to Service Book)</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label required">Gender</label>
                <div class="d-flex">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="gender_m" value="M" required {{ old('gender', 'M') == 'M' ? 'checked' : '' }}>
                        <label class="form-check-label" for="gender_m">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="gender_f" value="F" required {{ old('gender') == 'F' ? 'checked' : '' }}>
                        <label class="form-check-label" for="gender_f">Female</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="housing_applicant_id" id="housing_applicant_id" value="{{ $applicantData['housing_applicant_id'] ?? '' }}">
    <input type="hidden" name="housing_hrms_id" id="housing_hrms_id" value="{{ $applicantData['hrms_id'] ?? '' }}">
    <input type="hidden" name="housing_hidden_uid" id="housing_hidden_uid" value="{{ $applicantData['uid'] ?? '' }}">
</div>

{{-- Official Information --}}
<div class="form-section">
    <h5><i class="fa fa-briefcase me-2"></i> Applicant's Official Information</h5>
    
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="hrms_id" name="hrms_id" 
                    placeholder="Employee HRMS ID" maxlength="10" value="{{ $applicantData['hrms_id'] ?? old('hrms_id') }}" 
                    {{ !empty($applicantData['hrms_id']) ? 'readonly' : '' }}>
                <label for="hrms_id">Employee HRMS ID</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="app_designation" name="app_designation" 
                    placeholder="Designation" value="{{ old('app_designation', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
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
                        <input class="form-check-input" type="radio" name="pay_type_new" id="pay_type_new_new" value="new" {{ old('pay_type_new', 'old') == 'new' ? 'checked' : '' }}>
                        <label class="form-check-label" for="pay_type_new_new">New</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="pay_type_new" id="pay_type_new_old" value="old" {{ old('pay_type_new', 'old') == 'old' ? 'checked' : '' }}>
                        <label class="form-check-label" for="pay_type_new_old">Old</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" id="pay_band_replace_div">
            <div class="form-floating">
                <select class="form-select" id="pay_band" name="pay_band" required>
                    <option value="">- Select Pay Band First -</option>
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
                    placeholder="Basic Pay" value="{{ old('pay_in', '1') }}">
                <label for="pay_in">Basic Pay</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="app_posting_place" name="app_posting_place" 
                    placeholder="Place of Posting" value="{{ old('app_posting_place', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="app_posting_place">Place of Posting</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="app_headquarter" name="app_headquarter" 
                    placeholder="Headquarter" value="{{ old('app_headquarter', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="app_headquarter">Headquarter</label>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="doj" name="doj" 
                    placeholder="DD/MM/YYYY" value="{{ old('doj', $applicantData['doj'] ?? '01/01/1945') }}" autocomplete="off">
                <label for="doj">Date of Joining</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="dor" name="dor" 
                    placeholder="DD/MM/YYYY" value="{{ old('dor') }}" required autocomplete="off">
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
                    placeholder="Name of the Office" value="{{ old('office_name', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="office_name">Name of the Office</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_street" name="office_street" 
                    placeholder="Address" value="{{ old('office_street', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="office_street">Address</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_city" name="office_city" 
                    placeholder="City/Town/Village" value="{{ old('office_city', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="office_city">City / Town / Village</label>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_post_office" name="office_post_office" 
                    placeholder="Post Office" value="{{ old('office_post_office', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="office_post_office">Post Office</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <select class="form-select" id="office_district" name="office_district">
                    <option value="">- Select -</option>
                </select>
                <label for="office_district">District</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="office_pincode" name="office_pincode" 
                    placeholder="Pincode" maxlength="6" value="{{ old('office_pincode', '700001') }}">
                <label for="office_pincode">Pincode</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="office_phone_no" name="office_phone_no" 
                    placeholder="Phone No" maxlength="15" value="{{ old('office_phone_no', '033-22222222') }}">
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
                <select class="form-select" id="district" name="district">
                    <option value="">- Select DDO District -</option>
                </select>
                <label for="district">DDO District</label>
            </div>
        </div>
        <div class="col-md-6" id="replace_designation">
            <div class="form-floating">
                <select class="form-select" id="designation" name="designation">
                    <option value="">- Select DDO Designation -</option>
                </select>
                <label for="designation">DDO Designation</label>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-12" id="replace_ddo_address">
            <div class="form-floating">
                <textarea class="form-control" id="ddo_address" name="ddo_address" 
                    placeholder="DDO Address" readonly style="height: 100px;">{{ old('ddo_address', $applicantData['ddo_address'] ?? '') }}</textarea>
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
                <select class="form-select" id="occupation_estate" name="occupation_estate" required>
                    <option value="">- Select Housing -</option>
                </select>
                <label for="occupation_estate" class="required">Select Housing</label>
            </div>
        </div>
        <div class="col-md-6" id="block_replace">
            <div class="form-floating">
                <select class="form-select" id="occupation_block" name="occupation_block" required>
                    <option value="">- Select Block -</option>
                </select>
                <label for="occupation_block" class="required">Select Block</label>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6" id="flat_no_replace">
            <div class="form-floating">
                <select class="form-select" id="occupation_flat" name="occupation_flat" required>
                    <option value="">- Select Flat No -</option>
                </select>
                <label for="occupation_flat" class="required">Flat No</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="possession_date" name="possession_date" 
                    placeholder="DD/MM/YYYY" required readonly>
                <label for="possession_date" class="required">Date of Possession</label>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="license_no" name="license_no" 
                    placeholder="License Number" value="{{ old('license_no', 'NA') }}" oninput="this.value=this.value.toUpperCase()">
                <label for="license_no">License Number</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="physical_application_no" name="physical_application_no" 
                    placeholder="Physical Application Number" value="{{ old('physical_application_no', 'NA') }}">
                <label for="physical_application_no">Physical Application Number</label>
            </div>
        </div>
    </div>
</div>

