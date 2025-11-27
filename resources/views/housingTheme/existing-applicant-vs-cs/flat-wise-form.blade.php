@extends('housingTheme.layouts.app')
@section('title', 'Flat wise Existing Applicant Details')
@section('page-header', 'Flat wise Existing Applicant Details')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <h3><i class="fa fa-building me-2"></i> Flat wise Existing Applicant Details</h3>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form id="flatWiseForm">
                    <div class="form-section-vs-cs">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="rhe_name" name="rhe_name" required>
                                        <option value="">- Select RHE -</option>
                                    </select>
                                    <label for="rhe_name">Name of the RHE</label>
                                </div>
                            </div>
                            <div class="col-md-4" id="flat_type_replace">
                                <!-- Flat Type will be loaded here via AJAX -->
                            </div>
                            <div class="col-md-4" id="block_name_replace">
                                <!-- Block Name will be loaded here via AJAX -->
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4" id="flat_no_replace">
                                <!-- Flat No will be loaded here via AJAX -->
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" id="flat_list_replace">
                        <!-- Applicant details will be loaded here via AJAX -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('/assets/housingTheme/jquery/jquery.min.js') }}"></script>
<script>
    window.appBaseUrl = "{{ url('/') }}"; 
    $(document).ready(function() {
        const backendUrl = '{{ env("BACKEND_API") }}';
        const token = '{{ session("api_token") }}';

        // Load RHE list
        function loadRheList() {
            $.ajax({
                url: backendUrl + '/api/existing-applicant-vs-cs-helpers/rhe-list',
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        const select = $('#rhe_name');
                        select.html('<option value="">- Select RHE -</option>');
                        $.each(response.data, function(key, value) {
                            select.append('<option value="' + key + '">' + value + '</option>');
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error loading RHE list:', xhr);
                }
            });
        }

        // Load flat types
        function loadFlatTypes(rheId) {
            if (!rheId) {
                $('#flat_type_replace').html('');
                return;
            }

            $.ajax({
                url: backendUrl + '/api/existing-applicant-vs-cs-helpers/flat-types',
                method: 'GET',
                data: { rhe_id: rheId },
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        let html = '<div class="form-floating">';
                        html += '<select class="form-select" id="flat_type" name="flat_type" required>';
                        html += '<option value="">- Select Flat Type -</option>';
                        $.each(response.data, function(key, value) {
                            html += '<option value="' + key + '">' + value + '</option>';
                        });
                        html += '</select>';
                        html += '<label for="flat_type">Flat Type</label>';
                        html += '</div>';
                        $('#flat_type_replace').html(html);
                        $('#block_name_replace').html('');
                        $('#flat_no_replace').html('');
                        $('#flat_list_replace').html('');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading flat types:', xhr);
                }
            });
        }

        // Load blocks
        function loadBlocks(rheId, flatTypeId) {
            if (!rheId || !flatTypeId) {
                $('#block_name_replace').html('');
                return;
            }

            $.ajax({
                url: backendUrl + '/api/existing-applicant-vs-cs-helpers/blocks',
                method: 'GET',
                data: { rhe_id: rheId, flat_type_id: flatTypeId },
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        let html = '<div class="form-floating">';
                        html += '<select class="form-select" id="block_name" name="block_name" required>';
                        html += '<option value="">- Select Block -</option>';
                        $.each(response.data, function(key, value) {
                            html += '<option value="' + key + '">' + value + '</option>';
                        });
                        html += '</select>';
                        html += '<label for="block_name">Name of the Block</label>';
                        html += '</div>';
                        $('#block_name_replace').html(html);
                        $('#flat_no_replace').html('');
                        $('#flat_list_replace').html('');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading blocks:', xhr);
                }
            });
        }

        // Load flats
        function loadFlats(rheId, flatTypeId, blockId) {
            if (!rheId || !flatTypeId || !blockId) {
                $('#flat_no_replace').html('');
                return;
            }

            $.ajax({
                url: backendUrl + '/api/existing-applicant-vs-cs-helpers/flats',
                method: 'GET',
                data: { rhe_id: rheId, flat_type_id: flatTypeId, block_id: blockId },
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        let html = '<div class="form-floating">';
                        html += '<select class="form-select" id="flat_no" name="flat_no" required>';
                        html += '<option value="">- Select Flat No -</option>';
                        $.each(response.data, function(key, value) {
                            html += '<option value="' + key + '">' + value + '</option>';
                        });
                        html += '</select>';
                        html += '<label for="flat_no">Flat No.</label>';
                        html += '</div>';
                        $('#flat_no_replace').html(html);
                        $('#flat_list_replace').html('');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading flats:', xhr);
                }
            });
        }

        // Load applicant details
        function loadApplicantDetails(rheId, flatTypeId, blockId, flatId) {
            if (!rheId || !flatTypeId || !blockId || !flatId) {
                $('#flat_list_replace').html('');
                return;
            }

            $.ajax({
                url: backendUrl + '/api/existing-applicant-vs-cs/flat-details',
                method: 'GET',
                data: {
                    rhe_name: rheId,
                    flat_type: flatTypeId,
                    block_name: blockId,
                    flat_id: flatId
                },
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        const data = response.data;
                        let html = '<div class="table-bottom">';
                        html += '<table class="table info-table">';
                        html += '<tr><th colspan="2">Application Information</th></tr>';
                        html += '<tr><td>Applicant Name:</td><td>' + (data.applicant_name || 'Not Available') + '</td></tr>';
                        html += '<tr><td>Estate Name:</td><td>' + (data.estate_name || 'Not Available') + '</td></tr>';
                        html += '<tr><td>Flat Type:</td><td>' + (data.flat_type || 'Not Available') + '</td></tr>';
                        html += '<tr><td>Block Name:</td><td>' + (data.block_name || 'Not Available') + '</td></tr>';
                        html += '<tr><td>Flat Number:</td><td>' + (data.flat_no || 'Not Available') + '</td></tr>';
                        html += '</table>';
                            console.log(response.encrypted_uid);
                            console.log(response.already_applied);
                            
                        if (!response.already_applied && response.encrypted_uid) {
                            html += '<div class="text-right mt-3">';

                            const encryptedUid = encodeURIComponent(response.encrypted_uid);
                            const createUrl = window.appBaseUrl + '/legay-vs-or-cs-form/' + encryptedUid;

                            html += '<a href="' + createUrl + '" class="btn btn-primary">Next</a>';
                            html += '</div>';
                        }
                        else {
                            html += '<div class="text-right mt-3">';
                            html += '<span style="color:red;">This Existing Applicant already applied for Vertical Shifting or Category Shifting</span>';
                            html += '</div>';
                        }

                        html += '</div>';
                        $('#flat_list_replace').html(html);
                    } else {
                        $('#flat_list_replace').html('<div class="alert alert-warning">' + (response.message || 'No applicant found for this flat.') + '</div>');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading applicant details:', xhr);
                    $('#flat_list_replace').html('<div class="alert alert-danger">Error loading applicant details.</div>');
                }
            });
        }

        // Event handlers
        $(document).on('change', '#rhe_name', function() {
            const rheId = $(this).val();
            loadFlatTypes(rheId);
        });

        $(document).on('change', '#flat_type', function() {
            const rheId = $('#rhe_name').val();
            const flatTypeId = $(this).val();
            loadBlocks(rheId, flatTypeId);
        });

        $(document).on('change', '#block_name', function() {
            const rheId = $('#rhe_name').val();
            const flatTypeId = $('#flat_type').val();
            const blockId = $(this).val();
            loadFlats(rheId, flatTypeId, blockId);
        });

        $(document).on('change', '#flat_no', function() {
            const rheId = $('#rhe_name').val();
            const flatTypeId = $('#flat_type').val();
            const blockId = $('#block_name').val();
            const flatId = $(this).val();
            loadApplicantDetails(rheId, flatTypeId, blockId, flatId);
        });

        // Initial load
        loadRheList();
    });
</script>
@endpush

