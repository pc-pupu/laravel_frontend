@extends('admin.layouts.app')

@section('title', 'Permissions Management')
@section('page-title', 'Permissions Management')

@section('content')

<div class="admin-card">
    <div class="admin-card-header">
        <h3>Permissions</h3>

        <button class="btn-admin btn-admin-primary btn-admin-sm"
                data-bs-toggle="modal"
                data-bs-target="#permissionModal"
                onclick="openPermissionModal()">
            <i class="fas fa-plus"></i> Add Permission
        </button>
    </div>

    <div class="admin-card-body">

        {{-- Search + Filter --}}
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <input type="text"
                       id="search-permissions"
                       class="form-control"
                       placeholder="Search permissions..."
                       onkeyup="searchPermissions()">
            </div>

            <div class="col-md-6 mb-2">
                <select id="filter-guard"
                        class="form-select"
                        onchange="loadPermissions()">
                    <option value="">All Guards</option>
                </select>
            </div>
        </div>

        {{-- Permissions Table --}}
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Guard</th>
                        <th>Roles</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>

                <tbody id="permissions-table-body">
                    <tr>
                        <td colspan="5" class="text-center">
                            <div class="spinner"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div id="permissions-pagination" class="mt-3"></div>

    </div>
</div>


<!-- ================================ -->
<!-- Permission Modal -->
<!-- ================================ -->
<div class="modal fade" id="permissionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="permissionModalTitle">Add Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="permissionForm">

                    <input type="hidden" id="permission-id" name="id">

                    <div class="form-group mb-2">
                        <label class="form-label">Permission Name *</label>
                        <input type="text"
                               id="permission-name"
                               name="name"
                               class="form-control"
                               required
                               oninput="clearPermissionFieldError('permission-name')">

                        <small class="text-muted">Example: <code>admin.users.create</code></small>

                        <div class="invalid-feedback" id="permission-name-error"></div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Guard Name</label>

                        <input type="text"
                               id="permission-guard-name"
                               name="guard_name"
                               class="form-control"
                               value="web"
                               oninput="clearPermissionFieldError('permission-guard-name')">

                        <small class="text-muted">Default: web</small>

                        <div class="invalid-feedback" id="permission-guard-name-error"></div>
                    </div>

                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn-admin btn-admin-secondary"
                        data-bs-dismiss="modal">Cancel</button>

                <button type="button" class="btn-admin btn-admin-primary"
                        onclick="savePermission()">
                    Save Permission
                </button>
            </div>

        </div>
    </div>
</div>

@endsection


{{-- ============================ --}}
{{-- Correct Script Order --}}
{{-- ============================ --}}
@section('scripts')
    {{-- <script src="{{ asset('/assets/admin/js/admin.js') }}"></script> --}}
    <script src="{{ asset('/assets/admin/js/permissions.js') }}"></script>
@endsection
