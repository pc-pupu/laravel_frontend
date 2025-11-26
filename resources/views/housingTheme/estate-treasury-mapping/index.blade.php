@extends('housingTheme.layouts.app')
@section('title', 'Estate Treasury Mapping')
@section('page-header', 'View Estate Treasury Mapping')

@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div>
                            <h3><i class="fa fa-map me-2"></i> Estate Treasury Mapping</h3>
                            <p>Manage estate to treasury mappings</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <a href="{{ route('estate-treasury-selection.create') }}" class="btn btn-light">
                                <i class="fa fa-plus me-2"></i> Add New
                            </a>
                        </div>
                    </div>
                </div>

                <div class="search-filter-box">
                    <form method="GET" action="{{ route('estate-treasury-selection.index') }}" class="row g-3">
                        <div class="col-md-10">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="search" name="search" 
                                    placeholder="Search" value="{{ $filters['search'] ?? '' }}">
                                <label for="search"><i class="fa fa-search me-2"></i>Search by Estate Name or Treasury Name</label>
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
                                    <th>Estate Name</th>
                                    <th>Treasury Name</th>
                                    <th>Activation Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mappings as $index => $item)
                                    <tr>
                                        <td><strong>{{ $mappings->firstItem() + $index }}</strong></td>
                                        <td>{{ $item['estate_name'] ?? '-' }}</td>
                                        <td>{{ $item['treasury_name'] ?? '-' }}</td>
                                        <td>
                                            @if(($item['is_active'] ?? 0) == 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('estate-treasury-selection.edit', \App\Helpers\UrlEncryptionHelper::encryptUrl($item['housing_treasury_estate_mapping_id'])) }}" 
                                                class="btn btn-sm bg-primary px-3 rounded-pill text-white fw-bolder">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            <a href="{{ route('estate-treasury-selection.destroy', \App\Helpers\UrlEncryptionHelper::encryptUrl($item['housing_treasury_estate_mapping_id'])) }}" 
                                                class="btn btn-sm bg-primary px-3 rounded-pill text-white fw-bolder"
                                                onclick="return confirm('Are you sure you want to Delete?')">
                                                <i class="fa fa-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fa fa-inbox fa-2x mb-2"></i>
                                                <p class="mb-0">No data found!</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($mappings->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $mappings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

