{{-- Personal Information Section --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-user me-2"></i> Personal Information (According to Service Book)</h5>
    
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="applicant_name" name="applicant_name" 
                    placeholder="Applicant Name" required oninput="this.value=this.value.toUpperCase()">
                <label for="applicant_name" class="required">Applicant's Name</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="applicant_father_name" name="applicant_father_name" 
                    placeholder="Father/Husband Name" required oninput="this.value=this.value.toUpperCase()">
                <label for="applicant_father_name" class="required">Father / Husband Name</label>
            </div>
        </div>
    </div>

    {{-- Permanent Address --}}
    <h6 class="mt-4 mb-3"><i class="fa fa-map-marker-alt me-2"></i> Permanent Address</h6>
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="permanent_street" name="permanent_street" 
                    placeholder="Address" oninput="this.value=this.value.toUpperCase()">
                <label for="permanent_street">Address</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="permanent_city_town_village" name="permanent_city_town_village" 
                    placeholder="City/Town/Village" oninput="this.value=this.value.toUpperCase()">
                <label for="permanent_city_town_village">City / Town / Village</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="permanent_post_office" name="permanent_post_office" 
                    placeholder="Post Office" oninput="this.value=this.value.toUpperCase()">
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
                    placeholder="Pincode" maxlength="6">
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
                    placeholder="Address" oninput="this.value=this.value.toUpperCase()">
                <label for="present_street">Address</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="present_city_town_village" name="present_city_town_village" 
                    placeholder="City/Town/Village" oninput="this.value=this.value.toUpperCase()">
                <label for="present_city_town_village">City / Town / Village</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="present_post_office" name="present_post_office" 
                    placeholder="Post Office" oninput="this.value=this.value.toUpperCase()">
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
                    placeholder="Pincode" maxlength="6">
                <label for="present_pincode">Pincode</label>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="mobile" name="mobile" 
                    placeholder="Mobile No" maxlength="10">
                <label for="mobile">Mobile No</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="email" name="email" 
                    placeholder="Email ID" oninput="this.value=this.value.toLowerCase()">
                <label for="email">Email ID</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="dob" name="dob" 
                    placeholder="DD/MM/YYYY" required autocomplete="off" maxlength="10">
                <label for="dob" class="required">Date of Birth (DD/MM/YYYY)</label>
            </div>
        </div>
        <div class="col-md-4">
            <label class="required mb-2 d-block">Gender</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="gender_m" value="M" required checked>
                <label class="form-check-label" for="gender_m">Male</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="gender_f" value="F" required>
                <label class="form-check-label" for="gender_f">Female</label>
            </div>
        </div>
    </div>
</div>

{{-- Official Information Section --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-briefcase me-2"></i> Applicant's Official Information</h5>
    
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="hrms_id" name="hrms_id" 
                    placeholder="HRMS ID" maxlength="10">
                <label for="hrms_id">Employee HRMS ID</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="app_designation" name="app_designation" 
                    placeholder="Designation" required oninput="this.value=this.value.toUpperCase()">
                <label for="app_designation" class="required">Designation</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="app_posting_place" name="app_posting_place" 
                    placeholder="Place of Posting" required oninput="this.value=this.value.toUpperCase()">
                <label for="app_posting_place" class="required">Place of Posting</label>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="required mb-2 d-block">Pay Band Type</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="pay_band_type" id="pay_band_type_old" value="old" required>
                <label class="form-check-label" for="pay_band_type_old">Old</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="pay_band_type" id="pay_band_type_new" value="new" required checked>
                <label class="form-check-label" for="pay_band_type_new">New</label>
            </div>
        </div>
        <div class="col-md-4" id="pay-band-wrapper">
            <div class="form-floating">
                <select class="form-select" id="pay_band" name="pay_band" required>
                    <option value="">- Select -</option>
                </select>
                <label for="pay_band" class="required">Basic Pay Range</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="pay_in" name="pay_in" 
                    placeholder="Basic Pay" required>
                <label for="pay_in" class="required">Basic Pay</label>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="app_headquarter" name="app_headquarter" 
                    placeholder="Headquarter" oninput="this.value=this.value.toUpperCase()">
                <label for="app_headquarter">Headquarter</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="doj" name="doj" 
                    placeholder="DD/MM/YYYY" autocomplete="off" maxlength="10">
                <label for="doj">Date of Joining</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="dor" name="dor" 
                    placeholder="DD/MM/YYYY" required autocomplete="off" maxlength="10">
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
                    placeholder="Office Name" required oninput="this.value=this.value.toUpperCase()">
                <label for="office_name" class="required">Name of the Office</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_street" name="office_street" 
                    placeholder="Address" required oninput="this.value=this.value.toUpperCase()">
                <label for="office_street" class="required">Address</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_city" name="office_city" 
                    placeholder="City/Town/Village" required oninput="this.value=this.value.toUpperCase()">
                <label for="office_city" class="required">City / Town / Village</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_post_office" name="office_post_office" 
                    placeholder="Post Office" oninput="this.value=this.value.toUpperCase()">
                <label for="office_post_office">Post Office</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <select class="form-select" id="office_district" name="office_district" required>
                    <option value="">- Select -</option>
                </select>
                <label for="office_district" class="required">District</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="office_pincode" name="office_pincode" 
                    placeholder="Pincode" required maxlength="6">
                <label for="office_pincode" class="required">Pincode</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="office_phone_no" name="office_phone_no" 
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
                <select class="form-select" id="ddo_district" name="ddo_district">
                    <option value="">- Select -</option>
                </select>
                <label for="ddo_district">DDO District</label>
            </div>
        </div>
        <div class="col-md-6" id="replace_designation">
            <div class="form-floating">
                <select class="form-select" id="designation" name="designation">
                    <option value="">- Select -</option>
                </select>
                <label for="designation">DDO Designation</label>
            </div>
        </div>
        <div class="col-md-12" id="replace_ddo_address" style="display: none;">
            <div class="form-floating">
                <textarea class="form-control" id="ddo_address" name="ddo_address" 
                    placeholder="DDO Address" readonly style="height: 100px"></textarea>
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
                    placeholder="Flat Type" required readonly>
                <label for="rhe_flat_type" class="required">Flat TYPE</label>
            </div>
        </div>
        <div class="col-md-6" id="replace_allotment_category">
            <div class="form-floating">
                <select class="form-select" id="reason" name="reason" required>
                    <option value="">--Choose Allotment Reason--</option>
                </select>
                <label for="reason" class="required">Allotment Category</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="doa" name="doa" 
                    placeholder="DD/MM/YYYY" required autocomplete="off" maxlength="10">
                <label for="doa" class="required">Date of Application</label>
            </div>
        </div>
        <div class="col-md-4" id="computr_serial_no">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="computer_serial_no" name="computer_serial_no" 
                    placeholder="Computer Serial No" required>
                <label for="computer_serial_no" class="required">Computer Serial No.</label>
            </div>
        </div>
        <div class="col-md-4" id="confirm_computr_serial_no">
            <div class="form-floating">
                <input type="password" class="form-control numeric_positive" id="confirm_computer_serial_no" name="confirm_computer_serial_no" 
                    placeholder="Confirm Computer Serial No" required>
                <label for="confirm_computer_serial_no" class="required">Confirm Computer Serial No.</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="physical_application_no" name="physical_application_no" 
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
                    placeholder="Remarks" style="height: 100px"></textarea>
                <label for="remarks">Remarks</label>
            </div>
        </div>
    </div>
</div>

