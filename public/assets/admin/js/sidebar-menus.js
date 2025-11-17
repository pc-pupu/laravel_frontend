// sidebar-menus.js — Sidebar Menus management via frontend proxy (/admin/sidebar-menus/*)

const H = window.adminHelpers;

let menusCurrentPage = 1;
let menusEditingId = null;
let allMenus = [];
let allRoles = [];

// Load menus (list)
async function loadMenus(page = 1) {
    menusCurrentPage = page;
    const search = document.getElementById('search-menus')?.value || '';
    let url = `/admin/sidebar-menus/list?page=${page}&per_page=15`;
    if (search) url += `&search=${encodeURIComponent(search)}`;

    // Show loading in table
    const tbody = document.getElementById('menus-table-body');
    if (tbody) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center"><div class="spinner"></div></td></tr>';
    }

    try {
        const data = await H.apiFetch(url, { method: 'GET' });
        if (data && data.status === 'success') {
            allMenus = data.data || [];
            displayMenus(allMenus);
        } else {
            H.showError(data?.message || 'Failed to load menus');
        }
    } catch (err) {
        H.showError('Error loading menus: ' + (err.message || 'Unknown'));
    }
}

function displayMenus(menus) {
    const tbody = document.getElementById('menus-table-body');
    if (!tbody) return;
    
    if (!menus || menus.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center">No menus found</td></tr>';
        return;
    }

    tbody.innerHTML = menus.map(menu => {
        const parentName = menu.parent_name || '-';
        const routeUrl = menu.route_name || menu.url || '-';
        const icon = menu.icon_class ? `<i class="${menu.icon_class}"></i>` : '-';
        const roles = menu.roles && menu.roles.length > 0 
            ? menu.roles.map(r => r.name).join(', ') 
            : 'No roles';
        const statusBadge = menu.is_active 
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-danger">Inactive</span>';

        return `
            <tr>
                <td>${menu.sidebar_menu_id}</td>
                <td>${menu.menu_name}</td>
                <td>${routeUrl}</td>
                <td>${icon}</td>
                <td>${parentName}</td>
                <td>${menu.order_no}</td>
                <td><small>${roles}</small></td>
                <td>${statusBadge}</td>
                <td>
                    <button class="btn-admin btn-admin-sm btn-admin-primary" onclick="editMenu(${menu.sidebar_menu_id}, this)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-admin btn-admin-sm btn-admin-danger" onclick="deleteMenu(${menu.sidebar_menu_id}, this)">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

// Debounced search
const searchMenus = H.debounce(() => {
    const search = document.getElementById('search-menus')?.value || '';
    const filtered = allMenus.filter(menu => 
        menu.menu_name.toLowerCase().includes(search.toLowerCase()) ||
        (menu.route_name && menu.route_name.toLowerCase().includes(search.toLowerCase())) ||
        (menu.url && menu.url.toLowerCase().includes(search.toLowerCase()))
    );
    displayMenus(filtered);
}, 500);
window.searchMenus = searchMenus;

// Load all menus for parent dropdown
async function loadAllMenusForParent() {
    try {
        const data = await H.apiFetch('/admin/sidebar-menus/list?per_page=1000', { method: 'GET' });
        if (data && data.status === 'success') {
            const parentSelect = document.getElementById('menu-parent');
            if (parentSelect) {
                const currentId = menusEditingId;
                const parents = (data.data || []).filter(m => 
                    !m.parent_id && m.sidebar_menu_id !== currentId
                );
                parentSelect.innerHTML = '<option value="">-- None (Top Level) --</option>' +
                    parents.map(menu => 
                        `<option value="${menu.sidebar_menu_id}">${menu.menu_name}</option>`
                    ).join('');
            }
        }
    } catch (err) {
        console.error('Error loading menus for parent:', err);
    }
}

// Load all roles for checkbox list
async function loadAllRoles() {
    try {
        const data = await H.apiFetch('/admin/roles/list?page=1&per_page=1000', { method: 'GET' });
        if (data && data.status === 'success') {
            allRoles = data.data.data || [];
            displayRolesCheckboxes();
        }
    } catch (err) {
        console.error('Error loading roles:', err);
    }
}

function displayRolesCheckboxes(selectedIds = []) {
    const container = document.getElementById('roles-list');
    if (!container) return;
    
    container.innerHTML = allRoles.map(role => {
        const checked = selectedIds.includes(role.id) ? 'checked' : '';
        return `
            <label style="display:block; margin:5px 0;">
                <input type="checkbox" name="roles[]" value="${role.id}" ${checked}>
                ${role.name}
            </label>
        `;
    }).join('');
}

// Open modal for add
async function openMenuModal() {
    menusEditingId = null;
    document.getElementById('menuModalTitle').textContent = 'Add Menu';
    document.getElementById('menuForm').reset();
    document.getElementById('menu-id').value = '';
    document.getElementById('menu-order').value = '0';
    document.getElementById('menu-active-1').checked = true;
    clearMenuErrors();
    await loadAllMenusForParent();
    await loadAllRoles();
    displayRolesCheckboxes();
}

// Edit menu (load menu data)
async function editMenu(menuId, button = null) {
    menusEditingId = menuId;
    if (button) H.setButtonLoading(button, true);
    try {
        const data = await H.apiFetch(`/admin/sidebar-menus/${menuId}`, { method: 'GET' });
        if (data && data.status === 'success') {
            const menu = data.data;
            document.getElementById('menuModalTitle').textContent = 'Edit Menu';
            document.getElementById('menu-id').value = menu.sidebar_menu_id;
            document.getElementById('menu-name').value = menu.menu_name || '';
            document.getElementById('menu-route').value = menu.route_name || '';
            document.getElementById('menu-url').value = menu.url || '';
            document.getElementById('menu-icon').value = menu.icon_class || '';
            document.getElementById('menu-order').value = menu.order_no || 0;
            
            if (menu.is_active) {
                document.getElementById('menu-active-1').checked = true;
            } else {
                document.getElementById('menu-active-0').checked = true;
            }
            
            clearMenuErrors();
            await loadAllMenusForParent();
            await loadAllRoles();
            
            // Set parent
            const parentSelect = document.getElementById('menu-parent');
            if (parentSelect && menu.parent_id) {
                parentSelect.value = menu.parent_id;
            } else if (parentSelect) {
                parentSelect.value = '';
            }
            
            // Set roles
            const selectedRoleIds = menu.roles ? menu.roles.map(r => r.id) : [];
            displayRolesCheckboxes(selectedRoleIds);
            
            // Open modal
            const modalElement = document.getElementById('menuModal');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        } else {
            H.showError(data?.message || 'Failed to load menu');
        }
    } catch (err) {
        H.showError('Error loading menu: ' + (err.message || 'Unknown'));
    } finally {
        if (button) H.setButtonLoading(button, false);
    }
}

// Save menu (create or update)
async function saveMenu() {
    const form = document.getElementById('menuForm');
    const saveButton = form.closest('.modal-content')?.querySelector('button[onclick*="saveMenu"]') ||
                      document.querySelector('button[onclick*="saveMenu"]');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    if (saveButton) H.setButtonLoading(saveButton, true);
    
    const formData = new FormData(form);
    const selectedRoles = Array.from(form.querySelectorAll('input[name="roles[]"]:checked'))
        .map(cb => parseInt(cb.value))
        .filter(n => !isNaN(n));
    
    if (selectedRoles.length === 0) {
        H.showError('Please select at least one role');
        if (saveButton) H.setButtonLoading(saveButton, false);
        return;
    }
    
    const menuData = {
        menu_name: formData.get('menu_name'),
        route_name: formData.get('route_name') || null,
        url: formData.get('url') || null,
        icon_class: formData.get('icon_class') || null,
        parent_id: formData.get('parent_id') || null,
        order_no: parseInt(formData.get('order_no')) || 0,
        is_active: formData.get('is_active') === '1',
        roles: selectedRoles
    };
    
    const id = menusEditingId;
    const method = id ? 'PUT' : 'POST';
    const url = id ? `/admin/sidebar-menus/${id}` : '/admin/sidebar-menus';
    
    try {
        const data = await H.apiFetch(url, { 
            method, 
            body: JSON.stringify(menuData) 
        });
        
        if (data && data.status === 'success') {
            H.showNotification(id ? 'Menu updated successfully' : 'Menu created successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('menuModal'))?.hide();
            loadMenus(menusCurrentPage);
        } else {
            if (data?.errors) displayMenuFieldErrors(data.errors);
            else H.showError(data?.message || 'Failed to save menu');
        }
    } catch (err) {
        H.showError('Error saving menu: ' + (err.data?.message || err.message || 'Unknown'));
        if (err.data?.errors) displayMenuFieldErrors(err.data.errors);
    } finally {
        if (saveButton) H.setButtonLoading(saveButton, false);
    }
}

// Delete menu
async function deleteMenu(menuId, button = null) {
    if (!confirm('Are you sure you want to delete this menu? This action cannot be undone.')) return;
    if (button) H.setButtonLoading(button, true);
    try {
        const data = await H.apiFetch(`/admin/sidebar-menus/${menuId}`, { method: 'DELETE' });
        if (data && data.status === 'success') {
            H.showNotification('Menu deleted successfully', 'success');
            loadMenus(menusCurrentPage);
        } else {
            H.showError(data?.message || 'Failed to delete menu');
        }
    } catch (err) {
        H.showError('Error deleting menu: ' + (err.message || 'Unknown'));
    } finally {
        if (button) H.setButtonLoading(button, false);
    }
}

// Validation helpers
function clearMenuErrors() {
    ['menu-name', 'menu-route', 'menu-url', 'menu-icon', 'menu-parent', 'menu-order'].forEach(id => {
        const f = document.getElementById(id);
        if (f) f.classList.remove('is-invalid');
        const e = document.getElementById(id + '-error');
        if (e) { e.textContent = ''; e.style.display = 'none'; }
    });
    const rolesErr = document.getElementById('roles-error');
    if (rolesErr) { rolesErr.textContent = ''; rolesErr.style.display = 'none'; }
}

function displayMenuFieldErrors(errors) {
    if (!errors || typeof errors !== 'object') return;
    clearMenuErrors();
    
    Object.keys(errors).forEach(field => {
        const fieldId = 'menu-' + field.replace('_', '-');
        const el = document.getElementById(fieldId);
        const err = document.getElementById(fieldId + '-error');
        if (el) el.classList.add('is-invalid');
        if (err) {
            err.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
            err.style.display = 'block';
        }
    });
    
    if (errors.roles) {
        const err = document.getElementById('roles-error');
        if (err) {
            err.textContent = Array.isArray(errors.roles) ? errors.roles[0] : errors.roles;
            err.style.display = 'block';
        }
    }
}

function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    const err = document.getElementById(fieldId + '-error');
    if (field) field.classList.remove('is-invalid');
    if (err) { err.textContent = ''; err.style.display = 'none'; }
}

// Export functions globally
window.loadMenus = loadMenus;
window.openMenuModal = openMenuModal;
window.editMenu = editMenu;
window.saveMenu = saveMenu;
window.deleteMenu = deleteMenu;
window.searchMenus = searchMenus;
window.clearFieldError = clearFieldError;

// Init on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => loadMenus(1), 50);
});

