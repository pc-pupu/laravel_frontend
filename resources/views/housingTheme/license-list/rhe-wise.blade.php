@extends('housingTheme.layouts.app')

@section('title', 'RHE Wise Licensee List')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <h3><i class="fa fa-list me-2"></i> RHE Wise Licensee List</h3>
                </div>

                @include('housingTheme.partials.alerts')

                <div class="cms-body">
                    <form method="GET" action="{{ route('license-list.rhe-wise') }}" id="rhe-wise-form">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="rhe_id" class="form-label">Name of the RHE</label>
                                <select name="rhe_id" id="rhe_id" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    @foreach($rhes as $rhe)
                                        <option value="{{ $rhe['estate_id'] }}" {{ request('rhe_id') == $rhe['estate_id'] ? 'selected' : '' }}>
                                            {{ $rhe['estate_name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    @if(count($licenses) > 0)
                        <div class="mb-3">
                            <a href="{{ route('license-list.rhe-wise.download-pdf', ['encrypted_rhe_id' => \App\Helpers\UrlEncryptionHelper::encryptUrl(request('rhe_id'))]) }}" 
                               class="btn btn-primary" target="_blank">
                                <i class="fa fa-file-pdf me-2"></i>
                                Download {{ $licenses[0]['estate_name'] ?? 'RHE' }} Licensee List
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-list table-striped">
                                <thead>
                                    <tr>
                                        <th>Licensee Name</th>
                                        <th>Licence No.</th>
                                        <th>Date of Issue</th>
                                        <th>Date of Expiry</th>
                                        <th>
                                            @if($user_role == 7 || $user_role == 8)
                                                Download
                                            @else
                                                View Details
                                            @endif
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($licenses as $license)
                                        <tr>
                                            <td>{{ $license['applicant_name'] ?? 'N/A' }}</td>
                                            <td>{{ $license['license_no'] ?? 'N/A' }}</td>
                                            <td>{{ $license['license_issue_date'] ? date('d/m/Y', strtotime($license['license_issue_date'])) : 'N/A' }}</td>
                                            <td>{{ $license['license_expiry_date'] ? date('d/m/Y', strtotime($license['license_expiry_date'])) : 'N/A' }}</td>
                                            <td>
                                                @if($user_role == 7 || $user_role == 8)
                                                    @if(!empty($license['uploaded_licence']) && !empty($license['occupant_license_id']))
                                                        <a href="{{ route('download-signed-license', ['encrypted_occupant_license_id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($license['occupant_license_id'])]) }}" 
                                                           class="btn btn-sm btn-success" download>
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                @else
                                                    <a href="{{ route('application.view', ['id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($license['online_application_id'])]) }}" 
                                                       target="_blank" class="btn btn-sm btn-info">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif(request('rhe_id'))
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
        $('#rhe_id').on('change', function() {
            $('#rhe-wise-form').submit();
        });
    });
</script>
@endpush
