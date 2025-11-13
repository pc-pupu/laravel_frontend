@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-card-value" id="total-users">-</div>
        <div class="stat-card-label">Total Users</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon">
            <i class="fas fa-user-tag"></i>
        </div>
        <div class="stat-card-value" id="total-roles">-</div>
        <div class="stat-card-label">Total Roles</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon">
            <i class="fas fa-key"></i>
        </div>
        <div class="stat-card-value" id="total-permissions">-</div>
        <div class="stat-card-label">Total Permissions</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-card-value" id="total-errors">-</div>
        <div class="stat-card-label">Error Logs (Today)</div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h3>Recent Activity</h3>
    </div>
    <div class="admin-card-body">
        <p>Welcome to the Admin Panel. Use the navigation menu to manage users, roles, permissions, and view error logs.</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Wait for API_TOKEN to be set from layout
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to ensure window.API_TOKEN is set
    setTimeout(function() {
        loadStats();
    }, 100);
});

function getToken() {
    // Try multiple sources for the token
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
    
    // Get from hidden input (most reliable since it's set server-side)
    const hiddenInput = document.getElementById('session-api-token');
    const tokenFromInput = hiddenInput ? hiddenInput.value : '';
    
    return window.API_TOKEN || 
           getCookie('api_token') || 
           localStorage.getItem('api_token') || 
           tokenFromInput ||
           '{{ session("api_token") }}' || 
           '';
}

function loadStats() {
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || '{{ config("services.api.base_url", "http://localhost:8000/api") }}';
    
    if (!token) {
        console.error('API token not found. Please login again.');
        // Show error message
        const errorMsg = document.createElement('div');
        errorMsg.className = 'alert alert-danger';
        errorMsg.textContent = 'Authentication token not found. Please refresh the page or login again.';
        document.querySelector('.admin-content').insertBefore(errorMsg, document.querySelector('.admin-content').firstChild);
        return;
    }
    
    // Load user count
    fetch(`${apiBaseUrl}/admin/users?per_page=1`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) {
            if (res.status === 401) {
                throw new Error('Unauthorized - Token may be invalid');
            }
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
    })
    .then(data => {
        if (data.status === 'success' && data.data && data.data.total !== undefined) {
            document.getElementById('total-users').textContent = data.data.total;
        }
    })
    .catch(err => {
        console.error('Error loading users:', err);
        document.getElementById('total-users').textContent = 'Error';
    });

    // Load role count
    fetch(`${apiBaseUrl}/admin/roles?per_page=1`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) {
            if (res.status === 401) {
                throw new Error('Unauthorized - Token may be invalid');
            }
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
    })
    .then(data => {
        if (data.status === 'success' && data.data && data.data.total !== undefined) {
            document.getElementById('total-roles').textContent = data.data.total;
        }
    })
    .catch(err => {
        console.error('Error loading roles:', err);
        document.getElementById('total-roles').textContent = 'Error';
    });

    // Load permission count
    fetch(`${apiBaseUrl}/admin/permissions?per_page=1`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) {
            if (res.status === 401) {
                throw new Error('Unauthorized - Token may be invalid');
            }
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
    })
    .then(data => {
        if (data.status === 'success' && data.data && data.data.total !== undefined) {
            document.getElementById('total-permissions').textContent = data.data.total;
        }
    })
    .catch(err => {
        console.error('Error loading permissions:', err);
        document.getElementById('total-permissions').textContent = 'Error';
    });

    // Load error log statistics
    fetch(`${apiBaseUrl}/admin/error-logs/statistics`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(res => {
        if (!res.ok) {
            if (res.status === 401) {
                throw new Error('Unauthorized - Token may be invalid');
            }
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        return res.json();
    })
    .then(data => {
        if (data.status === 'success' && data.data && data.data.today !== undefined) {
            document.getElementById('total-errors').textContent = data.data.today;
        }
    })
    .catch(err => {
        console.error('Error loading error logs:', err);
        document.getElementById('total-errors').textContent = 'Error';
    });
}
</script>
@endsection


@endsection

