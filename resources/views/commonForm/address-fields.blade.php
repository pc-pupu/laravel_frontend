{{-- Common Address Fields (Permanent and Present) --}}
@php
    $data = $data ?? [];
    $addressType = $addressType ?? 'permanent'; // 'permanent' or 'present'
    $prefix = $addressType == 'permanent' ? 'permanent' : 'present';
@endphp

<h6 class="mt-4 mb-3"><i class="fa fa-map-marker-alt me-2"></i> {{ ucfirst($addressType) }} Address</h6>
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="form-floating">
            <input type="text" class="form-control" id="{{ $prefix }}_street" name="{{ $prefix }}_street" 
                value="{{ $data[$prefix . '_street'] ?? old($prefix . '_street', '') }}" 
                placeholder="Address" oninput="this.value=this.value.toUpperCase()">
            <label for="{{ $prefix }}_street">Address</label>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-floating">
            <input type="text" class="form-control" id="{{ $prefix }}_city_town_village" name="{{ $prefix }}_city_town_village" 
                value="{{ $data[$prefix . '_city_town_village'] ?? old($prefix . '_city_town_village', '') }}" 
                placeholder="City/Town/Village" oninput="this.value=this.value.toUpperCase()">
            <label for="{{ $prefix }}_city_town_village">City / Town / Village</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating">
            <input type="text" class="form-control" id="{{ $prefix }}_post_office" name="{{ $prefix }}_post_office" 
                value="{{ $data[$prefix . '_post_office'] ?? old($prefix . '_post_office', '') }}" 
                placeholder="Post Office" oninput="this.value=this.value.toUpperCase()">
            <label for="{{ $prefix }}_post_office">Post Office</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating">
            <select class="form-select" id="{{ $prefix }}_district" name="{{ $prefix }}_district">
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
            <label for="{{ $prefix }}_district">District</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-floating">
            <input type="text" class="form-control numeric_positive" id="{{ $prefix }}_pincode" name="{{ $prefix }}_pincode" 
                value="{{ $data[$prefix . '_pincode'] ?? old($prefix . '_pincode', '') }}" 
                placeholder="Pincode" maxlength="6">
            <label for="{{ $prefix }}_pincode">Pincode</label>
        </div>
    </div>
</div>

