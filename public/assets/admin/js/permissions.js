// permissions.js — Permissions management via /admin/permissions/*


const H = window.adminHelpers;

let permsCurrentPage = 1;
let permsEditingId = null;

async function loadPermissions(page = 1) {
    permsCurrentPage = page;
    const search = document.getElementById('search-permissions')?.value || '';
    const guard = document.getElementById('filter-guard')?.value || '';
    let url = `/admin/permissions/list?page=${page}&per_page=15`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (guard) url += `&guard_name=${encodeURIComponent(guard)}`;

    // Show loading in table
    const tbody = document.getElementById('permissions-table-body');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center"><div class="spinner"></div></td></tr>';
    }

    try {
        const data = await H.apiFetch(url, { method: 'GET' });
        if (data && data.status === 'success') {
            displayPermissions(data.data.data || []);
            displayPermissionsPagination(data.data);
        } else {
            H.showError(data?.message || 'Failed to load permissions');
        }
    } catch (err) {
        H.showError('Error loading permissions: ' + (err.message || 'Unknown'));
    }
}

function displayPermissions(perms) {
    const tbody = document.getElementById('permissions-table-body');
    if (!tbody) return;
    if (!perms || perms.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No permissions found</td></tr>';
        return;
    }
    tbody.innerHTML = perms.map(p => {
        const rolesCount = p.roles ? p.roles.length : 0;
        return `
            <tr>
                <td>${p.id}</td>
                <td>${p.name}</td>
                <td>${p.guard_name || 'web'}</td>
                <td>${rolesCount} role(s)</td>
                <td>
                    <button class="btn-admin btn-admin-sm btn-admin-primary" onclick="editPermission(${p.id}, this)"><i class="fas fa-edit"></i> Edit</button>
                    <button class="btn-admin btn-admin-sm btn-admin-danger" onclick="deletePermission(${p.id}, this)"><i class="fas fa-trash"></i> Delete</button>
                </td>
            </tr>
        `;
    }).join('');
}

function displayPermissionsPagination(pagination) {
    const paginationDiv = document.getElementById('permissions-pagination');
    if (!paginationDiv) return;
    if (!pagination || !pagination.total) { paginationDiv.innerHTML = ''; return; }
    const totalPages = pagination.last_page;
    let html = '<div class="pagination">';
    if (pagination.current_page > 1) html += `<a href="#" class="page-link" onclick="event.preventDefault(); const btn = this; H.setElementLoading(btn, true, 'Loading...'); loadPermissions(${pagination.current_page - 1}).finally(() => H.setElementLoading(btn, false)); return false;">Previous</a>`;
    for (let i = 1; i <= totalPages; i++) {
        html += i === pagination.current_page ? `<span class="page-link active">${i}</span>` : `<a href="#" class="page-link" onclick="event.preventDefault(); const btn = this; H.setElementLoading(btn, true, 'Loading...'); loadPermissions(${i}).finally(() => H.setElementLoading(btn, false)); return false;">${i}</a>`;
    }
    if (pagination.current_page < totalPages) html += `<a href="#" class="page-link" onclick="event.preventDefault(); const btn = this; H.setElementLoading(btn, true, 'Loading...'); loadPermissions(${pagination.current_page + 1}).finally(() => H.setElementLoading(btn, false)); return false;">Next</a>`;
    html += '</div>';
    paginationDiv.innerHTML = html;
}

const searchPermissions = H.debounce(() => loadPermissions(1), 500);
window.searchPermissions = searchPermissions;

async function loadGuards() {
    try {
        const data = await H.apiFetch('/admin/permissions/list?page=1&per_page=1000', { method: 'GET' });
        if (data && data.status === 'success') {
            const guardSelect = document.getElementById('filter-guard');
            if (guardSelect) {
                (data.data || []).forEach(item => {
                    // backend returns full permission objects; extract unique guard names
                });
                // Build unique guard names
                const guards = Array.from(new Set((data.data.data || []).map(p => p.guard_name || 'web'))).sort();
                guardSelect.innerHTML = '<option value="">All</option>' + guards.map(g => `<option value="${g}">${g}</option>`).join('');
            }
        }
    } catch (err) {
        console.error('Error loading guards:', err);
    }
}

function openPermissionModal() {
    permsEditingId = null;
    document.getElementById('permissionModalTitle').textContent = 'Add Permission';
    document.getElementById('permissionForm').reset();
    document.getElementById('permission-id').value = '';
    document.getElementById('permission-guard-name').value = 'web';
    clearPermissionErrors();
    new bootstrap.Modal(document.getElementById('permissionModal')).show();
}

