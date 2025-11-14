@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-card-icon"><i class="fas fa-users"></i></div>
        <div class="stat-card-value" id="total-users">-</div>
        <div class="stat-card-label">Total Users</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon"><i class="fas fa-user-tag"></i></div>
        <div class="stat-card-value" id="total-roles">-</div>
        <div class="stat-card-label">Total Roles</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon"><i class="fas fa-key"></i></div>
        <div class="stat-card-value" id="total-permissions">-</div>
        <div class="stat-card-label">Total Permissions</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="stat-card-value" id="total-errors">-</div>
        <div class="stat-card-label">Error Logs (Today)</div>
    </div>

</div>


<div class="admin-card mt-4">
    <div class="admin-card-header">
        <h3>Recent Activity</h3>
    </div>
    <div class="admin-card-body">
        <p>
            Welcome to the Admin Panel.  
            Use the navigation to manage users, roles, permissions, and view system error logs.
        </p>
    </div>
</div>

@endsection


{{-- ============================ --}}
{{-- Scripts --}}
{{-- ============================ --}}
@section('scripts')
{{-- <script src="{{ asset('/assets/admin/js/admin.js') }}"></script> --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    loadStats();
});

/**
 * Dashboard Summary Stats Loader
 * Uses secure proxy calls (no token in browser)
 */
async function loadStats() {

    try {
        // Users count
        const users = await fetchProxy('/admin/users/list?page=1&per_page=1');
        document.getElementById('total-users').textContent =
            users?.data?.total ?? '-';

        // Roles count
        const roles = await fetchProxy('/admin/roles/list?page=1&per_page=1');
        document.getElementById('total-roles').textContent =
            roles?.data?.total ?? '-';

        // Permissions count
        const perms = await fetchProxy('/admin/permissions/list?page=1&per_page=1');
        document.getElementById('total-permissions').textContent =
            perms?.data?.total ?? '-';

        // Error logs today
        const errors = await fetchProxy('/admin/error-logs/statistics');
        document.getElementById('total-errors').textContent =
            errors?.data?.today ?? '-';

    } catch (e) {
        console.error("Dashboard load error:", e);

        document.getElementById('total-users').textContent = 'Error';
        document.getElementById('total-roles').textContent = 'Error';
        document.getElementById('total-permissions').textContent = 'Error';
        document.getElementById('total-errors').textContent = 'Error';
    }
}
</script>
@endsection
