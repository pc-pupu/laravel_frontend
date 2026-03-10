// error-logs.js — Error logs listing & management via /admin/error-logs/*


const H = window.adminHelpers;
let logsCurrentPage = 1;

async function loadErrorLogs(page = 1) {
    logsCurrentPage = page;
    const level = document.getElementById('filter-level')?.value || '';
    const dateFrom = document.getElementById('filter-date-from')?.value || '';
    const dateTo = document.getElementById('filter-date-to')?.value || '';
    const search = document.getElementById('search-logs')?.value || '';

    let url = `/admin/error-logs/list?page=${page}&per_page=15`;
    if (level) url += `&level=${encodeURIComponent(level)}`;
    if (dateFrom) url += `&date_from=${encodeURIComponent(dateFrom)}`;
    if (dateTo) url += `&date_to=${encodeURIComponent(dateTo)}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;

    try {
        const data = await H.apiFetch(url, { method: 'GET' });
        if (data && data.status === 'success') {
            displayErrorLogs(data.data.data || []);
            displayErrorLogsPagination(data.data);
        } else {
            H.showError(data?.message || 'Failed to load error logs');
        }
    } catch (err) {
        H.showError('Error loading error logs: ' + (err.message || 'Unknown'));
    }
}

function displayErrorLogs(logs) {
    const tbody = document.getElementById('logs-table-body');
    if (!tbody) return;
    if (!logs || logs.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No error logs found</td></tr>';
        return;
    }

    tbody.innerHTML = logs.map(log => {
        const levelBadge = getLevelBadge(log.level);
        const userName = log.user ? log.user.name : '—';
        const time = H.formatDate(log.created_at);
        const errorType = log.exception_type ? (log.exception_type.length > 40 ? log.exception_type.substring(0, 40) + '…' : log.exception_type) : '—';
        const msg = log.message ? (log.message.length > 50 ? log.message.substring(0, 50) + '…' : log.message) : '—';

        return `
            <tr>
                <td>${log.id}</td>
                <td>${levelBadge}</td>
                <td><span class="small" title="${(log.exception_type || '').replace(/"/g, '&quot;')}">${errorType}</span></td>
                <td>${msg}</td>
                <td>${userName}</td>
                <td>${time}</td>
                <td>
                    <button class="btn-admin btn-admin-sm btn-admin-primary" onclick="viewLogDetail(${log.id})"><i class="fas fa-eye"></i> View</button>
                    <button class="btn-admin btn-admin-sm btn-admin-danger" onclick="deleteLog(${log.id})"><i class="fas fa-trash"></i> Delete</button>
                </td>
            </tr>
        `;
    }).join('');
}

function getLevelBadge(level) {
    const badges = {
        'error': '<span class="badge badge-danger">Error</span>',
        'warning': '<span class="badge badge-warning">Warning</span>',
        'info': '<span class="badge badge-info">Info</span>',
        'debug': '<span class="badge badge-secondary">Debug</span>'
    };
    return badges[level] || `<span class="badge">${level}</span>`;
}

function displayErrorLogsPagination(pagination) {
    const paginationDiv = document.getElementById('logs-pagination');
    if (!paginationDiv) return;
    if (!pagination || !pagination.total) { paginationDiv.innerHTML = ''; return; }
    const totalPages = pagination.last_page;
    let html = '<div class="pagination">';
    if (pagination.current_page > 1) html += `<a href="#" class="page-link" onclick="loadErrorLogs(${pagination.current_page - 1}); return false;">Previous</a>`;
    for (let i = 1; i <= totalPages; i++) {
        html += i === pagination.current_page ? `<span class="page-link active">${i}</span>` : `<a href="#" class="page-link" onclick="loadErrorLogs(${i}); return false;">${i}</a>`;
    }
    if (pagination.current_page < totalPages) html += `<a href="#" class="page-link" onclick="loadErrorLogs(${pagination.current_page + 1}); return false;">Next</a>`;
    html += '</div>';
    paginationDiv.innerHTML = html;
}

const searchLogs = H.debounce(() => loadErrorLogs(1), 500);
window.searchLogs = searchLogs;

async function viewLogDetail(logId) {
    try {
        const data = await H.apiFetch(`/admin/error-logs/${logId}`, { method: 'GET' });
        if (data && data.status === 'success') {
            const log = data.data;
            const content = document.getElementById('log-detail-content');
            if (content) {
                content.innerHTML = `
                    <div class="mb-3"><strong>Level:</strong> ${getLevelBadge(log.level)}</div>
                    <div class="mb-3"><strong>Error type:</strong> ${log.exception_type || '—'}</div>
                    <div class="mb-3"><strong>Message:</strong><br><pre style="background:#f5f5f5;padding:10px;border-radius:5px;white-space:pre-wrap;">${log.message || '-'}</pre></div>
                    <div class="mb-3"><strong>User:</strong> ${log.user ? log.user.name : '—'}</div>
                    <div class="mb-3"><strong>Time:</strong> ${H.formatDate(log.created_at)}</div>
                    <div class="mb-3"><strong>URL:</strong> ${log.url || '-'}</div>
                    <div class="mb-3"><strong>Method:</strong> ${log.method || '-'}</div>
                    <div class="mb-3"><strong>IP Address:</strong> ${log.ip_address || '-'}</div>
                    <div class="mb-3"><strong>File:</strong> ${log.file || '-'}</div>
                    <div class="mb-3"><strong>Line:</strong> ${log.line || '-'}</div>
                    ${log.context ? `<div class="mb-3"><strong>Context:</strong><pre style="background:#f5f5f5;padding:10px;border-radius:5px;white-space:pre-wrap;">${JSON.stringify(log.context,null,2)}</pre></div>` : ''}
                    ${log.trace ? `<div class="mb-3"><strong>Stack Trace:</strong><pre style="background:#f5f5f5;padding:10px;border-radius:5px;white-space:pre-wrap;max-height:400px;overflow-y:auto;">${JSON.stringify(log.trace,null,2)}</pre></div>` : ''}
                `;
            }
            new bootstrap.Modal(document.getElementById('logDetailModal')).show();
        } else {
            H.showError(data?.message || 'Failed to load log details');
        }
    } catch (err) {
        H.showError('Error loading log details: ' + (err.message || 'Unknown'));
    }
}

async function deleteLog(logId) {
    if (!confirm('Are you sure you want to delete this error log?')) return;
    try {
        const data = await H.apiFetch(`/admin/error-logs/${logId}`, { method: 'DELETE' });
        if (data && data.status === 'success') {
            H.showNotification('Error log deleted', 'success');
            loadErrorLogs(logsCurrentPage);
        } else {
            H.showError(data?.message || 'Failed to delete error log');
        }
    } catch (err) {
        H.showError('Error deleting error log: ' + (err.message || 'Unknown'));
    }
}

async function clearAllLogs() {
    if (!confirm('Clear all error logs? This cannot be undone.')) return;
    try {
        const data = await H.apiFetch('/admin/error-logs', { method: 'DELETE' });
        if (data && data.status === 'success') {
            H.showNotification('All error logs cleared', 'success');
            loadErrorLogs(1);
        } else {
            H.showError(data?.message || 'Failed to clear logs');
        }
    } catch (err) {
        H.showError('Error clearing logs: ' + (err.message || 'Unknown'));
    }
}

async function clearByTime() {
    const olderThanDays = document.getElementById('clear-older-than-days')?.value || '';
    const dateFrom = document.getElementById('clear-date-from')?.value || '';
    const dateTo = document.getElementById('clear-date-to')?.value || '';

    if (!olderThanDays && !dateFrom && !dateTo) {
        H.showError('Select "Older than" days or set From/To date.');
        return;
    }
    if (olderThanDays && (dateFrom || dateTo)) {
        H.showError('Use either "Older than" or date range, not both.');
        return;
    }

    const msg = olderThanDays
        ? `Delete all error logs older than ${olderThanDays} day(s)?`
        : `Delete error logs from ${dateFrom || '…'} to ${dateTo || '…'}?`;
    if (!confirm(msg)) return;

    const body = olderThanDays ? { older_than_days: parseInt(olderThanDays, 10) } : {};
    if (dateFrom) body.date_from = dateFrom;
    if (dateTo) body.date_to = dateTo;

    try {
        const data = await H.apiFetch('/admin/error-logs/clear-by-time', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        if (data && data.status === 'success') {
            H.showNotification(data.message || 'Errors cleared', 'success');
            loadErrorLogs(1);
            document.getElementById('clear-older-than-days').value = '';
            document.getElementById('clear-date-from').value = '';
            document.getElementById('clear-date-to').value = '';
        } else {
            H.showError(data?.message || 'Failed to clear logs');
        }
    } catch (err) {
        H.showError('Error clearing logs: ' + (err.message || 'Unknown'));
    }
}

window.loadErrorLogs = loadErrorLogs;
window.viewLogDetail = viewLogDetail;
window.deleteLog = deleteLog;
window.clearAllLogs = clearAllLogs;
window.clearByTime = clearByTime;

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => loadErrorLogs(1), 50);
});
