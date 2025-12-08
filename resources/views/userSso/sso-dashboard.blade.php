@extends('housingTheme.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Welcome, {{ $output['user_info']['applicantName'] ?? 'User' }}</h2>

            {{-- Display messages --}}
            @if(session('message'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- User Information --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">User Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> {{ $output['user_info']['applicantName'] ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $output['user_info']['email'] ?? 'N/A' }}</p>
                            <p><strong>Designation:</strong> {{ $output['user_info']['applicantDesignation'] ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Office:</strong> {{ $output['user_info']['officeName'] ?? 'N/A' }}</p>
                            <p><strong>Mobile:</strong> {{ $output['user_info']['mobileNo'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Applicant Dashboard --}}
            @if(in_array($output['user_role'] ?? 0, [4, 5]))
                {{-- Application Status --}}
                @if(!empty($output['fetch_current_status']))
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Current Status</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Status:</strong> {{ $output['fetch_current_status']->short_code ?? 'N/A' }}</p>
                        <p><strong>Allotment No:</strong> {{ $output['fetch_current_status']->allotment_no ?? 'N/A' }}</p>
                    </div>
                </div>
                @endif

                {{-- All Applications --}}
                @if(!empty($output['all-application-data']) && count($output['all-application-data']) > 0)
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">All Applications</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Application No</th>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($output['all-application-data'] as $app)
                                    <tr>
                                        <td>{{ $app->application_no }}</td>
                                        <td>{{ \Carbon\Carbon::parse($app->date_of_application)->format('d/m/Y') }}</td>
                                        <td>{{ $app->applicant_name }}</td>
                                        <td>{{ $app->status_description }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            @endif

            {{-- Admin Dashboard --}}
            @if(in_array($output['user_role'] ?? 0, [6, 7, 8, 10, 11, 13, 17]))
                <div class="row">
                    @if(isset($output['new-apply']))
                    <div class="col-md-4 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5>New Applications</h5>
                                <p class="display-6">{{ $output['new-apply'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(isset($output['vs']))
                    <div class="col-md-4 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5>VS Applications</h5>
                                <p class="display-6">{{ $output['vs'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(isset($output['cs']))
                    <div class="col-md-4 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5>CS Applications</h5>
                                <p class="display-6">{{ $output['cs'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(isset($output['all-applications']))
                    <div class="col-md-4 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5>All Applications</h5>
                                <p class="display-6">{{ $output['all-applications'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(isset($output['all-exsting-occupant']))
                    <div class="col-md-4 mb-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body">
                                <h5>Existing Occupants</h5>
                                <p class="display-6">{{ $output['all-exsting-occupant'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

