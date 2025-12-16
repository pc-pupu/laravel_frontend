<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class ApplicationStatusController extends Controller
{
    private string $backend;

    public function __construct()
    {
        $this->backend = rtrim(env('BACKEND_API'), '/');
    }

    /**
     * Get authorized request with token
     */
    private function authorizedRequest()
    {
        $token = session('api_token');
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ]);
    }

    /**
     * Show application status check form
     * GET /application_status
     */
    public function index(Request $request)
    {
        $applicationNo = $request->input('application_no');
        $statusHistory = [];

        if ($applicationNo) {
            try {
                $user = $request->session()->get('user');
                $uid = $user['uid'] ?? null;

                $response = $this->authorizedRequest()
                    ->get($this->backend . '/api/application-status/' . urlencode($applicationNo), [
                        'uid' => $uid,
                    ]);

                if ($response->successful()) {
                    $statusHistory = $response->json('data') ?? [];
                }
            } catch (\Exception $e) {
                Log::error('Application Status Error', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('housingTheme.application-status.index', [
            'applicationNo' => $applicationNo,
            'statusHistory' => $statusHistory,
        ]);
    }

    /**
     * Show application status check form (for officials)
     * GET /application_status_check
     */
    public function checkIndex(Request $request)
    {
        return view('housingTheme.application-status-check.index');
    }

    /**
     * Search application (for officials)
     * POST /application_status_check
     */
    public function checkSearch(Request $request)
    {
        $validator = $request->validate([
            'select_button' => 'required|in:1,2',
            'application_or_hrms_no' => 'required|string',
        ]);

        try {
            $response = $this->authorizedRequest()
                ->post($this->backend . '/api/application-status-check/search', [
                    'search_type' => $request->input('select_button'),
                    'search_value' => trim($request->input('application_or_hrms_no')),
                ]);

            if (!$response->successful()) {
                $message = $response->json('message') ?? 'Invalid search. Please check your input.';
                return redirect()->back()
                    ->with('error', $message)
                    ->withInput();
            }

            $data = $response->json('data');
            $application = $data['application'] ?? $data; // Handle both formats
            $encryptedId = UrlEncryptionHelper::encryptUrl($application['online_application_id']);
            $encryptedStatus = UrlEncryptionHelper::encryptUrl($application['status']);

            return redirect()->route('application-status-check.view', [
                'id' => $encryptedId,
                'status' => $encryptedStatus,
            ]);

        } catch (\Exception $e) {
            Log::error('Application Status Check Search Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to search application.')
                ->withInput();
        }
    }

    /**
     * View application list (search results)
     * GET /common-application-view/{id}/{status}
     */
    public function viewList(Request $request, $id, $status)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $userRole = $user['role'] ?? null;

        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($id);
            $decryptedStatus = UrlEncryptionHelper::decryptUrl($status);

            // First get the search result data
            $searchResponse = $this->authorizedRequest()
                ->get($this->backend . '/api/application-status-check/' . $applicationId);

            if (!$searchResponse->successful() || !$searchResponse->json('data')) {
                return redirect()->route('application-status-check.index')
                    ->with('error', 'Application not found.');
            }

            $data = $searchResponse->json('data');
            $application = $data['application'] ?? [];
            
            // Get license and possession data from the search result
            $licenseNo = $application['license_no'] ?? '';
            $possessionDate = $application['possession_date'] ?? '';
            $releaseDate = $application['release_date'] ?? '';

            return view('housingTheme.application-status-check.view-list', [
                'application' => $application,
                'id' => $id,
                'status' => $status,
                'userRole' => $userRole,
                'licenseNo' => $licenseNo,
                'possessionDate' => $possessionDate,
                'releaseDate' => $releaseDate,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            Log::error('View Application List Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('application-status-check.index')
                ->with('error', 'Failed to load application details.');
        }
    }

    /**
     * View application detail
     * GET /common-application-view-det/{id}/{status}
     */
    public function viewDetail(Request $request, $id, $status)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($id);
            $decryptedStatus = UrlEncryptionHelper::decryptUrl($status);

            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/application-status-check/' . $applicationId);

            if (!$response->successful() || !$response->json('data')) {
                return redirect()->back()
                    ->with('error', 'Application not found.');
            }

            $data = $response->json('data');
            $application = $data['application'] ?? [];
            $estatePreferences = $data['estate_preferences'] ?? [];
            $allotmentDetails = $data['allotment_details'] ?? null;

            return view('housingTheme.application-status-check.view-detail', [
                'application' => $application,
                'estatePreferences' => $estatePreferences,
                'allotmentDetails' => $allotmentDetails,
                'id' => $id,
                'status' => $status,
            ]);

        } catch (\Exception $e) {
            Log::error('View Application Detail Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to load application details.');
        }
    }

    /**
     * Show add possession date form
     * GET /add-possession-det/{id}/{status}
     */
    public function showAddPossessionForm(Request $request, $id, $status)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        return view('housingTheme.application-status-check.add-possession', [
            'id' => $id,
            'status' => $status,
        ]);
    }

    /**
     * Store possession date
     * POST /add-possession-det/{id}/{status}
     */
    public function storePossessionDate(Request $request, $id, $status)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $validator = $request->validate([
            'add_possession_date' => 'required|date_format:d/m/Y',
        ]);

        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($id);
            $user = $request->session()->get('user');
            $uid = $user['uid'] ?? null;

            $response = $this->authorizedRequest()
                ->post($this->backend . '/api/application-status-check/' . $applicationId . '/add-possession', [
                    'possession_date' => $request->input('add_possession_date'),
                    'uid' => $uid,
                ]);

            if (!$response->successful()) {
                $message = $response->json('message') ?? 'Failed to add possession date.';
                return redirect()->back()
                    ->with('error', $message)
                    ->withInput();
            }

            return redirect()->route('application-status-check.view-list', [
                'id' => $id,
                'status' => $status,
            ])->with('success', $response->json('message') ?? 'Possession Date Added Successfully.');

        } catch (\Exception $e) {
            Log::error('Store Possession Date Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to add possession date.')
                ->withInput();
        }
    }

    /**
     * Show add release date form
     * GET /add-release-date/{id}/{status}
     */
    public function showAddReleaseForm(Request $request, $id, $status)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        return view('housingTheme.application-status-check.add-release', [
            'id' => $id,
            'status' => $status,
        ]);
    }

    /**
     * Store release date
     * POST /add-release-date/{id}/{status}
     */
    public function storeReleaseDate(Request $request, $id, $status)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $validator = $request->validate([
            'add_release_date' => 'required|date_format:d/m/Y',
        ]);

        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($id);
            $user = $request->session()->get('user');
            $uid = $user['uid'] ?? null;

            $response = $this->authorizedRequest()
                ->post($this->backend . '/api/application-status-check/' . $applicationId . '/add-release-date', [
                    'release_date' => $request->input('add_release_date'),
                    'uid' => $uid,
                ]);

            if (!$response->successful()) {
                $message = $response->json('message') ?? 'Failed to add release date.';
                return redirect()->back()
                    ->with('error', $message)
                    ->withInput();
            }

            return redirect()->route('application-status-check.view-list', [
                'id' => $id,
                'status' => $status,
            ])->with('success', $response->json('message') ?? 'Release Date Added Successfully.');

        } catch (\Exception $e) {
            Log::error('Store Release Date Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to add release date.')
                ->withInput();
        }
    }

    /**
     * Show request license extension form
     * GET /request-for-license-extension/{id}/{status}/{uid}/{official_detail_id}
     */
    public function showLicenseExtensionForm(Request $request, $id, $status, $uid, $officialDetailId)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        return view('housingTheme.application-status-check.license-extension', [
            'id' => $id,
            'status' => $status,
            'uid' => $uid,
            'officialDetailId' => $officialDetailId,
        ]);
    }

    /**
     * Store license extension request
     * POST /request-for-license-extension/{id}/{status}/{uid}/{official_detail_id}
     */
    public function storeLicenseExtension(Request $request, $id, $status, $uid, $officialDetailId)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $validator = $request->validate([
            'license_extension_reason_dropdown' => 'required|in:Reason_1,Reason_2',
            'add_license_extension_date' => 'required|date_format:d/m/Y',
            'license_extension_reason_file' => 'required|file|mimes:pdf|max:2048',
        ]);

        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($id);
            $decryptedUid = UrlEncryptionHelper::decryptUrl($uid);
            $decryptedOfficialDetailId = UrlEncryptionHelper::decryptUrl($officialDetailId);
            $user = $request->session()->get('user');
            $departmentalUid = $user['uid'] ?? null;

            // Create multipart form data
            $multipart = [
                [
                    'name' => 'extension_reason',
                    'contents' => $request->input('license_extension_reason_dropdown'),
                ],
                [
                    'name' => 'extension_date',
                    'contents' => $request->input('add_license_extension_date'),
                ],
                [
                    'name' => 'uid',
                    'contents' => $decryptedUid,
                ],
                [
                    'name' => 'official_detail_id',
                    'contents' => $decryptedOfficialDetailId,
                ],
                [
                    'name' => 'departmental_uid',
                    'contents' => $departmentalUid,
                ],
                [
                    'name' => 'document',
                    'contents' => fopen($request->file('license_extension_reason_file')->getRealPath(), 'r'),
                    'filename' => $request->file('license_extension_reason_file')->getClientOriginalName(),
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . session('api_token'),
                'Accept' => 'application/json',
            ])->asMultipart()->post($this->backend . '/api/application-status-check/' . $applicationId . '/request-license-extension', $multipart);

            if (!$response->successful()) {
                $message = $response->json('message') ?? 'Failed to request license extension.';
                return redirect()->back()
                    ->with('error', $message)
                    ->withInput();
            }

            return redirect()->route('application-status-check.view-list', [
                'id' => $id,
                'status' => $status,
            ])->with('success', $response->json('message') ?? 'License has been Extended!');

        } catch (\Exception $e) {
            Log::error('Store License Extension Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to request license extension.')
                ->withInput();
        }
    }

    /**
     * Show request offer letter extension form
     * GET /request-for-offer-letter-extension/{id}/{status}/{uid}/{official_detail_id}/{date_of_verified}
     */
    public function showOfferLetterExtensionForm(Request $request, $id, $status, $uid, $officialDetailId, $dateOfVerified)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $decryptedDateOfVerified = UrlEncryptionHelper::decryptUrl($dateOfVerified);
        // Format date for JavaScript datepicker
        $dateOnly = date('Y-m-d', strtotime($decryptedDateOfVerified));

        return view('housingTheme.application-status-check.offer-letter-extension', [
            'id' => $id,
            'status' => $status,
            'uid' => $uid,
            'officialDetailId' => $officialDetailId,
            'dateOfVerified' => $dateOnly,
        ]);
    }

    /**
     * Store offer letter extension request
     * POST /request-for-offer-letter-extension/{id}/{status}/{uid}/{official_detail_id}/{date_of_verified}
     */
    public function storeOfferLetterExtension(Request $request, $id, $status, $uid, $officialDetailId, $dateOfVerified)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $validator = $request->validate([
            'offer_letter_extension_reason_dropdown' => 'required|in:Reason_1,Reason_2',
            'add_offer_letter_extension_date' => 'required|date_format:d/m/Y',
            'offer_letter_extension_reason_file' => 'required|file|mimes:pdf|max:2048',
        ]);

        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($id);
            $decryptedUid = UrlEncryptionHelper::decryptUrl($uid);
            $decryptedOfficialDetailId = UrlEncryptionHelper::decryptUrl($officialDetailId);
            $decryptedDateOfVerified = UrlEncryptionHelper::decryptUrl($dateOfVerified);
            $user = $request->session()->get('user');
            $departmentalUid = $user['uid'] ?? null;

            // Create multipart form data
            $multipart = [
                [
                    'name' => 'extension_reason',
                    'contents' => $request->input('offer_letter_extension_reason_dropdown'),
                ],
                [
                    'name' => 'extension_date',
                    'contents' => $request->input('add_offer_letter_extension_date'),
                ],
                [
                    'name' => 'uid',
                    'contents' => $decryptedUid,
                ],
                [
                    'name' => 'official_detail_id',
                    'contents' => $decryptedOfficialDetailId,
                ],
                [
                    'name' => 'date_of_verified',
                    'contents' => $decryptedDateOfVerified,
                ],
                [
                    'name' => 'departmental_uid',
                    'contents' => $departmentalUid,
                ],
                [
                    'name' => 'document',
                    'contents' => fopen($request->file('offer_letter_extension_reason_file')->getRealPath(), 'r'),
                    'filename' => $request->file('offer_letter_extension_reason_file')->getClientOriginalName(),
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . session('api_token'),
                'Accept' => 'application/json',
            ])->asMultipart()->post($this->backend . '/api/application-status-check/' . $applicationId . '/request-offer-letter-extension', $multipart);

            if (!$response->successful()) {
                $message = $response->json('message') ?? 'Failed to request offer letter extension.';
                return redirect()->back()
                    ->with('error', $message)
                    ->withInput();
            }

            return redirect()->route('application-status-check.view-list', [
                'id' => $id,
                'status' => $status,
            ])->with('success', $response->json('message') ?? 'Offer Letter has been Extended!');

        } catch (\Exception $e) {
            Log::error('Store Offer Letter Extension Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to request offer letter extension.')
                ->withInput();
        }
    }
}

