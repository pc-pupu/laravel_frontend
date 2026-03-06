@extends('housingTheme.layouts.app')

@section('title', 'List of Applications Approved by Housing Approver')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3><i class="fa fa-list me-2"></i> List of Applications Approved by Housing Approver</h3>
                            <p class="mb-0">Filter and manage applications for special recommendation</p>
                        </div>
                        <a href="{{ route('dashboard') }}" class="btn btn-light">
                            <i class="fa fa-arrow-left me-2"></i> Back to Dashboard
                        </a>
                    </div>
                </div>

                @include('housingTheme.partials.alerts')

                <form method="GET" action="{{ route('special-recommendation.housing-approver-list') }}" id="filter-form">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="type_of_category" id="type_of_category" class="form-select" required>
                                    @foreach($allotmentCategories as $value => $label)
                                        <option value="{{ $value }}" {{ $selectedCategory == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="type_of_category">Select Allotment Reason</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select name="flat_type" id="flat_type" class="form-select" required>
                                    @foreach($flatTypes as $value => $label)
                                        <option value="{{ $value }}" {{ $selectedFlatType == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="flat_type">Select Flat Type</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn bg-primary btn-sm px-5 rounded-pill text-white mb-4 fw-bolder w-100">
                                <i class="fa fa-search me-2"></i> Search
                            </button>
                        </div>
                    </div>
                </form>

                @if(count($applications) > 0)
                    <div class="mb-3">
                        <span style="color:#ff0000">* Only records with the HRMS id are shown in this list</span>
                    </div>
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
                                    <th>Action</th>
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
                                        <td>
                                            @if($app['is_special_recommended'])
                                                <span style="color: blue; font-size: 15px;">Added for Special</span><br>
                                                <span style="color: blue; font-size: 15px;">Recommendation</span>
                                                <br><br>
                                                <a href="{{ route('special-recommendation.remove', \App\Helpers\UrlEncryptionHelper::encryptUrl($app['online_application_id'])) }}" 
                                                   class="btn bg-danger btn-sm fa fa-minus text-white rounded" 
                                                   style="font-size:12px" 
                                                   title="Remove from Special Recommendation List"
                                                   onclick="return confirm('Are you sure you want to remove this application from special recommendation list?');">
                                                    Remove
                                                </a>
                                                <br><br>
                                                <a href="{{ route('special-recommendation.view-details', \App\Helpers\UrlEncryptionHelper::encryptUrl($app['online_application_id'])) }}" 
                                                   target="_blank" 
                                                   class="btn bg-primary btn-sm px-4.5 rounded-pill text-white fw-bolder">
                                                    View Details
                                                </a>
                                            @else
                                                <a href="{{ route('special-recommendation.add', [
                                                    'encrypted_online_application_id' => \App\Helpers\UrlEncryptionHelper::encryptUrl($app['online_application_id']),
                                                    'encrypted_allotment_category' => \App\Helpers\UrlEncryptionHelper::encryptUrl($app['allotment_category'])
                                                ]) }}" 
                                                   class="btn bg-success btn-sm fa fa-plus text-white rounded" 
                                                   style="font-size:12px" 
                                                   title="Add to Special Recommendation List"
                                                   onclick="return confirm('Are you sure you want to add this application to special recommendation list?');">
                                                    Add to List
                                                </a>
                                                <br><br>
                                                <a href="{{ route('special-recommendation.view-details', \App\Helpers\UrlEncryptionHelper::encryptUrl($app['online_application_id'])) }}" 
                                                   target="_blank" 
                                                   class="btn bg-primary btn-sm px-4.5 rounded-pill text-white fw-bolder">
                                                    View Details
                                                </a>
                                                @if($app['allotment_category'] == 'Recommended')
                                                    <br><br>
                                                    <a href="{{ route('special-recommendation.convert-to-general', \App\Helpers\UrlEncryptionHelper::encryptUrl($app['online_application_id'])) }}" 
                                                       class="btn bg-success btn-sm fa fa-hourglass-1 text-white rounded" 
                                                       style="font-size:12px" 
                                                       title="Convert to General Category with same waiting list"
                                                       onclick="return confirm('Are you sure you want to convert this application to General category?');">
                                                        Convert to General
                                                    </a>
                                                @endif
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
