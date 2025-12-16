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

@php
    $occupant = $occupant ?? [];
    $isEdit = isset($occupant['online_application_id']) || isset($occupant['applicant_name']);
    $normalizedOccupant = [
        'applicant_name' => $occupant['applicant_name'] ?? old('occupant_name', ''),
        'guardian_name' => $occupant['guardian_name'] ?? old('occupant_father_name', 'NA'),
        'mobile_no' => $occupant['mobile_no'] ?? old('mobile', '9999999999'),
        'email' => $occupant['email'] ?? old('email', 'test@gmail.com'),
        'dob' => $occupant['dob'] ?? $occupant['date_of_birth'] ?? old('dob', '01/01/1900'),
        'gender' => $occupant['gender'] ?? old('gender', 'M'),
    ];
@endphp

{{-- Personal Information --}}
<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-user me-2"></i> Occupant's Personal Information (According to Service Book)</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="occupant_name" name="occupant_name" 
                    value="{{ $normalizedOccupant['applicant_name'] }}" placeholder="Occupant's Name" required>
                <label for="occupant_name" class="required">Occupant's Name</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="occupant_father_name" name="occupant_father_name" 
                    value="{{ $normalizedOccupant['guardian_name'] }}" placeholder="Father's / Husband's Name" required>
                <label for="occupant_father_name" class="required">Father's / Husband's Name</label>
            </div>
        </div>
    </div>

    {{-- Permanent Address --}}
    @include('commonForm.address-fields', [
        'data' => array_merge($normalizedOccupant, $occupant ?? []), 
        'addressType' => 'permanent'
    ])

    {{-- Present Address --}}
    @include('commonForm.address-fields', [
        'data' => array_merge($normalizedOccupant, $occupant ?? []), 
        'addressType' => 'present'
    ])

    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="mobile" name="mobile" 
                    value="{{ $normalizedOccupant['mobile_no'] }}" placeholder="Mobile No" maxlength="10" required>
                <label for="mobile" class="required">Mobile No</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" 
                    value="{{ $normalizedOccupant['email'] }}" placeholder="Email ID" required>
                <label for="email" class="required">Email ID</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <input type="text" class="form-control" id="dob" name="dob" 
                    value="{{ $normalizedOccupant['dob'] }}" placeholder="Date of Birth (DD/MM/YYYY)" 
                    readonly required>
                <label for="dob" class="required">Date of Birth (According to Service Book)</label>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-floating">
                <div class="form-control d-flex align-items-center" style="height: 58px;">
                    <div class="form-check form-check-inline me-3">
                        <input class="form-check-input" type="radio" name="gender" id="gender_m" value="M" 
                            {{ $normalizedOccupant['gender'] == 'M' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="gender_m">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="gender_f" value="F" 
                            {{ $normalizedOccupant['gender'] == 'F' ? 'checked' : '' }} required>
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
                    value="{{ $occupant['hrms_id'] ?? old('hrms_id', '') }}" 
                    placeholder="Employee HRMS ID" maxlength="10" required>
                <label for="hrms_id" class="required">Employee HRMS ID</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="occupant_designation" name="occupant_designation" 
                    value="{{ $occupant['applicant_designation'] ?? old('occupant_designation', 'NA') }}" 
                    placeholder="Designation" required>
                <label for="occupant_designation" class="required">Designation</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                @php
                    $currentPayBand = $occupant['pay_band_id'] ?? old('pay_band', '');
                @endphp
                <select class="form-select" id="pay_band" name="pay_band" required>
                    <option value="" {{ empty($currentPayBand) ? 'selected' : '' }}>- Select -</option>
                    @foreach($metaData['payBands'] ?? [] as $payBand)
                        <option value="{{ $payBand['value'] }}" 
                            {{ $currentPayBand == $payBand['value'] ? 'selected' : '' }}>
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
                    value="{{ $occupant['pay_in_the_pay_band'] ?? old('pay_in', '1') }}" 
                    placeholder="Basic Pay" required>
                <label for="pay_in" class="required">Basic Pay</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="occupant_posting_place" name="occupant_posting_place" 
                    value="{{ $occupant['applicant_posting_place'] ?? old('occupant_posting_place', 'NA') }}" 
                    placeholder="Place of Posting" required>
                <label for="occupant_posting_place" class="required">Place of Posting</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="occupant_headquarter" name="occupant_headquarter" 
                    value="{{ $occupant['applicant_headquarter'] ?? old('occupant_headquarter', 'NA') }}" 
                    placeholder="Headquarter" required>
                <label for="occupant_headquarter" class="required">Headquarter</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="doj" name="doj" 
                    value="{{ $occupant['date_of_joining'] ?? old('doj', '01/01/1900') }}" 
                    placeholder="Date of Joining (DD/MM/YYYY)" readonly required>
                <label for="doj" class="required">Date of Joining</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="dor" name="dor" 
                    value="{{ $occupant['date_of_retirement'] ?? old('dor', '01/01/1900') }}" 
                    placeholder="Date of Retirement (DD/MM/YYYY)" readonly required>
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
                    value="{{ $occupant['office_name'] ?? old('office_name', 'NA') }}" 
                    placeholder="Name of the Office" required>
                <label for="office_name" class="required">Name of the Office</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_street" name="office_street" 
                    value="{{ $occupant['office_street'] ?? old('office_street', 'NA') }}" 
                    placeholder="Address of the Office" required>
                <label for="office_street" class="required">Address of the Office</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_city" name="office_city" 
                    value="{{ $occupant['office_city_town_village'] ?? $occupant['office_city'] ?? old('office_city', 'NA') }}" 
                    placeholder="City / Town / Village" required>
                <label for="office_city" class="required">City / Town / Village</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="office_post_office" name="office_post_office" 
                    value="{{ $occupant['office_post_office'] ?? old('office_post_office', 'NA') }}" 
                    placeholder="Post Office" required>
                <label for="office_post_office" class="required">Post Office</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                @php
                    $currentOfficeDistrict = $occupant['office_district_code'] ?? old('office_district', '');
                @endphp
                <select class="form-select" id="office_district" name="office_district" required>
                    <option value="" {{ empty($currentOfficeDistrict) ? 'selected' : '' }}>- Select -</option>
                    @foreach($metaData['districts'] ?? [] as $district)
                        <option value="{{ $district->district_code }}" 
                            {{ $currentOfficeDistrict == $district->district_code ? 'selected' : '' }}>
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
                    value="{{ $occupant['office_pin_code'] ?? $occupant['office_pincode'] ?? old('office_pincode', '700001') }}" 
                    placeholder="Pincode" maxlength="6" required>
                <label for="office_pincode" class="required">Pincode</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="office_phone_no" name="office_phone_no" 
                    value="{{ $occupant['office_phone_no'] ?? old('office_phone_no', '033-22222222') }}" 
                    placeholder="Phone No. (With STD Code)" maxlength="15" required>
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
                @php
                    $currentDdoDistrict = $occupant['ddo_district_code'] ?? old('ddo_district', '');
                @endphp
                <select class="form-select" id="ddo_district" name="ddo_district" required>
                    <option value="" {{ empty($currentDdoDistrict) ? 'selected' : '' }}>- Select -</option>
                    @foreach($metaData['districts'] ?? [] as $district)
                        <option value="{{ $district->district_code }}" 
                            {{ $currentDdoDistrict == $district->district_code ? 'selected' : '' }}>
                            {{ $district->district_name }}
                        </option>
                    @endforeach
                </select>
                <label for="ddo_district" class="required">DDO District</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                @php
                    $currentDdoId = $occupant['ddo_id'] ?? old('ddo_id', '');
                @endphp
                <select class="form-select" id="ddo_id" name="ddo_id" required disabled>
                    <option value="" {{ empty($currentDdoId) ? 'selected' : '' }}>- Select -</option>
                    @foreach($metaData['ddos'] ?? [] as $ddo)
                        <option value="{{ $ddo->ddo_id }}" 
                            {{ $currentDdoId == $ddo->ddo_id ? 'selected' : '' }}
                            data-address="{{ $ddo->ddo_address ?? '' }}">
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
                    value="{{ $occupant['ddo_address'] ?? old('ddo_address', '') }}" 
                    placeholder="DDO Address" style="height: 100px" readonly>{{ $occupant['ddo_address'] ?? old('ddo_address', '') }}</textarea>
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
                    value="{{ $occupant['license_no'] ?? old('license_no', 'NA') }}" 
                    placeholder="License No." required>
                <label for="license_no" class="required">License No.</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="dol" name="dol" 
                    value="{{ $occupant['license_issue_date'] ?? $occupant['dol'] ?? old('dol', '') }}" 
                    placeholder="License Issue Date (DD/MM/YYYY)" readonly required>
                <label for="dol" class="required">License Issue Date</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                @php
                    $currentAuthorised = $occupant['authorised_or_not'] ?? old('authorised_or_not', '');
                @endphp
                <select class="form-select" id="authorised_or_not" name="authorised_or_not" required>
                    <option value="" {{ empty($currentAuthorised) ? 'selected' : '' }}>- Select -</option>
                    <option value="authorised" {{ $currentAuthorised == 'authorised' ? 'selected' : '' }}>Authorised Occupant</option>
                    <option value="unauthorised" {{ $currentAuthorised == 'unauthorised' ? 'selected' : '' }}>Unauthorised Occupant</option>
                </select>
                <label for="authorised_or_not" class="required">Occupant Status</label>
            </div>
        </div>
    </div>
</div>