async function editPermission(id, button = null) {
    permsEditingId = id;
    if (button) H.setButtonLoading(button, true);
    try {
        const data = await H.apiFetch(`/admin/permissions/${id}`, { method: 'GET' });
        if (data && data.status === 'success') {
            const perm = data.data;
            document.getElementById('permissionModalTitle').textContent = 'Edit Permission';
            document.getElementById('permission-id').value = perm.id;
            document.getElementById('permission-name').value = perm.name || '';
            document.getElementById('permission-guard-name').value = perm.guard_name || 'web';
            clearPermissionErrors();
            new bootstrap.Modal(document.getElementById('permissionModal')).show();
        } else {
            H.showError(data?.message || 'Failed to load permission');
        }
    } catch (err) {
        H.showError('Error loading permission: ' + (err.message || 'Unknown'));
    } finally {
        if (button) H.setButtonLoading(button, false);
    }
}

async function savePermission() {
    const form = document.getElementById('permissionForm');
    const saveButton = form.closest('.modal-content')?.querySelector('button[onclick*="savePermission"]') ||
                      document.querySelector('button[onclick*="savePermission"]');
    if (!form.checkValidity()) { form.reportValidity(); return; }
    
    if (saveButton) H.setButtonLoading(saveButton, true);
    
    const fd = new FormData(form);
    const payload = {
        name: fd.get('name'),
        guard_name: fd.get('guard_name') || 'web'
    };
    const id = permsEditingId;
    const method = id ? 'PUT' : 'POST';
    const url = id ? `/admin/permissions/${id}` : '/admin/permissions';
    try {
        const data = await H.apiFetch(url, { method, body: JSON.stringify(payload) });
        if (data && data.status === 'success') {
            H.showNotification(id ? 'Permission updated' : 'Permission created', 'success');
            bootstrap.Modal.getInstance(document.getElementById('permissionModal'))?.hide();
            loadPermissions(permsCurrentPage);
        } else {
            if (data?.errors) displayPermissionFieldErrors(data.errors);
            else H.showError(data?.message || 'Failed to save permission');
        }
    } catch (err) {
        H.showError('Error saving permission: ' + (err.data?.message || err.message || 'Unknown'));
    } finally {
        if (saveButton) H.setButtonLoading(saveButton, false);
    }
}

async function deletePermission(id, button = null) {
    if (!confirm('Delete this permission?')) return;
    if (button) H.setButtonLoading(button, true);
    try {
        const data = await H.apiFetch(`/admin/permissions/${id}`, { method: 'DELETE' });
        if (data && data.status === 'success') {
            H.showNotification('Permission deleted', 'success');
            loadPermissions(permsCurrentPage);
        } else {
            H.showError(data?.message || 'Failed to delete permission');
        }
    } catch (err) {
        H.showError('Error deleting permission: ' + (err.message || 'Unknown'));
    } finally {
        if (button) H.setButtonLoading(button, false);
    }
}

function clearPermissionErrors() {
    ['permission-name','permission-guard-name'].forEach(id => {
        const f = document.getElementById(id);
        if (f) f.classList.remove('is-invalid');
        const e = document.getElementById(id + '-error');
        if (e) { e.textContent = ''; e.style.display = 'none'; }
    });
}

function displayPermissionFieldErrors(errors) {
    if (!errors || typeof errors !== 'object') return;
    clearPermissionErrors();
    if (errors.name) {
        const el = document.getElementById('permission-name');
        const err = document.getElementById('permission-name-error');
        if (el) el.classList.add('is-invalid');
        if (err) { err.textContent = (Array.isArray(errors.name) ? errors.name[0] : errors.name); err.style.display = 'block'; }
    }
    if (errors.guard_name) {
        const el = document.getElementById('permission-guard-name');
        const err = document.getElementById('permission-guard-name-error');
        if (el) el.classList.add('is-invalid');
        if (err) { err.textContent = (Array.isArray(errors.guard_name) ? errors.guard_name[0] : errors.guard_name); err.style.display = 'block'; }
    }
}

window.loadPermissions = loadPermissions;
window.openPermissionModal = openPermissionModal;
window.editPermission = editPermission;
window.savePermission = savePermission;
window.deletePermission = deletePermission;
window.loadGuards = loadGuards;

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        loadPermissions(1);
        loadGuards();
    }, 50);
});
