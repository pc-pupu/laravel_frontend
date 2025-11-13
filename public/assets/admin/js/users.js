// Users Management JavaScript

let currentPage = 1;
let currentUserId = null;

// Get token helper function
function getToken() {
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
    
    // Try multiple sources in order of reliability
    const hiddenInput = document.getElementById('session-api-token');
    const tokenFromInput = hiddenInput ? hiddenInput.value.trim() : '';
    const tokenFromCookie = getCookie('api_token');
    const tokenFromStorage = localStorage.getItem('api_token');
    const tokenFromWindow = window.API_TOKEN;
    
    const token = tokenFromInput || tokenFromCookie || tokenFromStorage || tokenFromWindow || '';
    
    // Debug logging (only if token is missing)
    if (!token) {
        console.warn('Token retrieval attempt:', {
            hiddenInput: tokenFromInput ? 'Found' : 'Not found',
            cookie: tokenFromCookie ? 'Found' : 'Not found',
            storage: tokenFromStorage ? 'Found' : 'Not found',
            window: tokenFromWindow ? 'Found' : 'Not found'
        });
    }
    
    return token;
}

// Load users on page load
document.addEventListener('DOMContentLoaded', function() {
    // Function to attempt loading with retries
    function attemptLoad(retries = 3, delay = 300) {
        const token = getToken();
        
        if (token && token.trim() !== '') {
            console.log('Token found, loading users and roles...');
            loadUsers();
            loadRoles();
            return;
        }
        
        if (retries > 0) {
            console.warn(`No token found. Retrying in ${delay}ms... (${retries} retries left)`);
            setTimeout(() => attemptLoad(retries - 1, delay), delay);
        } else {
            console.error('Failed to retrieve token after all retries');
            showError('Authentication token not found. Please refresh the page or login again.');
            
            // Show detailed error with token sources
            if (window.debugToken) {
                const debug = window.debugToken();
                console.error('Token debug info:', debug);
            }
        }
    }
    
    // Start attempting to load
    attemptLoad();
});

// Load users from API
async function loadUsers(page = 1) {
    currentPage = page;
    const search = document.getElementById('search-users')?.value || '';
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';
    
    let url = `${apiBaseUrl}/admin/users?page=${page}&per_page=15`;
    if (search) {
        url += `&search=${encodeURIComponent(search)}`;
    }

    if (!token) {
        showError('Authentication token not found. Please refresh the page.');
        return;
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
            displayUsers(data.data.data || []);
            displayPagination(data.data);
        } else {
            showError(data.message || 'Failed to load users');
        }
    } catch (error) {
        console.error('Error loading users:', error);
        showError('Error loading users: ' + error.message);
    }
}

// Display users in table
function displayUsers(users) {
    const tbody = document.getElementById('users-table-body');
    
    if (users.length === 0) {
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
                    <button class="btn-admin btn-admin-sm btn-admin-primary" onclick="editUser(${user.uid})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn-admin btn-admin-sm btn-admin-danger" onclick="deleteUser(${user.uid})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

// Display pagination
function displayPagination(pagination) {
    const paginationDiv = document.getElementById('users-pagination');
    if (!pagination || !pagination.total) {
        paginationDiv.innerHTML = '';
        return;
    }

    const totalPages = pagination.last_page;
    let html = '<div class="pagination">';

    // Previous button
    if (pagination.current_page > 1) {
        html += `<a href="#" class="page-link" onclick="loadUsers(${pagination.current_page - 1}); return false;">Previous</a>`;
    }

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === pagination.current_page) {
            html += `<span class="page-link" style="background: var(--primary-gradient); color: #fff;">${i}</span>`;
        } else {
            html += `<a href="#" class="page-link" onclick="loadUsers(${i}); return false;">${i}</a>`;
        }
    }

    // Next button
    if (pagination.current_page < totalPages) {
        html += `<a href="#" class="page-link" onclick="loadUsers(${pagination.current_page + 1}); return false;">Next</a>`;
    }

    html += '</div>';
    paginationDiv.innerHTML = html;
}

// Search users
function searchUsers() {
    clearTimeout(searchUsers.timeout);
    searchUsers.timeout = setTimeout(() => {
        loadUsers(1);
    }, 500);
}

