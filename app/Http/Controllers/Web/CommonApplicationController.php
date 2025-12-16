<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CommonApplicationController extends Controller
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
     * Show the common application form
     * GET /new-apply (or other routes that use this form)
     */
    public function create(Request $request, $appType = 'NA')
    {
        // Check if user is logged in
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];

        try {
            // Fetch dropdown data
            $districts = $this->getDistricts();
            $payBands = $this->getPayBands();
            
            // Fetch existing applicant data if available
            $personalInfo = $this->getApplicantPersonalInfo($uid);
            $officialInfo = $this->getApplicantOfficialInfo($uid);

            // Get DDO designations if district is selected
            $ddoDesignations = [];
            if ($officialInfo && isset($officialInfo['district_code'])) {
                $ddoDesignations = $this->getDdoDesignations($officialInfo['district_code']);
            }

            return view('housingTheme.common-application.form', [
                'appType' => $appType,
                'districts' => $districts,
                'payBands' => $payBands,
                'ddoDesignations' => $ddoDesignations,
                'personalInfo' => $personalInfo,
                'officialInfo' => $officialInfo,
                'uid' => $uid,
            ]);

        } catch (\Exception $e) {
            Log::error('Common Application Create Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load application form.');
        }
    }

    /**
     * Store the common application form
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
            $formData = $request->all();
            $formData['uid'] = $uid;
            $formData['app_type'] = $request->input('app_type', 'NA');

            $response = $this->authorizedRequest()
                ->post($this->backend . '/api/common-application', $formData);

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

            if ($onlineApplicationId) {
                // Encrypt the application ID for URL
                $encryptedId = \App\Helpers\UrlEncryptionHelper::encryptUrl($onlineApplicationId);
                return redirect()->route('application.view', ['id' => $encryptedId])
                    ->with('success', 'Application submitted successfully.');
            }

            return redirect()->route('dashboard')
                ->with('success', 'Application submitted successfully.');

        } catch (\Exception $e) {
            Log::error('Common Application Store Error', [
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

        return ['' => '- Select -'];
    }

    /**
     * Get pay bands list
     */
    private function getPayBands()
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/existing-applicants-helpers/pay-bands', [
                    'type' => 'new',
                ]);

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch pay bands', ['error' => $e->getMessage()]);
        }

        return ['' => '- Select -'];
    }

    /**
     * Get DDO designations
     */
    private function getDdoDesignations($districtCode)
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/existing-applicants-helpers/ddo-designations', [
                    'district_code' => $districtCode,
                ]);

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch DDO designations', ['error' => $e->getMessage()]);
        }

        return ['' => '- Select -'];
    }

    /**
     * Get applicant personal info
     */
    private function getApplicantPersonalInfo($uid)
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/common-application/personal-info', [
                    'uid' => $uid,
                ]);

            if ($response->successful()) {
                return $response->json('data');
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch personal info', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Get applicant official info
     */
    private function getApplicantOfficialInfo($uid, $onlineApplicationId = null)
    {
        try {
            $params = ['uid' => $uid];
            if ($onlineApplicationId) {
                $params['online_application_id'] = $onlineApplicationId;
            }

            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/common-application/official-info', $params);

            if ($response->successful()) {
                return $response->json('data');
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch official info', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * AJAX endpoint to get DDO designations
     */
    public function getDdoDesignationsAjax(Request $request)
    {
        $districtCode = $request->input('district_code');

        if (!$districtCode) {
            return response()->json([
                'status' => 'error',
                'message' => 'District code is required',
            ], 422);
        }

        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/existing-applicants-helpers/ddo-designations', [
                    'district_code' => $districtCode,
                ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $response->json('data') ?? [],
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch DDO designations',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to fetch DDO designations (AJAX)', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * AJAX endpoint to get DDO address
     */
    public function getDdoAddressAjax(Request $request)
    {
        $ddoId = $request->input('ddo_id');

        if (!$ddoId) {
            return response()->json([
                'status' => 'success',
                'data' => '',
            ]);
        }

        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/existing-applicants-helpers/ddo-address', [
                    'ddo_id' => $ddoId,
                ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $response->json('data') ?? '',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch DDO address',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to fetch DDO address (AJAX)', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 500);
        }
    }
}

