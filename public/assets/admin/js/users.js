
console.log("users.js loaded at", performance.now());


const H = window.adminHelpers;


// State
let usersCurrentPage = 1;
let usersEditingId = null;

// Load users (list)
async function loadUsers(page = 1) {
    usersCurrentPage = page;
    const search = document.getElementById('search-users')?.value || '';
    let url = `/admin/users/list?page=${page}&per_page=15`;
    if (search) url += `&search=${encodeURIComponent(search)}`;

    // Show loading in table
    const tbody = document.getElementById('users-table-body');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center"><div class="spinner"></div></td></tr>';
    }

    try {
        const data = await H.apiFetch(url, { method: 'GET' });
        // Expecting backend proxied response shape: { status:'success', data: { data: [...], ... } }
        if (data && data.status === 'success') {
            displayUsers(data.data.data || []);
            displayUsersPagination(data.data);
        } else {
            H.showError(data?.message || 'Failed to load users');
        }
    } catch (err) {
        H.showError('Error loading users: ' + (err.message || 'Unknown error'));
    }
}

function displayUsers(users) {
    const tbody = document.getElementById('users-table-body');
    if (!tbody) return;
    if (!users || users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No users found</td></tr>';
        return;
    }

    tbody.innerHTML = users.map(user => {
        const roles = user.roles ? user.roles.map(r => r.name).join(', ') : 'No roles';
        const statusBadge = user.status == 1
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-danger">Inactive</span>';

        return `
            <tr>
                <td>${user.uid}</td>
                <td>${user.name}</td>
                <td>${user.email || '-'}</td>
                <td>${roles}</td>
                <td>${statusBadge}</td>
                <td>
                    <button class="btn-admin btn-admin-sm btn-admin-primary" onclick="editUser(${user.uid}, this)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-admin btn-admin-sm btn-admin-danger" onclick="deleteUser(${user.uid}, this)">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function displayUsersPagination(pagination) {
    const paginationDiv = document.getElementById('users-pagination');
    if (!paginationDiv) return;

    if (!pagination || !pagination.total) {
        paginationDiv.innerHTML = '';
        return;
    }

    const current = pagination.current_page;
    const last = pagination.last_page;

    let pages = [];

    // Always show first 3 pages
    for (let i = 1; i <= 3 && i <= last; i++) {
        pages.push(i);
    }

    // Add left ellipsis (...)
    if (current > 5) pages.push('...');

    // Show current-1, current, current+1
    for (let i = current - 1; i <= current + 1; i++) {
        if (i > 3 && i < last - 2) pages.push(i);
    }

    // Add right ellipsis (...)
    if (current < last - 4) pages.push('...');

    // Always show last 3 pages
    for (let i = last - 2; i <= last; i++) {
        if (i > 0) pages.push(i);
    }

    // Remove duplicates and sort
    pages = [...new Set(pages)].sort((a, b) => a - b);

    let html = '<div class="pagination">';

    if (current > 1) {
        html += `<a href="#" class="page-link" onclick="event.preventDefault(); loadUsers(${current - 1})">Previous</a>`;
    }

    pages.forEach(p => {
        if (p === '...') {
            html += `<span class="page-link dots">...</span>`;
        } else if (p === current) {
            html += `<span class="page-link active">${p}</span>`;
        } else {
            html += `<a href="#" class="page-link" onclick="event.preventDefault(); loadUsers(${p})">${p}</a>`;
        }
    });

    if (current < last) {
        html += `<a href="#" class="page-link" onclick="event.preventDefault(); loadUsers(${current + 1})">Next</a>`;
    }

    html += '</div>';

    paginationDiv.innerHTML = html;
}


// H.Debounced search
const searchUsers = H.debounce(() => loadUsers(1), 500);
window.searchUsers = searchUsers;

// Load roles for dropdown (from proxy)
async function loadRolesForUsers() {
    try {
        const data = await H.apiFetch('/admin/roles/list?page=1&per_page=1000', { method: 'GET' });
        if (data && data.status === 'success') {
            const rolesSelect = document.getElementById('user-roles');
            if (rolesSelect) {
                rolesSelect.innerHTML = '<option value="">Select roles...</option>' +
                    (data.data.data || []).map(role => `<option value="${role.id}">${role.name}</option>`).join('');
            }
        }
    } catch (err) {
        console.error('Error loading roles:', err);
    }
}

// Open modal for add
async function openUserModal() {
    usersEditingId = null;
    document.getElementById('userModalTitle').textContent = 'Add User';
    document.getElementById('userForm').reset();
    document.getElementById('user-id').value = '';
    document.getElementById('password-required').style.display = 'inline';
    document.getElementById('user-password').required = true;
    document.getElementById('user-password-confirm').required = true;
    document.getElementById('user-status').value = '1';
    clearUserErrors();
    await loadRolesForUsers();
    const rolesSelect = document.getElementById('user-roles');
    if (rolesSelect) Array.from(rolesSelect.options).forEach(o => o.selected = false);
    new bootstrap.Modal(document.getElementById('userModal')).show();
}

// Edit user (load user data)
async function editUser(userId, button = null) {
    usersEditingId = userId;
    if (button) H.setButtonLoading(button, true);
    try {
        const data = await H.apiFetch(`/admin/users/${userId}`, { method: 'GET' });
        if (data && data.status === 'success') {
            const user = data.data;
            document.getElementById('userModalTitle').textContent = 'Edit User';
            document.getElementById('user-id').value = user.uid;
            document.getElementById('user-name').value = user.name || '';
            document.getElementById('user-email').value = user.email || '';
            document.getElementById('user-status').value = user.status !== undefined ? user.status : 1;
            document.getElementById('password-required').style.display = 'none';
            document.getElementById('user-password').required = false;
            document.getElementById('user-password-confirm').required = false;
            document.getElementById('user-password').value = '';
            document.getElementById('user-password-confirm').value = '';
            clearUserErrors();
            await loadRolesForUsers();
            // set selected roles
            if (user.roles && user.roles.length > 0) {
                const roleIds = user.roles.map(r => r.id);
                const rolesSelect = document.getElementById('user-roles');
                if (rolesSelect) {
                    Array.from(rolesSelect.options).forEach(option => {
                        option.selected = roleIds.includes(parseInt(option.value));
                    });
                }
            }
            new bootstrap.Modal(document.getElementById('userModal')).show();
        } else {
            H.showError(data?.message || 'Failed to load user');
        }
    } catch (err) {
        H.showError('Error loading user: ' + (err.message || 'Unknown'));
    } finally {
        if (button) H.setButtonLoading(button, false);
    }
}

// Save user (create or update)
async function saveUser() {
    const form = document.getElementById('userForm');
    const saveButton = form.closest('.modal-content')?.querySelector('button[onclick*="saveUser"]') ||
                      document.querySelector('button[onclick*="saveUser"]');
    clearUserErrors();

    if (!form.checkValidity()) {
        form.reportValidity();
        const invalid = form.querySelector(':invalid');
        if (invalid) invalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    if (saveButton) H.setButtonLoading(saveButton, true);

    const fd = new FormData(form);
    const userData = {
        name: fd.get('name'),
        email: fd.get('email'),
        status: parseInt(fd.get('status')) || 1,
        roles: Array.from(document.getElementById('user-roles').selectedOptions).map(o => parseInt(o.value)).filter(n => !isNaN(n))
    };

    const uid = usersEditingId;
    const password = fd.get('password');
    const passwordConfirm = fd.get('password_confirmation');

    if (!uid) {
        // new user - require password
        if (!password || password.length < 8) {
            const el = document.getElementById('user-password');
            showFieldError('user-password', 'Password is required and must be at least 8 characters.');
            if (el) el.focus();
            if (saveButton) H.setButtonLoading(saveButton, false);
            return;
        }
        if (password !== passwordConfirm) {
            showFieldError('user-password-confirm', 'Passwords do not match.');
            if (saveButton) H.setButtonLoading(saveButton, false);
            return;
        }
        userData.password = password;
        userData.password_confirmation = passwordConfirm;
    } else {
        // update: password optional
        if (password) {
            if (password.length < 8) {
                showFieldError('user-password', 'Password must be at least 8 characters.');
                if (saveButton) H.setButtonLoading(saveButton, false);
                return;
            }
            if (password !== passwordConfirm) {
                showFieldError('user-password-confirm', 'Passwords do not match.');
                if (saveButton) H.setButtonLoading(saveButton, false);
                return;
            }
            userData.password = password;
            userData.password_confirmation = passwordConfirm;
        }
    }

    try {
        const method = uid ? 'PUT' : 'POST';
        const url = uid ? `/admin/users/${uid}` : `/admin/users`;
        const data = await H.apiFetch(url, { method, body: JSON.stringify(userData) });

        if (data && data.status === 'success') {
            H.showNotification(uid ? 'User updated successfully' : 'User created successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('userModal'))?.hide();
            loadUsers(usersCurrentPage);
        } else {
            // handle validation errors coming from backend
            if (data && data.errors) {
                displayUserFieldErrors(data.errors);
            } else {
                H.showError(data?.message || 'Failed to save user');
            }
        }
    } catch (err) {
        H.showError('Error saving user: ' + (err.data?.message || err.message || 'Unknown'));
        if (err.data?.errors) displayUserFieldErrors(err.data.errors);
    } finally {
        if (saveButton) H.setButtonLoading(saveButton, false);
    }
}

// Delete user
async function deleteUser(userId, button = null) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;
    if (button) H.setButtonLoading(button, true);
    try {
        const data = await H.apiFetch(`/admin/users/${userId}`, { method: 'DELETE' });
        if (data && data.status === 'success') {
            H.showNotification('User deleted successfully', 'success');
            loadUsers(usersCurrentPage);
        } else {
            H.showError(data?.message || 'Failed to delete user');
        }
    } catch (err) {
        H.showError('Error deleting user: ' + (err.message || 'Unknown'));
    } finally {
        if (button) H.setButtonLoading(button, false);
    }
}

// Field error helpers (local)
function showFieldError(fieldId, message) {
    const el = document.getElementById(fieldId);
    const err = document.getElementById(fieldId + '-error');
    if (el) el.classList.add('is-invalid');
    if (err) {
        err.textContent = message;
        err.style.display = 'block';
    }
}

function clearUserFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    const err = document.getElementById(fieldId + '-error');
    if (field) field.classList.remove('is-invalid');
    if (err) { err.textContent = ''; err.style.display = 'none'; }
}

function clearUserErrors() {
    ['user-name','user-email','user-password','user-password-confirm','user-roles','user-status'].forEach(clearUserFieldError);
}

// Map backend validation errors to fields
function displayUserFieldErrors(errors) {
    if (!errors || typeof errors !== 'object') return;
    if (errors.name) showFieldError('user-name', Array.isArray(errors.name) ? errors.name[0] : errors.name);
    if (errors.email) showFieldError('user-email', Array.isArray(errors.email) ? errors.email[0] : errors.email);
    if (errors.password) showFieldError('user-password', Array.isArray(errors.password) ? errors.password[0] : errors.password);
    if (errors.password_confirmation) showFieldError('user-password-confirm', Array.isArray(errors.password_confirmation) ? errors.password_confirmation[0] : errors.password_confirmation);
    if (errors.roles) {
        const e = Array.isArray(errors.roles) ? errors.roles[0] : errors.roles;
        const el = document.getElementById('user-roles-error');
        if (el) { el.textContent = e; el.style.display = 'block'; }
    }
}

// Export some functions globally if needed in HTML (onclick etc.)
window.loadUsers = loadUsers;
window.openUserModal = openUserModal;
window.editUser = editUser;
window.saveUser = saveUser;
window.deleteUser = deleteUser;
window.loadRolesForUsers = loadRolesForUsers;

// Init on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => loadUsers(1), 50);
});
