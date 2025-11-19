@extends('housingTheme.layouts.app')
@section('title', 'CMS Content')
@section('page-header', 'CMS Content Management')


@section('content')
<div class="cms-wrapper">
    <div class="row">
        <div class="col-md-12">
            <div class="cms-card">
                <div class="cms-header">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <div>
                            <h3><i class="fa fa-file-text me-2"></i> CMS Content Management</h3>
                            <p>Manage About Us, FAQ, Notice, Contact and other CMS sections.</p>
                        </div>
                        <a href="{{ route('cms-content.create') }}" class="btn btn-add-cms mt-3 mt-md-0">
                            <i class="fa fa-plus me-2"></i> Add New Content
                        </a>
                    </div>
                </div>

                <div class="search-filter-box">
                    <form method="GET" action="{{ route('cms-content.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="search" name="search" placeholder="Search"
                                    value="{{ $filters['search'] ?? '' }}">
                                <label for="search"><i class="fa fa-search me-2"></i>Search Title / Link Title</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select" id="content_type" name="content_type">
                                    <option value="">All Content Types</option>
                                    @foreach ($contentTypes as $value => $label)
                                        <option value="{{ $value }}" @selected(($filters['content_type'] ?? '') === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="content_type"><i class="fa fa-filter me-2"></i>Content Type</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="">All Status</option>
                                    <option value="1" @selected(($filters['is_active'] ?? '') === '1')>Active</option>
                                    <option value="0" @selected(($filters['is_active'] ?? '') === '0')>Inactive</option>
                                </select>
                                <label for="is_active"><i class="fa fa-toggle-on me-2"></i>Status</label>
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button class="btn btn-search w-100" type="submit">
                                <i class="fa fa-search"></i>
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
                                    <th>Content Type</th>
                                    <th>Title</th>
                                    <th>Notification Date</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Is New</th>
                                    <th>File</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($contents as $index => $item)
                                    <tr>
                                        <td><strong>{{ $contents->firstItem() + $index }}</strong></td>
                                        <td>
                                            <span class="badge badge-custom bg-info text-white">
                                                {{ str_replace('_', ' ', strtoupper($item['content_type'])) }}
                                            </span>
                                        </td>
                                        <td><strong>{{ $item['content_title'] }}</strong></td>
                                        <td>{{ $item['date_of_notification'] ? \Carbon\Carbon::parse($item['date_of_notification'])->format('d/m/Y') : '-' }}</td>
                                        <td>
                                            <span class="badge badge-custom" style="background: linear-gradient(135deg, #4980f7 0%, #19bbd3 100%); color: white;">
                                                {{ $item['order_no'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-custom bg-{{ $item['is_active'] ? 'success' : 'danger' }}">
                                                <i class="fa fa-{{ $item['is_active'] ? 'check-circle' : 'times-circle' }} me-1"></i>
                                                {{ $item['is_active'] ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-custom bg-{{ $item['is_new'] ? 'warning text-dark' : 'secondary' }}">
                                                {{ $item['is_new'] ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($item['file_url'])
                                                <a href="{{ $item['file_url'] }}" target="_blank" class="btn btn-outline-danger btn-sm">
                                                    <i class="fa fa-file-pdf"></i> PDF
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('cms-content.edit', $item['housing_cms_id']) }}" class="btn btn-action-edit">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('cms-content.destroy', $item['housing_cms_id']) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this content?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-action-delete" type="submit">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5">
                                            <i class="fa fa-inbox fa-3x mb-3 d-block" style="color: #4980f7; opacity: 0.5;"></i>
                                            <p class="mb-0">No content available.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $contents->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

