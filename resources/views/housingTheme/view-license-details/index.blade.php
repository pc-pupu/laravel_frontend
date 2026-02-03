@extends('housingTheme.layouts.app')

@section('title', 'View Licence Details')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <h3><i class="fa fa-file-alt me-2"></i> View Licence Details</h3>
                </div>

                @include('housingTheme.partials.alerts')

                <div class="cms-body">
                    <form method="GET" action="{{ route('view-license-details.index') }}" id="license-details-form">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="license_type" class="form-label">Select Licence Type</label>
                                <select name="license_type" id="license_type" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    <option value="new" {{ request('license_type') == 'new' ? 'selected' : '' }}>New Licence</option>
                                    <option value="vs" {{ request('license_type') == 'vs' ? 'selected' : '' }}>Vertical Shifting Licence</option>
                                    <option value="cs" {{ request('license_type') == 'cs' ? 'selected' : '' }}>Category Shifting Licence</option>
                                </select>
                            </div>
                        </div>
                    </form>

                    @if(count($licenses) > 0)
                        <div class="table-responsive">
                            <table class="table table-list table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            @if(request('license_type') == 'new')
                                                New Licence Online Application No.
                                            @elseif(request('license_type') == 'vs')
                                                VS Licence Online Application No.
                                            @elseif(request('license_type') == 'cs')
                                                CS Licence Online Application No.
                                            @else
                                                Online Application No.
                                            @endif
                                        </th>
                                        <th>Licence Issue Date</th>
                                        <th>Licence Expiry Date</th>
                                        <th>Licence No.</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($licenses as $license)
                                        <tr>
                                            <td>{{ $license['application_no'] ?? 'N/A' }}</td>
                                            <td>{{ $license['license_issue_date'] ? date('d/m/Y', strtotime($license['license_issue_date'])) : 'N/A' }}</td>
                                            <td>{{ $license['license_expiry_date'] ? date('d/m/Y', strtotime($license['license_expiry_date'])) : 'N/A' }}</td>
                                            <td>{{ $license['license_no'] ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('view-license-details.download-pdf', [
                                                    'encrypted_license_type' => \App\Helpers\UrlEncryptionHelper::encryptUrl(request('license_type')),
                                                    'encrypted_app_id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($license['online_application_id']),
                                                    'encrypted_flat_occupant_id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($license['flat_occupant_id']),
                                                    'encrypted_license_app_id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($license['license_application_id'])
                                                ]) }}" 
                                                   class="btn btn-sm btn-primary" target="_blank">
                                                    <i class="fa fa-file-pdf me-2"></i> Download Licence
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif(request('license_type'))
                        <div class="table-responsive">
                            <table class="datatable_no_data_found table table-list">
                                <tr class="tr_no_data_found">
                                    <th class="th_no_data_found"></th>
                                </tr>
                                <tr class="tr_no_data_found">
                                    <td class="td_no_data_found">No data found!</td>
                                </tr>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#license_type').on('change', function() {
            $('#license-details-form').submit();
        });
    });
</script>
@endpush
