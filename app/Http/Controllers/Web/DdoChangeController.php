<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class DdoChangeController extends Controller
{
    protected $backend;

    public function __construct()
    {
        $this->backend = config('services.api.base_url');
    }

    /**
     * Show DDO change page
     * GET /ddo-change/{encrypted_app_id}
     */
    public function index($encryptedAppId, Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $uid = $user['uid'] ?? null;
            $username = $user['name'] ?? '';

            if (!$uid) {
                return redirect()->route('homepage')->with('error', 'Please login to view DDO change page.');
            }

            // Decrypt application ID
            $appId = UrlEncryptionHelper::decryptUrl($encryptedAppId, false);

            $token = $request->session()->get('api_token');
            
            if (!$token) {
                return redirect()->route('view-allotment-details.index')
                    ->with('error', 'Authentication required.');
            }

            // Get DDO information
            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/ddo/by-application/' . $appId, [
                    'uid' => $uid,
                    'username' => $username
                ]);

            if (!$response->successful()) {
                return redirect()->route('view-allotment-details.index')
                    ->with('error', 'Failed to load DDO information.');
            }

            $ddoData = $response->json('data');

            return view('housingTheme.ddo-change.index', [
                'encrypted_app_id' => $encryptedAppId,
                'app_id' => $appId,
                'old_ddo' => $ddoData['old_ddo'] ?? null,
                'current_ddo' => $ddoData['current_ddo'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('DDO Change Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('view-allotment-details.index')
                ->with('error', 'Failed to load DDO change page.');
        }
    }

    /**
     * Submit DDO change
     * POST /ddo-change/{encrypted_app_id}
     */
    public function submit($encryptedAppId, Request $request)
    {
        try {
            $request->validate([
                'agree_declaration' => 'required|accepted'
            ]);

            $user = $request->session()->get('user');
            $uid = $user['uid'] ?? null;

            if (!$uid) {
                return redirect()->route('homepage')->with('error', 'Please login.');
            }

            // Decrypt application ID
            $appId = UrlEncryptionHelper::decryptUrl($encryptedAppId, false);

            $token = $request->session()->get('api_token');
            
            if (!$token) {
                return redirect()->route('view-allotment-details.index')
                    ->with('error', 'Authentication required.');
            }

            // Get DDO information first to get the IDs
            $ddoResponse = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/ddo/by-application/' . $appId, [
                    'uid' => $uid,
                    'username' => $user['name'] ?? ''
                ]);

            if (!$ddoResponse->successful()) {
                return redirect()->route('ddo-change.index', ['encrypted_app_id' => $encryptedAppId])
                    ->with('error', 'Failed to load DDO information.');
            }

            $ddoData = $ddoResponse->json('data');
            $oldDdo = $ddoData['old_ddo'] ?? [];
            $currentDdo = $ddoData['current_ddo'] ?? [];

            // Submit DDO change
            $response = Http::withToken($token)
                ->acceptJson()
                ->post($this->backend . '/ddo/update-change', [
                    'online_application_id' => $appId,
                    'old_ddo_id' => $oldDdo['ddo_id'] ?? 0,
                    'current_ddo_id' => $currentDdo['ddo_id'] ?? 0,
                    'applicant_official_detail_id' => $oldDdo['applicant_official_detail_id'] ?? null,
                    'old_ddo_code' => $oldDdo['ddo_code'] ?? '',
                    'current_ddo_code' => $currentDdo['ddo_code'] ?? '',
                    'uid' => $uid,
                    'agree_declaration' => 1
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                return redirect()->route('dashboard')
                    ->with('success', $responseData['message'] ?? 'DDO details updated successfully.');
            } else {
                $errorMessage = $response->json('message') ?? 'Failed to update DDO change.';
                return redirect()->route('ddo-change.index', ['encrypted_app_id' => $encryptedAppId])
                    ->with('error', $errorMessage);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('ddo-change.index', ['encrypted_app_id' => $encryptedAppId])
                ->withErrors(['agree_declaration' => 'Please agree to the declaration to proceed.'])
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Submit DDO Change Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('ddo-change.index', ['encrypted_app_id' => $encryptedAppId])
                ->with('error', 'An error occurred while processing the request.');
        }
    }
}

