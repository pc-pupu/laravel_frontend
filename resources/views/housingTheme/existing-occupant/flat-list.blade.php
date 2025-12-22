@extends('housingTheme.layouts.app')
@section('title', 'Select Flat for Existing Occupant')
@section('page-header', 'Data Entry For Existing Occupant')


@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-list me-2"></i> Select Flat for Existing Occupant Entry</h3>
                            <p>Choose RHE, Flat Type, Block, and Flat Number</p>
                        </div>
                        <a href="{{ route('existing-occupant.index') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="form-section">
                    <form id="flatSelectionForm" method="GET" action="#">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="rhe_name" name="rhe_name" required>
                                        <option value="">Select RHE</option>
                                        @foreach($rheList as $rhe)
                                            <option value="{{ $rhe['value'] }}">{{ $rhe['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <label for="rhe_name" class="required">Name of the RHE</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="flat_type" name="flat_type" required disabled>
                                        <option value="">Select Flat Type</option>
                                    </select>
                                    <label for="flat_type" class="required">Flat Type</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="block_name" name="block_name" required disabled>
                                        <option value="">Select Block</option>
                                    </select>
                                    <label for="block_name" class="required">Name of the Block</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" id="flat_no" name="flat_no" required disabled>
                                        <option value="">Select Flat No.</option>
                                    </select>
                                    <label for="flat_no" class="required">Flat No.</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div id="flatOccupancyStatus" class="alert d-none"></div>
                            </div>
                            <div class="col-md-12">
                                <button type="button" id="proceedBtn" class="btn btn-primary" disabled>
                                    <i class="fa fa-arrow-right me-2"></i> Proceed to Data Entry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const backendUrl = '{{ env("BACKEND_API") }}/api';
    const token = '{{ session("api_token") }}';
    
    const rheSelect = document.getElementById('rhe_name');
    const flatTypeSelect = document.getElementById('flat_type');
    const blockSelect = document.getElementById('block_name');
    const flatNoSelect = document.getElementById('flat_no');
    const proceedBtn = document.getElementById('proceedBtn');
    const statusDiv = document.getElementById('flatOccupancyStatus');

    // Load flat types when RHE is selected
    rheSelect.addEventListener('change', function() {
        const rheId = this.value;
        flatTypeSelect.disabled = true;
        flatTypeSelect.innerHTML = '<option value="">Select Flat Type</option>';
        blockSelect.disabled = true;
        blockSelect.innerHTML = '<option value="">Select Block</option>';
        flatNoSelect.disabled = true;
        flatNoSelect.innerHTML = '<option value="">Select Flat No.</option>';
        proceedBtn.disabled = true;
        statusDiv.classList.add('d-none');

        if (rheId) {
            fetch(`${backendUrl}/existing-occupants/meta/flat-types/${rheId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    flatTypeSelect.innerHTML = '<option value="">Select Flat Type</option>';
                    data.data.forEach(item => {
                        flatTypeSelect.innerHTML += `<option value="${item.flat_type_id}">${item.flat_type}</option>`;
                    });
                    flatTypeSelect.disabled = false;
                }
            });
        }
    });

    // Load blocks when flat type is selected
    flatTypeSelect.addEventListener('change', function() {
        const rheId = rheSelect.value;
        const flatTypeId = this.value;
        blockSelect.disabled = true;
        blockSelect.innerHTML = '<option value="">Select Block</option>';
        flatNoSelect.disabled = true;
        flatNoSelect.innerHTML = '<option value="">Select Flat No.</option>';
        proceedBtn.disabled = true;
        statusDiv.classList.add('d-none');

        if (rheId && flatTypeId) {
            fetch(`${backendUrl}/existing-occupants/meta/blocks/${rheId}/${flatTypeId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    blockSelect.innerHTML = '<option value="">Select Block</option>';
                    data.data.forEach(item => {
                        blockSelect.innerHTML += `<option value="${item.block_id}">${item.block_name}</option>`;
                    });
                    blockSelect.disabled = false;
                }
            });
        }
    });

    // Load flats when block is selected
    blockSelect.addEventListener('change', function() {
        const rheId = rheSelect.value;
        const flatTypeId = flatTypeSelect.value;
        const blockId = this.value;
        flatNoSelect.disabled = true;
        flatNoSelect.innerHTML = '<option value="">Select Flat No.</option>';
        proceedBtn.disabled = true;
        statusDiv.classList.add('d-none');

        if (rheId && flatTypeId && blockId) {
            fetch(`${backendUrl}/existing-occupants/meta/flats/${rheId}/${flatTypeId}/${blockId}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    flatNoSelect.innerHTML = '<option value="">Select Flat No.</option>';
                    data.data.forEach(item => {
                        flatNoSelect.innerHTML += `<option value="${item.flat_id}">${item.flat_no}</option>`;
                    });
                    flatNoSelect.disabled = false;
                }
            });
        }
    });

    // Check flat occupancy and enable proceed button
    flatNoSelect.addEventListener('change', function() {
        const flatId = this.value;
        proceedBtn.disabled = true;
        statusDiv.classList.add('d-none');

        if (flatId) {
            fetch(`${backendUrl}/existing-occupants/flat/${flatId}/check`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (data.data.is_available) {
                        statusDiv.className = 'alert alert-success';
                        statusDiv.innerHTML = '<i class="fa fa-check-circle me-2"></i>Flat is available for occupant entry.';
                        proceedBtn.disabled = false;
                    } else {
                        statusDiv.className = 'alert alert-danger';
                        statusDiv.innerHTML = '<i class="fa fa-exclamation-circle me-2"></i>This flat already has an occupant or draft entry.';
                    }
                    statusDiv.classList.remove('d-none');
                }
            });
        }
    });

    // Proceed to create form
    proceedBtn.addEventListener('click', function() {
        const flatId = flatNoSelect.value;
        if (flatId) {
            // Use the correct route with flat_id parameter
            // The controller handles both encrypted and plain IDs
            window.location.href = `{{ url('rhewise_occupant_data_entry') }}/${flatId}`;
        }
    });
});
</script>
@endpush

