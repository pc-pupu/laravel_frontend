@extends('housingTheme.layouts.app')

@section('title', 'Request for Offer Letter Extension')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-history me-2"></i> Request for Offer Letter Extension</h3>
                            <p class="mb-0">Submit offer letter extension request</p>
                        </div>
                        <a href="{{ route('application-status-check.view-list', ['id' => $id, 'status' => $status]) }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <div class="cms-body">
                    <form action="{{ route('application-status-check.store-offer-letter-extension', [
                        'id' => $id,
                        'status' => $status,
                        'uid' => $uid,
                        'official_detail_id' => $officialDetailId,
                        'date_of_verified' => \App\Helpers\UrlEncryptionHelper::encryptUrl($dateOfVerified)
                    ]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('offer_letter_extension_reason_dropdown') is-invalid @enderror" 
                                            id="offer_letter_extension_reason_dropdown" 
                                            name="offer_letter_extension_reason_dropdown" 
                                            required>
                                        <option value="">- Select Reason -</option>
                                        <option value="Reason_1" {{ old('offer_letter_extension_reason_dropdown') == 'Reason_1' ? 'selected' : '' }}>Reason-1</option>
                                        <option value="Reason_2" {{ old('offer_letter_extension_reason_dropdown') == 'Reason_2' ? 'selected' : '' }}>Reason-2</option>
                                    </select>
                                    <label for="offer_letter_extension_reason_dropdown">Reason for Offer Letter Extension</label>
                                    @error('offer_letter_extension_reason_dropdown')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control datepicker @error('add_offer_letter_extension_date') is-invalid @enderror" 
                                           id="add_offer_letter_extension_date" 
                                           name="add_offer_letter_extension_date" 
                                           placeholder="Enter Extension Date" 
                                           value="{{ old('add_offer_letter_extension_date') }}"
                                           required>
                                    <label for="add_offer_letter_extension_date">Enter Extension Date</label>
                                    @error('add_offer_letter_extension_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="file" 
                                           class="form-control @error('offer_letter_extension_reason_file') is-invalid @enderror" 
                                           id="offer_letter_extension_reason_file" 
                                           name="offer_letter_extension_reason_file" 
                                           accept=".pdf"
                                           required>
                                    <label for="offer_letter_extension_reason_file">Upload valid reason for offer letter extension</label>
                                    <small class="form-text text-muted">
                                        <b>Allowed Extension: pdf<br>Maximum File Size: 2 MB</b>
                                    </small>
                                    @error('offer_letter_extension_reason_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn bg-primary btn-sm px-5 rounded-pill text-white fw-bolder">
                                <i class="fa fa-paper-plane me-2"></i> Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/housingTheme/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/housingTheme/jquery-ui/jquery-ui.min.js') }}"></script>
<script>
    $(function() {
        const dateOnly = '{{ $dateOfVerified }}'; // This should be passed from controller
        const minDate = dateOnly ? new Date(dateOnly) : new Date();
        
        $("#add_offer_letter_extension_date").datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '2020:2050',
            minDate: minDate // Extension date must be after offer letter generation date
        });
    });
</script>
@endpush

