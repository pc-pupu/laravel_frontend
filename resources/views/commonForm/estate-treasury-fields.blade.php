{{-- Common Estate Treasury Mapping Fields --}}
@php
    $mapping = $mapping ?? [];
    $estates = $estates ?? [];
    $treasuries = $treasuries ?? [];
    $isEdit = isset($mapping['estate_treasury_mapping_id']) || isset($mapping['id']);
    $selectedEstate = old('estate_id', $mapping['estate_id'] ?? '');
    $selectedTreasury = old('treasury_id', $mapping['treasury_id'] ?? '');
    $isActive = old('is_active', $mapping['is_active'] ?? '1');
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="form-floating">
            <select class="form-control @error('estate_id') is-invalid @enderror" 
                id="{{ $isEdit ? 'estate_edit_dropdown' : 'estate_dropdown' }}" name="estate_id" required>
                <option value="" {{ empty($selectedEstate) ? 'selected' : '' }}>- Select -</option>
                @foreach($estates as $estateId => $estateName)
                    <option value="{{ $estateId }}" {{ $selectedEstate == $estateId ? 'selected' : '' }}>
                        {{ $estateName }}
                    </option>
                @endforeach
            </select>
            <label for="{{ $isEdit ? 'estate_edit_dropdown' : 'estate_dropdown' }}">Select housing estate <span class="text-danger">*</span></label>
            @error('estate_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-floating">
            <select class="form-control @error('treasury_id') is-invalid @enderror" 
                id="{{ $isEdit ? 'treasury_edit_dropdown' : 'treasury_dropdown' }}" name="treasury_id" required>
                <option value="" {{ empty($selectedTreasury) ? 'selected' : '' }}>- Select -</option>
                @foreach($treasuries as $treasuryId => $treasuryName)
                    <option value="{{ $treasuryId }}" {{ $selectedTreasury == $treasuryId ? 'selected' : '' }}>
                        {{ $treasuryName }}
                    </option>
                @endforeach
            </select>
            <label for="{{ $isEdit ? 'treasury_edit_dropdown' : 'treasury_dropdown' }}">Select respective treasury <span class="text-danger">*</span></label>
            @error('treasury_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="form-floating">
            <div class="form-check form-check-inline {{ $isEdit ? 'mt-3' : '' }}">
                <input class="form-check-input @error('is_active') is-invalid @enderror" 
                    type="radio" name="is_active" id="{{ $isEdit ? 'edit_' : '' }}is_active_1" value="1" 
                    {{ $isActive == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $isEdit ? 'edit_' : '' }}is_active_1">Active</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input @error('is_active') is-invalid @enderror" 
                    type="radio" name="is_active" id="{{ $isEdit ? 'edit_' : '' }}is_active_0" value="0" 
                    {{ $isActive == '0' ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $isEdit ? 'edit_' : '' }}is_active_0">Inactive</label>
            </div>
            <label class="d-block mb-2">{{ $isEdit ? 'Select' : '' }} Activation Status <span class="text-danger">*</span></label>
            @error('is_active')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

