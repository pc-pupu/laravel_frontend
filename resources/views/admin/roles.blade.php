@extends('admin.layouts.app')

@section('title', 'Roles Management')
@section('page-title', 'Roles Management')

@section('content')

<div class="admin-card">
    <div class="admin-card-header">
        <h3>Roles</h3>
        <button class="btn-admin btn-admin-primary btn-admin-sm"
                data-bs-toggle="modal"
                data-bs-target="#roleModal"
                onclick="openRoleModal()">
            <i class="fas fa-plus"></i> Add Role
        </button>
    </div>

    <div class="admin-card-body">

        {{-- Search --}}
        <div class="mb-3">
            <input type="text"
                   id="search-roles"
                   class="form-control"
                   placeholder="Search roles..."
                   onkeyup="searchRoles()">
        </div>

        {{-- Roles Table --}}
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Guard</th>
                        <th>Permissions</th>
                        <th>Users</th>
                        <th>Is Active?</th>
                        <th style="width: 160px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="roles-table-body">
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="spinner"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div id="roles-pagination" class="mt-3"></div>

    </div>
</div>


<!-- =============================== -->
<!-- Role Modal -->
<!-- =============================== -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="roleModalTitle">Add Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <form id="roleForm">
                    <input type="hidden" id="role-id" name="id">

                    <div class="form-group mb-2">
                        <label class="form-label">Role Name *</label>
                        <input type="text" id="role-name" name="name"
                               class="form-control" required
                               oninput="clearFieldError('role-name')">
                        <div class="invalid-feedback" id="role-name-error"></div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Guard Name</label>
                        <input type="text" id="role-guard-name" name="guard_name"
                               value="web"
                               class="form-control"
                               oninput="clearFieldError('role-guard-name')">
                        <small class="text-muted">Default: web</small>
                        <div class="invalid-feedback" id="role-guard-name-error"></div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Permissions</label>

                        <div id="permissions-list"
                             style="max-height: 300px; overflow-y: auto;
                                    border: 1px solid #dee2e6;
                                    padding: 15px; border-radius: 5px;">
                            <!-- Loaded dynamically -->
                        </div>

                        <div class="invalid-feedback d-block" id="permissions-error"></div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label d-block">Status</label>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_active" id="status-active" value="1" checked >
                            <label class="form-check-label" for="status-active">Active</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_active" id="status-inactive" value="0" >
                            <label class="form-check-label" for="status-inactive">Inactive</label>
                        </div>

                        <!-- Hidden input for validation handling -->
                        <input type="hidden" id="role-status">

                        <div class="invalid-feedback d-block" id="role-status-error"></div>
                    </div>

                </form>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn-admin btn-admin-secondary"
                        data-bs-dismiss="modal">Cancel</button>

                <button type="button" class="btn-admin btn-admin-primary"
                        onclick="saveRole()">
                    Save Role
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
    <script src="{{ asset('/assets/admin/js/roles.js') }}"></script>
@endsection
