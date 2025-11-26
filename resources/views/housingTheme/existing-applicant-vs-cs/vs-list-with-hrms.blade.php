@extends('housingTheme.layouts.app')
@section('title', 'VS List (With HRMS)')
@section('page-header', 'Existing Applicant\'s List for Floor Shifting (with HRMS ID)')


@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="search-filter-box">
                    <form method="GET" action="{{ route('existing-applicant-vs-cs.vs-list-with-hrms') }}" id="filterForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" id="rhe_name" name="rhe_name">
                                        <option value="">- Select RHE -</option>
                                    </select>
                                    <label for="rhe_name">Name of the RHE</label>
                                </div>
                            </div>
                            <div class="col-md-4" id="flat_type_replace">
                                <!-- Flat Type will be loaded here -->
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-search me-2"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-container">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Sl. No.</th>
                                <th>Application No</th>
                                <th>Applicant Name</th>
                                <th>Date of Application</th>
                                <th>HRMS ID</th>
                                <th>Mobile No.</th>
                                <th>Gender</th>
                                <th>Current Occupancy Details</th>
                                <th>Flat Type Applied For</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $index => $app)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $app['application_no'] ?? 'N/A' }}</td>
                                    <td>{{ $app['applicant_name'] ?? 'N/A' }}</td>
                                    <td>{{ $app['date_of_application'] ?? 'N/A' }}</td>
                                    <td>{{ $app['hrms_id'] ?? 'N/A' }}</td>
                                    <td>{{ $app['mobile_no'] ?? 'N/A' }}</td>
                                    <td>{{ $app['gender'] ?? 'N/A' }}</td>
                                    <td>{{ ($app['estate_name'] ?? '') . ', ' . ($app['block_name'] ?? '') . ', ' . ($app['flat_no'] ?? '') }}</td>
                                    <td>{{ $app['flat_type'] ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('existing-applicant-vs-cs.edit', encrypt($app['online_application_id'])) }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No applications found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('/assets/housingTheme/jquery/jquery.min.js') }}"></script>
<script>
$(document).ready(function() {
    const backendUrl = '{{ env("BACKEND_API") }}';
    const token = '{{ session("api_token") }}';

    // Load RHE list
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
                for (const id in response.data) {
                    select.append('<option value="' + id + '">' + response.data[id] + '</option>');
                }
                select.val('{{ $filters["rhe_name"] ?? "" }}');
                if (select.val()) {
                    select.trigger('change');
                }
            }
        }
    });

    // Load flat types
    $('#rhe_name').on('change', function() {
        const rheId = $(this).val();
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
                    html += '<select class="form-select" id="flat_type" name="flat_type">';
                    html += '<option value="">- Select Flat Type -</option>';
                    for (const id in response.data) {
                        html += '<option value="' + id + '">' + response.data[id] + '</option>';
                    }
                    html += '</select>';
                    html += '<label for="flat_type">Flat Type</label>';
                    html += '</div>';
                    $('#flat_type_replace').html(html);
                    $('#flat_type').val('{{ $filters["flat_type"] ?? "" }}');
                }
            }
        });
    });

    // Trigger change if RHE is already selected
    if ($('#rhe_name').val()) {
        $('#rhe_name').trigger('change');
    }
});
</script>
@endpush

