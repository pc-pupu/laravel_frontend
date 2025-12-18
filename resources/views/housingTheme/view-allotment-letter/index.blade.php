@extends('housingTheme.layouts.app')

@section('title', 'View Proposed RHE')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-list me-2"></i> View Proposed RHE</h3>
                            <p class="mb-0">View and manage allotment letters by flat type</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <form method="GET" action="{{ route('view-allotment-letter.index') }}" id="allotment-letter-form">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="flat_type_id" id="flat_type" class="form-select" required>
                                    <option value="">Select RHE Flat Type</option>
                                    @foreach($flatTypes as $flatType)
                                        <option value="{{ $flatType['value'] }}" {{ old('flat_type_id', $flatTypeId) == $flatType['value'] ? 'selected' : '' }}>
                                            {{ $flatType['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="flat_type">RHE Flat Type</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary" style="margin-top: 43px; height: 58px;">
                                <i class="fa fa-search me-2"></i> Search
                            </button>
                        </div>
                    </div>
                </form>

                @if($flatTypeId && count($allotmentLetters) > 0)
                    <div class="table-responsive">
                        <table class="table table-list table-striped">
                            <thead>
                                <tr>
                                    <th>Applicant Name</th>
                                    <th>Application Type</th>
                                    <th>Roaster Counter</th>
                                    <th>Offer</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allotmentLetters as $index => $letter)
                                    @php
                                        $allotmentCategory = '';
                                        $applicationType = '';
                                        if (!empty($letter['n_allotment_category'])) {
                                            $allotmentCategory = $letter['n_allotment_category'];
                                            $applicationType = 'New Allotment';
                                        } elseif (!empty($letter['v_allotment_category'])) {
                                            $allotmentCategory = $letter['v_allotment_category'];
                                            $applicationType = 'Vertical Shifting';
                                        } elseif (!empty($letter['c_allotment_category'])) {
                                            $allotmentCategory = $letter['c_allotment_category'];
                                            $applicationType = 'Category Shifting';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <h6>
                                                <label style="color:#2F709B;letter-spacing: 0.06em;">{{ str_replace(' ', '&nbsp;', $letter['applicant_name']) }}</label>
                                                <label style="color:#0090C7;font-weight: 400;display:block;">({{ str_replace(' ', '&nbsp;', $allotmentCategory) }})</label>
                                            </h6>
                                        </td>
                                        <td>
                                            <h6>
                                                <label style="color:#0090C7;font-weight: 400;">{{ $applicationType }}</label>
                                            </h6>
                                        </td>
                                        <td>
                                            <h6>
                                                <label style="color:#596C26;display: block;font-weight: 500;">{{ $letter['roaster_counter'] }} / List {{ $letter['list_no'] }}</label>
                                            </h6>
                                        </td>
                                        <td>
                                            <h6>
                                                <label>Flat:&nbsp;{{ $letter['flat_no'] }}&nbsp;
                                                    <label style="color:#0090C7;font-weight: 400;">[&nbsp;{{ str_replace(' ', '&nbsp;', $letter['estate_name']) }}&nbsp;]</label>
                                                </label>
                                            </h6>
                                        </td>
                                        <td>
                                            @if($index == 0)
                                                <a href="{{ route('view-allotment-letter.update-allotment', [
                                                    'encrypted_app_id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($letter['online_application_id']),
                                                    'encrypted_status' => \App\Helpers\UrlEncryptionHelper::encryptUrl('allot')
                                                ]) }}" 
                                                   class="btn btn-sm btn-success"
                                                   onclick="return confirm('Are you sure you want to Allot?')">
                                                    Allot
                                                </a>
                                                &nbsp;|&nbsp;
                                                <a href="{{ route('view-allotment-letter.update-allotment', [
                                                    'encrypted_app_id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($letter['online_application_id']),
                                                    'encrypted_status' => \App\Helpers\UrlEncryptionHelper::encryptUrl('cancel')
                                                ]) }}" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Are you sure you want to Cancel?')">
                                                    Cancel
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($flatTypeId && count($allotmentLetters) == 0)
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i> No allotment letters found for the selected flat type.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#flat_type').on('change', function() {
        // Auto-submit form when flat type is selected (optional)
    });
});
</script>
@endpush
@endsection

