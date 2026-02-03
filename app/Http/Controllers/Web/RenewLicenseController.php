<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class RenewLicenseController extends Controller
{
    protected $backend;

    public function __construct()
    {
        $this->backend = config('app.backend_url');
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
     * Show renew license application form
     * GET /renew-license
     */
    public function index(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];

        try {
            // Check for draft application
            $draftResponse = $this->authorizedRequest()
                ->get($this->backend . '/api/renew-license/check-draft', ['uid' => $uid]);

            if ($draftResponse->successful() && $draftResponse->json('has_draft')) {
                $onlineApplicationId = $draftResponse->json('online_application_id');
                $encryptedId = UrlEncryptionHelper::encryptUrl($onlineApplicationId);
                return redirect()->route('application.view', ['id' => $encryptedId]);
            }

            // Get license and allotment details
            $licenseResponse = $this->authorizedRequest()
                ->get($this->backend . '/api/renew-license/license-details', ['uid' => $uid]);

            $licenseData = null;
            $allotmentData = null;
            $onlineApplicationId = 0;
            if ($licenseResponse->successful()) {
                $licenseData = $licenseResponse->json('license_data');
                $allotmentData = $licenseResponse->json('allotment_data');
                $onlineApplicationId = $licenseResponse->json('online_application_id', 0);
            }

            // Get common application data
            $commonAppController = new CommonApplicationController();
            $districts = $commonAppController->getDistricts();
            $payBands = $commonAppController->getPayBands();
            $personalInfo = $commonAppController->getApplicantPersonalInfo($uid);
            $officialInfo = $commonAppController->getApplicantOfficialInfo($uid);
            
            $ddoDesignations = [];
            if ($officialInfo && isset($officialInfo['district_code'])) {
                $ddoDesignations = $commonAppController->getDdoDesignations($officialInfo['district_code']);
            }

            return view('housingTheme.license-application.renew-license', [
                'appType' => 'NL',
                'districts' => $districts,
                'payBands' => $payBands,
                'ddoDesignations' => $ddoDesignations,
                'personalInfo' => $personalInfo,
                'officialInfo' => $officialInfo,
                'licenseData' => $licenseData,
                'allotmentData' => $allotmentData,
                'onlineApplicationId' => $onlineApplicationId,
                'uid' => $uid,
            ]);

        } catch (\Exception $e) {
            Log::error('Renew License Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load application form.');
        }
    }

    /**
     * Store renew license application
     * POST /renew-license
     */
    public function store(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];

        try {
            $formData = $request->all();
            $formData['uid'] = $uid;
            $formData['action'] = $request->input('action', 'draft'); // 'draft' or 'applied'

            // Handle file uploads (if any)
            $builder = $this->authorizedRequest();

            // Attach all form fields
            foreach ($formData as $key => $value) {
                if (!is_null($value)) {
                    $builder = $builder->attach($key, $value);
                }
            }

            $response = $builder->post($this->backend . '/api/renew-license/store');

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
                $encryptedId = UrlEncryptionHelper::encryptUrl($onlineApplicationId);
                return redirect()->route('application.view', ['id' => $encryptedId])
                    ->with('success', $response->json('message') ?? 'Application saved successfully.');
            }

            return redirect()->route('dashboard')
                ->with('success', $response->json('message') ?? 'Application saved successfully.');

        } catch (\Exception $e) {
            Log::error('Renew License Store Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'An error occurred while saving the application.');
        }
    }
}
