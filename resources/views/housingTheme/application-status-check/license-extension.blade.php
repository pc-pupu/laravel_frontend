@extends('housingTheme.layouts.app')

@section('title', 'Request for License Extension')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-history me-2"></i> Request for License Extension</h3>
                            <p class="mb-0">Submit license extension request</p>
                        </div>
                        <a href="{{ route('application-status-check.view-list', ['id' => $id, 'status' => $status]) }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <div class="cms-body">
                    <form action="{{ route('application-status-check.store-license-extension', [
                        'id' => $id,
                        'status' => $status,
                        'uid' => $uid,
                        'official_detail_id' => $officialDetailId
                    ]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('license_extension_reason_dropdown') is-invalid @enderror" 
                                            id="license_extension_reason_dropdown" 
                                            name="license_extension_reason_dropdown" 
                                            required>
                                        <option value="">- Select Reason -</option>
                                        <option value="Reason_1" {{ old('license_extension_reason_dropdown') == 'Reason_1' ? 'selected' : '' }}>Reason-1</option>
                                        <option value="Reason_2" {{ old('license_extension_reason_dropdown') == 'Reason_2' ? 'selected' : '' }}>Reason-2</option>
                                    </select>
                                    <label for="license_extension_reason_dropdown">Reason for License Extension</label>
                                    @error('license_extension_reason_dropdown')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" 
                                           class="form-control datepicker @error('add_license_extension_date') is-invalid @enderror" 
                                           id="add_license_extension_date" 
                                           name="add_license_extension_date" 
                                           placeholder="Enter Extension Date" 
                                           value="{{ old('add_license_extension_date') }}"
                                           required>
                                    <label for="add_license_extension_date">Enter Extension Date</label>
                                    @error('add_license_extension_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="file" 
                                           class="form-control @error('license_extension_reason_file') is-invalid @enderror" 
                                           id="license_extension_reason_file" 
                                           name="license_extension_reason_file" 
                                           accept=".pdf"
                                           required>
                                    <label for="license_extension_reason_file">Upload valid reason for license extension</label>
                                    <small class="form-text text-muted">
                                        <b>Allowed Extension: pdf<br>Maximum File Size: 2 MB</b>
                                    </small>
                                    @error('license_extension_reason_file')
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
        $("#add_license_extension_date").datepicker({
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            yearRange: '2020:2050',
            minDate: new Date() // Extension date should be in the future
        });
    });
</script>
@endpush

