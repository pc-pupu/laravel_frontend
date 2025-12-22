{{-- Common Address Fields (Permanent and Present) --}}
@php
    $data = $data ?? [];
    $hrmsData = $hrmsData ?? [];
    $addressType = $addressType ?? 'permanent'; // 'permanent' or 'present'
    $prefix = $addressType == 'permanent' ? 'permanent' : 'present';
    
    // Map HRMS field names (camelCase) to form field names (snake_case)
    $hrmsFieldMap = [
        'permanent' => [
            'street' => 'permanentStreet',
            'city_town_village' => 'permanentCityTownVillage',
            'post_office' => 'permanentPostOffice',
            'district' => 'permanentDistrictCode',
            'pincode' => 'permanentPincode',
        ],
        'present' => [
            'street' => 'presentStreet',
            'city_town_village' => 'presentCityTownVillage',
            'post_office' => 'presentPostOffice',
            'district' => 'presentDistrictCode',
            'pincode' => 'presentPincode',
        ],
    ];
    
    // Check if fields have HRMS data
    // For district, check both the HRMS district code and if the converted district exists in data
    $hasHrmsStreet = !empty($hrmsData[$hrmsFieldMap[$prefix]['street']]) || !empty($data[$hrmsFieldMap[$prefix]['street']]);
    $hasHrmsCity = !empty($hrmsData[$hrmsFieldMap[$prefix]['city_town_village']]) || !empty($data[$hrmsFieldMap[$prefix]['city_town_village']]);
    $hasHrmsPostOffice = !empty($hrmsData[$hrmsFieldMap[$prefix]['post_office']]) || !empty($data[$hrmsFieldMap[$prefix]['post_office']]);
    $hasHrmsDistrict = !empty($hrmsData[$hrmsFieldMap[$prefix]['district']]) || 
                       (!empty($data[$prefix . '_district']) && isset($hrmsData[$hrmsFieldMap[$prefix]['district']]));
    $hasHrmsPincode = !empty($hrmsData[$hrmsFieldMap[$prefix]['pincode']]) || !empty($data[$hrmsFieldMap[$prefix]['pincode']]);
@endphp

<div class="form-section">
    <h5 class="mb-3"><i class="fa fa-address-card me-2"></i> {{ ucfirst($addressType) }} Address</h5>
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="{{ $prefix }}_street" name="{{ $prefix }}_street" 
                    value="{{ $data[$prefix . '_street'] ?? old($prefix . '_street', '') }}" 
                    placeholder="Address" oninput="this.value=this.value.toUpperCase()" required 
                    {{ $hasHrmsStreet ? 'readonly' : '' }}>
                <label for="{{ $prefix }}_street" class="required">Address</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="{{ $prefix }}_city_town_village" name="{{ $prefix }}_city_town_village" 
                    value="{{ $data[$prefix . '_city_town_village'] ?? old($prefix . '_city_town_village', '') }}" 
                    placeholder="City/Town/Village" oninput="this.value=this.value.toUpperCase()" required 
                    {{ $hasHrmsCity ? 'readonly' : '' }}>
                <label for="{{ $prefix }}_city_town_village" class="required">City / Town / Village</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="{{ $prefix }}_post_office" name="{{ $prefix }}_post_office" 
                    value="{{ $data[$prefix . '_post_office'] ?? old($prefix . '_post_office', '') }}" 
                    placeholder="Post Office" oninput="this.value=this.value.toUpperCase()" required 
                    {{ $hasHrmsPostOffice ? 'readonly' : '' }}>
                <label for="{{ $prefix }}_post_office" class="required">Post Office</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <select class="form-select" id="{{ $prefix }}_district" name="{{ $prefix }}_district" required 
                    {{ $hasHrmsDistrict ? 'disabled' : '' }}>
                    @php
                        $currentValue = $data[$prefix . '_district'] ?? $data[$prefix . '_district_code'] ?? old($prefix . '_district', '');
                        $districts = $districts ?? [];
                    @endphp
                    <option value="" {{ empty($currentValue) ? 'selected' : '' }}>- Select -</option>
                    @if(!empty($districts))
                        @foreach($districts as $code => $name)
                            @if($code !== '')
                                <option value="{{ $code }}" {{ $currentValue == $code ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <label for="{{ $prefix }}_district" class="required">District</label>
                @if($hasHrmsDistrict)
                    <input type="hidden" name="{{ $prefix }}_district" value="{{ $currentValue }}">
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control numeric_positive" id="{{ $prefix }}_pincode" name="{{ $prefix }}_pincode" 
                    value="{{ $data[$prefix . '_pincode'] ?? old($prefix . '_pincode', '') }}" 
                    placeholder="Pincode" maxlength="6" required 
                    {{ $hasHrmsPincode ? 'readonly' : '' }}>
                <label for="{{ $prefix }}_pincode" class="required">Pincode</label>
            </div>
        </div>
    </div>
</div>


