<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class AllotmentListController extends Controller
{
    protected $backend;

    public function __construct()
    {
        $this->backend = config('services.api.base_url');
    }

    /**
     * Display the allotment list page
     */
    public function index(Request $request)
    {
        try {
            $token = $request->session()->get('api_token');
            
            $httpClient = Http::acceptJson();
            if ($token) {
                $httpClient = $httpClient->withToken($token);
            }

            // Get process dates
            $datesResponse = $httpClient->get($this->backend . '/allotment-list/process-dates');
            $processDates = $datesResponse->successful() ? $datesResponse->json('data') : [];

            // Get process types
            $typesResponse = $httpClient->get($this->backend . '/allotment-list/process-types');
            $processTypes = $typesResponse->successful() ? $typesResponse->json('data') : [];

            // Get data from session (if available from POST request)
            $allotmentProcessDate = $request->session()->get('allotment_process_date');
            $allotmentProcessNo = $request->session()->get('allotment_process_no');
            $allotmentProcessType = $request->session()->get('allotment_process_type');

            $allottees = [];
            $processNumbers = [];

            if ($allotmentProcessDate) {
                // Get process numbers
                $processNosResponse = $httpClient->get($this->backend . '/allotment-list/process-numbers', [
                    'allotment_process_date' => $allotmentProcessDate
                ]);
                $processNumbers = $processNosResponse->successful() ? $processNosResponse->json('data') : [];

                // echo $allotmentProcessDate.'--'.$allotmentProcessNo.'--'.$allotmentProcessType; die;
                if ($allotmentProcessDate && $allotmentProcessNo && $allotmentProcessType) {
                    // Get allottee list
                    $allotteesResponse = $httpClient->get($this->backend . '/allotment-list/allottees', [
                        'allotment_process_date' => $allotmentProcessDate,
                        'allotment_process_no' => $allotmentProcessNo,
                        'allotment_process_type' => $allotmentProcessType
                    ]);
                    // Convert array to objects for Blade template compatibility
                    $allotteesData = $allotteesResponse->successful() ? $allotteesResponse->json('data') : [];
                    $allottees = array_map(function($item) {
                        return (object) $item;
                    }, $allotteesData);
                }
            }

            return view('housingTheme.allotment-list.index', [
                'processDates' => $processDates,
                'processNumbers' => $processNumbers,
                'processTypes' => $processTypes,
                'allotmentProcessDate' => $allotmentProcessDate,
                'allotmentProcessNo' => $allotmentProcessNo,
                'allotmentProcessType' => $allotmentProcessType,
                'allottees' => $allottees
            ]);

        } catch (\Exception $e) {
            Log::error('Allotment List Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')->with('error', 'Failed to load allotment list.');
        }
    }

    /**
     * Handle POST request to show allotment list (hide URL parameters)
     */
    public function show(Request $request)
    {
        try {
            $request->validate([
                'allotment_process_date' => 'required|date',
                'allotment_process_no' => 'nullable|string',
                'allotment_process_type' => 'nullable|string'
            ]);

            // Store in session to avoid URL parameters
            $request->session()->put('allotment_process_date', $request->allotment_process_date);
            $request->session()->put('allotment_process_no', $request->allotment_process_no);
            $request->session()->put('allotment_process_type', $request->allotment_process_type);

            // Redirect to index without parameters
            return redirect()->route('allotment-list.index');

        } catch (\Exception $e) {
            Log::error('Allotment List Show Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('allotment-list.index')->with('error', 'Failed to load allotment list.');
        }
    }

    /**
     * Display the allotment approval page
     */
    public function approve(Request $request)
    {
        try {
            $token = $request->session()->get('api_token');
            
            $httpClient = Http::acceptJson();
            if ($token) {
                $httpClient = $httpClient->withToken($token);
            }

            // Get process dates
            $datesResponse = $httpClient->get($this->backend . '/allotment-list/process-dates');
            $processDates = $datesResponse->successful() ? $datesResponse->json('data') : [];

            $allotmentProcessDate = $request->get('allotment_process_date');
            $allotmentProcessNo = $request->get('allotment_process_no');
            $allotmentProcessType = $request->get('allotment_process_type');

            $allottees = [];
            $processNumbers = [];

            if ($allotmentProcessDate) {
                // Get process numbers
                $processNosResponse = $httpClient->get($this->backend . '/allotment-list/process-numbers', [
                    'allotment_process_date' => $allotmentProcessDate
                ]);
                $processNumbers = $processNosResponse->successful() ? $processNosResponse->json('data') : [];

                if ($allotmentProcessDate && $allotmentProcessNo && $allotmentProcessType) {
                    // Get allottee list for approval
                    $allotteesResponse = $httpClient->get($this->backend . '/allotment-list/allottees-for-approve', [
                        'allotment_process_date' => $allotmentProcessDate,
                        'allotment_process_no' => $allotmentProcessNo,
                        'allotment_process_type' => $allotmentProcessType
                    ]);
                    // Convert array to objects for Blade template compatibility
                    $allotteesData = $allotteesResponse->successful() ? $allotteesResponse->json('data') : [];
                    $allottees = array_map(function($item) {
                        return (object) $item;
                    }, $allotteesData);
                }
            }

            return view('housingTheme.allotment-list.approve', [
                'processDates' => $processDates,
                'processNumbers' => $processNumbers,
                'allotmentProcessDate' => $allotmentProcessDate,
                'allotmentProcessNo' => $allotmentProcessNo,
                'allotmentProcessType' => $allotmentProcessType,
                'allottees' => $allottees
            ]);

        } catch (\Exception $e) {
            Log::error('Allotment List Approve Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')->with('error', 'Failed to load approval page.');
        }
    }

    /**
     * Handle approve/reject/hold actions
     */
    public function updateStatus(Request $request)
    {
        
        try {
            $request->validate([
                'action' => 'required|string|in:approve,reject,hold',
                'online_application_ids' => 'required' // JSON string from form
            ]);

            $token = $request->session()->get('api_token');
            
            if (!$token) {
                return redirect()->back()->with('error', 'Authentication required.');
            }

            // Parse JSON string to array
            $onlineApplicationIds = json_decode($request->online_application_ids, true);
            
            if (!is_array($onlineApplicationIds) || empty($onlineApplicationIds)) {
                return redirect()->back()->with('error', 'Please select at least one allottee.');
            }

            $action = $request->action;
            $endpoint = $this->backend . '/allotment-list/' . $action;
            // echo $endpoint;
            $response = Http::withToken($token)
                ->acceptJson()
                ->asJson()
                ->post($endpoint, [
                    'online_application_ids' => $onlineApplicationIds
                ]);

                
            // print_r($response->body()); exit;
            if ($response->successful()) {
                $message = $response->json('message') ?? ucfirst($action) . ' completed successfully.';
                // Preserve session data for redirect
                $request->session()->keep(['allotment_process_date', 'allotment_process_no', 'allotment_process_type']);
                return redirect()->route('allotment-list.index')->with('success', $message);
            } else {
                $errorMessage = $response->json('message') ?? 'Failed to ' . $action . ' allotments.';
                // Preserve session data for redirect
                $request->session()->keep(['allotment_process_date', 'allotment_process_no', 'allotment_process_type']);
                return redirect()->route('allotment-list.index')->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('Allotment List Update Status Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'An error occurred while processing the request.');
        }
    }

    /**
     * Display allotment detail page
     */
    public function detail($encryptedAppId)
    {
        try {
            $token = request()->session()->get('api_token');
            
            $httpClient = Http::acceptJson();
            if ($token) {
                $httpClient = $httpClient->withToken($token);
            }

            $response = $httpClient->get($this->backend . '/allotment-list/detail/' . $encryptedAppId);

            // print_r($response->body()); exit;
            if (!$response->successful()) {
                return redirect()->route('allotment-list.index')->with('error', 'Allotment not found.');
            }

            // Convert array to object for Blade template compatibility
            $allotmentData = $response->json('data');
            $allotment = $allotmentData ? (object) $allotmentData : null;

            return view('housingTheme.allotment-list.detail', [
                'allotment' => $allotment
            ]);

        } catch (\Exception $e) {
            Log::error('Allotment Detail Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('allotment-list.index')->with('error', 'Failed to load allotment details.');
        }
    }

    /**
     * Display allottee list on hold
     */
    public function hold(Request $request)
    {
        try {
            $token = $request->session()->get('api_token');
            
            $httpClient = Http::acceptJson();
            if ($token) {
                $httpClient = $httpClient->withToken($token);
            }

            $response = $httpClient->get($this->backend . '/allotment-list/allottees-on-hold');
            // Convert array to objects for Blade template compatibility
            $allotteesData = $response->successful() ? $response->json('data') : [];
            $allottees = array_map(function($item) {
                return (object) $item;
            }, $allotteesData);

            return view('housingTheme.allotment-list.hold', [
                'allottees' => $allottees
            ]);

        } catch (\Exception $e) {
            Log::error('Allotment List Hold Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')->with('error', 'Failed to load hold list.');
        }
    }
}

