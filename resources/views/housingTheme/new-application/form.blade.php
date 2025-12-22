@extends('housingTheme.layouts.app')

@section('title', 'Application for New Allotment')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-file-alt me-2"></i> Application for New Allotment</h3>
                            <p class="mb-0">Fill in all the required information</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <form id="new-application-form" method="POST" action="{{ route('new-application.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="app_type" value="NA">
                    <input type="hidden" name="online_application_id" id="online_application_id" value="{{ $existingAppData['online_application_id'] ?? 0 }}">
                    <input type="hidden" name="action" id="form_action" value="draft">

                    {{-- Personal Information Section --}}
                    @include('commonForm.personal-info', [
                        'data' => array_merge($personalInfo ?? [], $hrmsData ?? [])
                    ])

                    {{-- Permanent Address --}}
                    @include('commonForm.address-fields', [
                        'data' => array_merge($personalInfo ?? [], $hrmsData ?? []),
                        'addressType' => 'permanent',
                        'districts' => $districts ?? []
                    ])

                    {{-- Present Address --}}
                    @include('commonForm.address-fields', [
                        'data' => array_merge($personalInfo ?? [], $hrmsData ?? []),
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
                                        value="{{ $officialInfo['hrms_id'] ?? $hrmsData['hrmsId'] ?? old('hrms_id', '') }}" 
                                        placeholder="Employee HRMS ID" maxlength="10" required readonly>
                                    <label for="hrms_id" class="required">Employee HRMS ID</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="app_designation" name="app_designation" 
                                        value="{{ $officialInfo['applicant_designation'] ?? $hrmsData['applicantDesignation'] ?? old('app_designation', '') }}" 
                                        placeholder="Designation" oninput="this.value=this.value.toUpperCase()" required readonly>
                                    <label for="app_designation" class="required">Designation</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="pay_band" name="pay_band" required {{ isset($hrmsData['payBandId']) ? 'disabled' : '' }}>
                                        <option value="" {{ empty($officialInfo['pay_band_id'] ?? old('pay_band')) ? 'selected' : '' }}>- Select -</option>
                                        @foreach($payBands as $id => $label)
                                            <option value="{{ $id }}" {{ ($hrmsData['payBandId'] ?? old('pay_band')) == $id ? 'selected' : '' }}>
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
                                        value="{{ $officialInfo['pay_in_the_pay_band'] ?? $hrmsData['payInThePayBand'] ?? old('pay_in', '') }}" 
                                        placeholder="Basic Pay" required readonly>
                                    <label for="pay_in" class="required">Basic Pay</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <textarea class="form-control" id="app_posting_place" name="app_posting_place" rows="4" placeholder="Place of Posting" oninput="this.value=this.value.toUpperCase()" rows="4" required readonly style="height: 85px">{{ $officialInfo['applicant_posting_place'] ?? $hrmsData['applicantPostingPlace'] ?? old('app_posting_place', '') }}</textarea>
                                    <label for="app_posting_place" class="required">Place of Posting2</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="app_headquarter" name="app_headquarter" 
                                        value="{{ $officialInfo['applicant_headquarter'] ?? $hrmsData['applicantHeadquarter'] ?? old('app_headquarter', '') }}" 
                                        placeholder="Headquarter" oninput="this.value=this.value.toUpperCase()" required readonly>
                                    <label for="app_headquarter" class="required">Headquarter</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control numeric_positive" id="doj" name="doj" 
                                        value="{{ $officialInfo['doj'] ?? $hrmsData['dateOfJoining'] ?? old('doj', '') }}" 
                                        placeholder="Date of Joining" required autocomplete="off">
                                    <label for="doj" class="required">Date of Joining</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control numeric_positive" id="dor" name="dor" 
                                        value="{{ $officialInfo['dor'] ?? $hrmsData['dateOfRetirement'] ?? old('dor', '') }}" 
                                        placeholder="Date of Retirement(According to Service Book)" required autocomplete="off">
                                    <label for="dor" class="required">Date of Retirement(According to Service Book)</label>
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
                                        value="{{ $officialInfo['office_name'] ?? $hrmsData['officeName'] ?? old('office_name', '') }}" 
                                        placeholder="Name of the Office" oninput="this.value=this.value.toUpperCase()" required readonly>
                                    <label for="office_name" class="required">Name of the Office</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <textarea class="form-control" id="office_street" name="office_street" 
                                        placeholder="Address" oninput="this.value=this.value.toUpperCase()" rows="4" required readonly style="height: 85px">{{ $officialInfo['office_street'] ?? $hrmsData['officeStreetCharacter'] ?? old('office_street', '') }}</textarea>
                                    <label for="office_street" class="required">Address</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="office_city" name="office_city" 
                                        value="{{ $officialInfo['office_city_town_village'] ?? $hrmsData['officeCityTownVillage'] ?? old('office_city', '') }}" 
                                        placeholder="City/Town/Village" oninput="this.value=this.value.toUpperCase()" required readonly>
                                    <label for="office_city" class="required">City / Town / Village</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="office_post_office" name="office_post_office" 
                                        value="{{ $officialInfo['office_post_office'] ?? $hrmsData['officePostOffice'] ?? old('office_post_office', '') }}" 
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
                                        value="{{ $officialInfo['office_pin_code'] ?? $hrmsData['officePinCode'] ?? old('office_pincode', '') }}" 
                                        placeholder="Pincode" maxlength="6" required readonly>
                                    <label for="office_pincode" class="required">Pincode</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="text" class="form-control numeric_positive" id="office_phone_no" name="office_phone_no" 
                                        value="{{ $officialInfo['office_phone_no'] ?? $hrmsData['mobileNo'] ?? old('office_phone_no', '') }}" 
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
                                        <option value="" {{ empty($hrmsData['ddoDistrictCode'] ?? old('district')) ? 'selected' : '' }}>- Select -</option>
                                        @foreach($districts as $code => $name)
                                            <option value="{{ $code }}" {{ ($hrmsData['ddoDistrictCode'] ?? old('district')) == $code ? 'selected' : '' }}>
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
                                        <option value="" {{ empty($officialInfo['designation'] ?? old('designation')) ? 'selected' : '' }}>- Select -</option>
                                        @foreach($ddoDesignations as $id => $name)
                                            <option value="{{ $id }}" {{ ($officialInfo['designation'] ?? old('designation')) == $id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="designation" class="required">DDO Designation</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Housing Preference Section --}}
                    <div class="form-section mt-4">
                        <h5 class="mb-3"><i class="fa fa-home me-2"></i> Housing Preference</h5>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="preference_selector" name="preference_selector" required>
                                        <option value="0" {{ ($existingAppData['preference_selector'] ?? old('preference_selector', '0')) == '0' ? 'selected' : '' }}>No Preference</option>
                                        <option value="1" {{ ($existingAppData['preference_selector'] ?? old('preference_selector', '0')) == '1' ? 'selected' : '' }}>Select Housing Preference</option>
                                    </select>
                                    <label for="preference_selector" class="required">Do you want to select housing preference?</label>
                                </div>
                            </div>
                        </div>

                        <div id="housing-preference-wrapper" class="row g-3" style="{{ ($existingAppData['preference_selector'] ?? old('preference_selector', '0')) == '1' ? '' : 'display:none;' }}">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="first_preference" name="first_preference" disabled>
                                        <option value="" {{ empty($existingAppData['preferences']['preference_1'] ?? old('first_preference')) ? 'selected' : '' }}>- Select -</option>
                                        @foreach($housingEstates as $id => $name)
                                            @if($id !== '')
                                                <option value="{{ $id }}" {{ ($existingAppData['preferences']['preference_1'] ?? old('first_preference')) == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label for="first_preference" class="required">First Preference</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="second_preference" name="second_preference" disabled>
                                        <option value="" {{ empty($existingAppData['preferences']['preference_2'] ?? old('second_preference')) ? 'selected' : '' }}>- Select -</option>
                                        @foreach($housingEstates as $id => $name)
                                            @if($id !== '')
                                                <option value="{{ $id }}" {{ ($existingAppData['preferences']['preference_2'] ?? old('second_preference')) == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label for="second_preference">Second Preference</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="third_preference" name="third_preference" disabled>
                                        <option value="" {{ empty($existingAppData['preferences']['preference_3'] ?? old('third_preference')) ? 'selected' : '' }}>- Select -</option>
                                        @foreach($housingEstates as $id => $name)
                                            @if($id !== '')
                                                <option value="{{ $id }}" {{ ($existingAppData['preferences']['preference_3'] ?? old('third_preference')) == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label for="third_preference">Third Preference</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="fourth_preference" name="fourth_preference" disabled>
                                        <option value="" {{ empty($existingAppData['preferences']['preference_4'] ?? old('fourth_preference')) ? 'selected' : '' }}>- Select -</option>
                                        @foreach($housingEstates as $id => $name)
                                            @if($id !== '')
                                                <option value="{{ $id }}" {{ ($existingAppData['preferences']['preference_4'] ?? old('fourth_preference')) == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label for="fourth_preference">Fourth Preference</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="fifth_preference" name="fifth_preference" disabled>
                                        <option value="" {{ empty($existingAppData['preferences']['preference_5'] ?? old('fifth_preference')) ? 'selected' : '' }}>- Select -</option>
                                        @foreach($housingEstates as $id => $name)
                                            @if($id !== '')
                                                <option value="{{ $id }}" {{ ($existingAppData['preferences']['preference_5'] ?? old('fifth_preference')) == $id ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label for="fifth_preference">Fifth Preference</label>
                                </div>
                            </div>
                        </div>      
                    </div>

                    {{-- Allotment Category Section --}}
                    <div class="form-section mt-4">
                        <h5 class="mb-3"><i class="fa fa-list me-2"></i> Allotment Category</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="rhe_flat_type" name="rhe_flat_type" 
                                        value="{{ $flatType ?? $existingAppData['rhe_flat_type'] ?? old('rhe_flat_type', '') }}" 
                                        placeholder="Flat TYPE" readonly required>
                                    <label for="rhe_flat_type" class="required">Flat TYPE</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="reason" name="reason" required>
                                        <option value="" {{ empty($existingAppData['allotment_category'] ?? old('reason')) ? 'selected' : '' }}>- Select -</option>
                                        @foreach($allotmentCategories as $value => $label)
                                            @if($value !== '')
                                                <option value="{{ $value }}" {{ ($existingAppData['allotment_category'] ?? old('reason')) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <label for="reason" class="required">Allotment Reason</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Document Upload Section --}}
                    <div class="form-section mt-4" id="document_upload_section" style="display: none;">
                        <h5 class="mb-3"><i class="fa fa-upload me-2"></i> Upload Documents</h5>
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="file" class="form-control" id="extra_doc" name="extra_doc" 
                                        accept=".pdf" 
                                        onchange="validateFileUpload(this)">
                                    <label for="extra_doc" class="required">Upload Allotment Reason Supporting Document</label>
                                    <small class="form-text text-muted">
                                        <b>Allowed Extension: pdf<br>Maximum File Size: 1 MB</b>
                                    </small>
                                    <div id="file_error" class="text-danger mt-2" style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="row mt-4">
                        <div class="col-12 border-top pt-3">
                            <button type="button" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder" 
                                onclick="submitApplication()">
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
            yearRange: "1947:" + new Date().getFullYear(),
            autoSize: true
        });

        $("#dor").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            yearRange: "1947:" + (new Date().getFullYear() + 20),
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

        // Pay Band change - load flat type and allotment categories
        $('#pay_band').on('change', function() {
            const payBandId = $(this).val();
            const basicPay = $('#pay_in').val();
            
            if (payBandId && basicPay) {
                loadFlatTypeAndCategories(payBandId, basicPay);
            }
        });

        // Preference selector change - show/hide preferences
        $('#preference_selector').on('change', function() {
            const selector = $(this).val();
            if (selector == '1') {
                $('#housing-preference-wrapper').show();
                $('#first_preference').prop('required', true);
            } else {
                $('#housing-preference-wrapper').hide();
                $('#first_preference').prop('required', false);
            }
        });

        // Allotment reason change - show/hide document upload
        $('#reason').on('change', function() {
            const reason = $(this).val();
            const documentRequiredReasons = [
                'Transfer',
                'Legal Heir',
                'Physically Handicaped Or Serious Illness',
                'Recommended',
                'Single Earning Lady',
                'Judicial Officer On Transfer'
            ];
            
            if (documentRequiredReasons.includes(reason)) {
                $('#document_upload_section').show();
                $('#extra_doc').prop('required', true);
            } else {
                $('#document_upload_section').hide();
                $('#extra_doc').prop('required', false);
                $('#extra_doc').val('');
            }
        });

        // Trigger on page load if reason is already selected
        if ($('#reason').val()) {
            $('#reason').trigger('change');
        }  
    });

    function toggleHousingPreferences() { // Function updated by Subham dt.22-12-2025
        const selector = $('#preference_selector').val();

        if (selector === '1') {
            $('#housing-preference-wrapper').show();
            $('#housing-preference-wrapper select').prop('disabled', false);
            $('#first_preference').prop('required', true);
            $('#second_preference, #third_preference, #fourth_preference, #fifth_preference').prop('required', false);
            loadHousingEstates();
        } else {
            $('#housing-preference-wrapper').hide();
            $('#housing-preference-wrapper select')
                .prop('disabled', true)
                .val('');
            $('#first_preference').prop('required', false);
        }
    }

    $('#preference_selector').on('change', toggleHousingPreferences);

    // Run once on page load (edit form support)
    toggleHousingPreferences();     

    function updatePreferenceOptions() {
        const selectedValues = [];

        // Collect selected values
        $('#housing-preference-wrapper select').each(function () {
            const val = $(this).val();
            if (val) {
                selectedValues.push(val);
            }
        });

        // Update options in each dropdown
        $('#housing-preference-wrapper select').each(function () {
            const currentSelect = $(this);
            const currentValue = currentSelect.val();

            currentSelect.find('option').each(function () {
                const optionValue = $(this).val();

                if (!optionValue) return; // skip empty option

                // Hide if selected in another dropdown
                if (selectedValues.includes(optionValue) && optionValue !== currentValue) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
    }  
    $('#housing-preference-wrapper select').on('change', updatePreferenceOptions); 
    
    function toggleDocumentUpload() {
        const selectedReason = $('#reason').val();

        // Array of reasons that require document upload
        const reasonsRequiringDoc = [
            'Transfer',
            'Legal Heir',
            'Physically Handicaped Or Serious Illness',
            'Recommended',
            'Single Earning Lady',
            'Judicial Officer On Transfer'
        ];

        if (reasonsRequiringDoc.includes(selectedReason)) {
            $('#document_upload_section').show();
            $('#extra_doc').prop('required', true);
        } else {
            $('#document_upload_section').hide();
            $('#extra_doc').prop('required', false);
            $('#extra_doc').val('');
            $('#file_error').hide();
        }
    }
    $('#reason').on('change', toggleDocumentUpload);

    toggleDocumentUpload(); 
    
    function loadDesignations(districtCode, selectedDesignation = '') {
        if(!districtCode) {
            $('#designation').html('<option value="">- Select -</option>');
            return;
        }

        $.ajax({
            url: '{{ route("common-application.ddo-designations") }}',
            method: 'GET',
            data: { district_code: districtCode },
            success: function(response) {
                if(response.status === 'success') {
                    let options = '<option value="">- Select -</option>';
                    $.each(response.data, function(id, name) {
                        options += `<option value="${id}" ${id == selectedDesignation ? 'selected' : ''}>${name}</option>`;
                    });
                    $('#designation').html(options);
                }
            },
            error: function() {
                alert('Failed to load designations.');
            }
        });
    }

    // On district change
    $('#district').on('change', function() {
        const districtCode = $(this).val();
        loadDesignations(districtCode);
    });

    // On page load, if district is already selected, pre-fill designation
    const districtCode = $('#district').val();
    const selectedDesignation = '{{ $officialInfo["designation"] ?? old("designation") }}';
    if(districtCode) {
        loadDesignations(districtCode, selectedDesignation);
    }
    
    // Added by Subham dt.22-12-2025 // End 

    function loadFlatTypeAndCategories(payBandId, basicPay) {
        $.ajax({
            url: '{{ route('new-application.flat-type-categories') }}',
            method: 'GET',
            data: {
                pay_band_id: payBandId,
                basic_pay: basicPay
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#rhe_flat_type').val(response.data.flat_type);
                    
                    // Update allotment categories
                    const select = $('#reason');
                    select.html('<option value="">- Select -</option>');
                    for (const [value, label] of Object.entries(response.data.allotment_categories)) {
                        if (value !== '') {
                            select.append($('<option></option>').val(value).text(label));
                        }
                    }
                }
            },
            error: function() {
                alert('Failed to load flat type and categories');
            }
        });
    }

    function loadHousingEstates() {
        const payBandId = $('#pay_band').val();
        const treasuryId = '{{ $treasuryId ?? '' }}';
        
        if (!payBandId || !treasuryId) {
            return;
        }

        $.ajax({
            url: '{{ route('new-application.housing-estates') }}',
            method: 'GET',
            data: {
                pay_band_id: payBandId,
                treasury_id: treasuryId
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Update all preference dropdowns
                    ['first', 'second', 'third', 'fourth', 'fifth'].forEach(function(pref) {
                        const select = $('#' + pref + '_preference');
                        const currentValue = select.val();
                        select.html('<option value="">- Select -</option>');
                        for (const [id, name] of Object.entries(response.data)) {
                            if (id !== '') {
                                const option = $('<option></option>').val(id).text(name);
                                if (id == currentValue) {
                                    option.prop('selected', true);
                                }
                                select.append(option);
                            }
                        }
                    });
                }
            },
            error: function() {
                alert('Failed to load housing estates');
            }
        });
    }
    updatePreferenceOptions();  // Added by Subham dt.22-12-2025

    function validateFileUpload(input) {
        const file = input.files[0];
        const errorDiv = $('#file_error');
        
        if (!file) {
            errorDiv.hide();
            return;
        }

        // Check file type
        if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
            errorDiv.text('Only PDF files are allowed.').show();
            input.value = '';
            return;
        }

        // Check file size (1MB = 1048576 bytes)
        if (file.size > 1048576) {
            errorDiv.text('File size exceeds 1MB limit.').show();
            input.value = '';
            return;
        }

        errorDiv.hide();
    }

    function submitApplication() {
        // Validate form
        if (!validateForm()) {
            return;
        }

        $('#form_action').val('applied');
        if (confirm('Are you sure you want to submit the form? Once the form submitted the data can\'t be edited.')) {
            $('#new-application-form').submit();
        }
    }

    function validateForm() {
        // Check if preference selector is 1, then first preference is required
        if ($('#preference_selector').val() == '1' && !$('#first_preference').val()) {
            alert('At least First preference is required.');
            return false;
        }

        // Check if document is required
        const reason = $('#reason').val();
        const documentRequiredReasons = [
            'Transfer',
            'Legal Heir',
            'Physically Handicaped Or Serious Illness',
            'Recommended',
            'Single Earning Lady',
            'Judicial Officer On Transfer'
        ];
        
        if (documentRequiredReasons.includes(reason)) {
            if (!$('#extra_doc').val()) {
                alert('Please Upload Allotment Reason Supporting Document.');
                return false;
            }
        } else {
            // If reason doesn't require document but document is uploaded, show error
            if ($('#extra_doc').val()) {
                alert('You have to choose Allotment Reason.');
                return false;
            }
        }

        return true;
    }
</script>
@endpush

