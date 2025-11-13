// Admin Panel JavaScript

// API Configuration - Set from window object in layout
const API_BASE_URL = window.API_BASE_URL || 'http://localhost:8000/api';
let API_TOKEN = window.API_TOKEN || localStorage.getItem('api_token') || '';

// Set up CSRF token for all requests
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Helper function to make API requests
async function apiRequest(url, options = {}) {
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...options.headers
    };

    if (API_TOKEN) {
        headers['Authorization'] = `Bearer ${API_TOKEN}`;
    }

    if (csrfToken && (options.method === 'POST' || options.method === 'PUT' || options.method === 'DELETE')) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }

    try {
        const response = await fetch(`${API_BASE_URL}${url}`, {
            ...options,
            headers
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'An error occurred');
        }

        return data;
    } catch (error) {
        console.error('API Request Error:', error);
        throw error;
    }
}

// Show notification
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
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is authenticated
    if (!API_TOKEN && window.location.pathname.includes('/admin')) {
        // Redirect to login if not authenticated
        // window.location.href = '/login';
    }
});

