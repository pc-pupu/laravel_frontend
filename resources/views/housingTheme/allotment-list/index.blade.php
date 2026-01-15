@extends('housingTheme.layouts.app')

@section('title', 'List of Allottees')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-list me-2"></i> List of Allottees</h3>
                            <p class="mb-0">View allottee list by filtering date, process number, and type</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <form method="POST" action="{{ route('allotment-list.show') }}" id="allotment-list-form">
                    @csrf
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
                                    @foreach($processTypes as $type)
                                        <option value="{{ $type['value'] }}" {{ old('allotment_process_type', $allotmentProcessType) == $type['value'] ? 'selected' : '' }}>
                                            {{ $type['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="allotment_process_type">Allotment Process Type</label>
                            </div>
                        </div>
                    </div>
                </form>

                <div id="allotment_list_replace">
                    @if($allotmentProcessDate && $allotmentProcessNo && $allotmentProcessType && count($allottees) > 0)
                        <form method="POST" action="{{ route('allotment-list.update-status') }}" id="allottee-action-form">
                            @csrf
                            <input type="hidden" name="action" id="action-input" value="approve">
                            <input type="hidden" name="online_application_ids" id="online_application_ids" value="">

                            <div class="row mb-3">
                                <div class="col-md-4 text-start">
                                    <button type="button" class="btn bg-success btn-sm px-5 rounded-pill text-white mb-4 fw-bolder w-100" 
                                            onclick="submitAction('approve')">
                                        Click to Approve Allotment
                                    </button>
                                </div>
                                <div class="col-md-4 text-center">
                                    <button type="button" class="btn bg-danger btn-sm px-5 rounded-pill text-white mb-4 fw-bolder w-100" 
                                            onclick="submitAction('reject')">
                                        Click to Reject Allotment
                                    </button>
                                </div>
                                <div class="col-md-4 text-end">
                                    <button type="button" class="btn bg-warning btn-sm px-5 rounded-pill text-white mb-4 fw-bolder w-100" 
                                            onclick="submitAction('hold')">
                                        Click to Hold Allotment
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-list">
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
                                            @elseif($allotmentProcessType == 'VSAL' || $allotmentProcessType == 'CSAL')
                                                <th>Name</th>
                                                <th>1. Date of Possession<br>2. Date of Application<br>3. Date of Retirement</th>
                                                <th>Allotted Flat No.</th>
                                                <th>Type of Flat</th>
                                                <th>Floor</th>
                                                <th>Name of Block</th>
                                                <th>From Flat No.</th>
                                                <th>To Flat No.</th>
                                            @endif
                                            <th>Remarks</th>
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
                            <i class="fa fa-info-circle me-2"></i> No allottee available.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.btn.bg-success {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}
.btn.bg-success:hover {
    background-color: #218838 !important;
    border-color: #1e7e34 !important;
}
.btn.bg-danger {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}
.btn.bg-danger:hover {
    background-color: #c82333 !important;
    border-color: #bd2130 !important;
}
.btn.bg-warning {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
}
.btn.bg-warning:hover {
    background-color: #e0a800 !important;
    border-color: #d39e00 !important;
}
</style>
@endpush

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
        alert('No allottees are selected for allotment approve or reject!!!');
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
    document.getElementById('allottee-action-form').submit();
}

$(document).ready(function() {
    $('#allotment_process_date').on('change', function() {
        var date = $(this).val();
        if (date) {
            $('#allotment-list-form').submit();
        } else {
            $('#allotment_process_no').prop('disabled', true).html('<option value="">Select Allotment Process No.</option>');
            $('#allotment_process_type').prop('disabled', true).html('<option value="">Select Allotment Process Type</option>');
            $('#allotment_list_replace').html('');
        }
    });

    $('#allotment_process_no').on('change', function() {
        var date = $('#allotment_process_date').val();
        if (date) {
            $('#allotment-list-form').submit();
        }
    });

    $('#allotment_process_type').on('change', function() {
        $('#allotment-list-form').submit();
    });
});
</script>
@endpush
@endsection

