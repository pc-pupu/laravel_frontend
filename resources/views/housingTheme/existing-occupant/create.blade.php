@extends('housingTheme.layouts.app')
@section('title', 'Add Existing Occupant')
@section('page-header', 'Data Entry For Existing Occupant')

@push('styles')
<style>
    .cms-wrapper {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f2ff 100%);
        min-height: calc(100vh - 200px);
        padding: 1.5rem 0;
    }
    .cms-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(73, 128, 247, 0.12);
        padding: 2rem;
        border: 1px solid rgba(73, 128, 247, 0.1);
    }
    .cms-header {
        background: linear-gradient(135deg, #4980f7 0%, #19bbd3 100%);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(73, 128, 247, 0.3);
    }
    .cms-header h3 {
        margin: 0;
        font-weight: 600;
        font-size: 1.75rem;
    }
    .form-section {
        background: linear-gradient(135deg, #f8faff 0%, #ffffff 100%);
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(73, 128, 247, 0.15);
    }
    .form-floating > label {
        color: #4980f7;
        font-weight: 500;
    }
    .form-control:focus, .form-select:focus {
        border-color: #4980f7;
        box-shadow: 0 0 0 0.2rem rgba(73, 128, 247, 0.25);
    }
    .required::after {
        content: " *";
        color: #e74c3c;
        font-weight: bold;
    }
    .btn-submit {
        background: linear-gradient(135deg, #4980f7 0%, #19bbd3 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(73, 128, 247, 0.3);
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(73, 128, 247, 0.4);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-plus-circle me-2"></i> Add New Existing Occupant</h3>
                            <p>Enter occupant data for flat: 
                                @if($flatDetails)
                                    <strong>{{ $flatDetails['estate_name'] ?? '' }} - {{ $flatDetails['flat_no'] ?? '' }}</strong>
                                @else
                                    <strong>Please select a flat first</strong>
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('existing-occupant.flat-list') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Flat Selection
                        </a>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if(!$flatDetails)
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        Please select a flat first from the <a href="{{ route('existing-occupant.flat-list') }}">flat selection page</a>.
                    </div>
                @else
                    <form method="POST" action="{{ route('existing-occupant.store') }}" id="existingOccupantForm" novalidate>
                        @csrf
                        <input type="hidden" name="flat_id" value="{{ $flatDetails['flat_id'] ?? '' }}">

                        @include('housingTheme.existing-occupant._form', [
                            'flatDetails' => $flatDetails,
                            'metaData' => $metaData ?? []
                        ])

                        <div class="mt-4 d-flex justify-content-end gap-3">
                            <a href="{{ route('existing-occupant.flat-list') }}" class="btn btn-outline-secondary px-4">Cancel</a>
                            <button type="submit" class="btn btn-submit">
                                <i class="fa fa-save me-2"></i>Submit
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date picker initialization (if using jQuery UI datepicker)
    if (typeof $.fn.datepicker !== 'undefined') {
        $('#dob, #doj, #dor, #dol').datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '1900:2100'
        });
    }

    // Auto-uppercase for text fields
    document.querySelectorAll('input[type="text"]').forEach(input => {
        if (input.name && (input.name.includes('name') || input.name.includes('address') || 
            input.name.includes('city') || input.name.includes('office') || 
            input.name.includes('designation') || input.name.includes('posting') || 
            input.name.includes('headquarter'))) {
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        }
    });

    // DDO selection based on district
    const districtSelect = document.getElementById('ddo_district');
    const ddoSelect = document.getElementById('ddo_id');
    const ddoAddressTextarea = document.getElementById('ddo_address');

    if (districtSelect && ddoSelect) {
        districtSelect.addEventListener('change', function() {
            const districtCode = this.value;
            ddoSelect.disabled = true;
            ddoSelect.innerHTML = '<option value="">Select DDO Designation</option>';
            ddoAddressTextarea.value = '';

            if (districtCode) {
                fetch(`{{ env('BACKEND_API') }}/api/admin/existing-occupants/meta/ddo-list?district_code=${districtCode}`, {
                    headers: {
                        'Authorization': `Bearer {{ session('api_token') }}`,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        ddoSelect.innerHTML = '<option value="">Select DDO Designation</option>';
                        data.data.forEach(ddo => {
                            ddoSelect.innerHTML += `<option value="${ddo.ddo_id}" data-address="${ddo.ddo_address || ''}">${ddo.ddo_designation}</option>`;
                        });
                        ddoSelect.disabled = false;
                    }
                });
            }
        });

        if (ddoSelect) {
            ddoSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const address = selectedOption.getAttribute('data-address') || '';
                if (ddoAddressTextarea) {
                    ddoAddressTextarea.value = address;
                }
            });
        }
    }
});
</script>
@endpush

