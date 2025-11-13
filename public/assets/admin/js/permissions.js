// Permissions Management JavaScript

let currentPage = 1;
let currentPermissionId = null;

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

// Load permissions on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        loadPermissions();
        loadGuards();
    }, 100);
});

// Load permissions from API
async function loadPermissions(page = 1) {
    currentPage = page;
    const search = document.getElementById('search-permissions')?.value || '';
    const guard = document.getElementById('filter-guard')?.value || '';
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';
    
    if (!token) {
        showError('Authentication token not found. Please refresh the page.');
        return;
    }
    
    let url = `${apiBaseUrl}/admin/permissions?page=${page}&per_page=15`;
    if (search) {
        url += `&search=${encodeURIComponent(search)}`;
    }
    if (guard) {
        url += `&guard_name=${encodeURIComponent(guard)}`;
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
            displayPermissions(data.data.data || []);
            displayPagination(data.data, 'permissions');
        } else {
            showError(data.message || 'Failed to load permissions');
        }
    } catch (error) {
        console.error('Error loading permissions:', error);
        showError('Error loading permissions: ' + error.message);
    }
}

// Display permissions in table
function displayPermissions(permissions) {
    const tbody = document.getElementById('permissions-table-body');
    
    if (permissions.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No permissions found</td></tr>';
        return;
    }

    tbody.innerHTML = permissions.map(perm => {
        const rolesCount = perm.roles ? perm.roles.length : 0;

        return `
            <tr>
                <td>${perm.id}</td>
                <td>${perm.name}</td>
                <td>${perm.guard_name || 'web'}</td>
                <td>${rolesCount} role(s)</td>
                <td>
                    <button class="btn-admin btn-admin-sm btn-admin-primary" onclick="editPermission(${perm.id})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-admin btn-admin-sm btn-admin-danger" onclick="deletePermission(${perm.id})">
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
        html += `<a href="#" class="page-link" onclick="loadPermissions(${pagination.current_page - 1}); return false;">Previous</a>`;
    }

    for (let i = 1; i <= totalPages; i++) {
        if (i === pagination.current_page) {
            html += `<span class="page-link" style="background: var(--primary-gradient); color: #fff;">${i}</span>`;
        } else {
            html += `<a href="#" class="page-link" onclick="loadPermissions(${i}); return false;">${i}</a>`;
        }
    }

    if (pagination.current_page < totalPages) {
        html += `<a href="#" class="page-link" onclick="loadPermissions(${pagination.current_page + 1}); return false;">Next</a>`;
    }

    html += '</div>';
    paginationDiv.innerHTML = html;
}

// Search permissions
function searchPermissions() {
    clearTimeout(searchPermissions.timeout);
    searchPermissions.timeout = setTimeout(() => {
        loadPermissions(1);
    }, 500);
}

// Load guard names for filter
async function loadGuards() {
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';

    if (!token) {
        console.error('Token not found for loading guards');
        return;
    }

    try {
        const response = await fetch(`${apiBaseUrl}/admin/permissions/modules/list`, {
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
            const guardSelect = document.getElementById('filter-guard');
            if (guardSelect) {
                (data.data || []).forEach(guard => {
                    const option = document.createElement('option');
                    option.value = guard;
                    option.textContent = guard;
                    guardSelect.appendChild(option);
                });
            }
        }
    } catch (error) {
        console.error('Error loading guards:', error);
    }
}

// Clear error for a specific field
function clearPermissionFieldError(fieldId) {
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
function clearPermissionErrors() {
    clearPermissionFieldError('permission-name');
    clearPermissionFieldError('permission-guard-name');
}

// Display field-specific errors
function displayPermissionFieldErrors(errors) {
    console.log('displayPermissionFieldErrors called with:', errors);
    console.log('Error type:', typeof errors);
    
    if (!errors || (typeof errors !== 'object')) {
        console.warn('displayPermissionFieldErrors called with invalid errors:', errors);
        return;
    }
    
    // Clear previous errors first
    clearPermissionErrors();
    
    console.log('Displaying permission field errors:', errors);
    console.log('Error keys:', Object.keys(errors));
    
    let errorsDisplayed = 0;
    
    // Display name errors
    if (errors.name) {
        const nameError = Array.isArray(errors.name) ? errors.name[0] : errors.name;
        const nameErrorEl = document.getElementById('permission-name-error');
        const nameField = document.getElementById('permission-name');
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
        const guardErrorEl = document.getElementById('permission-guard-name-error');
        const guardField = document.getElementById('permission-guard-name');
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
    
    console.log(`Total errors displayed: ${errorsDisplayed}`);
    
    // Scroll to first error field
    setTimeout(() => {
        const firstErrorField = document.querySelector('#permissionModal .is-invalid');
        const firstErrorEl = document.querySelector('#permissionModal .invalid-feedback[style*="display: block"]');
        console.log('First error field:', firstErrorField, 'First error element:', firstErrorEl);
        if (firstErrorField) {
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstErrorField.focus();
        }
    }, 200);
}

// Open permission modal for adding
function openPermissionModal() {
    currentPermissionId = null;
    document.getElementById('permissionModalTitle').textContent = 'Add Permission';
    document.getElementById('permissionForm').reset();
    document.getElementById('permission-id').value = '';
    document.getElementById('permission-guard-name').value = 'web';
    clearPermissionErrors();
}

// Edit permission
async function editPermission(permissionId) {
    currentPermissionId = permissionId;
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';

    if (!token) {
        showError('Authentication token not found.');
        return;
    }

    try {
        const response = await fetch(`${apiBaseUrl}/admin/permissions/${permissionId}`, {
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
            const perm = data.data;
            document.getElementById('permissionModalTitle').textContent = 'Edit Permission';
            document.getElementById('permission-id').value = perm.id;
            document.getElementById('permission-name').value = perm.name || '';
            document.getElementById('permission-guard-name').value = perm.guard_name || 'web';
            
            clearPermissionErrors();

            new bootstrap.Modal(document.getElementById('permissionModal')).show();
        } else {
            showError(data.message || 'Failed to load permission details');
        }
    } catch (error) {
        console.error('Error loading permission:', error);
        showError('Error loading permission details: ' + error.message);
    }
}

// Save permission
async function savePermission() {
    const form = document.getElementById('permissionForm');
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
    
    const permissionData = {
        name: formData.get('name'),
        guard_name: formData.get('guard_name') || 'web'
    };

    const permissionId = currentPermissionId;
    const url = permissionId ? `${apiBaseUrl}/admin/permissions/${permissionId}` : `${apiBaseUrl}/admin/permissions`;
    const method = permissionId ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(permissionData)
        });

        // Parse response first
        let data;
        try {
            data = await response.json();
        } catch (parseError) {
            // If JSON parsing fails, show generic error
            showError('An error occurred while saving the permission. Please try again.');
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
                    const modalElement = document.getElementById('permissionModal');
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
                            displayPermissionFieldErrors(errors);
                        }, { once: true });
                        
                        // Fallback timeout
                        setTimeout(() => {
                            console.log('Fallback: displaying errors after timeout');
                            displayPermissionFieldErrors(errors);
                        }, 500);
                    } else {
                        // Modal is already open, display errors immediately
                        console.log('Modal already open, displaying errors immediately');
                        // Use setTimeout to ensure DOM is ready
                        setTimeout(() => {
                            displayPermissionFieldErrors(errors);
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
                const errorMsg = data.message || 'An error occurred while saving the permission. Please try again.';
                console.log('Non-validation error:', errorMsg);
                showError(errorMsg);
            }
            return;
        }

        if (data.status === 'success') {
            clearPermissionErrors();
            showNotification(permissionId ? 'Permission updated successfully' : 'Permission created successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('permissionModal')).hide();
            loadPermissions(currentPage);
        } else {
            const errors = data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
            showError(errors || 'Failed to save permission');
        }
    } catch (error) {
        console.error('Error saving permission:', error);
        showError('Error saving permission: ' + error.message);
    }
}

// Delete permission
async function deletePermission(permissionId) {
    if (!confirm('Are you sure you want to delete this permission? This action cannot be undone.')) {
        return;
    }

    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';

    if (!token) {
        showError('Authentication token not found.');
        return;
    }

    try {
        const response = await fetch(`${apiBaseUrl}/admin/permissions/${permissionId}`, {
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
            showNotification('Permission deleted successfully', 'success');
            loadPermissions(currentPage);
        } else {
            showError(data.message || 'Failed to delete permission');
        }
    } catch (error) {
        console.error('Error deleting permission:', error);
        showError('Error deleting permission: ' + error.message);
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
