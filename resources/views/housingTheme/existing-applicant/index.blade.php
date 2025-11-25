@extends('housingTheme.layouts.app')
@section('title', 'Existing Applicants')
@section('page-header', $title ?? 'Legacy Applicant List')



@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div>
                            <h3><i class="fa fa-users me-2"></i> {{ $title ?? 'Legacy Applicant List' }}</h3>
                            <p>Manage existing/physical applicants (waiting list applicants)</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <a href="{{ route('existing-applicant.search') }}" class="btn btn-light me-2">
                                <i class="fa fa-search me-2"></i> Search
                            </a>
                            <a href="{{ route('existing-applicant.create') }}" class="btn btn-light">
                                <i class="fa fa-plus me-2"></i> Add New
                            </a>
                        </div>
                    </div>
                </div>

                <div class="search-filter-box">
                    <form method="GET" action="{{ route('existing-applicant.index') }}" class="row g-3">
                        <div class="col-md-10">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="search" name="search" 
                                    placeholder="Search" value="{{ $filters['search'] ?? '' }}">
                                <label for="search"><i class="fa fa-search me-2"></i>Search by Name, Application No, or Computer Serial No</label>
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
                                    <th>Application No</th>
                                    <th>Applicant Name</th>
                                    <th>Date of Application</th>
                                    <th>Computer Serial No</th>
                                    <th>HRMS ID</th>
                                    <th>Mobile No</th>
                                    <th>Gender</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($applicants as $index => $item)
                                    <tr>
                                        <td><strong>{{ $applicants->firstItem() + $index }}</strong></td>
                                        <td><strong>{{ $item['application_no'] ?? '-' }}</strong></td>
                                        <td>{{ $item['applicant_name'] ?? '-' }}</td>
                                        <td>{{ $item['date_of_application'] ? \Carbon\Carbon::parse($item['date_of_application'])->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $item['computer_serial_no'] ?? '-' }}</td>
                                        <td>{{ $item['hrms_id'] ?? '-' }}</td>
                                        <td>{{ $item['mobile_no'] ?? '-' }}</td>
                                        <td>{{ $item['gender'] ?? '-' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('existing-applicant.view', encrypt($item['online_application_id'])) }}" 
                                                class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('existing-applicant.edit', encrypt($item['online_application_id'])) }}" 
                                                class="btn btn-sm btn-info">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5">
                                            <i class="fa fa-inbox fa-3x mb-3 d-block" style="color: #4980f7; opacity: 0.5;"></i>
                                            <p class="mb-0">No applicants found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $applicants->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

