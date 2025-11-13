// Token Debug Utility
// This script helps debug token issues

function debugToken() {
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
    
    const hiddenInput = document.getElementById('session-api-token');
    const tokenFromInput = hiddenInput ? hiddenInput.value.trim() : '';
    const tokenFromCookie = getCookie('api_token');
    const tokenFromStorage = localStorage.getItem('api_token');
    const tokenFromWindow = window.API_TOKEN || '';
    
    const debugInfo = {
        hiddenInput: {
            exists: !!hiddenInput,
            value: tokenFromInput ? tokenFromInput.substring(0, 20) + '...' : 'empty',
            length: tokenFromInput.length
        },
        cookie: {
            value: tokenFromCookie ? tokenFromCookie.substring(0, 20) + '...' : 'not found',
            length: tokenFromCookie ? tokenFromCookie.length : 0
        },
        localStorage: {
            value: tokenFromStorage ? tokenFromStorage.substring(0, 20) + '...' : 'not found',
            length: tokenFromStorage ? tokenFromStorage.length : 0
        },
        window: {
            value: tokenFromWindow ? tokenFromWindow.substring(0, 20) + '...' : 'not set',
            length: tokenFromWindow ? tokenFromWindow.length : 0
        },
        finalToken: (tokenFromInput || tokenFromCookie || tokenFromStorage || tokenFromWindow || 'NONE'),
        finalTokenLength: (tokenFromInput || tokenFromCookie || tokenFromStorage || tokenFromWindow || '').length
    };
    
    console.table(debugInfo);
    return debugInfo;
}

// Make it available globally
window.debugToken = debugToken;

