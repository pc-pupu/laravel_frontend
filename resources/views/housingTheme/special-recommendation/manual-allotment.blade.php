@extends('housingTheme.layouts.app')

@section('title', 'Flat Tagging for Special Recommendation')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-home me-2"></i> Flat Tagging for Special Recommendation</h3>
                            <p class="mb-0">Select flat details for manual allotment</p>
                        </div>
                        <a href="{{ route('special-recommendation.final-list') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to List
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <form method="POST" action="{{ route('special-recommendation.submit-manual-allotment') }}" id="manual-allotment-form">
                    @csrf
                    <input type="hidden" name="online_application_id" value="{{ $onlineApplicationId }}">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="allotment_process_date" name="allotment_date" 
                                    placeholder="DD/MM/YYYY" required autocomplete="off">
                                <label for="allotment_process_date" class="required">Date of Allotment</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="updated_rhe_name" name="rhe_id" required>
                                    <option value="">- Select -</option>
                                    @foreach($rheList as $rhe)
                                        <option value="{{ $rhe['value'] }}">{{ $rhe['label'] }}</option>
                                    @endforeach
                                </select>
                                <label for="updated_rhe_name" class="required">Name of the RHE</label>
                            </div>
                        </div>

                        <div class="col-md-4" id="flat_type_replace_updated">
                            <div class="form-floating">
                                <select class="form-select" id="updated_flat_type" name="flat_type_id" required disabled>
                                    <option value="">- Select RHE First -</option>
                                </select>
                                <label for="updated_flat_type" class="required">Flat Type</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4" id="block_name_replace_updated">
                            <div class="form-floating">
                                <select class="form-select" id="updated_block_name" name="block_id" required disabled>
                                    <option value="">- Select Flat Type First -</option>
                                </select>
                                <label for="updated_block_name" class="required">Name of the Block</label>
                            </div>
                        </div>

                        <div class="col-md-4" id="floor_no_replace_updated">
                            <div class="form-floating">
                                <select class="form-select" id="updated_floor_no" name="floor_no" required disabled>
                                    <option value="">- Select Block First -</option>
                                </select>
                                <label for="updated_floor_no" class="required">Floor No.</label>
                            </div>
                        </div>

                        <div class="col-md-4" id="flat_no_replace_updated">
                            <div class="form-floating">
                                <select class="form-select" id="updated_flat_no" name="flat_id" required disabled>
                                    <option value="">- Select Floor First -</option>
                                </select>
                                <label for="updated_flat_no" class="required">Flat No.</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder">
                                <i class="fa fa-check me-2"></i> Submit
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
        // Initialize date picker
        $("#allotment_process_date").datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            changeYear: true,
            yearRange: "-10:+10",
            maxDate: "+0",
            autoSize: true
        });

        // RHE change - load flat types
        $('#updated_rhe_name').on('change', function() {
            const rheId = $(this).val();
            if (rheId) {
                loadFlatTypes(rheId);
            } else {
                resetDropdowns(['flat_type', 'block', 'floor', 'flat']);
            }
        });

        // Flat type change - load blocks
        $('#updated_flat_type').on('change', function() {
            const rheId = $('#updated_rhe_name').val();
            const flatTypeId = $(this).val();
            if (rheId && flatTypeId) {
                loadBlocks(rheId, flatTypeId);
            } else {
                resetDropdowns(['block', 'floor', 'flat']);
            }
        });

        // Block change - load floors
        $('#updated_block_name').on('change', function() {
            const rheId = $('#updated_rhe_name').val();
            const flatTypeId = $('#updated_flat_type').val();
            const blockId = $(this).val();
            if (rheId && flatTypeId && blockId) {
                loadFloors(rheId, flatTypeId, blockId);
            } else {
                resetDropdowns(['floor', 'flat']);
            }
        });

        // Floor change - load flats
        $('#updated_floor_no').on('change', function() {
            const rheId = $('#updated_rhe_name').val();
            const flatTypeId = $('#updated_flat_type').val();
            const blockId = $('#updated_block_name').val();
            const floorNo = $(this).val();
            if (rheId && flatTypeId && blockId && floorNo) {
                loadFlats(rheId, flatTypeId, blockId, floorNo);
            } else {
                resetDropdowns(['flat']);
            }
        });
    });

    function loadFlatTypes(rheId) {
        $.ajax({
            url: '{{ route('special-recommendation.get-flat-types') }}',
            method: 'GET',
            data: { rhe_id: rheId },
            success: function(response) {
                if (response.status === 'success') {
                    const select = $('#updated_flat_type');
                    select.html('<option value="">- Select -</option>');
                    response.data.forEach(function(item) {
                        select.append($('<option></option>').val(item.value).text(item.label));
                    });
                    select.prop('disabled', false);
                }
            },
            error: function() {
                alert('Failed to load flat types');
            }
        });
    }

    function loadBlocks(rheId, flatTypeId) {
        $.ajax({
            url: '{{ route('special-recommendation.get-blocks') }}',
            method: 'GET',
            data: { rhe_id: rheId, flat_type_id: flatTypeId },
            success: function(response) {
                if (response.status === 'success') {
                    const select = $('#updated_block_name');
                    select.html('<option value="">- Select -</option>');
                    response.data.forEach(function(item) {
                        select.append($('<option></option>').val(item.value).text(item.label));
                    });
                    select.prop('disabled', false);
                }
            },
            error: function() {
                alert('Failed to load blocks');
            }
        });
    }

    function loadFloors(rheId, flatTypeId, blockId) {
        $.ajax({
            url: '{{ route('special-recommendation.get-floors') }}',
            method: 'GET',
            data: { rhe_id: rheId, flat_type_id: flatTypeId, block_id: blockId },
            success: function(response) {
                if (response.status === 'success') {
                    const select = $('#updated_floor_no');
                    select.html('<option value="">- Select -</option>');
                    response.data.forEach(function(item) {
                        select.append($('<option></option>').val(item.value).text(item.label));
                    });
                    select.prop('disabled', false);
                }
            },
            error: function() {
                alert('Failed to load floors');
            }
        });
    }

    function loadFlats(rheId, flatTypeId, blockId, floorNo) {
        $.ajax({
            url: '{{ route('special-recommendation.get-flats') }}',
            method: 'GET',
            data: { rhe_id: rheId, flat_type_id: flatTypeId, block_id: blockId, floor_no: floorNo },
            success: function(response) {
                if (response.status === 'success') {
                    const select = $('#updated_flat_no');
                    select.html('<option value="">- Select -</option>');
                    response.data.forEach(function(item) {
                        select.append($('<option></option>').val(item.value).text(item.label));
                    });
                    select.prop('disabled', false);
                }
            },
            error: function() {
                alert('Failed to load flats');
            }
        });
    }

    function resetDropdowns(types) {
        const map = {
            'flat_type': '#updated_flat_type',
            'block': '#updated_block_name',
            'floor': '#updated_floor_no',
            'flat': '#updated_flat_no'
        };

        types.forEach(function(type) {
            const selector = map[type];
            if (selector) {
                $(selector).html('<option value="">- Select -</option>').prop('disabled', true);
            }
        });
    }
</script>
@endpush
