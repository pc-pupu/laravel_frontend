<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class UserSsoController extends Controller
{
    /**
     * HRMS SSO Login Handler
     * GET /user/sso/{token}
     */
    public function hrmsSsoLogin(Request $request, $token)
    {
        try {
            // Validate token using helper
            $tokenValidation = $this->validateSsoToken($token, 120);
            
            if (!$tokenValidation['valid']) {
                return response()->json([
                    'error' => $tokenValidation['error']
                ], 400);
            }

            $hrmscode = $tokenValidation['code'];

            // Load user by name (HRMS ID)
            $account = DB::table('users')->where('name', $hrmscode)->first();

            if (!$account || !$account->uid) {
                return response()->json([
                    'error' => 'Error: Invalid Token and User or User Not Found'
                ], 400);
            }

            // Check if current user is same as token user
            $currentUser = Auth::user();
            if ($currentUser && $currentUser->uid == $account->uid) {
                return redirect()->route('dashboard');
            }

            // Force logout if different user
            if ($currentUser && $currentUser->uid != $account->uid) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('logout');
            }

            // Update login time
            DB::table('users')
                ->where('uid', $account->uid)
                ->update(['login' => now()]);

            // Log the login
            Log::info('Session opened via HRMS', ['name' => $account->name]);

            // Create session for the user (matching LoginController pattern)
            $user = [
                'uid' => $account->uid,
                'name' => $account->name,
                'email' => $account->email ?? null,
            ];
            
            $request->session()->put('user', $user);
            $request->session()->put('api_token', null); // Will be set on next API call if needed

            // Regenerate session ID
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
            // Validate token using helper (5 minutes for DDO)
            $tokenValidation = $this->validateSsoToken($token, 300);
            
            if (!$tokenValidation['valid']) {
                return response()->json([
                    'error' => $tokenValidation['error']
                ], 400);
            }

            $ddocode = $tokenValidation['code'];

            // Load user by name (DDO code)
            $account = DB::table('users')->where('name', $ddocode)->first();

            if (!$account || !$account->uid) {
                return response()->json([
                    'error' => 'Error: Invalid User from Token or User Not Found'
                ], 400);
            }

            // Check if current user is same as token user
            $currentUser = Auth::user();
            if ($currentUser && $currentUser->uid == $account->uid) {
                return redirect()->route('dashboard');
            }

            // Force logout if different user
            if ($currentUser && $currentUser->uid != $account->uid) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('logout');
            }

            // Update login time
            DB::table('users')
                ->where('uid', $account->uid)
                ->update(['login' => now()]);

            // Log the login
            Log::info('Session opened via HRMS', ['name' => $account->name]);

            // Create session for the user (matching LoginController pattern)
            $user = [
                'uid' => $account->uid,
                'name' => $account->name,
                'email' => $account->email ?? null,
            ];
            
            $request->session()->put('user', $user);
            $request->session()->put('api_token', null);

            // Regenerate session ID
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

        // Check if user exists, create if not
        $account = DB::table('users')->where('name', $hrmsId)->first();
        
        if (empty($account)) {
            $mail = $hrmsId . '@gmail.com';
            
            $userData = [
                'name' => $hrmsId,
                'password' => \Illuminate\Support\Facades\Hash::make($hrmsId),
                'password_old' => \Illuminate\Support\Facades\Hash::make($hrmsId),
                'email' => $mail,
                'status' => 1,
                'new_pass_set' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $uid = DB::table('users')->insertGetId($userData, 'uid');
            
            // Assign Applicant role (role ID 4)
            DB::table('users_roles')->insert([
                'uid' => $uid,
                'rid' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Generate SSO token
        $hrmscode = UrlEncryptionHelper::encryptUrl($hrmsId);
        $timestamp = time();
        $message = $hrmscode . "|" . $timestamp;
        $hmacSecret = config('services.hrms.hmac_secret_me', '1Po/Pt8oRnNzy9QZ7NZJjA==');
        $hmac = hash_hmac("sha256", $message, $hmacSecret);
        $token = base64_encode($message . "|" . $hmac);

        return redirect()->route('user-sso.hrms-sso', ['token' => urlencode($token)]);
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

    /**
     * Validate SSO Token
     */
    private function validateSsoToken($token, $maxAge = 120)
    {
        if (empty($token)) {
            return ['valid' => false, 'error' => 'Error: No SSO token provided'];
        }

        $decoded = base64_decode($token);
        if (!$decoded || substr_count($decoded, '|') !== 2) {
            return ['valid' => false, 'error' => 'Error: Invalid token format'];
        }

        list($code, $timestamp, $receivedHmac) = explode("|", $decoded);

        // Compute expected HMAC
        $hmacSecret = config('services.hrms.hmac_secret_me', '1Po/Pt8oRnNzy9QZ7NZJjA==');
        $expectedHmac = hash_hmac("sha256", $code . "|" . $timestamp, $hmacSecret);

        if (!hash_equals($expectedHmac, $receivedHmac)) {
            return ['valid' => false, 'error' => 'Error: Invalid Token'];
        }

        // Check timestamp validity
        if (abs(time() - (int)$timestamp) > $maxAge) {
            return ['valid' => false, 'error' => 'Error: Request Token Expired'];
        }

        // Decrypt the code
        $decryptedCode = UrlEncryptionHelper::decryptUrl($code);

        return [
            'valid' => true,
            'code' => $decryptedCode,
            'timestamp' => (int)$timestamp
        ];
    }
}

