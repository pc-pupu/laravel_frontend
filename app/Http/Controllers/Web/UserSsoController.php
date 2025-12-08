<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserSsoController extends Controller
{
    /**
     * HRMS SSO Login Handler
     * GET /user/sso/{token}
     */
    public function hrmsSsoLogin(Request $request, $token)
    {
        try {
            // Call backend API to validate token
            // Note: Laravel already URL-decodes route parameters, so no need to urldecode again
            $response = Http::post(config('services.api.base_url') . '/validate-sso-token', [
                'token' => $token,
                'max_age' => 120
            ]);

            if (!$response->successful()) {
                $error = $response->json()['message'] ?? 'Invalid Token';
                return response()->json(['error' => $error], 400);
            }

            $data = $response->json();
            $userData = $data['user'];

            // Check if current user is same as token user
            $currentUserSession = $request->session()->get('user');
            if ($currentUserSession && $currentUserSession['uid'] == $userData['uid']) {
                return redirect()->route('sso-dashboard');
            }

            // Force logout if different user
            if ($currentUserSession && $currentUserSession['uid'] != $userData['uid']) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('logout');
            }

            // Create session for the user
            $request->session()->put('user', $userData);
            $request->session()->put('api_token', null);
            $request->session()->regenerate();

            return redirect()->route('sso-dashboard');

        } catch (\Exception $e) {
            Log::error('HRMS SSO Login Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error: Invalid Token'
            ], 400);
        }
    }

    /**
     * DDO SSO Login Handler
     * GET /sso/ddo/{token}
     */
    public function ddoSsoLogin(Request $request, $token)
    {
        try {
            // Call backend API to validate token (5 minutes for DDO)
            // Note: Laravel already URL-decodes route parameters, so no need to urldecode again
            $response = Http::post(config('services.api.base_url') . '/validate-sso-token', [
                'token' => $token,
                'max_age' => 300
            ]);

            if (!$response->successful()) {
                $error = $response->json()['message'] ?? 'Invalid Token';
                return response()->json(['error' => $error], 400);
            }

            $data = $response->json();
            $userData = $data['user'];

            // Check if current user is same as token user
            $currentUserSession = $request->session()->get('user');
            if ($currentUserSession && $currentUserSession['uid'] == $userData['uid']) {
                return redirect()->route('sso-dashboard');
            }

            // Force logout if different user
            if ($currentUserSession && $currentUserSession['uid'] != $userData['uid']) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('logout');
            }

            // Create session for the user
            $request->session()->put('user', $userData);
            $request->session()->put('api_token', null);
            $request->session()->regenerate();

            return redirect()->route('sso-dashboard');

        } catch (\Exception $e) {
            Log::error('DDO SSO Login Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error: Invalid Token'
            ], 400);
        }
    }

    /**
     * HRMS Login Form
     * GET /hrms-login
     */
    public function hrmsLoginForm(Request $request)
    {
        // If already logged in, redirect to dashboard
        if (Auth::check() || $request->session()->has('user')) {
            return redirect()->route('sso-dashboard');
        }

        return view('userSso.hrms-login');
    }

    /**
     * HRMS Login Form Submit
     * POST /hrms-login
     */
    public function hrmsLoginSubmit(Request $request)
    {
        $request->validate([
            'hrms_id' => 'required|string'
        ]);
        
        $hrmsId = trim($request->input('hrms_id'));
       

        try {
            // Call backend API to create user and generate token
            $response = Http::post(config('services.api.base_url') . '/hrms-login-manual', [
                'hrms_id' => $hrmsId
            ]);
            
            if (!$response->successful()) {
                $error = $response->json()['message'] ?? 'Login failed';
                return back()->withErrors(['hrms_id' => $error])->withInput();
            }

            $data = $response->json();
            $token = $data['token'];
            
            return redirect()->route('user-sso.hrms-sso', ['token' => urlencode($token)]);

        } catch (\Exception $e) {
            Log::error('HRMS Manual Login Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['hrms_id' => 'Login failed. Please try again.'])->withInput();
        }
    }

    /**
     * Get API Key (for logged-in users)
     * GET /get-key
     */
    public function getApiKey(Request $request)
    {
        if (!Auth::check() && !$request->session()->has('user')) {
            return response()->json(['error' => 'Unauthorized access'], 401);
        }

        $apiKey = config('services.hrms.api_secret_token', '');
        
        return response()->json(['key' => $apiKey]);
    }

    /**
     * Get Test Info (for testing HRMS data fetch)
     * GET /get-test-info/{hrmsId}
     */
    public function getTestInfo($hrmsId = '')
    {
        // This is a test endpoint - implement if needed
        return response()->json([
            'message' => 'Test info endpoint',
            'hrms_id' => $hrmsId
        ]);
    }
}

