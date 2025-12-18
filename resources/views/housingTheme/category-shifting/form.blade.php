@extends('housingTheme.layouts.app')

@section('title', 'Application for Category Shifting')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-exchange-alt me-2"></i> Application for Category Shifting</h3>
                            <p class="mb-0">Fill in all the required information</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <form id="cs-application-form" method="POST" action="{{ route('category-shifting.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="online_cs_id" id="online_cs_id" value="{{ $appData['online_cs_id'] ?? 0 }}">
                    <input type="hidden" name="action" id="form_action" value="draft">

                    {{-- Personal Information Section --}}
                    @include('commonForm.personal-info', [
                        'data' => $officialInfo ?? []
                    ])

                    {{-- Permanent Address --}}
                    @include('commonForm.address-fields', [
                        'data' => $officialInfo ?? [],
                        'addressType' => 'permanent',
                        'districts' => $districts ?? []
                    ])

                    {{-- Present Address --}}
                    @include('commonForm.address-fields', [
                        'data' => $officialInfo ?? [],
                        'addressType' => 'present',
                        'districts' => $districts ?? []
                    ])

                    {{-- Applicant's Official Information --}}
                    <div class="form-section mt-4">
                        <h5 class="mb-3"><i class="fa fa-briefcase me-2"></i> Applicant's Official Information</h5>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control numeric_positive" id="hrms_id" name="hrms_id" 
                                        value="{{ $officialInfo['hrms_id'] ?? old('hrms_id', '') }}" 
                                        placeholder="Employee HRMS ID" maxlength="10" required readonly>
                                    <label for="hrms_id" class="required">Employee HRMS ID</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="app_designation" name="app_designation" 
                                        value="{{ $officialInfo['applicant_designation'] ?? old('app_designation', '') }}" 
                                        placeholder="Designation" oninput="this.value=this.value.toUpperCase()" required readonly>
                                    <label for="app_designation" class="required">Designation</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="pay_band" name="pay_band" required>
                                        <option value="" {{ empty($officialInfo['pay_band_id'] ?? old('pay_band')) ? 'selected' : '' }}>- Select -</option>
                                        @foreach($payBands as $id => $label)
                                            <option value="{{ $id }}" {{ ($officialInfo['pay_band_id'] ?? old('pay_band')) == $id ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="pay_band" class="required">Basic Pay Range</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control numeric_positive" id="pay_in" name="pay_in" 
                                        value="{{ $officialInfo['pay_in_the_pay_band'] ?? old('pay_in', '') }}" 
                                        placeholder="Basic Pay" required>
                                    <label for="pay_in" class="required">Basic Pay</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control numeric_positive" id="grade_pay" name="grade_pay" 
                                        value="{{ $officialInfo['grade_pay'] ?? old('grade_pay', '') }}" 
                                        placeholder="Grade Pay">
                                    <label for="grade_pay">Grade Pay</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <textarea class="form-control" id="app_posting_place" name="app_posting_place" 
                                        placeholder="Place of Posting" oninput="this.value=this.value.toUpperCase()" required readonly>{{ $officialInfo['applicant_posting_place'] ?? old('app_posting_place', '') }}</textarea>
                                    <label for="app_posting_place" class="required">Place of Posting</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="app_headquarter" name="app_headquarter" 
                                        value="{{ $officialInfo['applicant_headquarter'] ?? old('app_headquarter', '') }}" 
                                        placeholder="Headquarter" oninput="this.value=this.value.toUpperCase()" required readonly>
                                    <label for="app_headquarter" class="required">Headquarter</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="doj" name="doj" 
                                        value="{{ $officialInfo['doj'] ?? old('doj', '') }}" 
                                        placeholder="DD/MM/YYYY" required autocomplete="off" readonly>
                                    <label for="doj" class="required">Date of Joining (DD/MM/YYYY)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="dor" name="dor" 
                                        value="{{ $officialInfo['dor'] ?? old('dor', '') }}" 
                                        placeholder="DD/MM/YYYY" required autocomplete="off" readonly>
                                    <label for="dor" class="required">Date of Retirement (DD/MM/YYYY)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Name and Address of the Office --}}
                    <div class="form-section mt-4">
                        <h5 class="mb-3"><i class="fa fa-building me-2"></i> Name and Address of the Office</h5>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="office_name" name="office_name" 
                                        value="{{ $officialInfo['office_name'] ?? old('office_name', '') }}" 
                                        placeholder="Name of the Office" oninput="this.value=this.value.toUpperCase()" required readonly>
                                    <label for="office_name" class="required">Name of the Office</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <textarea class="form-control" id="office_street" name="office_street" 
                                        placeholder="Address" oninput="this.value=this.value.toUpperCase()" required readonly>{{ $officialInfo['office_street'] ?? old('office_street', '') }}</textarea>
                                    <label for="office_street" class="required">Address</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="office_city" name="office_city" 
                                        value="{{ $officialInfo['office_city_town_village'] ?? old('office_city', '') }}" 
                                        placeholder="City/Town/Village" oninput="this.value=this.value.toUpperCase()" required readonly>
                                    <label for="office_city" class="required">City / Town / Village</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="office_post_office" name="office_post_office" 
                                        value="{{ $officialInfo['office_post_office'] ?? old('office_post_office', '') }}" 
                                        placeholder="Post Office" oninput="this.value=this.value.toUpperCase()" required readonly>
                                    <label for="office_post_office" class="required">Post Office</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select" id="office_district" name="office_district" required disabled>
                                        <option value="" {{ empty($officialInfo['office_district'] ?? old('office_district')) ? 'selected' : '' }}>- Select -</option>
                                        @foreach($districts as $code => $name)
                                            <option value="{{ $code }}" {{ ($officialInfo['office_district'] ?? old('office_district')) == $code ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="office_district" class="required">District</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control numeric_positive" id="office_pincode" name="office_pincode" 
                                        value="{{ $officialInfo['office_pin_code'] ?? old('office_pincode', '') }}" 
                                        placeholder="Pincode" maxlength="6" required readonly>
                                    <label for="office_pincode" class="required">Pincode</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control numeric_positive" id="office_phone_no" name="office_phone_no" 
                                        value="{{ $officialInfo['office_phone_no'] ?? old('office_phone_no', '') }}" 
                                        placeholder="Phone No (With STD Code)" maxlength="15" required readonly>
                                    <label for="office_phone_no" class="required">Phone No. (With STD Code)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DDO Details --}}
                    <div class="form-section mt-4">
                        <h5 class="mb-3"><i class="fa fa-id-card me-2"></i> DDO with full address</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="district" name="district" required>
                                        <option value="" {{ empty($officialInfo['district_code'] ?? old('district')) ? 'selected' : '' }}>- Select -</option>
                                        @foreach($districts as $code => $name)
                                            <option value="{{ $code }}" {{ ($officialInfo['district_code'] ?? old('district')) == $code ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="district" class="required">DDO District</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="designation" name="designation" required>
                                        <option value="" {{ empty($officialInfo['ddo_id'] ?? old('designation')) ? 'selected' : '' }}>- Select -</option>
                                        @foreach($ddoDesignations as $id => $name)
                                            <option value="{{ $id }}" {{ ($officialInfo['ddo_id'] ?? old('designation')) == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="designation" class="required">DDO Designation</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Current Occupation Specification (CS Specific) --}}
                    <div class="form-section mt-4">
                        <h5 class="mb-3"><i class="fa fa-home me-2"></i> Current Occupation Specification</h5>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="cs_occupation_estate" name="cs_occupation_estate" required>
                                        <option value="" {{ empty($appData['occupation_estate'] ?? $occupation['estate_id'] ?? old('cs_occupation_estate')) ? 'selected' : '' }}>- Select -</option>
                                        @if(isset($occupation['estate_id']))
                                            <option value="{{ $occupation['estate_id'] }}" selected>{{ $occupation['estate_name'] }}</option>
                                        @endif
                                    </select>
                                    <label for="cs_occupation_estate" class="required">Select Housing</label>
                                </div>
                            </div>
                            <div class="col-md-6" id="block_replace_cs">
                                <div class="form-floating">
                                    <select class="form-select" id="cs_occupation_block" name="cs_occupation_block" required>
                                        <option value="" {{ empty($appData['occupation_block'] ?? $occupation['block_id'] ?? old('cs_occupation_block')) ? 'selected' : '' }}>- Select -</option>
                                        @if(isset($occupation['block_id']))
                                            <option value="{{ $occupation['block_id'] }}" selected>{{ $occupation['block_name'] }}</option>
                                        @endif
                                    </select>
                                    <label for="cs_occupation_block" class="required">Select Block</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6" id="flat_no_replace_cs">
                                <div class="form-floating">
                                    <select class="form-select" id="cs_occupation_flat" name="cs_occupation_flat" required>
                                        <option value="" {{ empty($appData['occupation_flat'] ?? $occupation['flat_id'] ?? old('cs_occupation_flat')) ? 'selected' : '' }}>- Select -</option>
                                        @if(isset($occupation['flat_id']))
                                            <option value="{{ $occupation['flat_id'] }}" selected>{{ $occupation['flat_no'] }}</option>
                                        @endif
                                    </select>
                                    <label for="cs_occupation_flat" class="required">Flat No</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="cs_possession_date" name="cs_possession_date" 
                                        value="{{ $appData['possession_date'] ?? old('cs_possession_date', '') }}" 
                                        placeholder="DD/MM/YYYY" required readonly>
                                    <label for="cs_possession_date" class="required">Date of Possession</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="file" class="form-control" id="cs_file_licence" name="cs_file_licence" 
                                        accept=".pdf" required>
                                    <label for="cs_file_licence" class="required">Upload Current Licence</label>
                                    <small class="text-muted">Allowed Extension: pdf | Maximum File Size: 1 MB</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="row mt-4">
                        <div class="col-12 border-top pt-3">
                            <button type="submit" name="save_draft" class="btn btn-secondary btn-sm px-4 rounded-pill me-2" 
                                onclick="document.getElementById('form_action').value='draft';">
                                <i class="fa fa-save me-2"></i> Save as Draft
                            </button>
                            <button type="submit" name="apply" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder" 
                                onclick="document.getElementById('form_action').value='applied'; return validate_cs_application_form();">
                                <i class="fa fa-paper-plane me-2"></i> Apply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/housingTheme/jquery-ui/jquery-ui.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/housingTheme/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/housingTheme/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Initialize date picker for possession date
        $("#cs_possession_date").datepicker({
            dateFormat: "dd/mm/yy",
            maxDate: "0",
            changeMonth: true,
            changeYear: true,
            yearRange: "-50:+0",
            autoSize: true
        });

        // Initialize other date pickers
        $("#dob").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            yearRange: "1947:" + (new Date().getFullYear() - 18),
            minDate: new Date(1947, 0, 1),
            maxDate: "-18Y",
            autoSize: true
        });

        $("#doj").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            yearRange: "-80:+0",
            maxDate: "0",
            autoSize: true
        });

        $("#dor").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            yearRange: "-80:+70",
            autoSize: true
        });

        // Numeric input restrictions
        $(".numeric_positive").keypress(function (e) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });

        // Estate change - load blocks
        $('#cs_occupation_estate').on('change', function() {
            const estateId = $(this).val();
            if (estateId) {
                loadBlocks(estateId);
            } else {
                $('#cs_occupation_block').html('<option value="">- Select -</option>');
                $('#cs_occupation_flat').html('<option value="">- Select -</option>');
            }
        });

        // Block change - load flats
        $('#cs_occupation_block').on('change', function() {
            const estateId = $('#cs_occupation_estate').val();
            const blockId = $(this).val();
            if (estateId && blockId) {
                loadFlats(estateId, blockId);
            } else {
                $('#cs_occupation_flat').html('<option value="">- Select -</option>');
            }
        });

        // DDO District change - load DDO designations
        $('#district').on('change', function() {
            const districtCode = $(this).val();
            if (districtCode) {
                loadDdoDesignations(districtCode);
            } else {
                $('#designation').html('<option value="">- Select -</option>');
            }
        });
    });

    function loadBlocks(estateId) {
        $.ajax({
            url: '{{ config('services.api.base_url') }}/api/user-tagging/helpers/blocks/' + estateId + '/0',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer {{ session('api_token') }}',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.status === 'success') {
                    const select = $('#cs_occupation_block');
                    select.html('<option value="">- Select -</option>');
                    if (response.data && Array.isArray(response.data)) {
                        response.data.forEach(function(block) {
                            select.append($('<option></option>').val(block.block_id).text(block.block_name));
                        });
                    }
                }
            },
            error: function() {
                alert('Failed to load blocks');
            }
        });
    }

    function loadFlats(estateId, blockId) {
        $.ajax({
            url: '{{ config('services.api.base_url') }}/api/user-tagging/helpers/flats/' + estateId + '/0/' + blockId + '/0',
            method: 'GET',
            headers: {
                'Authorization': 'Bearer {{ session('api_token') }}',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.status === 'success') {
                    const select = $('#cs_occupation_flat');
                    select.html('<option value="">- Select -</option>');
                    if (response.data && Array.isArray(response.data)) {
                        response.data.forEach(function(flat) {
                            select.append($('<option></option>').val(flat.flat_id).text(flat.flat_no));
                        });
                    }
                }
            },
            error: function() {
                alert('Failed to load flats');
            }
        });
    }

    function loadDdoDesignations(districtCode) {
        $.ajax({
            url: '{{ route('common-application.ddo-designations') }}',
            method: 'GET',
            data: { district_code: districtCode },
            success: function(response) {
                if (response.status === 'success') {
                    const select = $('#designation');
                    select.html('<option value="">- Select -</option>');
                    for (const [id, name] of Object.entries(response.data)) {
                        if (id !== '') {
                            select.append($('<option></option>').val(id).text(name));
                        }
                    }
                }
            },
            error: function() {
                alert('Failed to load DDO designations');
            }
        });
    }

    function validate_cs_application_form() {
        // Basic validation
        if (!$('#cs_occupation_estate').val()) {
            alert('Please select housing');
            return false;
        }
        if (!$('#cs_occupation_block').val()) {
            alert('Please select block');
            return false;
        }
        if (!$('#cs_occupation_flat').val()) {
            alert('Please select flat no.');
            return false;
        }
        if (!$('#cs_possession_date').val()) {
            alert('Please enter date of possession');
            return false;
        }
        if (!$('#cs_file_licence')[0].files.length) {
            alert('Please upload current licence');
            return false;
        }

        // File size validation (1MB = 1048576 bytes)
        const file = $('#cs_file_licence')[0].files[0];
        if (file && file.size > 1048576) {
            alert('The file exceeds 1 MB, the maximum allowed size for uploads.');
            return false;
        }

        return confirm('Are you sure you want to submit the form? Once the form submitted the data can\'t be edited.');
    }
</script>
@endpush

