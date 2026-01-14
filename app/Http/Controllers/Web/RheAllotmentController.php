<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RheAllotmentController extends Controller
{
    protected $backend;

    public function __construct()
    {
        // Use same pattern as other controllers - ensure no trailing slash
        $this->backend = rtrim(config('services.api.base_url'), '/');
    }

    /**
     * Display the RHE Allotment form
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
            // Check if backend URL already includes /api
            $apiPrefix = (str_ends_with($this->backend, '/api')) ? '' : '/api';
            $apiUrl = $this->backend . $apiPrefix . '/rhe-allotment/flat-types';
            
            $flatTypesResponse = $httpClient->get($apiUrl);
            
            if (!$flatTypesResponse->successful()) {
                Log::error('RHE Allotment Flat Types API Error', [
                    'status' => $flatTypesResponse->status(),
                    'body' => $flatTypesResponse->body(),
                    'json' => $flatTypesResponse->json()
                ]);
            }
            
            $responseData = $flatTypesResponse->json();
            $flatTypes = ($flatTypesResponse->successful() && isset($responseData['data'])) 
                ? $responseData['data'] 
                : [];

            // Get from session if available (from POST redirect)
            $allotmentType = $request->session()->get('rhe_allotment_type') ?? $request->get('allotment_type');
            $districtCode = $request->session()->get('rhe_allotment_district_code') ?? $request->get('district_code', 17);
            $reportData = $request->session()->get('rhe_allotment_report_data');

            // Clear session data after use
            $request->session()->forget(['rhe_allotment_type', 'rhe_allotment_district_code', 'rhe_allotment_report_data']);

            return view('housingTheme.rhe-allotment.index', [
                'flatTypes' => $flatTypes,
                'allotmentType' => $allotmentType,
                'districtCode' => $districtCode,
                'reportData' => $reportData
            ]);

        } catch (\Exception $e) {
            Log::error('RHE Allotment Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to load RHE Allotment page: ' . $e->getMessage());
        }
    }

    /**
     * Show vacancy report (POST handler to hide parameters from URL)
     */
    public function showVacancy(Request $request)
    {
        try {
            $request->validate([
                'allotment_type' => 'required|integer',
                'district_code' => 'nullable|integer'
            ]);

            $token = $request->session()->get('api_token');
            
            $httpClient = Http::acceptJson();
            if ($token) {
                $httpClient = $httpClient->withToken($token);
            }

            $allotmentType = $request->input('allotment_type');
            $districtCode = $request->input('district_code', 17);

            // Fetch vacancy report
            $apiPrefix = (str_ends_with($this->backend, '/api')) ? '' : '/api';
            $vacancyUrl = $this->backend . $apiPrefix . '/rhe-allotment/show-vacancy';
            $vacancyResponse = $httpClient->get($vacancyUrl, [
                'allotment_type' => $allotmentType,
                'district_code' => $districtCode
            ]);

            $reportData = null;
            if ($vacancyResponse->successful()) {
                $reportData = $vacancyResponse->json('data');
            } else {
                Log::error('RHE Allotment Show Vacancy API Error', [
                    'status' => $vacancyResponse->status(),
                    'body' => $vacancyResponse->body(),
                    'json' => $vacancyResponse->json()
                ]);
            }

            // Store in session to pass to index view
            $request->session()->put('rhe_allotment_type', $allotmentType);
            $request->session()->put('rhe_allotment_district_code', $districtCode);
            $request->session()->put('rhe_allotment_report_data', $reportData);

            return redirect()->route('rhe-allotment.index');

        } catch (\Exception $e) {
            Log::error('RHE Allotment Show Vacancy Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('rhe-allotment.index')
                ->with('error', 'Failed to fetch vacancy report: ' . $e->getMessage());
        }
    }

    /**
     * Process the allotment
     */
    public function processAllotment(Request $request)
    {
        try {
            $request->validate([
                'allotment_type' => 'required|integer',
                'district_code' => 'nullable|integer'
            ]);

            $token = $request->session()->get('api_token');
            
            $httpClient = Http::acceptJson();
            if ($token) {
                $httpClient = $httpClient->withToken($token);
            }

            // Check if backend URL already includes /api
            $apiPrefix = (str_ends_with($this->backend, '/api')) ? '' : '/api';
            $processUrl = $this->backend . $apiPrefix . '/rhe-allotment/process';
            
            $response = $httpClient->post($processUrl, [
                'allotment_type' => $request->input('allotment_type'),
                'district_code' => $request->input('district_code', 17)
            ]);

            if ($response->successful()) {
                $data = $response->json();
                // Store in session to maintain state without URL parameters
                $request->session()->put('rhe_allotment_type', $request->input('allotment_type'));
                $request->session()->put('rhe_allotment_district_code', $request->input('district_code', 17));
                return redirect()->route('rhe-allotment.index')
                    ->with('success', $data['message'] ?? 'Allotment Process Completed Successfully');
            } else {
                $error = $response->json('message') ?? 'Allotment Process Failed';
                // Store in session to maintain state
                $request->session()->put('rhe_allotment_type', $request->input('allotment_type'));
                $request->session()->put('rhe_allotment_district_code', $request->input('district_code', 17));
                return redirect()->route('rhe-allotment.index')->with('error', $error);
            }

        } catch (\Exception $e) {
            Log::error('RHE Allotment Process Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Allotment Process Failed: ' . $e->getMessage())->withInput();
        }
    }
}