// Load roles for dropdown
async function loadRoles() {
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';

    if (!token) {
        console.error('Token not found for loading roles');
        return;
    }

    try {
        const response = await fetch(`${apiBaseUrl}/admin/roles?per_page=1000`, {
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
            const rolesSelect = document.getElementById('user-roles');
            if (rolesSelect) {
                rolesSelect.innerHTML = '<option value="">Select roles...</option>' + 
                    (data.data.data || []).map(role => 
                        `<option value="${role.id}">${role.name}</option>`
                    ).join('');
            }
        }
    } catch (error) {
        console.error('Error loading roles:', error);
        showError('Error loading roles: ' + error.message);
    }
}

// Open user modal for adding
async function openUserModal() {
    currentUserId = null;
    document.getElementById('userModalTitle').textContent = 'Add User';
    document.getElementById('userForm').reset();
    document.getElementById('user-id').value = '';
    document.getElementById('password-required').style.display = 'inline';
    document.getElementById('user-password').required = true;
    clearUserErrors();
    document.getElementById('user-password-confirm').required = true;
    document.getElementById('user-status').value = '1';
    
    // Ensure roles are loaded
    await loadRoles();
    
    // Clear role selections
    const rolesSelect = document.getElementById('user-roles');
    if (rolesSelect) {
        Array.from(rolesSelect.options).forEach(option => {
            option.selected = false;
        });
    }
}

// Edit user
async function editUser(userId) {
    currentUserId = userId;
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';

    if (!token) {
        showError('Authentication token not found.');
        return;
    }

    try {
        const response = await fetch(`${apiBaseUrl}/admin/users/${userId}`, {
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

            // Ensure roles are loaded first
            await loadRoles();

            // Set selected roles
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
            showError(data.message || 'Failed to load user details');
        }
    } catch (error) {
        console.error('Error loading user:', error);
        showError('Error loading user details: ' + error.message);
    }
}

// Save user
async function saveUser() {
    const form = document.getElementById('userForm');
    
    // Clear all previous errors first
    clearUserErrors();
    
    if (!form.checkValidity()) {
        form.reportValidity();
        // Also show field-level errors for HTML5 validation
        const invalidFields = form.querySelectorAll(':invalid');
        invalidFields.forEach(field => {
            const fieldId = field.id;
            if (fieldId) {
                const errorEl = document.getElementById(fieldId + '-error');
                if (errorEl) {
                    errorEl.textContent = field.validationMessage || 'This field is required.';
                    errorEl.style.setProperty('display', 'block', 'important');
                    errorEl.style.setProperty('visibility', 'visible', 'important');
                    errorEl.style.setProperty('opacity', '1', 'important');
                    field.classList.add('is-invalid');
                }
            }
        });
        // Scroll to first invalid field
        if (invalidFields.length > 0) {
            invalidFields[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            invalidFields[0].focus();
        }
        return;
    }

    const formData = new FormData(form);
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';
    
    if (!token) {
        showError('Authentication token not found.');
        return;
    }
    
    const userData = {
        name: formData.get('name'),
        email: formData.get('email'),
        status: parseInt(formData.get('status')) || 1,
        roles: Array.from(document.getElementById('user-roles').selectedOptions)
            .map(opt => parseInt(opt.value))
            .filter(id => !isNaN(id))
    };

    // Password is required for new users
    const userId = currentUserId;
    if (!userId) {
        const password = formData.get('password');
        const passwordConfirm = formData.get('password_confirmation');
        
        // Clear previous errors
        clearUserErrors();
        
        if (!password || password.length < 8) {
            const passwordError = 'Password is required and must be at least 8 characters.';
            const passwordErrorEl = document.getElementById('user-password-error');
            const passwordField = document.getElementById('user-password');
            if (passwordErrorEl && passwordField) {
                passwordErrorEl.textContent = passwordError;
                passwordErrorEl.style.setProperty('display', 'block', 'important');
                passwordErrorEl.style.setProperty('visibility', 'visible', 'important');
                passwordErrorEl.style.setProperty('opacity', '1', 'important');
                passwordField.classList.add('is-invalid');
                passwordField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                passwordField.focus();
            }
            return;
        }
        
        if (password !== passwordConfirm) {
            const passwordConfirmError = 'Passwords do not match.';
            const passwordConfirmErrorEl = document.getElementById('user-password-confirm-error');
            const passwordConfirmField = document.getElementById('user-password-confirm');
            if (passwordConfirmErrorEl && passwordConfirmField) {
                passwordConfirmErrorEl.textContent = passwordConfirmError;
                passwordConfirmErrorEl.style.setProperty('display', 'block', 'important');
                passwordConfirmErrorEl.style.setProperty('visibility', 'visible', 'important');
                passwordConfirmErrorEl.style.setProperty('opacity', '1', 'important');
                passwordConfirmField.classList.add('is-invalid');
                passwordConfirmField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                passwordConfirmField.focus();
            }
            return;
        }
        
        userData.password = password;
        userData.password_confirmation = passwordConfirm;
    } else {
        // For existing users, password is optional
        const password = formData.get('password');
        if (password && password.length > 0) {
            const passwordConfirm = formData.get('password_confirmation');
            
            // Clear previous errors
            clearUserFieldError('user-password');
            clearUserFieldError('user-password-confirm');
            
            if (password !== passwordConfirm) {
                const passwordConfirmError = 'Passwords do not match.';
                const passwordConfirmErrorEl = document.getElementById('user-password-confirm-error');
                const passwordConfirmField = document.getElementById('user-password-confirm');
                if (passwordConfirmErrorEl && passwordConfirmField) {
                    passwordConfirmErrorEl.textContent = passwordConfirmError;
                    passwordConfirmErrorEl.style.setProperty('display', 'block', 'important');
                    passwordConfirmErrorEl.style.setProperty('visibility', 'visible', 'important');
                    passwordConfirmErrorEl.style.setProperty('opacity', '1', 'important');
                    passwordConfirmField.classList.add('is-invalid');
                    passwordConfirmField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    passwordConfirmField.focus();
                }
                return;
            }
            if (password.length < 8) {
                const passwordError = 'Password must be at least 8 characters.';
                const passwordErrorEl = document.getElementById('user-password-error');
                const passwordField = document.getElementById('user-password');
                if (passwordErrorEl && passwordField) {
                    passwordErrorEl.textContent = passwordError;
                    passwordErrorEl.style.setProperty('display', 'block', 'important');
                    passwordErrorEl.style.setProperty('visibility', 'visible', 'important');
                    passwordErrorEl.style.setProperty('opacity', '1', 'important');
                    passwordField.classList.add('is-invalid');
                    passwordField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    passwordField.focus();
                }
                return;
            }
            userData.password = password;
            userData.password_confirmation = passwordConfirm;
        }
    }

    const url = userId ? `${apiBaseUrl}/admin/users/${userId}` : `${apiBaseUrl}/admin/users`;
    const method = userId ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(userData)
        });

        // Parse response first
        let data;
        try {
            data = await response.json();
        } catch (parseError) {
            // If JSON parsing fails, show generic error
            showError('An error occurred while saving the user. Please try again.');
            console.error('Error parsing response:', parseError);
            return;
        }

        if (!response.ok) {
            // Handle validation errors (422 status)
            if (response.status === 422) {
                // Check if errors object exists, if not, create one from message
                let errors = data.errors;
                if (!errors && data.message) {
                    // If we have a message but no errors object, check if it's a password-related error
                    const message = data.message.toLowerCase();
                    if (message.includes('password') || message.includes('last 3')) {
                        errors = {
                            password: [data.message]
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
                    const modalElement = document.getElementById('userModal');
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
                            displayUserFieldErrors(errors);
                        }, { once: true });
                        
                        // Fallback timeout
                        setTimeout(() => {
                            console.log('Fallback: displaying errors after timeout');
                            displayUserFieldErrors(errors);
                        }, 500);
                    } else {
                        // Modal is already open, display errors immediately
                        console.log('Modal already open, displaying errors immediately');
                        // Use setTimeout to ensure DOM is ready
                        setTimeout(() => {
                            displayUserFieldErrors(errors);
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
                const errorMsg = data.message || 'An error occurred while saving the user. Please try again.';
                console.log('Non-validation error:', errorMsg);
                showError(errorMsg);
            }
            return;
        }

        if (data.status === 'success') {
            clearUserErrors();
            showNotification(userId ? 'User updated successfully' : 'User created successfully', 'success');
            bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
            loadUsers(currentPage);
        } else {
            const errors = data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
            showError(errors || 'Failed to save user');
        }
    } catch (error) {
        console.error('Error saving user:', error);
        showError('Error saving user: ' + error.message);
    }
}

// Delete user
async function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }

    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';

    if (!token) {
        showError('Authentication token not found.');
        return;
    }

    try {
        const response = await fetch(`${apiBaseUrl}/admin/users/${userId}`, {
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
            showNotification('User deleted successfully', 'success');
            loadUsers(currentPage);
        } else {
            showError(data.message || 'Failed to delete user');
        }
    } catch (error) {
        console.error('Error deleting user:', error);
        showError('Error deleting user: ' + error.message);
    }
}

// Show error notification
function showError(message) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger alert-dismissible fade show';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const content = document.querySelector('.admin-content');
    if (content) {
        content.insertBefore(alert, content.firstChild);
        setTimeout(() => alert.remove(), 5000);
    }
}

// Clear error for a specific field
function clearUserFieldError(fieldId) {
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
function clearUserErrors() {
    clearUserFieldError('user-name');
    clearUserFieldError('user-email');
    clearUserFieldError('user-password');
    clearUserFieldError('user-password-confirm');
    clearUserFieldError('user-roles');
    clearUserFieldError('user-status');
}

// Display field-specific errors
function displayUserFieldErrors(errors) {
    console.log('displayUserFieldErrors called with:', errors);
    console.log('Error type:', typeof errors);
    console.log('Is array:', Array.isArray(errors));
    
    if (!errors || (typeof errors !== 'object')) {
        console.warn('displayUserFieldErrors called with invalid errors:', errors);
        return;
    }
    
    // Clear previous errors first
    clearUserErrors();
    
    console.log('Displaying user field errors:', errors);
    console.log('Error keys:', Object.keys(errors));
    
    let errorsDisplayed = 0;
    
    // Display name errors
    if (errors.name) {
        const nameError = Array.isArray(errors.name) ? errors.name[0] : errors.name;
        const nameErrorEl = document.getElementById('user-name-error');
        const nameField = document.getElementById('user-name');
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
    
    // Display email errors
    if (errors.email) {
        const emailError = Array.isArray(errors.email) ? errors.email[0] : errors.email;
        const emailErrorEl = document.getElementById('user-email-error');
        const emailField = document.getElementById('user-email');
        console.log('Email error element:', emailErrorEl, 'Email field:', emailField);
        if (emailErrorEl && emailField) {
            emailErrorEl.textContent = emailError;
            emailErrorEl.style.setProperty('display', 'block', 'important');
            emailErrorEl.style.setProperty('visibility', 'visible', 'important');
            emailErrorEl.style.setProperty('opacity', '1', 'important');
            emailField.classList.add('is-invalid');
            errorsDisplayed++;
            console.log('✓ Displayed email error:', emailError);
        } else {
            console.error('✗ Email error element not found. ErrorEl:', emailErrorEl, 'Field:', emailField);
        }
    }
    
    // Display password errors
    if (errors.password) {
        const passwordError = Array.isArray(errors.password) ? errors.password[0] : errors.password;
        const passwordErrorEl = document.getElementById('user-password-error');
        const passwordField = document.getElementById('user-password');
        console.log('Password error element:', passwordErrorEl, 'Password field:', passwordField);
        if (passwordErrorEl && passwordField) {
            passwordErrorEl.textContent = passwordError;
            passwordErrorEl.style.setProperty('display', 'block', 'important');
            passwordErrorEl.style.setProperty('visibility', 'visible', 'important');
            passwordErrorEl.style.setProperty('opacity', '1', 'important');
            passwordField.classList.add('is-invalid');
            errorsDisplayed++;
            console.log('✓ Displayed password error:', passwordError);
        } else {
            console.error('✗ Password error element not found. ErrorEl:', passwordErrorEl, 'Field:', passwordField);
        }
    }
    
    // Display password confirmation errors
    if (errors.password_confirmation) {
        const passwordConfirmError = Array.isArray(errors.password_confirmation) ? errors.password_confirmation[0] : errors.password_confirmation;
        const passwordConfirmErrorEl = document.getElementById('user-password-confirm-error');
        const passwordConfirmField = document.getElementById('user-password-confirm');
        console.log('Password confirm error element:', passwordConfirmErrorEl, 'Password confirm field:', passwordConfirmField);
        if (passwordConfirmErrorEl && passwordConfirmField) {
            passwordConfirmErrorEl.textContent = passwordConfirmError;
            passwordConfirmErrorEl.style.setProperty('display', 'block', 'important');
            passwordConfirmErrorEl.style.setProperty('visibility', 'visible', 'important');
            passwordConfirmErrorEl.style.setProperty('opacity', '1', 'important');
            passwordConfirmField.classList.add('is-invalid');
            errorsDisplayed++;
            console.log('✓ Displayed password confirmation error:', passwordConfirmError);
        } else {
            console.error('✗ Password confirmation error element not found. ErrorEl:', passwordConfirmErrorEl, 'Field:', passwordConfirmField);
        }
    }
    
    // Display roles errors
    if (errors.roles) {
        const rolesError = Array.isArray(errors.roles) ? errors.roles[0] : errors.roles;
        const rolesErrorEl = document.getElementById('user-roles-error');
        const rolesField = document.getElementById('user-roles');
        console.log('Roles error element:', rolesErrorEl, 'Roles field:', rolesField);
        if (rolesErrorEl && rolesField) {
            rolesErrorEl.textContent = rolesError;
            rolesErrorEl.style.setProperty('display', 'block', 'important');
            rolesErrorEl.style.setProperty('visibility', 'visible', 'important');
            rolesErrorEl.style.setProperty('opacity', '1', 'important');
            rolesField.classList.add('is-invalid');
            errorsDisplayed++;
            console.log('✓ Displayed roles error:', rolesError);
        } else {
            console.error('✗ Roles error element not found. ErrorEl:', rolesErrorEl, 'Field:', rolesField);
        }
    }
    
    // Display status errors
    if (errors.status) {
        const statusError = Array.isArray(errors.status) ? errors.status[0] : errors.status;
        const statusErrorEl = document.getElementById('user-status-error');
        const statusField = document.getElementById('user-status');
        console.log('Status error element:', statusErrorEl, 'Status field:', statusField);
        if (statusErrorEl && statusField) {
            statusErrorEl.textContent = statusError;
            statusErrorEl.style.setProperty('display', 'block', 'important');
            statusErrorEl.style.setProperty('visibility', 'visible', 'important');
            statusErrorEl.style.setProperty('opacity', '1', 'important');
            statusField.classList.add('is-invalid');
            errorsDisplayed++;
            console.log('✓ Displayed status error:', statusError);
        } else {
            console.error('✗ Status error element not found. ErrorEl:', statusErrorEl, 'Field:', statusField);
        }
    }
    
    console.log(`Total errors displayed: ${errorsDisplayed}`);
    
    // Scroll to first error field
    setTimeout(() => {
        const firstErrorField = document.querySelector('#userModal .is-invalid');
        const firstErrorEl = document.querySelector('#userModal .invalid-feedback[style*="display: block"]');
        console.log('First error field:', firstErrorField, 'First error element:', firstErrorEl);
        if (firstErrorField) {
            firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstErrorField.focus();
        }
    }, 200);
}

// Show success notification
function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const content = document.querySelector('.admin-content');
    if (content) {
        content.insertBefore(alert, content.firstChild);
        setTimeout(() => alert.remove(), 5000);
    }
}

// Test token function (for debugging)
async function testToken() {
    const token = getToken();
    const apiBaseUrl = window.API_BASE_URL || 'http://localhost:8000/api';
    const resultSpan = document.getElementById('token-test-result');
    
    if (!token) {
        if (resultSpan) resultSpan.innerHTML = '<span style="color: red;">❌ No token found</span>';
        console.error('No token available for testing');
        return;
    }
    
    if (resultSpan) resultSpan.innerHTML = '<span>Testing...</span>';
    
    try {
        const response = await fetch(`${apiBaseUrl}/user`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (resultSpan) resultSpan.innerHTML = '<span style="color: green;">✅ Token valid - User: ' + (data.name || 'Unknown') + '</span>';
            console.log('Token test successful:', data);
        } else {
            if (resultSpan) resultSpan.innerHTML = '<span style="color: red;">❌ Token invalid (Status: ' + response.status + ')</span>';
            console.error('Token test failed:', response.status, await response.text());
        }
    } catch (error) {
        if (resultSpan) resultSpan.innerHTML = '<span style="color: red;">❌ Error: ' + error.message + '</span>';
        console.error('Token test error:', error);
    }
}

