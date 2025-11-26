@extends('housingTheme.layouts.app')
@section('title', 'Add Estate Treasury Mapping')
@section('page-header', 'Add Estate Treasury Mapping')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-plus-circle me-2"></i> Add New Estate Treasury Mapping</h3>
                            <p>Create new estate to treasury mapping</p>
                        </div>
                        <a href="{{ route('estate-treasury-selection.index') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to List
                        </a>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('estate-treasury-selection.store') }}" id="estateTreasuryMappingForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-control @error('estate_id') is-invalid @enderror" 
                                    id="estate_dropdown" name="estate_id" required>
                                    <option value="">- Select -</option>
                                    @foreach($estates as $estateId => $estateName)
                                        <option value="{{ $estateId }}" {{ old('estate_id') == $estateId ? 'selected' : '' }}>
                                            {{ $estateName }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="estate_dropdown">Select housing estate <span class="text-danger">*</span></label>
                                @error('estate_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-control @error('treasury_id') is-invalid @enderror" 
                                    id="treasury_dropdown" name="treasury_id" required>
                                    <option value="">- Select -</option>
                                    @foreach($treasuries as $treasuryId => $treasuryName)
                                        <option value="{{ $treasuryId }}" {{ old('treasury_id') == $treasuryId ? 'selected' : '' }}>
                                            {{ $treasuryName }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="treasury_dropdown">Select respective treasury <span class="text-danger">*</span></label>
                                @error('treasury_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input @error('is_active') is-invalid @enderror" 
                                            type="radio" name="is_active" id="is_active_1" value="1" 
                                            {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active_1">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input @error('is_active') is-invalid @enderror" 
                                            type="radio" name="is_active" id="is_active_0" value="0" 
                                            {{ old('is_active') == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active_0">Inactive</label>
                                    </div>
                                    <label class="d-block mb-2">Activation Status <span class="text-danger">*</span></label>
                                    @error('is_active')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <br><br>
                                <button type="submit" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder">
                                    <i class="fa fa-save me-2"></i> Save
                                </button>
                            </div>
                        </div>
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
    $(document).ready(function() {
        // Form validation
        $('#estateTreasuryMappingForm').on('submit', function(e) {
            const estateId = $('#estate_dropdown').val();
            const treasuryId = $('#treasury_dropdown').val();
            const isActive = $('input[name="is_active"]:checked').val();

            if (!estateId) {
                alert('Please select a housing estate.');
                e.preventDefault();
                return false;
            }

            if (!treasuryId) {
                alert('Please select a treasury.');
                e.preventDefault();
                return false;
            }

            if (typeof isActive === 'undefined') {
                alert('Please select activation status.');
                e.preventDefault();
                return false;
            }

            return true;
        });
    });
</script>
@endpush

