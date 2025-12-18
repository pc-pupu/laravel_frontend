@extends('housingTheme.layouts.app')

@section('title', 'List of Allottees for Approval')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-check-circle me-2"></i> List of Allottees for Approval</h3>
                            <p class="mb-0">Approve, reject, or hold allottee applications</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <form method="GET" action="{{ route('allotment-list.approve') }}" id="allotment-approve-form">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="allotment_process_date" id="allotment_process_date" class="form-select" required>
                                    <option value="">Select Allotment Process Date</option>
                                    @foreach($processDates as $date)
                                        <option value="{{ $date['value'] }}" {{ old('allotment_process_date', $allotmentProcessDate) == $date['value'] ? 'selected' : '' }}>
                                            {{ $date['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="allotment_process_date">Allotment Process Date</label>
                            </div>
                        </div>
                        <div class="col-md-4" id="allotment_process_no_replace">
                            <div class="form-floating">
                                <select name="allotment_process_no" id="allotment_process_no" class="form-select" {{ !$allotmentProcessDate ? 'disabled' : '' }}>
                                    <option value="">Select Allotment Process No.</option>
                                    @foreach($processNumbers as $processNo)
                                        <option value="{{ $processNo['value'] }}" {{ old('allotment_process_no', $allotmentProcessNo) == $processNo['value'] ? 'selected' : '' }}>
                                            {{ $processNo['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="allotment_process_no">Allotment Process No.</label>
                            </div>
                        </div>
                        <div class="col-md-4" id="allotment_process_type_replace">
                            <div class="form-floating">
                                <select name="allotment_process_type" id="allotment_process_type" class="form-select" {{ !$allotmentProcessDate || !$allotmentProcessNo ? 'disabled' : '' }}>
                                    <option value="">Select Allotment Process Type</option>
                                    <option value="NAL" {{ old('allotment_process_type', $allotmentProcessType) == 'NAL' ? 'selected' : '' }}>New Allotment</option>
                                    <option value="VSAL" {{ old('allotment_process_type', $allotmentProcessType) == 'VSAL' ? 'selected' : '' }}>Floor Shifting</option>
                                    <option value="CSAL" {{ old('allotment_process_type', $allotmentProcessType) == 'CSAL' ? 'selected' : '' }}>Category Shifting</option>
                                    <option value="MSR" {{ old('allotment_process_type', $allotmentProcessType) == 'MSR' ? 'selected' : '' }}>Manual Special Recommendation</option>
                                </select>
                                <label for="allotment_process_type">Allotment Process Type</label>
                            </div>
                        </div>
                    </div>
                </form>

                @if($allotmentProcessDate && $allotmentProcessNo && $allotmentProcessType && count($allottees) > 0)
                    <form method="POST" action="{{ route('allotment-list.update-status') }}" id="allottee-approve-form">
                        @csrf
                        <input type="hidden" name="action" id="action-input" value="approve">
                        <input type="hidden" name="online_application_ids" id="online_application_ids" value="">

                        <div class="row mb-3">
                            <div class="col-md-4 text-start">
                                <button type="button" class="btn btn-success btn-sm px-5 rounded-pill text-white mb-4 fw-bolder w-100" 
                                        onclick="submitAction('approve')">
                                    <i class="fa fa-check me-2"></i> Click to Approve Allotment
                                </button>
                            </div>
                            <div class="col-md-4 text-center">
                                <button type="button" class="btn btn-danger btn-sm px-5 rounded-pill text-white mb-4 fw-bolder w-100" 
                                        onclick="submitAction('reject')">
                                    <i class="fa fa-times me-2"></i> Click to Reject Allotment
                                </button>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" class="btn btn-warning btn-sm px-5 rounded-pill text-white mb-4 fw-bolder w-100" 
                                        onclick="submitAction('hold')">
                                    <i class="fa fa-pause me-2"></i> Click to Hold Allotment
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-list table-striped">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                        </th>
                                        <th>Sl. No.</th>
                                        @if($allotmentProcessType == 'NAL' || $allotmentProcessType == 'MSR')
                                            <th>Name and Designation</th>
                                            <th>1. Application No.<br>2. Place of Posting<br>3. Date of Application</th>
                                            <th>Flat Allotted</th>
                                            <th>Floor</th>
                                            <th>Name of R.H.E.</th>
                                            <th>Name of Block</th>
                                            <th>Date of Retirement</th>
                                            <th>Remarks</th>
                                        @elseif($allotmentProcessType == 'VSAL' || $allotmentProcessType == 'CSAL')
                                            <th>Name</th>
                                            <th>1. Date of Possession<br>2. Date of Application<br>3. Date of Retirement</th>
                                            <th>Allotted Flat No.</th>
                                            <th>Type of Flat</th>
                                            <th>Floor</th>
                                            <th>Name of Block</th>
                                            <th>From Flat No.</th>
                                            <th>To Flat No.</th>
                                            <th>Remarks</th>
                                        @endif
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allottees as $index => $allottee)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_allottees[]" 
                                                       value="{{ $allottee->online_application_id }}" 
                                                       class="allottee-checkbox">
                                            </td>
                                            <td>{{ $index + 1 }}</td>
                                            @if($allotmentProcessType == 'NAL' || $allotmentProcessType == 'MSR')
                                                <td>{{ $allottee->applicant_name }}<br>{{ $allottee->applicant_designation }}</td>
                                                <td>
                                                    1. {{ $allottee->application_no }}<br>
                                                    2. {{ $allottee->applicant_posting_place }}<br>
                                                    3. {{ date('d/m/Y', strtotime($allottee->date_of_application)) }}
                                                </td>
                                                <td>{{ $allottee->flat_no }}</td>
                                                <td>{{ $allottee->floor }}</td>
                                                <td>{{ $allottee->estate_name }}</td>
                                                <td>{{ $allottee->block_name }}</td>
                                                <td>{{ $allottee->date_of_retirement ? date('d/m/Y', strtotime($allottee->date_of_retirement)) : '-' }}</td>
                                                <td>
                                                    @if($allottee->roaster_vacancy_position)
                                                        @php
                                                            $position = $allottee->roaster_vacancy_position;
                                                            $suffix = ($position == 1 || $position == 21) ? 'st' : (($position == 2 || $position == 22) ? 'nd' : (($position == 3 || $position == 23) ? 'rd' : 'th'));
                                                        @endphp
                                                        {{ $position }}{{ $suffix }} Vacancy<br>
                                                        {{ $allottee->allotment_reason }}
                                                    @else
                                                        {{ $allottee->allotment_reason ?? '-' }}
                                                    @endif
                                                </td>
                                            @elseif($allotmentProcessType == 'VSAL' || $allotmentProcessType == 'CSAL')
                                                <td>{{ $allottee->applicant_name }}</td>
                                                <td>
                                                    1. {{ $allottee->possession_date ? date('d/m/Y', strtotime($allottee->possession_date)) : '-' }}<br>
                                                    2. {{ date('d/m/Y', strtotime($allottee->date_of_application)) }}<br>
                                                    3. {{ $allottee->date_of_retirement ? date('d/m/Y', strtotime($allottee->date_of_retirement)) : '-' }}
                                                </td>
                                                <td>{{ $allottee->flat_no }}</td>
                                                <td>{{ $allottee->flat_type }}</td>
                                                <td>{{ $allottee->floor }}</td>
                                                <td>{{ $allottee->block_name }}</td>
                                                <td>{{ $allotmentProcessType == 'VSAL' ? ($allottee->occupied_flat_vs ?? '-') : ($allottee->occupied_flat_cs ?? '-') }}</td>
                                                <td>{{ $allottee->flat_no }}</td>
                                                <td>{{ $allotmentProcessType == 'VSAL' ? 'Floor Shifting' : 'Category Shifting' }}</td>
                                            @endif
                                            <td>{{ $allottee->status_description ?? $allottee->status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                @elseif($allotmentProcessDate && $allotmentProcessNo && $allotmentProcessType && count($allottees) == 0)
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i> No allottees found for the selected criteria.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleSelectAll() {
    var selectAll = document.getElementById('select-all');
    var checkboxes = document.querySelectorAll('.allottee-checkbox');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = selectAll.checked;
    });
}

function submitAction(action) {
    var checkboxes = document.querySelectorAll('.allottee-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Please select at least one allottee.');
        return;
    }

    var confirmMessage = '';
    if (action === 'approve') {
        confirmMessage = 'Are you sure you want to Approve?';
    } else if (action === 'reject') {
        confirmMessage = 'Are you sure you want to Reject?';
    } else if (action === 'hold') {
        confirmMessage = 'Are you sure you want to Hold?';
    }

    if (!confirm(confirmMessage)) {
        return;
    }

    var applicationIds = Array.from(checkboxes).map(function(cb) {
        return parseInt(cb.value);
    });

    document.getElementById('action-input').value = action;
    document.getElementById('online_application_ids').value = JSON.stringify(applicationIds);
    document.getElementById('allottee-approve-form').submit();
}

$(document).ready(function() {
    $('#allotment_process_date').on('change', function() {
        var date = $(this).val();
        if (date) {
            window.location.href = '{{ route("allotment-list.approve") }}?allotment_process_date=' + date;
        } else {
            $('#allotment_process_no').prop('disabled', true).html('<option value="">Select Allotment Process No.</option>');
            $('#allotment_process_type').prop('disabled', true).html('<option value="">Select Allotment Process Type</option>');
        }
    });

    $('#allotment_process_no').on('change', function() {
        var date = $('#allotment_process_date').val();
        var processNo = $(this).val();
        if (date && processNo) {
            window.location.href = '{{ route("allotment-list.approve") }}?allotment_process_date=' + date + '&allotment_process_no=' + processNo;
        }
    });

    $('#allotment_process_type').on('change', function() {
        $('#allotment-approve-form').submit();
    });
});
</script>
@endpush
@endsection

