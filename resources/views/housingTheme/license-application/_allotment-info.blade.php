{{-- Allotment Information Section --}}
<div class="form-section mt-4">
    <h5 class="mb-3"><i class="fa fa-home me-2"></i> Allotment Information</h5>
    
    <div class="row g-3">
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="allotment_no" name="allotment_no" 
                    value="{{ $allotmentData['allotment_no'] ?? old('allotment_no', '') }}" 
                    placeholder="Allotment No" readonly required>
                <label for="allotment_no" class="required">Allotment No</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="allotment_date" name="allotment_date" 
                    value="{{ $allotmentData['allotment_date'] ?? old('allotment_date', '') }}" 
                    placeholder="DD/MM/YYYY" readonly required autocomplete="off">
                <label for="allotment_date" class="required">Allotment Date</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-floating">
                <input type="text" class="form-control" id="allotment_district" name="allotment_district" 
                    value="{{ $allotmentData['allotment_district'] ?? old('allotment_district', '') }}" 
                    placeholder="District" readonly required>
                <label for="allotment_district" class="required">District</label>
            </div>
        </div>
    </div>
    
    <div class="row g-3 mt-2">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="allotment_estate" name="allotment_estate" 
                    value="{{ $allotmentData['allotment_estate'] ?? old('allotment_estate', '') }}" 
                    placeholder="Housing" readonly required>
                <label for="allotment_estate" class="required">Housing</label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <textarea class="form-control" id="allotment_address" name="allotment_address" 
                    placeholder="Housing Address" readonly>{{ $allotmentData['allotment_address'] ?? old('allotment_address', '') }}</textarea>
                <label for="allotment_address">Housing Address</label>
            </div>
        </div>
    </div>
    
    <input type="hidden" id="allotment_flat_id" name="allotment_flat_id" 
        value="{{ $allotmentData['allotment_flat_id'] ?? old('allotment_flat_id', '0') }}">
</div>
