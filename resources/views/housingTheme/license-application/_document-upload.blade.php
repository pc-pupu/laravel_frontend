{{-- Document Upload Section --}}
<div class="form-section mt-4">
    <h5 class="mb-3"><i class="fa fa-upload me-2"></i> Upload Documents</h5>
    
    <div class="row g-3">
        <div class="col-md-6">
            <div class="form-floating">
                <input type="file" class="form-control" id="document" name="document" 
                    accept=".pdf" {{ isset($allotmentData) && !empty($allotmentData) ? '' : 'required' }}>
                <label for="document" class="required">Upload Necessary Document (Latest Payslip)</label>
                <small class="text-muted">Allowed Extension: pdf | Maximum File Size: 1 MB</small>
            </div>
            @if(isset($allotmentData['document_uri']) && !empty($allotmentData['document_uri']))
                <div class="mt-2">
                    <a href="{{ asset('storage/' . $allotmentData['document_uri']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-file-pdf me-1"></i> View Current Document
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
