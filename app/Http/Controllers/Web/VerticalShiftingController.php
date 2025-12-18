<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class VerticalShiftingController extends Controller
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
        $http = Http::acceptJson();
        if ($token) {
            $http = $http->withToken($token);
        }
        return $http;
    }

    /**
     * Show the Vertical Shifting (VS) application form
     * GET /vs
     */
    public function create(Request $request)
    {
        // Check if user is logged in
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];

        try {
            // Step 1: Check for draft application
            $draftResponse = $this->authorizedRequest()
                ->get($this->backend . '/api/vertical-shifting/check-draft', ['uid' => $uid]);

            if ($draftResponse->successful()) {
                $draftData = $draftResponse->json();
                if (isset($draftData['has_draft']) && $draftData['has_draft'] && isset($draftData['online_application_id'])) {
                    // Redirect to view application if draft exists and not rejected
                    $encryptedId = UrlEncryptionHelper::encryptUrl($draftData['online_application_id']);
                    return redirect()->route('application.view', ['id' => $encryptedId]);
                }
            }

            // Step 2: Get current occupation details
            $occupationResponse = $this->authorizedRequest()
                ->get($this->backend . '/api/vertical-shifting/get-current-occupation', ['uid' => $uid]);

            if (!$occupationResponse->successful()) {
                $errorMsg = $occupationResponse->json('message') ?? 'Failed to load current occupation details.';
                return redirect()->route('dashboard')->with('error', $errorMsg);
            }

            $occupation = $occupationResponse->json('data');

            // Step 3: Get application data if exists
            $appDataResponse = $this->authorizedRequest()
                ->get($this->backend . '/api/vertical-shifting/get-application-data', ['uid' => $uid]);

            $appData = null;
            if ($appDataResponse->successful()) {
                $appData = $appDataResponse->json('data');
            }

            // Step 4: Fetch common application data (districts, pay bands, etc.)
            $districts = $this->getDistricts();
            $payBands = $this->getPayBands();

            // Step 5: Get DDO designations and address (will be populated via AJAX based on district)
            $ddoDesignations = [];
            $ddoAddress = '';

            // Step 6: Get existing applicant official info for pre-filling
            $officialInfo = $this->getApplicantOfficialInfo($uid);
            if ($officialInfo && isset($officialInfo['district_code'])) {
                $ddoDesignations = $this->getDdoDesignations($officialInfo['district_code']);
                $ddoAddress = $this->getDdoAddress($officialInfo['district_code'], $officialInfo['ddo_designation_id'] ?? null);
            }

            // Step 7: Get blocks and flats for current estate
            $blocks = $this->getBlocks($occupation['estate_id']);
            $flats = $this->getFlats($occupation['estate_id'], $occupation['block_id']);

            return view('housingTheme.vertical-shifting.form', [
                'districts' => $districts,
                'payBands' => $payBands,
                'ddoDesignations' => $ddoDesignations,
                'ddoAddress' => $ddoAddress,
                'occupation' => $occupation,
                'appData' => $appData,
                'officialInfo' => $officialInfo,
                'blocks' => $blocks,
                'flats' => $flats,
                'uid' => $uid,
            ]);

        } catch (\Exception $e) {
            Log::error('Vertical Shifting Create Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load application form.');
        }
    }

    /**
     * Store the Vertical Shifting application
     * POST /vs
     */
    public function store(Request $request)
    {
        // Check if user is logged in
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];

        try {
            // Determine action (draft or applied)
            $action = $request->input('action', 'draft');
            if ($request->has('apply')) {
                $action = 'applied';
            }

            // Prepare form data
            $formData = $request->all();
            $formData['uid'] = $uid;
            $formData['action'] = $action;

            // Create multipart form data for file uploads
            $response = $this->authorizedRequest();

            if ($request->hasFile('file_licence')) {
                $response = $response->attach('file_licence', 
                    file_get_contents($request->file('file_licence')->getRealPath()), 
                    $request->file('file_licence')->getClientOriginalName());
            }

            if ($request->hasFile('vs_scanned_sign')) {
                $response = $response->attach('vs_scanned_sign', 
                    file_get_contents($request->file('vs_scanned_sign')->getRealPath()), 
                    $request->file('vs_scanned_sign')->getClientOriginalName());
            }

            $response = $response->post($this->backend . '/api/vertical-shifting/store', $formData);

            if (!$response->successful()) {
                $errors = $response->json('errors') ?? [];
                $message = $response->json('message') ?? 'Failed to submit application.';

                return back()
                    ->withInput()
                    ->withErrors($errors)
                    ->with('error', $message);
            }

            $data = $response->json('data');
            $onlineApplicationId = $data['online_application_id'] ?? null;
            $message = $response->json('message') ?? ($action == 'draft' ? 'Application saved as draft.' : 'You have successfully applied.');

            if ($onlineApplicationId) {
                // Encrypt the application ID for URL
                $encryptedId = UrlEncryptionHelper::encryptUrl($onlineApplicationId);
                return redirect()->route('application.view', ['id' => $encryptedId])
                    ->with('success', $message);
            }

            return redirect()->route('dashboard')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Vertical Shifting Store Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'An error occurred while submitting the application.');
        }
    }

    /**
     * Get districts list
     */
    private function getDistricts()
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/existing-applicants-helpers/districts');

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch districts', ['error' => $e->getMessage()]);
        }
        return [];
    }

    /**
     * Get pay bands list
     */
    private function getPayBands()
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/existing-applicants-helpers/pay-bands');

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch pay bands', ['error' => $e->getMessage()]);
        }
        return [];
    }

    /**
     * Get DDO designations by district
     */
    private function getDdoDesignations($districtCode)
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/common-application/ddo-designations', ['district_code' => $districtCode]);

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch DDO designations', ['error' => $e->getMessage()]);
        }
        return [];
    }

    /**
     * Get DDO address
     */
    private function getDdoAddress($districtCode, $ddoDesignationId)
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/common-application/ddo-address', [
                    'district_code' => $districtCode,
                    'ddo_designation_id' => $ddoDesignationId,
                ]);

            if ($response->successful()) {
                return $response->json('data.address') ?? '';
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch DDO address', ['error' => $e->getMessage()]);
        }
        return '';
    }

    /**
     * Get applicant official info
     */
    private function getApplicantOfficialInfo($uid)
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/existing-applicants-helpers/applicant-official-info', ['uid' => $uid]);

            if ($response->successful()) {
                return $response->json('data') ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch applicant official info', ['error' => $e->json('message')]);
        }
        return null;
    }

    /**
     * Get blocks by estate
     */
    private function getBlocks($estateId)
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/user-tagging/helpers/blocks/' . $estateId . '/0');

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch blocks', ['error' => $e->getMessage()]);
        }
        return [];
    }

    /**
     * Get flats by estate and block
     */
    private function getFlats($estateId, $blockId)
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/user-tagging/helpers/flats/' . $estateId . '/0/' . $blockId . '/0');

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch flats', ['error' => $e->getMessage()]);
        }
        return [];
    }
}

