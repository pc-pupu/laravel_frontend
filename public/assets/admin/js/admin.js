// admin.js — Shared helpers for admin pages

// CSRF token from meta
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || null;

// Generic API fetch to your frontend proxy (no Bearer token here)
async function apiFetch(path, options = {}) {
    // Default headers
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...(options.headers || {})
    };

    // Add CSRF for mutating requests
    if (csrfToken && ['POST', 'PUT', 'DELETE', 'PATCH'].includes((options.method || 'GET').toUpperCase())) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }

    try {
        const resp = await fetch(path, {
            ...options,
            headers
        });

        // Try to parse JSON if present
        const text = await resp.text();
        let data = null;
        try {
            data = text ? JSON.parse(text) : null;
        } catch (e) {
            // not json
            data = text;
        }

        if (!resp.ok) {
            const errMsg = (data && data.message) ? data.message : `HTTP error: ${resp.status}`;
            const error = new Error(errMsg);
            error.response = resp;
            error.data = data;
            throw error;
        }

        return data;
    } catch (err) {
        console.error('apiFetch error:', err);
        throw err;
    }
}

// Notifications
function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    const content = document.querySelector('.admin-content') || document.body;
    content.insertBefore(alert, content.firstChild);
    setTimeout(() => alert.remove(), 5000);
}

function showError(message) {
    showNotification(message, 'error');
}

// Utility: format date/time in local format
function formatDate(dateString) {
    if (!dateString) return '-';
    const d = new Date(dateString);
    if (isNaN(d)) return dateString;
    return d.toLocaleString();
}

// Simple debounce helper used by several modules
function debounce(fn, wait = 300) {
    let t;
    return function (...args) {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), wait);
    };
}

// Button loading state helpers
function setButtonLoading(button, isLoading) {
    if (!button) return;
    
    if (isLoading) {
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    } else {
        button.disabled = false;
        if (button.dataset.originalText) {
            button.innerHTML = button.dataset.originalText;
            delete button.dataset.originalText;
        }
    }
}

function setElementLoading(element, isLoading, loadingText = 'Loading...') {
    if (!element) return;
    
    if (isLoading) {
        element.disabled = true;
        if (element.tagName === 'BUTTON' || element.tagName === 'A') {
            element.dataset.originalText = element.innerHTML;
            element.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${loadingText}`;
        }
    } else {
        element.disabled = false;
        if (element.dataset.originalText) {
            element.innerHTML = element.dataset.originalText;
            delete element.dataset.originalText;
        }
    }
}

// Export helpers to global so other modules (plain JS) can use them
window.adminHelpers = {
    apiFetch,
    showNotification,
    showError,
    formatDate,
    debounce,
    setButtonLoading,
    setElementLoading
};

// Basic initialization
document.addEventListener('DOMContentLoaded', function () {
    // nothing here by default; pages will call their init functions
});
