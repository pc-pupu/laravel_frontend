@extends('housingTheme.layouts.app')

@section('title', 'User Tagging Form')

@section('content')

<div class="dashboard">
    {{-- <div class="sidebar d-flex flex-column p-3">
        <a href="{{ url('/') }}" class="d-flex flex-column align-items-center mb-5 text-center text-decoration-none">
            <img src="{{ asset('housingTheme/images/wb-logo.png') }}" class="img-fluid" alt="e-Allotment of Rental Housing Estate" onerror="this.src='https://via.placeholder.com/150x150?text=WB+Logo'">
            <div class="dashboard-logo">
                <div class="fs-5 fw-semibold lh-1">e-Allotment of Rental Housing Estate</div>
                <small>Housing Department <br> Government of West Bengal</small>
            </div>
        </a>
    </div> --}}
    @if($hasSubmitted)
        {{-- User has already submitted - Show message instead of form --}}
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    {{-- <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i> --}}
                    <i class="fa fa-check-circle" style="font-size: 4rem; color: rgb(23, 138, 99);" aria-hidden="true"></i>
                </div>
                <h4 class="mb-3">Application Already Submitted</h4>
                
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @else
                    <p class="lead text-muted">
                        Your tagging request has been submitted successfully.<br>
                        Please wait for the departmental approval.
                    </p>
                @endif

                <div class="mt-4">
                    <form method="POST" action="{{ route('logout') }}" class="btn btn-info px-4">
                        @csrf
                        <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="fa fa-home" style="font-size:24px"> Exit</i>
                        </a>
                    </form>
                    {{-- <a href="{{ route('logout') }}" class="btn btn-info px-4">
                        <i class="fa fa-home" style="font-size:24px"> Exit</i> 
                    </a> --}}
                </div>
            </div>
        </div>
    @else

        <div id="content-wrapper" class="content-wrapper">
            <div class="main-content p-5 min-vh-100">
                @if(isset($checkExist) && $checkExist == 0)
                    {{-- Show form and links if hrms_id doesn't exist in tagging table --}}
                    <div class="counter-box p-3 rounded mb-3 position-relative shadow-sm row">
                        <h6 class="">
                            <b class="text-danger">If you have already submitted your flat allotment application in physical form to the Housing Department, Government of West Bengal, click here to link it with your HRMS code
                            <a href="{{ route('existing-applicant.search') }}" class="btn btn-success btn-sm">Click Here</a>
                            </b>
                        </h6>
                         <h6 class="mt-3">
                             <b class="text-danger">Continue to Dashboard for Fresh Online Application & Related Activities
                             <a href="{{ route('dashboard') }}" class="btn btn-success btn-sm" id="my-button" onclick="return false;">Click to Continue</a>
                             </b>
                         </h6>
                    </div>
                    <div class="pt-5">
                        <h5><b>If you are currently occupying a rental housing flat alloted by the Housing Department Govt. of WB, please fill the form for verification and click Submit.</b></h5>
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($hasSubmitted)
                            {{-- User has already submitted - Show message --}}
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <div class="mb-4">
                                        <i class="fa fa-check-circle" style="font-size: 4rem; color: rgb(23, 138, 99);" aria-hidden="true"></i>
                                    </div>
                                    <h4 class="mb-3">Application Already Submitted</h4>
                                    <p class="lead text-muted">
                                        Your tagging request has been submitted successfully.<br>
                                        Please wait for the departmental approval.
                                    </p>
                                </div>
                            </div>
                        @else
                            {{-- Show the form --}}
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" action="{{ route('user-tagging.store') }}" id="userTaggingForm">
                                        @csrf

                                        {{-- Personal Information --}}
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="applicant_name" class="form-label">Applicant's Name *</label>
                                                <input type="text" class="form-control" id="applicant_name" name="applicant_name" 
                                                    value="{{ old('applicant_name', $hrmsData['name'] ?? '') }}" 
                                                    style="text-transform: uppercase" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="mobile_no" class="form-label">Mobile No *</label>
                                                <input type="text" class="form-control" id="mobile_no" name="mobile_no" 
                                                    maxlength="10" pattern="[0-9]{10}" 
                                                    value="{{ old('mobile_no', $hrmsData['mobile'] ?? '') }}" 
                                                    title="Please enter a valid 10-digit mobile number" required>
                                                <div class="invalid-feedback">Please enter a valid 10-digit mobile number.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="email" class="form-label">Email ID *</label>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                    value="{{ old('email') }}" style="text-transform: lowercase" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="license_no" class="form-label">Licence No. *</label>
                                                <input type="text" class="form-control" id="license_no" name="license_no" 
                                                    value="{{ old('license_no') }}" style="text-transform: uppercase" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="license_issue_date" class="form-label">License Issue Date *</label>
                                                <input type="text" class="form-control" id="license_issue_date" name="license_issue_date" 
                                                    placeholder="DD/MM/YYYY" value="{{ old('license_issue_date') }}" 
                                                    autocomplete="off" readonly required>
                                                <div class="invalid-feedback">Please enter license issue date.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="license_expiry_date" class="form-label">License Expiry Date</label>
                                                <input type="text" class="form-control" id="license_expiry_date" name="license_expiry_date" 
                                                    placeholder="DD/MM/YYYY" value="{{ old('license_expiry_date') }}" 
                                                    autocomplete="off" readonly>
                                                <div class="invalid-feedback" id="license_expiry_error">
                                                    The license expiry date cannot be more than 3 years after the issue date.
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <label class="form-label text-danger fw-bold">Select "Yes" if you have previously submitted a physical application for Vertical Shifting or Category Shifting. *</label>
                                                <div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="physical_application_vs_cs" 
                                                            id="physical_yes" value="yes" {{ old('physical_application_vs_cs') == 'yes' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="physical_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="physical_application_vs_cs" 
                                                            id="physical_no" value="no" {{ old('physical_application_vs_cs', 'no') == 'no' ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="physical_no">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3" id="physical_app_fields" style="display: none;">
                                            <div class="col-md-6">
                                                <label for="physical_application_no" class="form-label">Physical Application No.</label>
                                                <input type="text" class="form-control" id="physical_application_no" name="physical_application_no" 
                                                    value="{{ old('physical_application_no') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="application_type" class="form-label">Application Type</label>
                                                @php
                                                    $currentApplicationType = old('application_type', '');
                                                @endphp
                                                <select class="form-select" id="application_type" name="application_type">
                                                    <option value="" {{ empty($currentApplicationType) ? 'selected' : '' }}>- Select -</option>
                                                    <option value="VS" {{ $currentApplicationType == 'VS' ? 'selected' : '' }}>Floor Shifting</option>
                                                    <option value="CS" {{ $currentApplicationType == 'CS' ? 'selected' : '' }}>Category Shifting</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- Flat Selection with AJAX Cascading Dropdowns --}}
                                        <h5 class="mt-4 mb-3">Flat Information</h5>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="rhe_name" class="form-label">Name of the RHE *</label>
                                                @php
                                                    $currentRheName = old('rhe_name', '');
                                                @endphp
                                                <select class="form-select" id="rhe_name" name="rhe_name" required>
                                                    <option value="" {{ empty($currentRheName) ? 'selected' : '' }}>- Select -</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4" id="flat_type_container">
                                                <label for="flat_type" class="form-label">Flat Type *</label>
                                                @php
                                                    $currentFlatType = old('flat_type', '');
                                                @endphp
                                                <select class="form-select" id="flat_type" name="flat_type" disabled required>
                                                    <option value="" {{ empty($currentFlatType) ? 'selected' : '' }}>- Select -</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4" id="block_name_container">
                                                <label for="block_name" class="form-label">Name of the Block *</label>
                                                @php
                                                    $currentBlockName = old('block_name', '');
                                                @endphp
                                                <select class="form-select" id="block_name" name="block_name" disabled required>
                                                    <option value="" {{ empty($currentBlockName) ? 'selected' : '' }}>- Select -</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4" id="floor_no_container">
                                                <label for="floor_no" class="form-label">Floor No. *</label>
                                                @php
                                                    $currentFloorNo = old('floor_no', '');
                                                @endphp
                                                <select class="form-select" id="floor_no" name="floor_no" disabled required>
                                                    <option value="" {{ empty($currentFloorNo) ? 'selected' : '' }}>- Select -</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4" id="flat_no_container">
                                                <label for="flat_no" class="form-label">Flat No. *</label>
                                                @php
                                                    $currentFlatNo = old('flat_no', '');
                                                @endphp
                                                <select class="form-select" id="flat_no" name="flat_no" disabled required>
                                                    <option value="" {{ empty($currentFlatNo) ? 'selected' : '' }}>- Select -</option>
                                                </select>
                                            </div>
                                        </div>

                                        <input type="hidden" id="flat_id" name="flat_id" value="{{ old('flat_id') }}">

                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <button type="submit" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    {{-- If hrms_id exists in tagging table, just show messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                @endif

                {{-- Exit Button --}}
                <div class="mt-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn bg-info btn-sm px-5 rounded-pill text-white fw-bolder">
                            Exit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="{{ asset('/assets/housingTheme/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('/assets/housingTheme/jquery/jquery-ui.min.js') }}"></script>
<script>
    // Cookie setting for dashboard redirect (matching Drupal custome.js)
    // This script should always load, not just when form is visible
    $(document).ready(function() {
        $('#my-button').on('click', function(e) {
            e.preventDefault(); // Prevent default link navigation
            e.stopPropagation(); // Stop event bubbling
            
            // Set cookie with proper expiration and attributes
            // Expires in 1 day
            const expires = new Date();
            expires.setTime(expires.getTime() + (24 * 60 * 60 * 1000)); // 24 hours
            
            // Set cookie with all necessary attributes for Laravel to read it
            // SameSite=Lax allows the cookie to be sent with same-site requests
            document.cookie = "user_type=new; path=/; expires=" + expires.toUTCString() + "; SameSite=Lax";
            
            // Verify cookie was set
            console.log('Cookie set. All cookies:', document.cookie);
            
            // Show alert
            alert('You are about to be redirected to the dashboard.');
            
            // Navigate to dashboard after a small delay to ensure cookie is set
            setTimeout(function() {
                window.location.href = "{{ route('dashboard') }}";
            }, 100);
            
            return false; // Additional safeguard
        });
    });
</script>
@if(!$hasSubmitted)
    <script>
        const apiBaseUrl = "{{ config('services.api.base_url') }}";

            // Only load form scripts if form is visible
            @if(isset($checkExist) && $checkExist == 0 && !$hasSubmitted)
            // Toggle physical application fields
            $('input[name="physical_application_vs_cs"]').on('change', function() {
                if ($(this).val() == 'yes') {
                    $('#physical_app_fields').show();
                    $('#physical_application_no, #application_type').prop('required', true);
                } else {
                    $('#physical_app_fields').hide();
                    $('#physical_application_no, #application_type').prop('required', false);
                }
            });

            // Trigger on page load
            if ($('input[name="physical_application_vs_cs"]:checked').val() == 'yes') {
                $('#physical_app_fields').show();
            }

            // Mobile number validation - only digits
            $('#mobile_no').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 10) {
                    this.value = this.value.slice(0, 10);
                }
            });

            // Date picker initialization for License Issue Date
            $('#license_issue_date').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+10',
                maxDate: '0', // Can't be future date
                onSelect: function(dateText) {
                    // Clear expiry date when issue date changes
                    $('#license_expiry_date').val('');
                    
                    // Update expiry date picker min/max based on issue date
                    const issueDate = $.datepicker.parseDate('dd/mm/yy', dateText);
                    const minExpiryDate = new Date(issueDate);
                    minExpiryDate.setDate(minExpiryDate.getDate() + 1); // Next day after issue
                    
                    const maxExpiryDate = new Date(issueDate);
                    maxExpiryDate.setFullYear(maxExpiryDate.getFullYear() + 3); // Exactly 3 years
                    
                    $('#license_expiry_date').datepicker('option', 'minDate', minExpiryDate);
                    $('#license_expiry_date').datepicker('option', 'maxDate', maxExpiryDate);
                }
            });

            // Date picker initialization for License Expiry Date
            $('#license_expiry_date').datepicker({
                dateFormat: 'dd/mm/yy',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+13',
                onSelect: function(dateText) {
                    validateExpiryDate();
                }
            });

            // Validate expiry date when it changes
            $('#license_expiry_date').on('change', function() {
                validateExpiryDate();
            });

            // AJAX Cascading Dropdowns
            $('#rhe_name').on('change', function() {
                const rheId = $(this).val();
                resetDependentDropdowns(['flat_type', 'block_name', 'floor_no', 'flat_no']);
                if (rheId) {
                    loadFlatTypes(rheId);
                }
            });

            $('#flat_type').on('change', function() {
                const rheId = $('#rhe_name').val();
                const flatTypeId = $(this).val();
                resetDependentDropdowns(['block_name', 'floor_no', 'flat_no']);
                if (rheId && flatTypeId) {
                    loadBlocks(rheId, flatTypeId);
                }
            });

            $('#block_name').on('change', function() {
                const rheId = $('#rhe_name').val();
                const flatTypeId = $('#flat_type').val();
                const blockId = $(this).val();
                resetDependentDropdowns(['floor_no', 'flat_no']);
                if (rheId && flatTypeId && blockId) {
                    loadFloors(rheId, flatTypeId, blockId);
                }
            });

            $('#floor_no').on('change', function() {
                const rheId = $('#rhe_name').val();
                const flatTypeId = $('#flat_type').val();
                const blockId = $('#block_name').val();
                const floor = $(this).val();
                resetDependentDropdowns(['flat_no']);
                if (rheId && flatTypeId && blockId && floor) {
                    loadFlats(rheId, flatTypeId, blockId, floor);
                }
            });

            $('#flat_no').on('change', function() {
                $('#flat_id').val($(this).val());
            });

            // Load RHE List on page load
            loadRheList();

            // Form submission validation
            $('#userTaggingForm').on('submit', function(e) {
                let isValid = true;

                // Validate mobile number
                const mobile = $('#mobile_no').val();
                if (!/^[0-9]{10}$/.test(mobile)) {
                    $('#mobile_no').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#mobile_no').removeClass('is-invalid');
                }

                // Validate license dates
                const issueDate = $('#license_issue_date').val();
                const expiryDate = $('#license_expiry_date').val();

                if (!issueDate) {
                    $('#license_issue_date').addClass('is-invalid');
                    isValid = false;
                } else {
                    $('#license_issue_date').removeClass('is-invalid');
                }

                // Validate expiry date if provided
                if (expiryDate && !validateExpiryDate()) {
                    isValid = false;
                }

                // Validate flat selection
                const flatId = $('#flat_id').val();
                if (!flatId) {
                    alert('Please select all flat information (RHE, Flat Type, Block, Floor, and Flat Number)');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
            });
            @endif
        });

        // Validate Expiry Date (cannot be more than 3 years after issue date)
        function validateExpiryDate() {
            const issueDate = $('#license_issue_date').val();
            const expiryDate = $('#license_expiry_date').val();

            if (!issueDate || !expiryDate) {
                $('#license_expiry_date').removeClass('is-invalid');
                return true;
            }

            // Parse dates (DD/MM/YYYY format)
            const issueParts = issueDate.split('/');
            const expiryParts = expiryDate.split('/');

            if (issueParts.length !== 3 || expiryParts.length !== 3) {
                return false;
            }

            const issueDateTime = new Date(issueParts[2], issueParts[1] - 1, issueParts[0]);
            const expiryDateTime = new Date(expiryParts[2], expiryParts[1] - 1, expiryParts[0]);

            // Check if expiry is before issue
            if (expiryDateTime <= issueDateTime) {
                $('#license_expiry_date').addClass('is-invalid');
                $('#license_expiry_error').text('License expiry date must be after issue date.');
                return false;
            }

            // Calculate difference in years, months, and days
            let years = expiryDateTime.getFullYear() - issueDateTime.getFullYear();
            let months = expiryDateTime.getMonth() - issueDateTime.getMonth();
            let days = expiryDateTime.getDate() - issueDateTime.getDate();

            // Adjust for negative months
            if (months < 0) {
                years--;
                months += 12;
            }

            // Adjust for negative days
            if (days < 0) {
                months--;
                const prevMonth = new Date(expiryDateTime.getFullYear(), expiryDateTime.getMonth(), 0);
                days += prevMonth.getDate();
            }

            // Check if more than 3 years OR exactly 3 years with additional months/days
            if (years > 3 || (years === 3 && (months > 0 || days > 0))) {
                $('#license_expiry_date').addClass('is-invalid');
                $('#license_expiry_error').text('The license expiry date cannot be more than 3 years after the issue date.');
                return false;
            }

            $('#license_expiry_date').removeClass('is-invalid');
            return true;
        }

        // Load RHE List
        function loadRheList() {
            $.ajax({
                url: apiBaseUrl + '/user-tagging/helpers/rhe-list',
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">- Select -</option>';
                        response.data.forEach(function(item) {
                            options += `<option value="${item.estate_id}">${item.label}</option>`;
                        });
                        $('#rhe_name').html(options);
                    }
                },
                error: function() {
                    alert('Failed to load RHE list');
                }
            });
        }

        // Load Flat Types
        function loadFlatTypes(rheId) {
            $.ajax({
                url: apiBaseUrl + '/user-tagging/helpers/flat-types/' + rheId,
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">- Select -</option>';
                        response.data.forEach(function(item) {
                            options += `<option value="${item.flat_type_id}">${item.flat_type}</option>`;
                        });
                        $('#flat_type').html(options).prop('disabled', false);
                    } else {
                        $('#flat_type').html('<option value="">No Data Found</option>').prop('disabled', true);
                    }
                },
                error: function() {
                    $('#flat_type').html('<option value="">Error loading data</option>').prop('disabled', true);
                }
            });
        }

        // Load Blocks
        function loadBlocks(rheId, flatTypeId) {
            $.ajax({
                url: apiBaseUrl + '/user-tagging/helpers/blocks/' + rheId + '/' + flatTypeId,
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">- Select -</option>';
                        response.data.forEach(function(item) {
                            options += `<option value="${item.block_id}">${item.block_name}</option>`;
                        });
                        $('#block_name').html(options).prop('disabled', false);
                    } else {
                        $('#block_name').html('<option value="">No Data Found</option>').prop('disabled', true);
                    }
                },
                error: function() {
                    $('#block_name').html('<option value="">Error loading data</option>').prop('disabled', true);
                }
            });
        }

        // Load Floors
        function loadFloors(rheId, flatTypeId, blockId) {
            $.ajax({
                url: apiBaseUrl + '/user-tagging/helpers/floors/' + rheId + '/' + flatTypeId + '/' + blockId,
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">- Select -</option>';
                        response.data.forEach(function(item) {
                            options += `<option value="${item}">${item}</option>`;
                        });
                        $('#floor_no').html(options).prop('disabled', false);
                    } else {
                        $('#floor_no').html('<option value="">No Data Found</option>').prop('disabled', true);
                    }
                },
                error: function() {
                    $('#floor_no').html('<option value="">Error loading data</option>').prop('disabled', true);
                }
            });
        }

        // Load Flats
        function loadFlats(rheId, flatTypeId, blockId, floor) {
            $.ajax({
                url: apiBaseUrl + '/user-tagging/helpers/flats/' + rheId + '/' + flatTypeId + '/' + blockId + '/' + floor,
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        let options = '<option value="">- Select -</option>';
                        response.data.forEach(function(item) {
                            options += `<option value="${item.flat_id}">${item.flat_no}</option>`;
                        });
                        $('#flat_no').html(options).prop('disabled', false);
                    } else {
                        $('#flat_no').html('<option value="">No Data Found</option>').prop('disabled', true);
                    }
                },
                error: function() {
                    $('#flat_no').html('<option value="">Error loading data</option>').prop('disabled', true);
                }
            });
        }

        // Reset dependent dropdowns
        function resetDependentDropdowns(fields) {
            fields.forEach(function(field) {
                $('#' + field).html('<option value="">- Select -</option>').prop('disabled', true);
            });
            $('#flat_id').val('');
        }
    </script>

@endif
@endpush
@endsection
