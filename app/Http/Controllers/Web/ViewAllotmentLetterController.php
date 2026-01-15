<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class ViewAllotmentLetterController extends Controller
{
    protected $backend;

    public function __construct()
    {
        $this->backend = config('services.api.base_url');
    }

    /**
     * Display view proposed RHE page
     */
    public function index(Request $request)
    {
        try {
            $token = $request->session()->get('api_token');
            
            $httpClient = Http::acceptJson();
            if ($token) {
                $httpClient = $httpClient->withToken($token);
            }

            // Get flat types
            $flatTypesResponse = $httpClient->get($this->backend . '/api/view-allotment-letter/flat-types');
            $flatTypes = $flatTypesResponse->successful() ? $flatTypesResponse->json('data') : [];

            $flatTypeId = $request->get('flat_type_id');
            $allotmentLetters = [];

            if ($flatTypeId) {
                // Get allotment letter list
                $listResponse = $httpClient->get($this->backend . '/api/view-allotment-letter/list', [
                    'flat_type_id' => $flatTypeId
                ]);
                $allotmentLetters = $listResponse->successful() ? $listResponse->json('data') : [];
            }

            return view('housingTheme.view-allotment-letter.index', [
                'flatTypes' => $flatTypes,
                'flatTypeId' => $flatTypeId,
                'allotmentLetters' => $allotmentLetters
            ]);

        } catch (\Exception $e) {
            Log::error('View Allotment Letter Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')->with('error', 'Failed to load view proposed RHE page.');
        }
    }

    /**
     * Allot or cancel applicant
     */
    public function updateAllotment($encryptedAppId, $encryptedStatus)
    {
        try {
            $appId = UrlEncryptionHelper::decryptUrl($encryptedAppId);
            $status = UrlEncryptionHelper::decryptUrl($encryptedStatus);

            if (!in_array($status, ['allot', 'cancel'])) {
                return redirect()->route('view-allotment-letter.index')
                    ->with('error', 'Invalid action.');
            }

            $token = request()->session()->get('api_token');
            
            if (!$token) {
                return redirect()->route('view-allotment-letter.index')
                    ->with('error', 'Authentication required.');
            }

            $endpoint = $status == 'allot' 
                ? $this->backend . '/api/view-allotment-letter/allot'
                : $this->backend . '/api/view-allotment-letter/cancel';

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($endpoint, [
                    'online_application_id' => $appId
                ]);

            if ($response->successful()) {
                $message = $response->json('message') ?? ucfirst($status) . ' completed successfully.';
                return redirect()->route('view-allotment-letter.index')
                    ->with('success', $message);
            } else {
                $errorMessage = $response->json('message') ?? 'Failed to ' . $status . ' allotment.';
                return redirect()->route('view-allotment-letter.index')
                    ->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('View Allotment Letter Update Allotment Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('view-allotment-letter.index')
                ->with('error', 'An error occurred while processing the request.');
        }
    }
}

