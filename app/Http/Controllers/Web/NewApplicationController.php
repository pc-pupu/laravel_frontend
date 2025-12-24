<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\UrlEncryptionHelper;
use App\Helpers\AuthEncryptionHelper;

class NewApplicationController extends Controller
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
     * Show the new application form
     * GET /new-apply
     */
    public function create(Request $request)
    {
        // Check if user is logged in
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];
        $hrmsId = $user['name']; // HRMS ID is stored in name field

        try {
            // Step 1: Check for draft application
            $draftStatus = $this->checkDraftStatus($uid);
            
            if ($draftStatus && isset($draftStatus['online_application_id'])) {
                // Redirect to view application if draft exists and not rejected
                $encryptedId = UrlEncryptionHelper::encryptUrl($draftStatus['online_application_id']);
                return redirect()->route('application.view', ['id' => $encryptedId]);
            }

            // Step 2: Fetch HRMS data
            $hrmsData = $this->getHRMSUserData($hrmsId);
            // echo '<pre>';print_r($hrmsData);die;
            
            if (empty($hrmsData)) {
                return redirect()->route('dashboard')
                    ->with('error', 'Error: HRMS NO User Data fetched.');
            }

            // Step 3: Fetch dropdown data
            $districts = $this->getDistricts();
            $payBands = $this->getPayBands();
            // echo '<pre>';print_r($payBands);die;
            
            // Step 4: Get DDO data from HRMS
            $ddoData = $this->getDdoData($hrmsData['ddoId'] ?? '');
           
            $treasuryId = $ddoData['treasury_id'] ?? null;

            // Step 5: Pre-fill form with HRMS data
            $formData = $this->prepareFormDataFromHRMS($hrmsData, $ddoData, $districts, $payBands);
        //    echo '<pre>';print_r($ddoData);die;
            // Step 6: Get flat type based on pay band and basic pay
            $flatType = $this->getFlatTypeByPayBand($formData['pay_band_id'],$formData['official']['pay_in']);

            $allotmentCategories = $this->getAllotmentCategories($flatType);
            
            // Step 7: Get housing estate preferences (if pay band and treasury available)
            $housingEstates = [];
            if ($formData['pay_band_id'] && $treasuryId) {
                $housingEstates = $this->getHousingEstatePreferences($formData['pay_band_id'], $treasuryId);
            }

            // Step 8: Check for existing application data
            $existingAppData = $this->getExistingApplicationData($uid);

            $hrmsData = array_merge($hrmsData, [
                'payBandId' => $formData['pay_band_id'] ?? '', 'ddoDistrictCode' => $ddoData['district_code'] ?? '',
            ]);

            return view('housingTheme.new-application.form', [
                'districts' => $districts,
                'payBands' => $payBands,
                'ddoDesignations' => $formData['ddo_designations'] ?? [],
                'personalInfo' => $formData['personal'] ?? [],
                'officialInfo' => $formData['official'] ?? [],
                'hrmsData' => $hrmsData,
                'flatType' => $flatType,
                'allotmentCategories' => $allotmentCategories,
                'housingEstates' => $housingEstates,
                'existingAppData' => $existingAppData,
                'treasuryId' => $treasuryId,
                'uid' => $uid,
            ]);

        } catch (\Exception $e) {
            Log::error('New Application Create Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load application form.');
        }
    }

    /**
     * Store the new application form
     */
    public function store(Request $request)
    {
        // Check if user is logged in
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];
        // print('<pre>');print_r($request->all());die;
        
        try {
            $formData = $request->all();
            $formData['uid'] = $uid;
            // $formData['app_type'] = 'NA';
            // $formData['action'] = $request->input('action', 'draft'); // 'draft' or 'applied'
            $formData['online_application_id'] = $request->input('online_application_id', 0);

            // Prepare multipart form data for file upload
            $multipartData = [];
            foreach ($formData as $key => $value) {
                if ($key !== 'extra_doc') {
                    $multipartData[] = [
                        'name' => $key,
                        'contents' => is_array($value) ? json_encode($value) : (string)$value,
                    ];
                }
            }

            // Add file if present
            if ($request->hasFile('extra_doc')) {
                $multipartData[] = [
                    'name' => 'extra_doc',
                    'contents' => fopen($request->file('extra_doc')->getRealPath(), 'r'),
                    'filename' => $request->file('extra_doc')->getClientOriginalName(),
                ];
            }

            // print('<pre>');print_r($multipartData);die;
            $token = session('api_token');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->asMultipart()->post($this->backend . '/api/new-application', $multipartData);

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

            if ($onlineApplicationId && $formData['action'] == 'applied') {
                // Send email and SMS notification
                $this->sendNotification($formData);

                $encryptedId = UrlEncryptionHelper::encryptUrl($onlineApplicationId);
                return redirect()->route('application.view', ['id' => $encryptedId])
                    ->with('success', 'Application submitted successfully.');
            }

            if ($onlineApplicationId) {
                $encryptedId = UrlEncryptionHelper::encryptUrl($onlineApplicationId);
                return redirect()->route('application.view', ['id' => $encryptedId])
                    ->with('success', 'Application saved as draft.');
            }

            return redirect()->route('dashboard')
                ->with('success', 'Application saved successfully.');

        } catch (\Exception $e) {
            Log::error('New Application Store Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'An error occurred while submitting the application.');
        }
    }

    /**
     * Check draft status
     */
    private function checkDraftStatus($uid)
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/new-application/check-draft', [
                    'uid' => $uid,
                ]);

            if ($response->successful() && $response->json('has_draft')) {
                return $response->json('data');
            }
        } catch (\Exception $e) {
            Log::error('Check Draft Status Error', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Get HRMS User Data (reuse from DashboardController logic)
     */
    private function getHRMSUserData($hrmsId)
    {
        try {
            // Call HRMS API
            $hrmsApiUrl = config(
                'services.hrms.api_url',
                'https://uat.wbifms.gov.in/hrms-External/housing/fetchEmployeeDetails'
            );
            $requestData = [
                'req' => [
                    'hrmsId' => $hrmsId,
                ],
            ];

            $response = Http::timeout(30)
                ->withOptions(['verify' => false])
                ->post($hrmsApiUrl, $requestData);


                
            $responseData = $response->json();
            

            if ($response->successful()) {
                $encryptedData = $responseData['resp']['data'];
                $decryptedJson = AuthEncryptionHelper::decrypt($encryptedData);
                $userDataArray = json_decode($decryptedJson, true);

                if (empty($userDataArray[0])) {
                    Log::error('HRMS Decryption Failed', [
                        'hrms_id' => $hrmsId,
                        'data' => $decryptedJson,
                    ]);

                    return $this->getDefaultData($hrmsId);
                }
            }
            return $userDataArray[0];
            if (!$response->successful()) {
                Log::error('HRMS API Error', [
                    'hrms_id' => $hrmsId,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);

                return $this->getDefaultData($hrmsId);
            }
        } catch (\Exception $e) {
            Log::error('HRMS API Error', ['error' => $e->getMessage()]);
        }

        return [];
    }

    /**
     * Get local dummy HRMS data for development
     */
    private function getLocalHRMSData($hrmsId)
    {
        // Return dummy data structure matching HRMS response
        return [
            'hrmsId' => $hrmsId,
            'applicantName' => 'TEST USER',
            'guardianName' => 'TEST FATHER',
            'dateOfBirth' => '01/01/1980',
            'gender' => 'Male',
            'mobileNo' => '9876543210',
            'email' => 'test@example.com',
            'permanentStreet' => 'TEST STREET',
            'permanentCityTownVillage' => 'KOLKATA',
            'permanentPostOffice' => 'KOLKATA',
            'permanentDistrictCode' => 17,
            'permanentPincode' => '700001',
            'presentStreet' => 'TEST PRESENT STREET',
            'presentCityTownVillage' => 'KOLKATA',
            'presentPostOffice' => 'KOLKATA',
            'presentDistrictCode' => 17,
            'presentPincode' => '700001',
            'applicantDesignation' => 'TEST DESIGNATION',
            'applicantPostingPlace' => 'TEST POSTING PLACE',
            'applicantHeadquarter' => 'KOLKATA',
            'dateOfJoining' => '01/01/2000',
            'dateOfRetirement' => '31/12/2040',
            'officeName' => 'TEST OFFICE',
            'officeStreetCharacter' => 'TEST OFFICE STREET',
            'officeCityTownVillage' => 'KOLKATA',
            'officePostOffice' => 'KOLKATA',
            'officeDistrict' => 17,
            'officePinCode' => '700001',
            'payInThePayBand' => 50000,
            'ddoId' => 'TESTDDO001',
        ];
    }

    /**
     * Get DDO data
     */
    private function getDdoData($ddoCode)
    {
        if (empty($ddoCode)) {
            return [];
        }

        try {
            $ddo = DB::table('housing_ddo')
                ->where('ddo_code', $ddoCode)
                ->where('is_active', 'Y')
                ->first();

            if ($ddo) {
                return [
                    'ddo_id' => $ddo->ddo_id,
                    'district_code' => $ddo->district_code,
                    'treasury_id' => $ddo->treasury_id ?? null,
                    'ddo_designation' => $ddo->ddo_designation,
                    'ddo_address' => $ddo->ddo_address,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Get DDO Data Error', ['error' => $e->getMessage()]);
        }

        return [];
    }

    /**
     * Prepare form data from HRMS data
     */
    private function prepareFormDataFromHRMS($hrmsData, $ddoData, $districts, $payBands)
    {
        $formData = [
            'personal' => [],
            'official' => [],
            'ddo_designations' => [],
        ];

        // Personal Information
        if (isset($hrmsData['applicantName'])) {
            $formData['personal']['applicant_name'] = $hrmsData['applicantName'];
        }
        if (isset($hrmsData['guardianName'])) {
            $formData['personal']['applicant_father_name'] = $hrmsData['guardianName'];
        }
        if (isset($hrmsData['dateOfBirth'])) {
            $formData['personal']['dob'] = $hrmsData['dateOfBirth'];
        }
        if (isset($hrmsData['gender'])) {
            $formData['personal']['gender'] = $hrmsData['gender'] == 'Male' ? 'M' : 'F';
        }
        if (isset($hrmsData['mobileNo'])) {
            $formData['personal']['mobile'] = $hrmsData['mobileNo'];
        }
        if (isset($hrmsData['email'])) {
            $formData['personal']['email'] = $hrmsData['email'];
        }

        // Permanent Address
        if (isset($hrmsData['permanentStreet'])) {
            $formData['personal']['permanent_street'] = $hrmsData['permanentStreet'];
        }
        if (isset($hrmsData['permanentCityTownVillage'])) {
            $formData['personal']['permanent_city_town_village'] = $hrmsData['permanentCityTownVillage'];
        }
        if (isset($hrmsData['permanentPostOffice'])) {
            $formData['personal']['permanent_post_office'] = $hrmsData['permanentPostOffice'];
        }
        if (isset($hrmsData['permanentDistrictCode'])) {
            // Convert HRMS district code to housing district code
            $housingDistrictCode = $this->getDistrictCodeByHRMS($hrmsData['permanentDistrictCode']);
            $formData['personal']['permanent_district'] = $housingDistrictCode;
        }
        if (isset($hrmsData['permanentPincode'])) {
            $formData['personal']['permanent_pincode'] = $hrmsData['permanentPincode'];
        }

        // Present Address
        if (isset($hrmsData['presentStreet'])) {
            $formData['personal']['present_street'] = $hrmsData['presentStreet'];
        }
        if (isset($hrmsData['presentCityTownVillage'])) {
            $formData['personal']['present_city_town_village'] = $hrmsData['presentCityTownVillage'];
        }
        if (isset($hrmsData['presentPostOffice'])) {
            $formData['personal']['present_post_office'] = $hrmsData['presentPostOffice'];
        }
        if (isset($hrmsData['presentDistrictCode'])) {
            $housingDistrictCode = $this->getDistrictCodeByHRMS($hrmsData['presentDistrictCode']);
            $formData['personal']['present_district'] = $housingDistrictCode;
        }
        if (isset($hrmsData['presentPincode'])) {
            $formData['personal']['present_pincode'] = $hrmsData['presentPincode'];
        }

        // Official Information
        if (isset($hrmsData['hrmsId'])) {
            $formData['official']['hrms_id'] = $hrmsData['hrmsId'];
        }
        if (isset($hrmsData['applicantDesignation'])) {
            $formData['official']['app_designation'] = $hrmsData['applicantDesignation'];
        }
        if (isset($hrmsData['payInThePayBand'])) {
            $formData['official']['pay_in'] = $hrmsData['payInThePayBand'];
            // Calculate pay band based on basic pay
            $formData['pay_band_id'] = $this->calculatePayBand($hrmsData['payInThePayBand']);
        }
        if (isset($hrmsData['applicantPostingPlace'])) {
            $formData['official']['app_posting_place'] = $hrmsData['applicantPostingPlace'];
        }
        if (isset($hrmsData['applicantHeadquarter'])) {
            $formData['official']['app_headquarter'] = $hrmsData['applicantHeadquarter'];
        }
        if (isset($hrmsData['dateOfJoining'])) {
            $formData['official']['doj'] = $hrmsData['dateOfJoining'];
        }
        if (isset($hrmsData['dateOfRetirement'])) {
            $formData['official']['dor'] = $hrmsData['dateOfRetirement'];
        }

        // Office Address
        if (isset($hrmsData['officeName'])) {
            $formData['official']['office_name'] = $hrmsData['officeName'];
        }
        if (isset($hrmsData['officeStreetCharacter'])) {
            $formData['official']['office_street'] = $hrmsData['officeStreetCharacter'];
        }
        if (isset($hrmsData['officeCityTownVillage'])) {
            $formData['official']['office_city'] = $hrmsData['officeCityTownVillage'];
        }
        if (isset($hrmsData['officePostOffice'])) {
            $formData['official']['office_post_office'] = $hrmsData['officePostOffice'];
        }
        if (isset($hrmsData['officeDistrict'])) {
            $housingDistrictCode = $this->getDistrictCodeByHRMS($hrmsData['officeDistrict']);
            $formData['official']['office_district'] = $housingDistrictCode;
        }
        if (isset($hrmsData['officePinCode'])) {
            $formData['official']['office_pincode'] = $hrmsData['officePinCode'];
        }
        if (isset($hrmsData['mobileNo'])) {
            $formData['official']['office_phone_no'] = $hrmsData['mobileNo'];
        }

        // DDO Details
        if (isset($ddoData['district_code'])) {
            $formData['official']['district'] = $ddoData['district_code'];
            $formData['ddo_designations'] = $this->getDdoDesignations($ddoData['district_code']);
        }
        if (isset($ddoData['ddo_id'])) {
            $formData['official']['designation'] = $ddoData['ddo_id'];
        }
        
        return $formData;
    }

    /**
     * Get district code by HRMS district ID
     */
    private function getDistrictCodeByHRMS($hrmsDistrictId)
    {
        if (empty($hrmsDistrictId)) {
            return null;
        }

        try {
            $districtCode = DB::table('housing_district')
                ->where('hrms_district_id', $hrmsDistrictId)
                ->value('district_code');

            return $districtCode;
        } catch (\Exception $e) {
            Log::error('Get District Code Error', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Calculate pay band based on basic pay
     */
    private function calculatePayBand($basicPay)
    {
        try {
            $query = DB::table('housing_pay_band_categories')
                ->where('flag', 'new');

            if ($basicPay > 95099) {
                $query->where('scale_from', '>=', 95100)
                    ->whereNull('scale_to');
            } else {
                $query->where('scale_from', '<=', $basicPay)
                    ->where('scale_to', '>', $basicPay);
            }

            $payBand = $query->value('pay_band_id');

            return $payBand;
        } catch (\Exception $e) {
            Log::error('Calculate Pay Band Error', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Get flat type by pay band and basic pay
     */
    private function getFlatTypeByPayBand($payBandId, $basicPay)
    {
        if (!$payBandId || !$basicPay) {
            return null;
        }
        // echo $payBandId;die;
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/new-application/flat-type-by-payband', [
                    'pay_band_id' => $payBandId,
                    'basic_pay' => $basicPay,
                ]);

            if ($response->successful()) {
                return $response->json('data')['flat_type'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Get Flat Type Error', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Get allotment categories
     */
    private function getAllotmentCategories($flatType)
    {
        if (!$flatType) {
            return ['' => '- Select -'];
        }

        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/existing-applicants-helpers/allotment-categories', [
                    'rhe_flat_type' => $flatType,
                ]);

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Get Allotment Categories Error', ['error' => $e->getMessage()]);
        }

        return ['' => '- Select -'];
    }

    /**
     * Get housing estate preferences
     */
    private function getHousingEstatePreferences($payBandId, $treasuryId)
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/new-application/housing-estate-preferences', [
                    'pay_band_id' => $payBandId,
                    'treasury_id' => $treasuryId,
                    'district_code' => 17, // Default district
                ]);

            if ($response->successful()) {
                return $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Get Housing Estate Preferences Error', ['error' => $e->getMessage()]);
        }

        return ['' => '- Select -'];
    }

    /**
     * Get existing application data
     */
    private function getExistingApplicationData($uid)
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/new-application/data', [
                    'uid' => $uid,
                ]);

            if ($response->successful()) {
                return $response->json('data');
            }
        } catch (\Exception $e) {
            Log::error('Get Existing Application Data Error', ['error' => $e->getMessage()]);
        }

        return null;
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
     * Send notification (email and SMS)
     */
    private function sendNotification($formData)
    {
        // This would typically call a notification service
        // For now, we'll just log it
        Log::info('Application submitted notification', [
            'email' => $formData['email'] ?? '',
            'mobile' => $formData['mobile'] ?? '',
            'applicant_name' => $formData['applicant_name'] ?? '',
        ]);
    }

    /**
     * AJAX endpoint to get flat type and allotment categories
     */
    public function getFlatTypeAndCategoriesAjax(Request $request)
    {
        $payBandId = $request->input('pay_band_id');
        $basicPay = $request->input('basic_pay');

        if (!$payBandId || !$basicPay) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pay band ID and basic pay are required',
            ], 422);
        }

        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/new-application/flat-type-by-payband', [
                    'pay_band_id' => $payBandId,
                    'basic_pay' => $basicPay,
                ]);

            if ($response->successful()) {
                $flatType = $response->json('data')['flat_type'] ?? null;
                
                if ($flatType) {
                    // Get allotment categories
                    $categoriesResponse = $this->authorizedRequest()
                        ->get($this->backend . '/api/existing-applicants-helpers/allotment-categories', [
                            'rhe_flat_type' => $flatType,
                        ]);

                    $categories = $categoriesResponse->successful() 
                        ? $categoriesResponse->json('data') ?? [] 
                        : [];

                    return response()->json([
                        'status' => 'success',
                        'data' => [
                            'flat_type' => $flatType,
                            'allotment_categories' => $categories,
                        ],
                    ]);
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch flat type',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Get Flat Type AJAX Error', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * AJAX endpoint to get housing estate preferences
     */
    public function getHousingEstatesAjax(Request $request)
    {
        $payBandId = $request->input('pay_band_id');
        $treasuryId = $request->input('treasury_id');

        if (!$payBandId || !$treasuryId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pay band ID and Treasury ID are required',
            ], 422);
        }

        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/new-application/housing-estate-preferences', [
                    'pay_band_id' => $payBandId,
                    'treasury_id' => $treasuryId,
                    'district_code' => 17,
                ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $response->json('data') ?? [],
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch housing estates',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Get Housing Estates AJAX Error', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 500);
        }
    }

        private function getDefaultData($hrmsId)
    {
        return [
            'hrmsId' => $hrmsId,
            'applicantName' => $hrmsId,
            'email' => 'N/A',
            'applicantDesignation' => 'N/A',
            'officeName' => 'N/A',
            'mobileNo' => 'N/A',
        ];
    }
}



