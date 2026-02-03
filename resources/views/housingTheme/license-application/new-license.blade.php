@extends('housingTheme.layouts.app')

@section('title', 'Application for New License')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-file-alt me-2"></i> PRESCRIBED FORM OF APPLICATION FOR NEW LICENSE</h3>
                            <p class="mb-0">Fill in all the required information</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <form id="new-license-form" method="POST" action="{{ route('new-license.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="app_type" value="NL">
                    <input type="hidden" name="online_application_id" value="{{ $onlineApplicationId }}">
                    <input type="hidden" name="action" id="form_action" value="draft">

                    {{-- Personal Information Section --}}
                    @include('commonForm.personal-info', [
                        'data' => $personalInfo ?? []
                    ])

                    {{-- Permanent Address --}}
                    @include('commonForm.address-fields', [
                        'data' => $personalInfo ?? [],
                        'addressType' => 'permanent',
                        'districts' => $districts ?? []
                    ])

                    {{-- Present Address --}}
                    @include('commonForm.address-fields', [
                        'data' => $personalInfo ?? [],
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
                                        placeholder="Employee HRMS ID" maxlength="10" required>
                                    <label for="hrms_id" class="required">Employee HRMS ID</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="app_designation" name="app_designation" 
                                        value="{{ $officialInfo['applicant_designation'] ?? old('app_designation', '') }}" 
                                        placeholder="Designation" oninput="this.value=this.value.toUpperCase()" required>
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
                                    <textarea class="form-control" id="app_posting_place" name="app_posting_place" 
                                        placeholder="Place of Posting" oninput="this.value=this.value.toUpperCase()" required>{{ $officialInfo['applicant_posting_place'] ?? old('app_posting_place', '') }}</textarea>
                                    <label for="app_posting_place" class="required">Place of Posting</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="app_headquarter" name="app_headquarter" 
                                        value="{{ $officialInfo['applicant_headquarter'] ?? old('app_headquarter', '') }}" 
                                        placeholder="Headquarter" oninput="this.value=this.value.toUpperCase()" required>
                                    <label for="app_headquarter" class="required">Headquarter</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="doj" name="doj" 
                                        value="{{ $officialInfo['doj'] ?? old('doj', '') }}" 
                                        placeholder="DD/MM/YYYY" required autocomplete="off">
                                    <label for="doj" class="required">Date of Joining (DD/MM/YYYY)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="dor" name="dor" 
                                        value="{{ $officialInfo['dor'] ?? old('dor', '') }}" 
                                        placeholder="DD/MM/YYYY" required autocomplete="off">
                                    <label for="dor" class="required">Date of Retirement (DD/MM/YYYY)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Name and Address of the Office --}}
                    <div class="form-section mt-4">
                        <h5 class="mb-3"><i class="fa fa-building me-2"></i> Name and Address of the Department/Directorate/Office</h5>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="office_name" name="office_name" 
                                        value="{{ $officialInfo['office_name'] ?? old('office_name', '') }}" 
                                        placeholder="Name of the Office" oninput="this.value=this.value.toUpperCase()" required>
                                    <label for="office_name" class="required">Name of the Department/Directorate/Office</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <textarea class="form-control" id="office_street" name="office_street" 
                                        placeholder="Address" oninput="this.value=this.value.toUpperCase()" required>{{ $officialInfo['office_street'] ?? old('office_street', '') }}</textarea>
                                    <label for="office_street" class="required">Address</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="office_district" name="office_district" required>
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
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control numeric_positive" id="office_phone_no" name="office_phone_no" 
                                        value="{{ $officialInfo['office_phone_no'] ?? old('office_phone_no', '') }}" 
                                        placeholder="Phone No" maxlength="10" required>
                                    <label for="office_phone_no" class="required">Phone No</label>
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

                    {{-- Allotment Information --}}
                    @include('housingTheme.license-application._allotment-info', [
                        'allotmentData' => $allotmentData ?? []
                    ])

                    {{-- Document Upload --}}
                    @include('housingTheme.license-application._document-upload', [
                        'allotmentData' => $allotmentData ?? []
                    ])

                    {{-- Scanned Signature Upload (if user has new application) --}}
                    @if(isset($onlineApplicationId) && $onlineApplicationId > 0)
                    <div class="form-section mt-4">
                        <h5 class="mb-3"><i class="fa fa-signature me-2"></i> Upload Your Scanned Signature</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="file" class="form-control" id="scaned_sign" name="scaned_sign" 
                                        accept=".jpg,.jpeg" required>
                                    <label for="scaned_sign" class="required">Upload Your Scanned Signature</label>
                                    <small class="text-muted">Allowed Extensions: jpg jpeg | Dimensions: 140px (Width) x 60px (Height) | Maximum File Size: 50 KB</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Submit Buttons --}}
                    <div class="row mt-4">
                        <div class="col-12 border-top pt-3">
                            <button type="submit" class="btn bg-secondary btn-sm px-4 rounded-pill text-white fw-bolder me-2" 
                                onclick="document.getElementById('form_action').value='draft';">
                                <i class="fa fa-save me-2"></i> Save as Draft
                            </button>
                            <button type="submit" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder" 
                                onclick="if(confirm('Are you sure you want to submit the form? Once the form submitted the data can\'t be edited.')) { document.getElementById('form_action').value='applied'; return true; } else { return false; }">
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
        // Initialize date pickers
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
        $("#mobile").keypress(function (e) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
            }
        });

        $(".numeric_positive").keypress(function (e) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                return false;
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
</script>
@endpush
