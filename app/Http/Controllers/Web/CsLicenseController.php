<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class CsLicenseController extends Controller
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
     * Show CS license application form
     * GET /cs-license
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
                ->get($this->backend . '/api/cs-license/check-draft', ['uid' => $uid]);

            if ($draftResponse->successful() && $draftResponse->json('has_draft')) {
                $onlineApplicationId = $draftResponse->json('online_application_id');
                $encryptedId = UrlEncryptionHelper::encryptUrl($onlineApplicationId);
                return redirect()->route('application.view', ['id' => $encryptedId]);
            }

            // Get allotment details
            $allotmentResponse = $this->authorizedRequest()
                ->get($this->backend . '/api/cs-license/allotment-details', ['uid' => $uid]);

            $allotmentData = null;
            $onlineApplicationId = 0;
            if ($allotmentResponse->successful()) {
                $allotmentData = $allotmentResponse->json('data');
                $onlineApplicationId = $allotmentResponse->json('online_application_id', 0);
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

            return view('housingTheme.license-application.cs-license', [
                'appType' => 'CSL',
                'districts' => $districts,
                'payBands' => $payBands,
                'ddoDesignations' => $ddoDesignations,
                'personalInfo' => $personalInfo,
                'officialInfo' => $officialInfo,
                'allotmentData' => $allotmentData,
                'onlineApplicationId' => $onlineApplicationId,
                'uid' => $uid,
            ]);

        } catch (\Exception $e) {
            Log::error('CS License Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load application form.');
        }
    }

    /**
     * Store CS license application
     * POST /cs-license
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

            // Handle file uploads
            $builder = $this->authorizedRequest();
            
            if ($request->hasFile('document')) {
                $builder = $builder->attach('document', 
                    file_get_contents($request->file('document')->getRealPath()),
                    $request->file('document')->getClientOriginalName()
                );
            }

            // Attach all other form fields
            foreach ($formData as $key => $value) {
                if ($key !== 'document' && !is_null($value)) {
                    $builder = $builder->attach($key, $value);
                }
            }

            $response = $builder->post($this->backend . '/api/cs-license/store');

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
            Log::error('CS License Store Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->with('error', 'An error occurred while saving the application.');
        }
    }
}
