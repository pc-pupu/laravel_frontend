<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class GenerateLicenseController extends Controller
{
    protected $backend;

    public function __construct()
    {
        $this->backend = config('services.api.base_url');
    }

    /**
     * Show generate license page (list of applications)
     * GET /generate-license
     */
    public function index(Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $uid = $user['uid'] ?? null;

            if (!$uid) {
                return redirect()->route('homepage')->with('error', 'Please login to view generate license page.');
            }

            $token = $request->session()->get('api_token');
            
            if (!$token) {
                return redirect()->route('homepage')
                    ->with('error', 'Authentication required.');
            }

            // Get list of applications ready for license generation
            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/generate-license/list');

            if (!$response->successful()) {
                return redirect()->route('dashboard')
                    ->with('error', 'Failed to load applications.');
            }

            $applications = $response->json('data') ?? [];

            return view('housingTheme.generate-license.index', [
                'applications' => $applications
            ]);

        } catch (\Exception $e) {
            Log::error('Generate License Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load generate license page.');
        }
    }

    /**
     * Generate license for an application
     * POST /generate-license/{encrypted_app_id}
     */
    public function generate($encryptedAppId, Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $uid = $user['uid'] ?? null;

            if (!$uid) {
                return redirect()->route('homepage')->with('error', 'Please login.');
            }

            // Decrypt application ID
            $appId = UrlEncryptionHelper::decryptUrl($encryptedAppId, false);

            $token = $request->session()->get('api_token');
            
            if (!$token) {
                return redirect()->route('generate-license.index')
                    ->with('error', 'Authentication required.');
            }

            // Generate license
            $response = Http::withToken($token)
                ->acceptJson()
                ->post($this->backend . '/generate-license', [
                    'online_application_id' => $appId,
                    'uid' => $uid
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                return redirect()->route('generate-license.index')
                    ->with('success', $responseData['message'] ?? 'License generated successfully.');
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['message'] ?? 'Failed to generate license.';
                
                Log::error('Generate License Failed', [
                    'status' => $response->status(),
                    'response' => $errorData,
                    'app_id' => $appId
                ]);
                
                return redirect()->route('generate-license.index')
                    ->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('Generate License Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('generate-license.index')
                ->with('error', 'An error occurred while generating license.');
        }
    }
}
