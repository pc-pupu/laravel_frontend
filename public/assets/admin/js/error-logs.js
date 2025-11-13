// Error Logs Management JavaScript

let currentPage = 1;

// Get token helper function
function getToken() {
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
    const hiddenInput = document.getElementById('session-api-token');
    const tokenFromInput = hiddenInput ? hiddenInput.value : '';
    return window.API_TOKEN || 
           getCookie('api_token') || 
           localStorage.getItem('api_token') || 
           tokenFromInput || '';
}

// Load error logs on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        loadErrorLogs();
    }, 100);
});

// Load error logs from API
async function loadErrorLogs(page = 1) {
    currentPage = page;
    const level = document.getElementById('filter-level')?.value || '';
    const dateFrom = document.getElementById('filter-date-from')?.value || '';
    const dateTo = document.getElementById('filter-date-to')?.value || '';
    const search = document.getElementById('search-logs')?.value || '';
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';
    
    if (!token) {
        showError('Authentication token not found. Please refresh the page.');
        return;
    }
    
    let url = `${apiBaseUrl}/admin/error-logs?page=${page}&per_page=15`;
    if (level) {
        url += `&level=${encodeURIComponent(level)}`;
    }
    if (dateFrom) {
        url += `&date_from=${encodeURIComponent(dateFrom)}`;
    }
    if (dateTo) {
        url += `&date_to=${encodeURIComponent(dateTo)}`;
    }
    if (search) {
        url += `&search=${encodeURIComponent(search)}`;
    }

    try {
        const response = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            if (response.status === 401) {
                showError('Unauthorized. Please login again.');
                return;
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.status === 'success') {
            displayErrorLogs(data.data.data || []);
            displayPagination(data.data);
        } else {
            showError(data.message || 'Failed to load error logs');
        }
    } catch (error) {
        console.error('Error loading error logs:', error);
        showError('Error loading error logs: ' + error.message);
    }
}

