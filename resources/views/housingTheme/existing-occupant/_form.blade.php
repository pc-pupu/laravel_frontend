{{-- Occupied Flat Information --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-home me-2"></i> Occupied Flat Information</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" value="{{ $flatDetails['estate_name'] ?? '' }}" readonly>
                <label>RHE Name</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" value="{{ $flatDetails['flat_type'] ?? '' }}" readonly>
                <label>Flat Type</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" value="{{ $flatDetails['block_name'] ?? '' }}" readonly>
                <label>Block Name</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" value="{{ $flatDetails['flat_no'] ?? '' }}" readonly>
                <label>Flat No.</label>
            </div>
        </div>
    </div>
</div>

{{-- Personal Information --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-user me-2"></i> Occupant's Personal Information (According to Service Book)</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="occupant_name" name="occupant_name" 
                    placeholder="Occupant's Name" required>
                <label for="occupant_name" class="required">Occupant's Name</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="occupant_father_name" name="occupant_father_name" 
                    placeholder="Father's / Husband's Name" value="NA" required>
                <label for="occupant_father_name" class="required">Father's / Husband's Name</label>
            </div>
        </div>
    </div>

    {{-- Permanent Address --}}
    <h6 class="mt-4 mb-3"><i class="fa fa-map-marker me-2"></i> Permanent Address</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="permanent_street" name="permanent_street" 
                    placeholder="Address" value="NA" required>
                <label for="permanent_street" class="required">Address</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="permanent_city_town_village" name="permanent_city_town_village" 
                    placeholder="City / Town / Village" value="NA" required>
                <label for="permanent_city_town_village" class="required">City / Town / Village</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="permanent_post_office" name="permanent_post_office" 
                    placeholder="Post Office" value="NA" required>
                <label for="permanent_post_office" class="required">Post Office</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <select class="form-select" id="permanent_district" name="permanent_district" required>
                    <option value="">Select District</option>
                    @foreach($metaData['districts'] ?? [] as $district)
                        <option value="{{ $district->district_code }}" {{ $district->district_code == '17' ? 'selected' : '' }}>
                            {{ $district->district_name }}
                        </option>
                    @endforeach
                </select>
                <label for="permanent_district" class="required">District</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="permanent_pincode" name="permanent_pincode" 
                    placeholder="Pincode" maxlength="6" value="700001" required>
                <label for="permanent_pincode" class="required">Pincode</label>
            </div>
        </div>
    </div>

    {{-- Present Address --}}
    <h6 class="mt-4 mb-3"><i class="fa fa-map-marker me-2"></i> Present Address</h6>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="present_street" name="present_street" 
                    placeholder="Address" value="NA" required>
                <label for="present_street" class="required">Address</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="present_city_town_village" name="present_city_town_village" 
                    placeholder="City / Town / Village" value="NA" required>
                <label for="present_city_town_village" class="required">City / Town / Village</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="present_post_office" name="present_post_office" 
                    placeholder="Post Office" value="NA" required>
                <label for="present_post_office" class="required">Post Office</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <select class="form-select" id="present_district" name="present_district" required>
                    <option value="">Select District</option>
                    @foreach($metaData['districts'] ?? [] as $district)
                        <option value="{{ $district->district_code }}" {{ $district->district_code == '17' ? 'selected' : '' }}>
                            {{ $district->district_name }}
                        </option>
                    @endforeach
                </select>
                <label for="present_district" class="required">District</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="present_pincode" name="present_pincode" 
                    placeholder="Pincode" maxlength="6" value="700001" required>
                <label for="present_pincode" class="required">Pincode</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="mobile" name="mobile" 
                    placeholder="Mobile No" maxlength="10" value="9999999999" required>
                <label for="mobile" class="required">Mobile No</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" 
                    placeholder="Email ID" value="test@gmail.com" required>
                <label for="email" class="required">Email ID</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="dob" name="dob" 
                    placeholder="Date of Birth (DD/MM/YYYY)" value="01/01/1900" readonly required>
                <label for="dob" class="required">Date of Birth (According to Service Book)</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <div class="form-control d-flex align-items-center" style="height: 58px;">
                    <div class="form-check form-check-inline me-3">
                        <input class="form-check-input" type="radio" name="gender" id="gender_m" value="M" checked required>
                        <label class="form-check-label" for="gender_m">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="gender_f" value="F" required>
                        <label class="form-check-label" for="gender_f">Female</label>
                    </div>
                </div>
                <label class="position-absolute" style="top: -0.5rem; left: 0.75rem; font-size: 0.875rem; color: #4980f7;" class="required">Gender</label>
            </div>
        </div>
    </div>
</div>

{{-- Official Information --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-briefcase me-2"></i> Occupant's Official Information</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="hrms_id" name="hrms_id" 
                    placeholder="Employee HRMS ID" maxlength="10" required>
                <label for="hrms_id" class="required">Employee HRMS ID</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="occupant_designation" name="occupant_designation" 
                    placeholder="Designation" value="NA" required>
                <label for="occupant_designation" class="required">Designation</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <select class="form-select" id="pay_band" name="pay_band" required>
                    <option value="">Select Basic Pay Range</option>
                    @foreach($metaData['payBands'] ?? [] as $payBand)
                        <option value="{{ $payBand['value'] }}" {{ $payBand['value'] == 1 ? 'selected' : '' }}>
                            {{ $payBand['label'] }}
                        </option>
                    @endforeach
                </select>
                <label for="pay_band" class="required">Basic Pay Range</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="pay_in" name="pay_in" 
                    placeholder="Basic Pay" value="1" required>
                <label for="pay_in" class="required">Basic Pay</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="occupant_posting_place" name="occupant_posting_place" 
                    placeholder="Place of Posting" value="NA" required>
                <label for="occupant_posting_place" class="required">Place of Posting</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="occupant_headquarter" name="occupant_headquarter" 
                    placeholder="Headquarter" value="NA" required>
                <label for="occupant_headquarter" class="required">Headquarter</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="doj" name="doj" 
                    placeholder="Date of Joining (DD/MM/YYYY)" value="01/01/1900" readonly required>
                <label for="doj" class="required">Date of Joining</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="dor" name="dor" 
                    placeholder="Date of Retirement (DD/MM/YYYY)" value="01/01/1900" readonly required>
                <label for="dor" class="required">Date of Retirement (According to Service Book)</label>
            </div>
        </div>
    </div>
</div>

{{-- Office Address --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-building me-2"></i> Name and Address of the Office</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_name" name="office_name" 
                    placeholder="Name of the Office" value="NA" required>
                <label for="office_name" class="required">Name of the Office</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_street" name="office_street" 
                    placeholder="Address of the Office" value="NA" required>
                <label for="office_street" class="required">Address of the Office</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_city" name="office_city" 
                    placeholder="City / Town / Village" value="NA" required>
                <label for="office_city" class="required">City / Town / Village</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_post_office" name="office_post_office" 
                    placeholder="Post Office" value="NA" required>
                <label for="office_post_office" class="required">Post Office</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <select class="form-select" id="office_district" name="office_district" required>
                    <option value="">Select District</option>
                    @foreach($metaData['districts'] ?? [] as $district)
                        <option value="{{ $district->district_code }}" {{ $district->district_code == '17' ? 'selected' : '' }}>
                            {{ $district->district_name }}
                        </option>
                    @endforeach
                </select>
                <label for="office_district" class="required">District</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="office_pincode" name="office_pincode" 
                    placeholder="Pincode" maxlength="6" value="700001" required>
                <label for="office_pincode" class="required">Pincode</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="office_phone_no" name="office_phone_no" 
                    placeholder="Phone No. (With STD Code)" maxlength="15" value="033-22222222" required>
                <label for="office_phone_no" class="required">Phone No. (With STD Code)</label>
            </div>
        </div>
    </div>
</div>

{{-- DDO Details --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-id-card me-2"></i> DDO with Full Address</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <select class="form-select" id="ddo_district" name="ddo_district" required>
                    <option value="">Select DDO District</option>
                    @foreach($metaData['districts'] ?? [] as $district)
                        <option value="{{ $district->district_code }}">
                            {{ $district->district_name }}
                        </option>
                    @endforeach
                </select>
                <label for="ddo_district" class="required">DDO District</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <select class="form-select" id="ddo_id" name="ddo_id" required disabled>
                    <option value="">Select DDO Designation</option>
                    @foreach($metaData['ddos'] ?? [] as $ddo)
                        <option value="{{ $ddo->ddo_id }}" data-address="{{ $ddo->ddo_address ?? '' }}">
                            {{ $ddo->ddo_designation }}
                        </option>
                    @endforeach
                </select>
                <label for="ddo_id" class="required">DDO Designation</label>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-floating">
                <textarea class="form-control" id="ddo_address" name="ddo_address" 
                    placeholder="DDO Address" style="height: 100px" readonly></textarea>
                <label for="ddo_address">DDO Address</label>
            </div>
        </div>
    </div>
</div>

{{-- License Details --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-file-text me-2"></i> License Details</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="license_no" name="license_no" 
                    placeholder="License No." value="NA" required>
                <label for="license_no" class="required">License No.</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="dol" name="dol" 
                    placeholder="License Issue Date (DD/MM/YYYY)" readonly required>
                <label for="dol" class="required">License Issue Date</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <select class="form-select" id="authorised_or_not" name="authorised_or_not" required>
                    <option value="">Select Occupant Status</option>
                    <option value="authorised">Authorised Occupant</option>
                    <option value="unauthorised">Unauthorised Occupant</option>
                </select>
                <label for="authorised_or_not" class="required">Occupant Status</label>
            </div>
        </div>
    </div>
</div>

