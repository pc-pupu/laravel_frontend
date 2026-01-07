// roles.js — Roles management via frontend proxy (/admin/roles/*)
const H = window.adminHelpers;

let rolesCurrentPage = 1;
let rolesEditingId = null;
let allPermissions = [];

async function loadRoles(page = 1) {
    rolesCurrentPage = page;
    const search = document.getElementById('search-roles')?.value || '';
    let url = `/admin/roles/list?page=${page}&per_page=15`;
    if (search) url += `&search=${encodeURIComponent(search)}`;

    // Show loading in table
    const tbody = document.getElementById('roles-table-body');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center"><div class="spinner"></div></td></tr>';
    }

    try {
        const data = await H.apiFetch(url, { method: 'GET' });
        if (data && data.status === 'success') {
            displayRoles(data.data.data || []);
            displayRolesPagination(data.data);
        } else {
            H.showError(data?.message || 'Failed to load roles');
        }
    } catch (err) {
        H.showError('Error loading roles: ' + (err.message || 'Unknown'));
    }
}

function displayRoles(roles) {
    const tbody = document.getElementById('roles-table-body');
    if (!tbody) return;
    if (!roles || roles.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No roles found</td></tr>';
        return;
    }

    tbody.innerHTML = roles.map(role => {
        const permissions = role.permissions ? role.permissions.length : 0;
        const users = role.users ? role.users.length : 0;
        const isActive = role.is_active ? 'Yes' : 'No';
        return `
            <tr>
                <td>${role.id}</td>
                <td>${role.name}</td>
                <td>${role.guard_name || 'web'}</td>
                <td>${permissions} permission(s)</td>
                <td>${users} user(s)</td>
                <td>${isActive}</td>
                <td>
                    <button class="btn-admin btn-admin-sm btn-admin-primary" onclick="editRole(${role.id}, this)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-admin btn-admin-sm btn-admin-danger" onclick="deleteRole(${role.id}, this)">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function displayRolesPagination(pagination) {
    const paginationDiv = document.getElementById('roles-pagination');
    if (!paginationDiv) return;
    if (!pagination || !pagination.total) {
        paginationDiv.innerHTML = '';
        return;
    }
    const totalPages = pagination.last_page;
    let html = '<div class="pagination">';
    if (pagination.current_page > 1) html += `<a href="#" class="page-link" onclick="event.preventDefault(); const btn = this; H.setElementLoading(btn, true, 'Loading...'); loadRoles(${pagination.current_page - 1}).finally(() => H.setElementLoading(btn, false)); return false;">Previous</a>`;
    for (let i = 1; i <= totalPages; i++) {
        html += i === pagination.current_page ? `<span class="page-link active">${i}</span>` : `<a href="#" class="page-link" onclick="event.preventDefault(); const btn = this; H.setElementLoading(btn, true, 'Loading...'); loadRoles(${i}).finally(() => H.setElementLoading(btn, false)); return false;">${i}</a>`;
    }
    if (pagination.current_page < totalPages) html += `<a href="#" class="page-link" onclick="event.preventDefault(); const btn = this; H.setElementLoading(btn, true, 'Loading...'); loadRoles(${pagination.current_page + 1}).finally(() => H.setElementLoading(btn, false)); return false;">Next</a>`;
    html += '</div>';
    paginationDiv.innerHTML = html;
}

const searchRoles = H.debounce(() => loadRoles(1), 500);
window.searchRoles = searchRoles;

async function loadAllPermissions() {
    try {
        const data = await H.apiFetch('/admin/permissions/list?page=1&per_page=1000', { method: 'GET' });
        if (data && data.status === 'success') {
            allPermissions = data.data.data || [];
            displayPermissionsCheckboxes();
        }
    } catch (err) {
        console.error('Error loading permissions:', err);
    }
}

function displayPermissionsCheckboxes(selectedIds = []) {
    const container = document.getElementById('permissions-list');
    if (!container) return;
    const grouped = {};
    allPermissions.forEach(p => {
        const guard = p.guard_name || 'web';
        grouped[guard] = grouped[guard] || [];
        grouped[guard].push(p);
    });
    let html = '';
    Object.keys(grouped).sort().forEach(guard => {
        html += `<div style="margin-bottom:15px;"><strong>${guard}</strong><br>`;
        grouped[guard].forEach(perm => {
            const checked = selectedIds.includes(perm.id) ? 'checked' : '';
            html += `<label style="display:block; margin:5px 0;">
                        <input type="checkbox" name="permissions[]" value="${perm.id}" ${checked}>
                        ${perm.name}
                    </label>`;
        });
        html += '</div>';
    });
    container.innerHTML = html;
}

function openRoleModal() {
    rolesEditingId = null;
    document.getElementById('roleModalTitle').textContent = 'Add Role';
    document.getElementById('roleForm').reset();
    document.getElementById('role-id').value = '';
    document.getElementById('role-guard-name').value = 'web';
    displayPermissionsCheckboxes();
    clearRoleErrors();
    new bootstrap.Modal(document.getElementById('roleModal')).show();
}

async function editRole(roleId, button = null) {
    rolesEditingId = roleId;
    if (button) H.setButtonLoading(button, true);
    try {
        const data = await H.apiFetch(`/admin/roles/${roleId}`, { method: 'GET' });
        if (data && data.status === 'success') {
            const role = data.data;
            document.getElementById('roleModalTitle').textContent = 'Edit Role';
            document.getElementById('role-id').value = role.id;
            document.getElementById('role-name').value = role.name || '';
            document.getElementById('role-guard-name').value = role.guard_name || 'web';

            document.querySelector(`input[name="is_active"][value="${role.is_active}"]`).checked = true;

            const selectedIds = role.permissions ? role.permissions.map(p => p.id) : [];
            await loadAllPermissions(); // ensure allPermissions set
            displayPermissionsCheckboxes(selectedIds);
            clearRoleErrors();
            new bootstrap.Modal(document.getElementById('roleModal')).show();
        } else {
            H.showError(data?.message || 'Failed to load role');
        }
    } catch (err) {
        H.showError('Error loading role: ' + (err.message || 'Unknown'));
    } finally {
        if (button) H.setButtonLoading(button, false);
    }
}

async function saveRole() {
    const form = document.getElementById('roleForm');
    const saveButton = form.closest('.modal-content')?.querySelector('button[onclick*="saveRole"]') ||
                      document.querySelector('button[onclick*="saveRole"]');
    if (!form.checkValidity()) { form.reportValidity(); return; }
    
    if (saveButton) H.setButtonLoading(saveButton, true);
    
    const fd = new FormData(form);
    const selectedPermissions = Array.from(form.querySelectorAll('input[name="permissions[]"]:checked')).map(cb => parseInt(cb.value)).filter(n => !isNaN(n));
    const roleData = {
        name: fd.get('name')?.trim(),
        guard_name: fd.get('guard_name') || 'web',
        permissions: selectedPermissions,
        is_active: fd.get('is_active') === '1' ? 1 : 0
    };

    const id = rolesEditingId;
    const method = id ? 'PUT' : 'POST';
    const url = id ? `/admin/roles/${id}` : '/admin/roles';

    try {
        const data = await H.apiFetch(url, { method, body: JSON.stringify(roleData) });
        if (data && data.status === 'success') {
            H.showNotification(id ? 'Role updated' : 'Role created', 'success');
            bootstrap.Modal.getInstance(document.getElementById('roleModal'))?.hide();
            loadRoles(rolesCurrentPage);
        } else {
            if (data?.errors) displayRoleFieldErrors(data.errors);
            else H.showError(data?.message || 'Failed to save role');
        }
    } catch (err) {
        H.showError('Error saving role: ' + (err.data?.message || err.message || 'Unknown'));
        if (err.data?.errors) displayRoleFieldErrors(err.data.errors);
    } finally {
        if (saveButton) H.setButtonLoading(saveButton, false);
    }
}

async function deleteRole(roleId, button = null) {
    if (!confirm('Are you sure you want to delete this role?')) return;
    if (button) H.setButtonLoading(button, true);
    try {
        const data = await H.apiFetch(`/admin/roles/${roleId}`, { method: 'DELETE' });
        if (data && data.status === 'success') {
            H.showNotification('Role deleted', 'success');
            loadRoles(rolesCurrentPage);
        } else {
            H.showError(data?.message || 'Failed to delete role');
        }
    } catch (err) {
        H.showError('Error deleting role: ' + (err.message || 'Unknown'));
    } finally {
        if (button) H.setButtonLoading(button, false);
    }
}

// Validation helpers (simple)
function clearRoleErrors() {
    ['role-name','role-guard-name'].forEach(id => {
        const f = document.getElementById(id);
        if (f) f.classList.remove('is-invalid');
        const e = document.getElementById(id + '-error');
        if (e) { e.textContent = ''; e.style.display = 'none'; }
    });
    const permErr = document.getElementById('permissions-error');
    if (permErr) { permErr.textContent = ''; permErr.style.display = 'none'; }
}

function displayRoleFieldErrors(errors) {
    if (!errors || typeof errors !== 'object') return;
    clearRoleErrors();
    if (errors.name) {
        const el = document.getElementById('role-name');
        const err = document.getElementById('role-name-error');
        if (el) el.classList.add('is-invalid');
        if (err) { err.textContent = (Array.isArray(errors.name) ? errors.name[0] : errors.name); err.style.display = 'block'; }
    }
    if (errors.guard_name) {
        const el = document.getElementById('role-guard-name');
        const err = document.getElementById('role-guard-name-error');
        if (el) el.classList.add('is-invalid');
        if (err) { err.textContent = (Array.isArray(errors.guard_name) ? errors.guard_name[0] : errors.guard_name); err.style.display = 'block'; }
    }
    if (errors.permissions) {
        const err = document.getElementById('permissions-error');
        if (err) { err.textContent = (Array.isArray(errors.permissions) ? errors.permissions[0] : errors.permissions); err.style.display = 'block'; }
    }
}

window.loadRoles = loadRoles;
window.openRoleModal = openRoleModal;
window.editRole = editRole;
window.saveRole = saveRole;
window.deleteRole = deleteRole;

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        loadRoles(1);
        loadAllPermissions();
    }, 50);
});
