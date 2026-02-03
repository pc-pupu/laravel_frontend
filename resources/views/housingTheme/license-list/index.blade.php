@extends('housingTheme.layouts.app')

@section('title', 'View Licensee List')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <h3><i class="fa fa-list me-2"></i> View Licensee List</h3>
                </div>

                @include('housingTheme.partials.alerts')

                <div class="cms-body">
                    <form method="GET" action="{{ route('license-list.index') }}" id="licensee-list-form">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="licensee_type" class="form-label">Licensee Type</label>
                                <select name="licensee_type" id="licensee_type" class="form-select" required>
                                    <option value="">-- Select --</option>
                                    <option value="1" {{ request('licensee_type') == '1' ? 'selected' : '' }}>New Licensee</option>
                                    <option value="2" {{ request('licensee_type') == '2' ? 'selected' : '' }}>Floor Shifting Licensee</option>
                                    <option value="3" {{ request('licensee_type') == '3' ? 'selected' : '' }}>Category Shifting Licensee</option>
                                </select>
                            </div>
                        </div>
                    </form>

                    @if(count($licenses) > 0)
                        <div class="mb-3">
                            <a href="{{ route('license-list.download-pdf', ['encrypted_licensee_type' => \App\Helpers\UrlEncryptionHelper::encryptUrl(request('licensee_type'))]) }}" 
                               class="btn btn-primary" target="_blank">
                                <i class="fa fa-file-pdf me-2"></i>
                                Download 
                                @if(isset($licenses[0]['type_of_application']))
                                    @if($licenses[0]['type_of_application'] == 'new')
                                        New Allotment
                                    @elseif($licenses[0]['type_of_application'] == 'vs')
                                        Floor Shifting
                                    @elseif($licenses[0]['type_of_application'] == 'cs')
                                        Category Shifting
                                    @endif
                                @endif
                                Licensee List
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
                                        <th>View Details</th>
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
                                                <a href="{{ route('application.view', ['id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($license['online_application_id'])]) }}" 
                                                   target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @elseif(request('licensee_type'))
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
        $('#licensee_type').on('change', function() {
            $('#licensee-list-form').submit();
        });
    });
</script>
@endpush
