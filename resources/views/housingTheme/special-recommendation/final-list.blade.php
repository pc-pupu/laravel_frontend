@extends('housingTheme.layouts.app')

@section('title', 'Final List of Special Recommendation')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-list me-2"></i> Final List of Special Recommendation</h3>
                            <p class="mb-0">View and manage special recommended applications</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                @if(count($applications) > 0)
                    <div class="table-responsive">
                        <table class="table table-list table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Application No.</th>
                                    <th>Date of Application</th>
                                    <th>Computer Serial NO.</th>
                                    <th>Allotment Reason</th>
                                    <th>Flat Type</th>
                                    <th>Approval/Rejection Date</th>
                                    <th>Priority Order</th>
                                    <th>Manual Allotment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $app)
                                    <tr>
                                        <td>{{ $app['applicant_name'] ?? 'N/A' }}</td>
                                        <td>{{ $app['application_no'] ?? 'N/A' }}</td>
                                        <td>{{ $app['date_of_application'] ? date('d/m/Y', strtotime($app['date_of_application'])) : 'N/A' }}</td>
                                        <td>{{ $app['computer_serial_no'] ?? 'N/A' }}</td>
                                        <td>{{ $app['allotment_category'] ?? 'N/A' }}</td>
                                        <td>{{ $app['flat_type'] ?? 'N/A' }}</td>
                                        <td>{{ $app['date_of_verified'] ? date('d/m/Y', strtotime($app['date_of_verified'])) : 'N/A' }}</td>
                                        <td>{{ $app['priority_order'] ?? 'N/A' }}</td>
                                        <td>
                                            @if($app['status'] == 'housingapprover_approved_1')
                                                <a href="{{ route('special-recommendation.manual-allotment', \App\Helpers\UrlEncryptionHelper::encryptUrl($app['online_application_id'])) }}" 
                                                   class="btn bg-success btn-sm fa fa-plus text-white rounded" 
                                                   style="font-size:12px">
                                                    Manual Allotment
                                                </a>
                                            @else
                                                Not Applicable
                                            @endif
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
@endsection
