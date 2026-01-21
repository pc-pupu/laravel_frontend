@extends('housingTheme.layouts.app')

@section('title', 'View License List')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa fa-file-alt me-2"></i> View License List
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(count($licenses) > 0)
                        <div class="table-responsive">
                            <table class="table table-list table-striped">
                                <thead>
                                    <tr>
                                        <th>Applicant Name</th>
                                        <th>Application No.</th>
                                        <th style="width: 20%;">Download License</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($licenses as $license)
                                        <tr>
                                            <td>{{ $license['applicant_name'] ?? 'N/A' }}</td>
                                            <td>{{ $license['application_no'] ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('download_licence_pdf', ['id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($license['online_application_id'])]) }}" 
                                                   class="btn bg-dark btn-sm text-white rounded" 
                                                   style="font-size: 12px;"
                                                   target="_blank">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
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
