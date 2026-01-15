<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;
use Illuminate\Support\Facades\Crypt;

class ViewAllotmentDetailsController extends Controller
{
    protected $backend;

    public function __construct()
    {
        $this->backend = config('services.api.base_url');
    }

    /**
     * Display allotment details page
     */
    public function index(Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $uid = $user['uid'] ?? null;

            if (!$uid) {
                return redirect()->route('homepage')->with('error', 'Please login to view allotment details.');
            }

            $token = $request->session()->get('api_token');
            
            $httpClient = Http::acceptJson();
            if ($token) {
                $httpClient = $httpClient->withToken($token);
            }

            // Get online application ID for the user
            $appIdResponse = $httpClient->get($this->backend . '/view-allotment-details', [
                'uid' => $uid
            ]);

            if (!$appIdResponse->successful()) {
                return view('housingTheme.view-allotment-details.index', [
                    'allotment' => null,
                    'documents' => null,
                    'canAcceptReject' => false
                ]);
            }

            $allotment = $appIdResponse->json('data');

            // Get uploaded documents
            $documentsResponse = $httpClient->get($this->backend . '/view-allotment-details/documents', [
                'uid' => $uid,
                'online_application_id' => $allotment['online_application_id'] ?? null
            ]);
            $documents = $documentsResponse->successful() ? $documentsResponse->json('data') : null;

            // Check if can accept/reject
            $canAcceptReject = false;
            if ($allotment) {
                $currentDate = date('Y-m-d');
                $allotmentDate = $allotment['allotment_approve_or_reject_date'] ?? null;
                
                if ($allotmentDate) {
                    $finalDate = date("Y-m-d", strtotime("+15 days", strtotime($allotmentDate)));
                    $canAcceptReject = ($currentDate <= $finalDate) && 
                                       ($allotment['accept_reject_status'] != 'Cancel') &&
                                       ($allotment['accept_reject_status'] == null || $allotment['status'] == 'offer_letter_extended');
                }
            }

            return view('housingTheme.view-allotment-details.index', [
                'allotment' => $allotment,
                'documents' => $documents,
                'canAcceptReject' => $canAcceptReject
            ]);

        } catch (\Exception $e) {
            Log::error('View Allotment Details Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')->with('error', 'Failed to load allotment details.');
        }
    }

    /**
     * Update status (Accept/Reject)
     */
    public function updateStatus($encryptedAppId, $encryptedStatus, Request $request)
    {
        // Get raw URL-encoded parameters from REQUEST_URI before Laravel decodes them
        // This is necessary because Laravel automatically decodes %2B to +, which breaks base64 decoding
        $requestUri = $request->server('REQUEST_URI');
        
        // Extract raw path segments directly from REQUEST_URI using regex
        // Pattern: /status_update/([^/]+)/([^/?]+)
        if (preg_match('#/status_update/([^/]+)/([^/?]+)#', $requestUri, $matches)) {
            // Get raw URL-encoded values (matches[1] and matches[2] are still URL-encoded)
            $rawEncryptedAppId = $matches[1];
            $rawEncryptedStatus = $matches[2];
            
            // Use raw URL-encoded values for decryption
            $appId = UrlEncryptionHelper::decryptUrl($rawEncryptedAppId, true);
            $status = UrlEncryptionHelper::decryptUrl($rawEncryptedStatus, true);
        } else {
            // Fallback: if we can't extract from URI, handle Laravel's decoded parameters
            $appId = UrlEncryptionHelper::decryptUrl($encryptedAppId, false);
            $status = UrlEncryptionHelper::decryptUrl($encryptedStatus, false);
        }
        
        
        try {

            if (!in_array($status, ['Accept', 'Reject'])) {
                return redirect()->route('view-allotment-details.index')
                    ->with('error', 'Invalid status.');
            }

            $token = request()->session()->get('api_token');
            
            if (!$token) {
                return redirect()->route('view-allotment-details.index')
                    ->with('error', 'Authentication required.');
            }

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($this->backend . '/view-allotment-details/update-status', [
                    'online_application_id' => $appId,
                    'status' => $status
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if ($status == 'Accept') {
                    // Redirect to declaration page (matching Drupal behavior)
                    $encryptedAppId = UrlEncryptionHelper::encryptUrl($appId);
                    return redirect()->route('view-allotment-details.declaration', ['encrypted_app_id' => $encryptedAppId])
                        ->with('success', $responseData['message'] ?? 'You have accepted the allotment. Please accept this Declaration to finalize your acceptance.');
                } else {
                    // For Reject, redirect back to view allotment details
                    return redirect()->route('view-allotment-details.index')
                        ->with('success', $responseData['message'] ?? 'You rejected the allotment.');
                }
            } else {
                $errorMessage = $response->json('message') ?? 'Failed to update status.';
                return redirect()->route('view-allotment-details.index')
                    ->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('View Allotment Details Update Status Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('view-allotment-details.index')
                ->with('error', 'An error occurred while processing the request.');
        }
    }

    /**
     * Show declaration page (download-and-upload)
     * GET /download-and-upload/{encrypted_app_id}
     */
    public function declaration($encryptedAppId, Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $uid = $user['uid'] ?? null;

            if (!$uid) {
                return redirect()->route('homepage')->with('error', 'Please login to view declaration.');
            }

            // Decrypt application ID
            $appId = UrlEncryptionHelper::decryptUrl($encryptedAppId, false);

            $token = $request->session()->get('api_token');
            
            if (!$token) {
                return redirect()->route('view-allotment-details.index')
                    ->with('error', 'Authentication required.');
            }

            // Get HRMS user data for declaration
            $hrmsResponse = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/dashboard', [
                    'uid' => $uid,
                    'username' => $user['name'] ?? ''
                ]);

            $hrmsData = $hrmsResponse->successful() ? $hrmsResponse->json('user_info') : null;

            return view('housingTheme.view-allotment-details.declaration', [
                'encrypted_app_id' => $encryptedAppId,
                'app_id' => $appId,
                'hrms_data' => $hrmsData,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('View Allotment Details Declaration Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('view-allotment-details.index')
                ->with('error', 'Failed to load declaration page.');
        }
    }

    /**
     * Submit declaration acceptance
     * POST /download-and-upload/{encrypted_app_id}
     */
    public function submitDeclaration($encryptedAppId, Request $request)
    {
        try {
            $request->validate([
                'accept_declaration' => 'required|accepted'
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

            // Get HRMS user data for declaration content
            $hrmsResponse = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/dashboard', [
                    'uid' => $uid,
                    'username' => $user['name'] ?? ''
                ]);

            $hrmsData = $hrmsResponse->successful() ? $hrmsResponse->json('user_info') : null;

            // Prepare declaration content
            $declarationContent = [
                'applicantName' => $hrmsData['applicantName'] ?? '',
                'guardianName' => $hrmsData['guardianName'] ?? '',
                'permanentStreet' => $hrmsData['permanentStreet'] ?? '',
                'permanentCityTownVillage' => $hrmsData['permanentCityTownVillage'] ?? '',
                'permanentPostOffice' => $hrmsData['permanentPostOffice'] ?? '',
                'permanentDistrictCode' => $hrmsData['permanentDistrictCode'] ?? '',
                'permanentPincode' => $hrmsData['permanentPincode'] ?? '',
                'applicantDesignation' => $hrmsData['applicantDesignation'] ?? '',
            ];

            // Submit declaration to backend
            $response = Http::withToken($token)
                ->acceptJson()
                ->post($this->backend . '/view-allotment-details/submit-declaration', [
                    'online_application_id' => $appId,
                    'declaration_content' => serialize($declarationContent),
                    'uid' => $uid
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                // After declaration acceptance, redirect to ddo-change (matching Drupal)
                // For now, redirect back to view allotment details until ddo-change is implemented
                $encryptedAppIdForRedirect = UrlEncryptionHelper::encryptUrl($appId);
                // TODO: Implement ddo-change route when available
                // return redirect()->route('view-allotment-details.ddo-change', ['encrypted_app_id' => $encryptedAppIdForRedirect])
                return redirect()->route('view-allotment-details.index')
                    ->with('success', $responseData['message'] ?? 'Declaration accepted successfully.');
            } else {
                $errorMessage = $response->json('message') ?? 'Failed to submit declaration.';
                return redirect()->route('view-allotment-details.declaration', ['encrypted_app_id' => $encryptedAppId])
                    ->with('error', $errorMessage);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('view-allotment-details.declaration', ['encrypted_app_id' => $encryptedAppId])
                ->withErrors(['accept_declaration' => 'Please accept the terms and conditions stated in the Declaration before Competent Authority.'])
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Submit Declaration Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('view-allotment-details.declaration', ['encrypted_app_id' => $encryptedAppId])
                ->with('error', 'An error occurred while submitting declaration.');
        }
    }
}

