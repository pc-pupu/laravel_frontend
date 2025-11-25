@extends('housingTheme.layouts.app')
@section('title', 'Existing Occupants')
@section('page-header', $title ?? 'Existing Occupant List')


@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div>
                            <h3><i class="fa fa-home me-2"></i> {{ $title ?? 'Existing Occupant List' }}</h3>
                            <p>Manage existing occupants data</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <a href="{{ route('existing-occupant.flat-list') }}" class="btn btn-light me-2">
                                <i class="fa fa-list me-2"></i> Select Flat
                            </a>
                            <a href="{{ route('existing-occupant.create') }}" class="btn btn-light">
                                <i class="fa fa-plus me-2"></i> Add New
                            </a>
                        </div>
                    </div>
                </div>

                <div class="search-filter-box">
                    <form method="GET" action="{{ route('existing-occupant.index') }}" class="row g-3">
                        <div class="col-md-10">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="search" name="search" 
                                    placeholder="Search" value="{{ $filters['search'] ?? '' }}">
                                <label for="search"><i class="fa fa-search me-2"></i>Search by Name, HRMS ID, or Estate Name</label>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100" type="submit">
                                <i class="fa fa-search"></i> Search
                            </button>
                        </div>
                    </form>
                </div>

                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Applicant Name</th>
                                    <th>HRMS ID</th>
                                    <th>Estate Name</th>
                                    <th>Flat Type</th>
                                    <th>Flat No</th>
                                    <th>Approval Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($occupants as $index => $item)
                                    <tr>
                                        <td><strong>{{ $occupants->firstItem() + $index }}</strong></td>
                                        <td><strong>{{ $item['applicant_name'] ?? '-' }}</strong></td>
                                        <td>{{ $item['hrms_id'] ?? '-' }}</td>
                                        <td>{{ $item['estate_name'] ?? '-' }}</td>
                                        <td>{{ $item['flat_type'] ?? '-' }}</td>
                                        <td>{{ $item['flat_no'] ?? '-' }}</td>
                                        <td>
                                            @if(isset($item['user_status']))
                                                <span class="badge bg-{{ $item['user_status'] == 0 ? 'warning' : 'success' }}">
                                                    {{ $item['user_status'] == 0 ? 'Pending Approval' : 'Approved' }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('existing-occupant.view', encrypt($item['uid'] ?? $item['online_application_id'])) }}" 
                                                class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('existing-occupant.edit', encrypt($item['online_application_id'])) }}" 
                                                class="btn btn-sm btn-info">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            <i class="fa fa-inbox fa-3x mb-3 d-block" style="color: #4980f7; opacity: 0.5;"></i>
                                            <p class="mb-0">No occupants found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $occupants->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

