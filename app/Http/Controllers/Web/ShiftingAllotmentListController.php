<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class ShiftingAllotmentListController extends Controller
{
    protected $backend;

    public function __construct()
    {
        $this->backend = config('services.api.base_url');
    }

    /**
     * Show VS Allotment List Form
     * GET /vs_allotment_list
     */
    public function vsAllotmentList(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $token = $request->session()->get('api_token');

            // Fetch process dates
            $datesResponse = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/shifting-allotment-list/vs/process-dates');

            $processDates = [];
            if ($datesResponse->successful()) {
                $processDates = ['' => 'Select VS Allotment Process Date'] + 
                    collect($datesResponse->json('data', []))->pluck('label', 'value')->toArray();
            }

            $selectedDate = $request->input('vs_allotment_process_date', '');
            $selectedProcessNo = $request->input('vs_allotment_process_no', '');
            $processNumbers = [];
            $allottees = [];

            if ($selectedDate) {
                // Fetch process numbers
                $processNosResponse = Http::withToken($token)
                    ->acceptJson()
                    ->get($this->backend . '/shifting-allotment-list/vs/process-numbers', [
                        'allotment_process_date' => $selectedDate
                    ]);

                if ($processNosResponse->successful()) {
                    $processNumbers = ['' => 'Select VS Allotment Process No.'] + 
                        collect($processNosResponse->json('data', []))->pluck('label', 'value')->toArray();
                }

                if ($selectedDate && $selectedProcessNo) {
                    // Fetch allottees
                    $allotteesResponse = Http::withToken($token)
                        ->acceptJson()
                        ->get($this->backend . '/shifting-allotment-list/vs/allottees', [
                            'allotment_process_date' => $selectedDate,
                            'allotment_process_no' => $selectedProcessNo
                        ]);

                    if ($allotteesResponse->successful()) {
                        $allottees = $allotteesResponse->json('data', []);
                        // Encrypt online_application_id for each allottee
                        foreach ($allottees as &$allottee) {
                            if (!empty($allottee['online_application_id'])) {
                                $allottee['encrypted_online_application_id'] = UrlEncryptionHelper::encryptUrl($allottee['online_application_id']);
                            }
                        }
                    }
                }
            }

            return view('housingTheme.shifting-allotment-list.vs-allotment-list', [
                'processDates' => $processDates,
                'processNumbers' => $processNumbers,
                'selectedDate' => $selectedDate,
                'selectedProcessNo' => $selectedProcessNo,
                'allottees' => $allottees
            ]);

        } catch (\Exception $e) {
            Log::error('VS Allotment List Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to load VS allotment list.');
        }
    }

    /**
     * Show CS Allotment List Form
     * GET /cs_allotment_list
     */
    public function csAllotmentList(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $token = $request->session()->get('api_token');

            // Fetch process dates
            $datesResponse = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/shifting-allotment-list/cs/process-dates');

            $processDates = [];
            if ($datesResponse->successful()) {
                $processDates = ['' => 'Select CS Allotment Process Date'] + 
                    collect($datesResponse->json('data', []))->pluck('label', 'value')->toArray();
            }

            $selectedDate = $request->input('cs_allotment_process_date', '');
            $selectedProcessNo = $request->input('cs_allotment_process_no', '');
            $processNumbers = [];
            $allottees = [];

            if ($selectedDate) {
                // Fetch process numbers
                $processNosResponse = Http::withToken($token)
                    ->acceptJson()
                    ->get($this->backend . '/shifting-allotment-list/cs/process-numbers', [
                        'allotment_process_date' => $selectedDate
                    ]);

                if ($processNosResponse->successful()) {
                    $processNumbers = ['' => 'Select CS Allotment Process No.'] + 
                        collect($processNosResponse->json('data', []))->pluck('label', 'value')->toArray();
                }

                if ($selectedDate && $selectedProcessNo) {
                    // Fetch allottees
                    $allotteesResponse = Http::withToken($token)
                        ->acceptJson()
                        ->get($this->backend . '/shifting-allotment-list/cs/allottees', [
                            'allotment_process_date' => $selectedDate,
                            'allotment_process_no' => $selectedProcessNo
                        ]);

                    if ($allotteesResponse->successful()) {
                        $allottees = $allotteesResponse->json('data', []);
                        // Encrypt online_application_id for each allottee
                        foreach ($allottees as &$allottee) {
                            if (!empty($allottee['online_application_id'])) {
                                $allottee['encrypted_online_application_id'] = UrlEncryptionHelper::encryptUrl($allottee['online_application_id']);
                            }
                        }
                    }
                }
            }

            return view('housingTheme.shifting-allotment-list.cs-allotment-list', [
                'processDates' => $processDates,
                'processNumbers' => $processNumbers,
                'selectedDate' => $selectedDate,
                'selectedProcessNo' => $selectedProcessNo,
                'allottees' => $allottees
            ]);

        } catch (\Exception $e) {
            Log::error('CS Allotment List Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to load CS allotment list.');
        }
    }

    /**
     * AJAX: Get VS Process Numbers
     */
    public function getVsProcessNumbersAjax(Request $request)
    {
        if (!$request->session()->has('user')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'allotment_process_date' => 'required|date'
        ]);

        try {
            $token = $request->session()->get('api_token');

            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/shifting-allotment-list/vs/process-numbers', [
                    'allotment_process_date' => $request->input('allotment_process_date')
                ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $response->json('data', [])
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch process numbers'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Get VS Process Numbers AJAX Error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch process numbers'
            ], 500);
        }
    }

    /**
     * AJAX: Get CS Process Numbers
     */
    public function getCsProcessNumbersAjax(Request $request)
    {
        if (!$request->session()->has('user')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'allotment_process_date' => 'required|date'
        ]);

        try {
            $token = $request->session()->get('api_token');

            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/shifting-allotment-list/cs/process-numbers', [
                    'allotment_process_date' => $request->input('allotment_process_date')
                ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $response->json('data', [])
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch process numbers'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Get CS Process Numbers AJAX Error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch process numbers'
            ], 500);
        }
    }

    /**
     * AJAX: Get VS Allottees
     */
    public function getVsAllotteesAjax(Request $request)
    {
        if (!$request->session()->has('user')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'allotment_process_date' => 'required|date',
            'allotment_process_no' => 'required|string'
        ]);

        try {
            $token = $request->session()->get('api_token');

            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/shifting-allotment-list/vs/allottees', [
                    'allotment_process_date' => $request->input('allotment_process_date'),
                    'allotment_process_no' => $request->input('allotment_process_no')
                ]);

            if ($response->successful()) {
                $allottees = $response->json('data', []);
                // Encrypt online_application_id for each allottee
                foreach ($allottees as &$allottee) {
                    if (!empty($allottee['online_application_id'])) {
                        $allottee['encrypted_online_application_id'] = UrlEncryptionHelper::encryptUrl($allottee['online_application_id']);
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'data' => $allottees
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch allottees'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Get VS Allottees AJAX Error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch allottees'
            ], 500);
        }
    }

    /**
     * AJAX: Get CS Allottees
     */
    public function getCsAllotteesAjax(Request $request)
    {
        if (!$request->session()->has('user')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'allotment_process_date' => 'required|date',
            'allotment_process_no' => 'required|string'
        ]);

        try {
            $token = $request->session()->get('api_token');

            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/shifting-allotment-list/cs/allottees', [
                    'allotment_process_date' => $request->input('allotment_process_date'),
                    'allotment_process_no' => $request->input('allotment_process_no')
                ]);

            if ($response->successful()) {
                $allottees = $response->json('data', []);
                // Encrypt online_application_id for each allottee
                foreach ($allottees as &$allottee) {
                    if (!empty($allottee['online_application_id'])) {
                        $allottee['encrypted_online_application_id'] = UrlEncryptionHelper::encryptUrl($allottee['online_application_id']);
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'data' => $allottees
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch allottees'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Get CS Allottees AJAX Error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch allottees'
            ], 500);
        }
    }
}
