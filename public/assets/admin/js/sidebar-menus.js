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
        let routeUrl = menu.route_name || menu.url || '-';
        
        // Show route_params if they exist
        if (menu.route_params && Object.keys(menu.route_params).length > 0) {
            const paramsStr = Object.entries(menu.route_params)
                .map(([key, val]) => `${key}: ${val}`)
                .join(', ');
            routeUrl += `<br><small class="text-muted" style="font-size: 0.85em;">Params: ${paramsStr}</small>`;
        }
        
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
    document.getElementById('menu-route-params').value = '';
    document.getElementById('menu-route-pattern').value = '';
    document.getElementById('route-params-inputs').innerHTML = '';
    document.getElementById('route-params-inputs').style.display = 'none';
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
            
            // Set route_params and pattern
            const routeParamsField = document.getElementById('menu-route-params');
            const patternField = document.getElementById('menu-route-pattern');
            const inputsContainer = document.getElementById('route-params-inputs');
            
            if (menu.route_params && typeof menu.route_params === 'object') {
                // Handle both array and object
                let params = menu.route_params;
                if (Array.isArray(menu.route_params) && menu.route_params.length > 0) {
                    params = menu.route_params[0];
                }
                
                // Extract parameter names and try to construct pattern
                const paramNames = Object.keys(params);
                if (paramNames.length > 0) {
                    // Construct pattern from route_name if possible, or just show params
                    let pattern = '';
                    if (menu.route_name) {
                        // Try to construct full pattern: route_base/{param1}/{param2}
                        // For now, we'll show just the params part and user can enter full pattern
                        pattern = paramNames.map(name => `{${name}}`).join('/');
                    } else {
                        pattern = paramNames.map(name => `{${name}}`).join('/');
                    }
                    
                    if (patternField) {
                        // If route_name exists, try to prepend it
                        if (menu.route_name) {
                            const routeBase = menu.route_name.replace('.dashboard', '').replace(/\./g, '_');
                            patternField.value = routeBase + '/' + pattern;
                        } else {
                            patternField.value = pattern;
                        }
                    }
                    
                    // Create input fields
                    if (inputsContainer) {
                        inputsContainer.innerHTML = '';
                        inputsContainer.style.display = 'block';
                        
                        paramNames.forEach(paramName => {
                            const div = document.createElement('div');
                            div.className = 'mb-2';
                            div.innerHTML = `
                                <label class="form-label small">${paramName}</label>
                                <input type="text" class="form-control form-control-sm route-param-input" 
                                       data-param="${paramName}" 
                                       value="${params[paramName] || ''}"
                                       placeholder="Enter value for ${paramName}"
                                       oninput="updateRouteParamsFromInputs()">
                            `;
                            inputsContainer.appendChild(div);
                        });
                    }
                }
                
                // Set JSON field
                if (routeParamsField) {
                    if (Array.isArray(menu.route_params)) {
                        routeParamsField.value = JSON.stringify(menu.route_params[0], null, 2);
                    } else {
                        routeParamsField.value = JSON.stringify(menu.route_params, null, 2);
                    }
                }
            } else {
                if (patternField) patternField.value = '';
                if (inputsContainer) {
                    inputsContainer.innerHTML = '';
                    inputsContainer.style.display = 'none';
                }
                if (routeParamsField) routeParamsField.value = '';
            }
            
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
    
    // Parse route_params - try from inputs first, then from textarea
    let routeParams = null;
    
    // Check if we have input fields (pattern-based entry)
    const inputsContainer = document.getElementById('route-params-inputs');
    if (inputsContainer && inputsContainer.style.display !== 'none') {
        const inputs = inputsContainer.querySelectorAll('.route-param-input');
        const params = {};
        
        inputs.forEach(input => {
            const paramName = input.getAttribute('data-param');
            const value = input.value.trim();
            if (paramName && value) {
                params[paramName] = value;
            }
        });
        
        if (Object.keys(params).length > 0) {
            routeParams = params;
        }
    }
    
    // If no params from inputs, try textarea
    if (!routeParams) {
        const routeParamsStr = formData.get('route_params')?.trim();
        if (routeParamsStr) {
            try {
                routeParams = JSON.parse(routeParamsStr);
                if (typeof routeParams !== 'object' || Array.isArray(routeParams)) {
                    throw new Error('Route params must be a JSON object');
                }
            } catch (e) {
                H.showError('Invalid JSON in Route Parameters: ' + e.message);
                if (saveButton) H.setButtonLoading(saveButton, false);
                const routeParamsField = document.getElementById('menu-route-params');
                if (routeParamsField) {
                    routeParamsField.classList.add('is-invalid');
                    const err = document.getElementById('menu-route-params-error');
                    if (err) {
                        err.textContent = 'Invalid JSON format: ' + e.message;
                        err.style.display = 'block';
                    }
                }
                return;
            }
        }
    }
    
    const menuData = {
        menu_name: formData.get('menu_name'),
        route_name: formData.get('route_name') || null,
        url: formData.get('url') || null,
        icon_class: formData.get('icon_class') || null,
        parent_id: formData.get('parent_id') || null,
        order_no: parseInt(formData.get('order_no')) || 0,
        is_active: formData.get('is_active') === '1',
        route_params: routeParams,
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
    ['menu-name', 'menu-route', 'menu-url', 'menu-icon', 'menu-parent', 'menu-order', 'menu-route-params'].forEach(id => {
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

// Update route params inputs from pattern
function updateRouteParamsFromPattern() {
    const patternField = document.getElementById('menu-route-pattern');
    const inputsContainer = document.getElementById('route-params-inputs');
    const jsonField = document.getElementById('menu-route-params');
    
    if (!patternField || !inputsContainer) return;
    
    const pattern = patternField.value.trim();
    const paramMatches = pattern.match(/\{([^}]+)\}/g);
    
    if (paramMatches && paramMatches.length > 0) {
        inputsContainer.innerHTML = '';
        inputsContainer.style.display = 'block';
        
        // Hide JSON field when using pattern
        if (jsonField) jsonField.style.display = 'none';
        
        // Get existing values from JSON field if any
        let existingValues = {};
        if (jsonField && jsonField.value.trim()) {
            try {
                existingValues = JSON.parse(jsonField.value) || {};
            } catch (e) {
                // Ignore parse errors
            }
        }
        
        paramMatches.forEach(match => {
            const paramName = match.replace(/[{}]/g, '');
            const div = document.createElement('div');
            div.className = 'mb-2';
            div.innerHTML = `
                <label class="form-label small">${paramName}</label>
                <input type="text" class="form-control form-control-sm route-param-input" 
                       data-param="${paramName}" 
                       value="${existingValues[paramName] || ''}"
                       placeholder="Enter value for ${paramName}"
                       oninput="updateRouteParamsFromInputs()">
            `;
            inputsContainer.appendChild(div);
        });
        
        updateRouteParamsFromInputs();
    } else {
        inputsContainer.innerHTML = '';
        inputsContainer.style.display = 'none';
        // Show JSON field if pattern is empty
        if (jsonField && !pattern) jsonField.style.display = 'block';
    }
}


// Update JSON field from input fields
function updateRouteParamsFromInputs() {
    const inputsContainer = document.getElementById('route-params-inputs');
    const jsonField = document.getElementById('menu-route-params');
    
    if (!inputsContainer || !jsonField) return;
    
    const inputs = inputsContainer.querySelectorAll('.route-param-input');
    const params = {};
    
    inputs.forEach(input => {
        const paramName = input.getAttribute('data-param');
        const value = input.value.trim();
        if (paramName && value) {
            params[paramName] = value;
        }
    });
    
    if (Object.keys(params).length > 0) {
        jsonField.value = JSON.stringify(params, null, 2);
    } else {
        jsonField.value = '';
    }
}

// Export functions globally
window.loadMenus = loadMenus;
window.openMenuModal = openMenuModal;
window.editMenu = editMenu;
window.saveMenu = saveMenu;
window.deleteMenu = deleteMenu;
window.searchMenus = searchMenus;
window.clearFieldError = clearFieldError;
window.updateRouteParamsFromPattern = updateRouteParamsFromPattern;
window.updateRouteParamsFromInputs = updateRouteParamsFromInputs;

// Init on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => loadMenus(1), 50);
});

