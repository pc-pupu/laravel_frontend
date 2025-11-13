@extends('admin.layouts.app')

@section('title', 'Roles Management')

@section('page-title', 'Roles Management')

@section('content')
<div class="admin-card">
    <div class="admin-card-header">
        <h3>Roles</h3>
        <button class="btn-admin btn-admin-primary btn-admin-sm" data-bs-toggle="modal" data-bs-target="#roleModal" onclick="openRoleModal()">
            <i class="fas fa-plus"></i> Add Role
        </button>
    </div>
    <div class="admin-card-body">
        <div class="mb-3">
            <input type="text" id="search-roles" class="form-control" placeholder="Search roles..." onkeyup="searchRoles()">
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Guard Name</th>
                        <th>Permissions</th>
                        <th>Users</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="roles-table-body">
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="spinner"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="roles-pagination"></div>
    </div>
</div>

<!-- Role Modal -->
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
                    <div class="form-group">
                        <label class="form-label">Role Name *</label>
                        <input type="text" id="role-name" name="name" class="form-control" required oninput="clearFieldError('role-name')">
                        <div class="invalid-feedback" id="role-name-error"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Guard Name</label>
                        <input type="text" id="role-guard-name" name="guard_name" class="form-control" value="web" oninput="clearFieldError('role-guard-name')">
                        <small class="text-muted">Default: web</small>
                        <div class="invalid-feedback" id="role-guard-name-error"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Permissions</label>
                        <div id="permissions-list" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px;">
                            <!-- Permissions will be loaded dynamically -->
                        </div>
                        <div class="invalid-feedback d-block" id="permissions-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-admin btn-admin-primary" onclick="saveRole()">Save Role</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('/assets/admin/js/roles.js') }}"></script>
@endsection

