@extends('housingTheme.layouts.app')

@section('title', 'Generate License')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa fa-file-alt me-2"></i> Generate License
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

                    @if(count($applications) > 0)
                        <div class="table-responsive">
                            <table class="table table-list table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th style="width: 20%;">Application No.</th>
                                        <th style="width: 20%;">License Generation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($applications as $app)
                                        <tr>
                                            <td>{{ $app['applicant_name'] ?? 'N/A' }}</td>
                                            <td>{{ $app['application_no'] ?? 'N/A' }}</td>
                                            <td>
                                                <form action="{{ route('generate-license.generate', ['encrypted_app_id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($app['online_application_id'])]) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn bg-success btn-sm text-white rounded" 
                                                            style="font-size: 12px;"
                                                            onclick="return confirm('Are you sure you want to generate License for Application No.={{ $app['application_no'] ?? '' }}?')">
                                                        Generate License
                                                    </button>
                                                </form>
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