// Display error logs in table
function displayErrorLogs(logs) {
    const tbody = document.getElementById('logs-table-body');
    
    if (logs.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">No error logs found</td></tr>';
        return;
    }

    tbody.innerHTML = logs.map(log => {
        const levelBadge = getLevelBadge(log.level);
        const userName = log.user ? log.user.name : '-';
        const date = new Date(log.created_at).toLocaleString();

        return `
            <tr>
                <td>${log.id}</td>
                <td>${levelBadge}</td>
                <td>${log.message ? (log.message.length > 50 ? log.message.substring(0, 50) + '...' : log.message) : '-'}</td>
                <td>${userName}</td>
                <td>${log.url ? (log.url.length > 30 ? log.url.substring(0, 30) + '...' : log.url) : '-'}</td>
                <td>${log.ip_address || '-'}</td>
                <td>${date}</td>
                <td>
                    <button class="btn-admin btn-admin-sm btn-admin-primary" onclick="viewLogDetail(${log.id})">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button class="btn-admin btn-admin-sm btn-admin-danger" onclick="deleteLog(${log.id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

// Get level badge
function getLevelBadge(level) {
    const badges = {
        'error': '<span class="badge badge-danger">Error</span>',
        'warning': '<span class="badge badge-warning">Warning</span>',
        'info': '<span class="badge badge-info">Info</span>',
        'debug': '<span class="badge badge-secondary">Debug</span>'
    };
    return badges[level] || `<span class="badge">${level}</span>`;
}

// Display pagination
function displayPagination(pagination) {
    const paginationDiv = document.getElementById('logs-pagination');
    if (!pagination || !pagination.total) {
        paginationDiv.innerHTML = '';
        return;
    }

    const totalPages = pagination.last_page;
    let html = '<div class="pagination">';

    if (pagination.current_page > 1) {
        html += `<a href="#" class="page-link" onclick="loadErrorLogs(${pagination.current_page - 1}); return false;">Previous</a>`;
    }

    for (let i = 1; i <= totalPages; i++) {
        if (i === pagination.current_page) {
            html += `<span class="page-link" style="background: var(--primary-gradient); color: #fff;">${i}</span>`;
        } else {
            html += `<a href="#" class="page-link" onclick="loadErrorLogs(${i}); return false;">${i}</a>`;
        }
    }

    if (pagination.current_page < totalPages) {
        html += `<a href="#" class="page-link" onclick="loadErrorLogs(${pagination.current_page + 1}); return false;">Next</a>`;
    }

    html += '</div>';
    paginationDiv.innerHTML = html;
}

// Search logs
function searchLogs() {
    clearTimeout(searchLogs.timeout);
    searchLogs.timeout = setTimeout(() => {
        loadErrorLogs(1);
    }, 500);
}

// View log detail
async function viewLogDetail(logId) {
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';

    if (!token) {
        showError('Authentication token not found.');
        return;
    }

    try {
        const response = await fetch(`${apiBaseUrl}/admin/error-logs/${logId}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.status === 'success') {
            const log = data.data;
            const content = document.getElementById('log-detail-content');
            
            content.innerHTML = `
                <div class="mb-3">
                    <strong>Level:</strong> ${getLevelBadge(log.level)}
                </div>
                <div class="mb-3">
                    <strong>Message:</strong><br>
                    <pre style="background: #f5f5f5; padding: 10px; border-radius: 5px; white-space: pre-wrap;">${log.message || '-'}</pre>
                </div>
                <div class="mb-3">
                    <strong>User:</strong> ${log.user ? log.user.name : '-'}
                </div>
                <div class="mb-3">
                    <strong>URL:</strong> ${log.url || '-'}
                </div>
                <div class="mb-3">
                    <strong>Method:</strong> ${log.method || '-'}
                </div>
                <div class="mb-3">
                    <strong>IP Address:</strong> ${log.ip_address || '-'}
                </div>
                <div class="mb-3">
                    <strong>File:</strong> ${log.file || '-'}
                </div>
                <div class="mb-3">
                    <strong>Line:</strong> ${log.line || '-'}
                </div>
                <div class="mb-3">
                    <strong>Date:</strong> ${new Date(log.created_at).toLocaleString()}
                </div>
                ${log.context ? `
                <div class="mb-3">
                    <strong>Context:</strong><br>
                    <pre style="background: #f5f5f5; padding: 10px; border-radius: 5px; white-space: pre-wrap;">${JSON.stringify(log.context, null, 2)}</pre>
                </div>
                ` : ''}
                ${log.trace ? `
                <div class="mb-3">
                    <strong>Stack Trace:</strong><br>
                    <pre style="background: #f5f5f5; padding: 10px; border-radius: 5px; white-space: pre-wrap; max-height: 400px; overflow-y: auto;">${JSON.stringify(log.trace, null, 2)}</pre>
                </div>
                ` : ''}
            `;

            new bootstrap.Modal(document.getElementById('logDetailModal')).show();
        } else {
            showError(data.message || 'Failed to load log details');
        }
    } catch (error) {
        console.error('Error loading log detail:', error);
        showError('Error loading log details: ' + error.message);
    }
}

// Delete log
async function deleteLog(logId) {
    if (!confirm('Are you sure you want to delete this error log?')) {
        return;
    }

    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';

    if (!token) {
        showError('Authentication token not found.');
        return;
    }

    try {
        const response = await fetch(`${apiBaseUrl}/admin/error-logs/${logId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.status === 'success') {
            showNotification('Error log deleted successfully', 'success');
            loadErrorLogs(currentPage);
        } else {
            showError(data.message || 'Failed to delete error log');
        }
    } catch (error) {
        console.error('Error deleting log:', error);
        showError('Error deleting error log: ' + error.message);
    }
}

// Clear all logs
async function clearAllLogs() {
    if (!confirm('Are you sure you want to clear all error logs? This action cannot be undone.')) {
        return;
    }

    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';

    if (!token) {
        showError('Authentication token not found.');
        return;
    }

    try {
        const response = await fetch(`${apiBaseUrl}/admin/error-logs`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.status === 'success') {
            showNotification('All error logs cleared successfully', 'success');
            loadErrorLogs(1);
        } else {
            showError(data.message || 'Failed to clear error logs');
        }
    } catch (error) {
        console.error('Error clearing logs:', error);
        showError('Error clearing error logs: ' + error.message);
    }
}

// Helper functions
function showError(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger alert-dismissible fade show';
    alert.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    const content = document.querySelector('.admin-content');
    if (content) {
        content.insertBefore(alert, content.firstChild);
        setTimeout(() => alert.remove(), 5000);
    }
}

function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show`;
    alert.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    const content = document.querySelector('.admin-content');
    if (content) {
        content.insertBefore(alert, content.firstChild);
        setTimeout(() => alert.remove(), 5000);
    }
}

