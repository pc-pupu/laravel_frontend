@extends('housingTheme.layouts.app')
@section('title', 'Existing Occupants')
@section('page-header', $title ?? 'Existing Occupant List')

@push('styles')
<style>
    .cms-wrapper {
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f2ff 100%);
        min-height: calc(100vh - 200px);
        padding: 1.5rem 0;
    }
    .cms-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(73, 128, 247, 0.12);
        padding: 2rem;
        border: 1px solid rgba(73, 128, 247, 0.1);
    }
    .cms-header {
        background: linear-gradient(135deg, #4980f7 0%, #19bbd3 100%);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(73, 128, 247, 0.3);
    }
    .cms-header h3 {
        margin: 0;
        font-weight: 600;
        font-size: 1.75rem;
    }
    .search-filter-box {
        background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        border: 1px solid rgba(73, 128, 247, 0.15);
    }
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(73, 128, 247, 0.1);
    }
    .table thead {
        background: linear-gradient(135deg, #4980f7 0%, #19bbd3 100%);
        color: white;
    }
    .table thead th {
        border: none;
        padding: 1.25rem 1rem;
        font-weight: 600;
        color: white;
    }
    .table tbody tr:hover {
        background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
    }
</style>
@endpush

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

