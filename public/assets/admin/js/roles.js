// Roles Management JavaScript

let currentPage = 1;
let currentRoleId = null;
let allPermissions = [];

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

// Load roles on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        loadRoles();
        loadAllPermissions();
    }, 100);
});

// Load roles from API
async function loadRoles(page = 1) {
    currentPage = page;
    const search = document.getElementById('search-roles')?.value || '';
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';
    
    if (!token) {
        showError('Authentication token not found. Please refresh the page.');
        return;
    }
    
    let url = `${apiBaseUrl}/admin/roles?page=${page}&per_page=15`;
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
console.log(url);

        if (!response.ok) {
            if (response.status === 401) {
                showError('Unauthorized. Please login again.');
                return;
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.status === 'success') {
            displayRoles(data.data.data || []);
            displayPagination(data.data, 'roles');
        } else {
            showError(data.message || 'Failed to load roles');
        }
    } catch (error) {
        console.error('Error loading roles:', error);
        showError('Error loading roles: ' + error.message);
    }
}

// Display roles in table
function displayRoles(roles) {
    const tbody = document.getElementById('roles-table-body');
    
    if (roles.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No roles found</td></tr>';
        return;
    }

    tbody.innerHTML = roles.map(role => {
        const permissions = role.permissions ? role.permissions.length : 0;
        const users = role.users ? role.users.length : 0;

        return `
            <tr>
                <td>${role.id}</td>
                <td>${role.name}</td>
                <td>${role.guard_name || 'web'}</td>
                <td>${permissions} permission(s)</td>
                <td>${users} user(s)</td>
                <td>
                    <button class="btn-admin btn-admin-sm btn-admin-primary" onclick="editRole(${role.id})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn-admin btn-admin-sm btn-admin-danger" onclick="deleteRole(${role.id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

// Display pagination
function displayPagination(pagination, type) {
    const paginationDiv = document.getElementById(`${type}-pagination`);
    if (!pagination || !pagination.total) {
        paginationDiv.innerHTML = '';
        return;
    }

    const totalPages = pagination.last_page;
    let html = '<div class="pagination">';

    if (pagination.current_page > 1) {
        html += `<a href="#" class="page-link" onclick="loadRoles(${pagination.current_page - 1}); return false;">Previous</a>`;
    }

    for (let i = 1; i <= totalPages; i++) {
        if (i === pagination.current_page) {
            html += `<span class="page-link" style="background: var(--primary-gradient); color: #fff;">${i}</span>`;
        } else {
            html += `<a href="#" class="page-link" onclick="loadRoles(${i}); return false;">${i}</a>`;
        }
    }

    if (pagination.current_page < totalPages) {
        html += `<a href="#" class="page-link" onclick="loadRoles(${pagination.current_page + 1}); return false;">Next</a>`;
    }

    html += '</div>';
    paginationDiv.innerHTML = html;
}

// Search roles
function searchRoles() {
    clearTimeout(searchRoles.timeout);
    searchRoles.timeout = setTimeout(() => {
        loadRoles(1);
    }, 500);
}

// Load all permissions
async function loadAllPermissions() {
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';

    if (!token) {
        console.error('Token not found for loading permissions');
        return;
    }

    try {
        const response = await fetch(`${apiBaseUrl}/admin/permissions?per_page=1000`, {
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
            allPermissions = data.data.data || [];
            displayPermissionsCheckboxes();
        }
    } catch (error) {
        console.error('Error loading permissions:', error);
        showError('Error loading permissions: ' + error.message);
    }
}

// Display permissions as checkboxes
function displayPermissionsCheckboxes(selectedIds = []) {
    const container = document.getElementById('permissions-list');
    
    // Group by guard_name
    const grouped = {};
    allPermissions.forEach(perm => {
        const guard = perm.guard_name || 'web';
        if (!grouped[guard]) {
            grouped[guard] = [];
        }
        grouped[guard].push(perm);
    });

    let html = '';
    Object.keys(grouped).sort().forEach(guard => {
        html += `<div style="margin-bottom: 15px;"><strong>${guard}</strong><br>`;
        grouped[guard].forEach(perm => {
            const checked = selectedIds.includes(perm.id) ? 'checked' : '';
            html += `
                <label style="display: block; margin: 5px 0;">
                    <input type="checkbox" name="permissions[]" value="${perm.id}" ${checked}>
                    ${perm.name}
                </label>
            `;
        });
        html += '</div>';
    });

    container.innerHTML = html;
}

// Clear error for a specific field
function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorEl = document.getElementById(fieldId + '-error');
    if (field) {
        field.classList.remove('is-invalid');
    }
    if (errorEl) {
        errorEl.textContent = '';
        errorEl.style.display = 'none';
    }
}

// Clear all error messages
function clearRoleErrors() {
    clearFieldError('role-name');
    clearFieldError('role-guard-name');
    
    document.getElementById('permissions-error').textContent = '';
    document.getElementById('permissions-error').style.display = 'none';
}

// Display field-specific errors
function displayRoleFieldErrors(errors) {
    console.log('displayRoleFieldErrors called with:', errors);
    console.log('Error type:', typeof errors);
    
    if (!errors || (typeof errors !== 'object')) {
        console.warn('displayRoleFieldErrors called with invalid errors:', errors);
        return;
    }
    
    // Clear previous errors first
    clearRoleErrors();
    
    console.log('Displaying role field errors:', errors);
    console.log('Error keys:', Object.keys(errors));
    
    let errorsDisplayed = 0;
    
    // Display name errors
    if (errors.name) {
        const nameError = Array.isArray(errors.name) ? errors.name[0] : errors.name;
        const nameErrorEl = document.getElementById('role-name-error');
        const nameField = document.getElementById('role-name');
        console.log('Name error element:', nameErrorEl, 'Name field:', nameField);
        if (nameErrorEl && nameField) {
            nameErrorEl.textContent = nameError;
            nameErrorEl.style.setProperty('display', 'block', 'important');
            nameErrorEl.style.setProperty('visibility', 'visible', 'important');
            nameErrorEl.style.setProperty('opacity', '1', 'important');
            nameField.classList.add('is-invalid');
            errorsDisplayed++;
            console.log('✓ Displayed name error:', nameError);
        } else {
            console.error('✗ Name error element not found. ErrorEl:', nameErrorEl, 'Field:', nameField);
        }
    }
    
    // Display guard_name errors
    if (errors.guard_name) {
        const guardError = Array.isArray(errors.guard_name) ? errors.guard_name[0] : errors.guard_name;
        const guardErrorEl = document.getElementById('role-guard-name-error');
        const guardField = document.getElementById('role-guard-name');
        console.log('Guard error element:', guardErrorEl, 'Guard field:', guardField);
        if (guardErrorEl && guardField) {
            guardErrorEl.textContent = guardError;
            guardErrorEl.style.setProperty('display', 'block', 'important');
            guardErrorEl.style.setProperty('visibility', 'visible', 'important');
            guardErrorEl.style.setProperty('opacity', '1', 'important');
            guardField.classList.add('is-invalid');
            errorsDisplayed++;
            console.log('✓ Displayed guard_name error:', guardError);
        } else {
            console.error('✗ Guard error element not found. ErrorEl:', guardErrorEl, 'Field:', guardField);
        }
    }
    
    // Display permissions errors
    if (errors.permissions) {
        const permError = Array.isArray(errors.permissions) ? errors.permissions[0] : errors.permissions;
        const permErrorEl = document.getElementById('permissions-error');
        console.log('Permissions error element:', permErrorEl);
        if (permErrorEl) {
            permErrorEl.textContent = permError;
            permErrorEl.style.setProperty('display', 'block', 'important');
            permErrorEl.style.setProperty('visibility', 'visible', 'important');
            permErrorEl.style.setProperty('opacity', '1', 'important');
            errorsDisplayed++;
            console.log('✓ Displayed permissions error:', permError);
        } else {
            console.error('✗ Permissions error element not found. ErrorEl:', permErrorEl);
        }
    }
    
    console.log(`Total errors displayed: ${errorsDisplayed}`);
    
    // Scroll to first error field
    setTimeout(() => {
        const firstErrorField = document.querySelector('#roleModal .is-invalid');
        const firstErrorEl = document.querySelector('#roleModal .invalid-feedback[style*="display: block"]');
        console.log('First error field:', firstErrorField, 'First error element:', firstErrorEl);
        if (firstErrorField) {
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstErrorField.focus();
        }
    }, 200);
}

// Open role modal for adding
function openRoleModal() {
    currentRoleId = null;
    document.getElementById('roleModalTitle').textContent = 'Add Role';
    document.getElementById('roleForm').reset();
    document.getElementById('role-id').value = '';
    document.getElementById('role-guard-name').value = 'web';
    clearRoleErrors();
    displayPermissionsCheckboxes();
}

// Edit role
async function editRole(roleId) {
    currentRoleId = roleId;
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';

    if (!token) {
        showError('Authentication token not found.');
        return;
    }

    try {
        const response = await fetch(`${apiBaseUrl}/admin/roles/${roleId}`, {
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
            const role = data.data;
            document.getElementById('roleModalTitle').textContent = 'Edit Role';
            document.getElementById('role-id').value = role.id;
            document.getElementById('role-name').value = role.name || '';
            document.getElementById('role-guard-name').value = role.guard_name || 'web';

            const selectedIds = role.permissions ? role.permissions.map(p => p.id) : [];
            displayPermissionsCheckboxes(selectedIds);

            new bootstrap.Modal(document.getElementById('roleModal')).show();
        } else {
            showError(data.message || 'Failed to load role details');
        }
    } catch (error) {
        console.error('Error loading role:', error);
        showError('Error loading role details: ' + error.message);
    }
}

// Save role
async function saveRole() {
    const form = document.getElementById('roleForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';
    
    if (!token) {
        showError('Authentication token not found.');
        return;
    }
    
    const selectedPermissions = Array.from(form.querySelectorAll('input[name="permissions[]"]:checked'))
        .map(cb => parseInt(cb.value))
        .filter(id => !isNaN(id));

    // Validate name is not empty
    const roleName = formData.get('name')?.trim();
    if (!roleName) {
        showError('Role name is required.');
        return;
    }

    const roleData = {
        name: roleName,
        guard_name: formData.get('guard_name') || 'web',
        permissions: selectedPermissions.length > 0 ? selectedPermissions : []
    };

    console.log('Sending role data:', roleData);

    const roleId = currentRoleId;
    const url = roleId ? `${apiBaseUrl}/admin/roles/${roleId}` : `${apiBaseUrl}/admin/roles`;
    const method = roleId ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(roleData)
        });

        // Parse response first
        let data;
        try {
            data = await response.json();
        } catch (parseError) {
            // If JSON parsing fails, show generic error
            showError('An error occurred while saving the role. Please try again.');
            console.error('Error parsing response:', parseError);
            return;
        }

        if (!response.ok) {
            // Handle validation errors (422 status)
            if (response.status === 422) {
                // Check if errors object exists, if not, create one from message
                let errors = data.errors;
                if (!errors && data.message) {
                    // If we have a message but no errors object, check if it's a field-related error
                    const message = data.message.toLowerCase();
                    if (message.includes('name') || message.includes('guard')) {
                        errors = {
                            name: message.includes('name') ? [data.message] : undefined,
                            guard_name: message.includes('guard') ? [data.message] : undefined
                        };
                    } else {
                        // For other messages without errors object, show as generic error
                        showError(data.message);
                        return;
                    }
                }
                
                if (errors) {
                    // Display field-specific errors
                    console.log('Validation failed, errors received:', errors);
                    console.log('Response status:', response.status);
                    console.log('Full response data:', data);
                    
                    // Ensure modal is open and visible
                    const modalElement = document.getElementById('roleModal');
                    if (!modalElement) {
                        console.error('Modal element not found!');
                        showError('An error occurred. Please refresh the page.');
                        return;
                    }
                    
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    const isModalVisible = modalElement.classList.contains('show') || 
                                          modalElement.classList.contains('in') ||
                                          window.getComputedStyle(modalElement).display !== 'none';
                    
                    console.log('Modal visible:', isModalVisible, 'Modal instance:', modal);
                    
                    if (!modal || !isModalVisible) {
                        // If modal is not open, open it
                        console.log('Opening modal to show errors');
                        const newModal = new bootstrap.Modal(modalElement);
                        newModal.show();
                        
                        // Wait for modal to be fully rendered
                        modalElement.addEventListener('shown.bs.modal', function onShown() {
                            modalElement.removeEventListener('shown.bs.modal', onShown);
                            console.log('Modal shown, displaying errors');
                            displayRoleFieldErrors(errors);
                        }, { once: true });
                        
                        // Fallback timeout
                        setTimeout(() => {
                            console.log('Fallback: displaying errors after timeout');
                            displayRoleFieldErrors(errors);
                        }, 500);
                    } else {
                        // Modal is already open, display errors immediately
                        console.log('Modal already open, displaying errors immediately');
                        // Use setTimeout to ensure DOM is ready
                        setTimeout(() => {
                            displayRoleFieldErrors(errors);
                        }, 100);
                    }
                } else {
                    // 422 but no errors object and no message to convert
                    const errorMsg = data.message || 'Validation failed. Please check your input.';
                    console.log('422 error without errors object:', errorMsg);
                    showError(errorMsg);
                }
            } else {
                // For non-422 errors, show user-friendly message
                const errorMsg = data.message || 'An error occurred while saving the role. Please try again.';
                console.log('Non-validation error:', errorMsg);
                showError(errorMsg);
            }
            return;
        }

        if (data.status === 'success') {
            clearRoleErrors();
            showNotification(roleId ? 'Role updated successfully' : 'Role created successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('roleModal')).hide();
            loadRoles(currentPage);
        } else {
            // This shouldn't happen if validation errors are handled above, but just in case
            const errors = data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
            showError(errors || 'Failed to save role');
        }
    } catch (error) {
        console.error('Error saving role:', error);
        showError('Error saving role: ' + error.message);
    }
}

// Delete role
async function deleteRole(roleId) {
    if (!confirm('Are you sure you want to delete this role? This action cannot be undone.')) {
        return;
    }

    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';

    if (!token) {
        showError('Authentication token not found.');
        return;
    }

    try {
        const response = await fetch(`${apiBaseUrl}/admin/roles/${roleId}`, {
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
            showNotification('Role deleted successfully', 'success');
            loadRoles(currentPage);
        } else {
            showError(data.message || 'Failed to delete role');
        }
    } catch (error) {
        console.error('Error deleting role:', error);
        showError('Error deleting role: ' + error.message);
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

