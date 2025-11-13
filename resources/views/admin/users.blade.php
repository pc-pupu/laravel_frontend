@extends('admin.layouts.app')

@section('title', 'Users Management')

@section('page-title', 'Users Management')

@section('content')
@if(config('app.debug'))
<div class="alert alert-info mb-3" style="font-size: 12px;">
    <strong>Debug Info:</strong> 
    <button onclick="testToken()" class="btn-admin btn-admin-sm btn-admin-secondary">Test Token</button>
    <span id="token-test-result"></span>
</div>
@endif

<div class="admin-card">
    <div class="admin-card-header">
        <h3>Users</h3>
        <button class="btn-admin btn-admin-primary btn-admin-sm" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openUserModal()">
            <i class="fas fa-plus"></i> Add User
        </button>
    </div>
    <div class="admin-card-body">
        <div class="mb-3">
            <input type="text" id="search-users" class="form-control" placeholder="Search users..." onkeyup="searchUsers()">
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="users-table-body">
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="spinner"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="users-pagination"></div>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="user-id" name="id">
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" id="user-name" name="name" class="form-control" required oninput="clearUserFieldError('user-name')">
                        <div class="invalid-feedback" id="user-name-error"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" id="user-email" name="email" class="form-control" required oninput="clearUserFieldError('user-email')">
                        <div class="invalid-feedback" id="user-email-error"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password <span id="password-required">*</span></label>
                        <input type="password" id="user-password" name="password" class="form-control" oninput="clearUserFieldError('user-password')">
                        <small class="text-muted">Leave blank to keep current password (when editing)</small>
                        <div class="invalid-feedback" id="user-password-error"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" id="user-password-confirm" name="password_confirmation" class="form-control" oninput="clearUserFieldError('user-password-confirm')">
                        <div class="invalid-feedback" id="user-password-confirm-error"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Roles</label>
                        <select id="user-roles" name="roles[]" class="form-select" multiple onchange="clearUserFieldError('user-roles')">
                            <!-- Roles will be loaded dynamically -->
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple roles</small>
                        <div class="invalid-feedback d-block" id="user-roles-error"></div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select id="user-status" name="status" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <div class="invalid-feedback" id="user-status-error"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-admin btn-admin-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn-admin btn-admin-primary" onclick="saveUser()">Save User</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('/assets/admin/js/users.js') }}"></script>
@endsection

