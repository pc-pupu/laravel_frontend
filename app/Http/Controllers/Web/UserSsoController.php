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
                return redirect()->route('dashboard');
            }

            // Force logout if different user
            if ($currentUserSession && $currentUserSession['uid'] != $userData['uid']) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('logout');
            }

            // Generate Sanctum token for API access
            // We need to create a token using the user's credentials
            // Since we don't have the password, we'll call a backend endpoint to generate token
            try {
                $tokenResponse = Http::post(config('services.api.base_url') . '/generate-sso-token', [
                    'uid' => $userData['uid'],
                    'name' => $userData['name']
                ]);

                if ($tokenResponse->successful()) {
                    $tokenData = $tokenResponse->json();
                    $apiToken = $tokenData['token'] ?? null;
                } else {
                    Log::warning('Failed to generate SSO token', [
                        'uid' => $userData['uid'],
                        'response' => $tokenResponse->json()
                    ]);
                    $apiToken = null;
                }
            } catch (\Exception $e) {
                Log::error('Error generating SSO token', [
                    'error' => $e->getMessage(),
                    'uid' => $userData['uid']
                ]);
                $apiToken = null;
            }

            // Create session for the user
            $request->session()->put('user', $userData);
            $request->session()->put('api_token', $apiToken);
            $request->session()->regenerate();

            return redirect()->route('dashboard');

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
                return redirect()->route('dashboard');
            }

            // Force logout if different user
            if ($currentUserSession && $currentUserSession['uid'] != $userData['uid']) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('logout');
            }

            // Generate Sanctum token for API access
            // We need to create a token using the user's credentials
            // Since we don't have the password, we'll call a backend endpoint to generate token
            try {
                $tokenResponse = Http::post(config('services.api.base_url') . '/generate-sso-token', [
                    'uid' => $userData['uid'],
                    'name' => $userData['name']
                ]);

                if ($tokenResponse->successful()) {
                    $tokenData = $tokenResponse->json();
                    $apiToken = $tokenData['token'] ?? null;
                } else {
                    Log::warning('Failed to generate SSO token', [
                        'uid' => $userData['uid'],
                        'response' => $tokenResponse->json()
                    ]);
                    $apiToken = null;
                }
            } catch (\Exception $e) {
                Log::error('Error generating SSO token', [
                    'error' => $e->getMessage(),
                    'uid' => $userData['uid']
                ]);
                $apiToken = null;
            }

            // Create session for the user
            $request->session()->put('user', $userData);
            $request->session()->put('api_token', $apiToken);
            $request->session()->regenerate();

            return redirect()->route('dashboard');

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
    //   print_r($request->session());die;
        // If already logged in, redirect to dashboard
        if (Auth::check() || $request->session()->has('user')) {
            return redirect()->route('dashboard');
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
     * Get Test Info (for testing HRMS data fetch) - Matching Drupal get_test_info
     * GET /get-test-info/{hrmsId}
     */
    public function getTestInfo($hrmsId = '')
    {
        $hrmsId = trim($hrmsId);
        
        if (empty($hrmsId)) {
            return response()->json([
                'error' => 'HRMS ID is required'
            ], 400);
        }

        try {
            // Call backend API to fetch HRMS test data
            $response = Http::get(config('services.api.base_url') . '/get-test-info/' . urlencode($hrmsId));
            
            if (!$response->successful()) {
                $error = $response->json();
                return response()->json([
                    'error' => $error['message'] ?? 'Failed to fetch HRMS data',
                    'details' => $error
                ], $response->status());
            }

            $data = $response->json();
            $userData = $data['data'] ?? null;

            // Display in formatted way like Drupal (with <pre> tags)
            if ($userData) {
                return response('<pre>' . print_r($userData, true) . '</pre>')
                    ->header('Content-Type', 'text/html; charset=utf-8');
            } else {
                return response('<pre>No user data found</pre>')
                    ->header('Content-Type', 'text/html; charset=utf-8');
            }

        } catch (\Exception $e) {
            Log::error('Get Test Info Error', [
                'error' => $e->getMessage(),
                'hrms_id' => $hrmsId,
                'trace' => $e->getTraceAsString()
            ]);

            return response('<pre>Error: ' . htmlspecialchars($e->getMessage()) . '</pre>')
                ->header('Content-Type', 'text/html; charset=utf-8');
        }
    }
}

