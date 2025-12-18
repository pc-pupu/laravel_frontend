<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

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
            $appIdResponse = $httpClient->get($this->backend . '/api/view-allotment-details', [
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
            $documentsResponse = $httpClient->get($this->backend . '/api/view-allotment-details/documents', [
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
    public function updateStatus($encryptedAppId, $encryptedStatus)
    {
        try {
            $appId = UrlEncryptionHelper::decrypt($encryptedAppId);
            $status = UrlEncryptionHelper::decrypt($encryptedStatus);

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
                ->post($this->backend . '/api/view-allotment-details/update-status', [
                    'online_application_id' => $appId,
                    'status' => $status
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if ($status == 'Accept' && isset($responseData['redirect'])) {
                    // Redirect to declaration page
                    return redirect($responseData['redirect'])
                        ->with('success', $responseData['message'] ?? 'You have accepted the allotment.');
                } else {
                    return redirect()->route('view-allotment-details.index')
                        ->with('success', $responseData['message'] ?? ucfirst($status) . ' completed successfully.');
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
}

