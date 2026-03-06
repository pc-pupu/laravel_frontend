<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShiftingAllotmentController extends Controller
{
    protected $backend;

    public function __construct()
    {
        $this->backend = config('services.api.base_url');
    }

    /**
     * Show VS Allotment Form
     * GET /vs_allotment
     */
    public function vsAllotmentForm(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $token = $request->session()->get('api_token');
            $user = $request->session()->get('user');

            // Fetch RHE list
            $rheResponse = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/shifting-allotment/vs/rhe-list');

            $rhes = [];
            if ($rheResponse->successful()) {
                $rheData = $rheResponse->json('data', []);
                $rhes = [0 => '- Select -'] + collect($rheData)->pluck('label', 'value')->toArray();
            }

            $selectedRheId = $request->input('rhe_id', 0);
            $vacancyCount = 0;
            $applicantCount = 0;

            if ($selectedRheId > 0) {
                // Fetch vacancy count
                $vacancyResponse = Http::withToken($token)
                    ->acceptJson()
                    ->get($this->backend . '/shifting-allotment/vs/vacancy-count', [
                        'rhe_id' => $selectedRheId
                    ]);

                if ($vacancyResponse->successful()) {
                    $vacancyCount = $vacancyResponse->json('data.vacancy_count', 0);
                }

                // Fetch applicant count
                $applicantResponse = Http::withToken($token)
                    ->acceptJson()
                    ->get($this->backend . '/shifting-allotment/vs/applicant-count', [
                        'rhe_id' => $selectedRheId
                    ]);

                if ($applicantResponse->successful()) {
                    $applicantCount = $applicantResponse->json('data.applicant_count', 0);
                }
            }

            return view('housingTheme.shifting-allotment.vs-allotment', [
                'rhes' => $rhes,
                'selectedRheId' => $selectedRheId,
                'vacancyCount' => $vacancyCount,
                'applicantCount' => $applicantCount
            ]);

        } catch (\Exception $e) {
            Log::error('VS Allotment Form Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to load VS allotment form.');
        }
    }

    /**
     * Process VS Allotment
     * POST /vs_allotment
     */
    public function processVsAllotment(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $request->validate([
            'rhe_id' => 'required|integer|min:1'
        ]);

        try {
            $token = $request->session()->get('api_token');
            $user = $request->session()->get('user');
            $uid = $user['uid'] ?? 1;

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($this->backend . '/shifting-allotment/vs/process', [
                    'rhe_id' => $request->input('rhe_id'),
                    'uid' => $uid
                ]);

            if (!$response->successful()) {
                $error = $response->json('message', 'Failed to process VS allotment');
                return redirect()->back()
                    ->withInput()
                    ->with('error', $error);
            }

            $data = $response->json();
            $message = $data['message'] ?? 'Successfully done allotment';

            return redirect()->route('shifting-allotment.vs')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Process VS Allotment Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process VS allotment.');
        }
    }

    /**
     * Show CS Allotment Form
     * GET /cs_allotment
     */
    public function csAllotmentForm(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $token = $request->session()->get('api_token');
            $user = $request->session()->get('user');

            // Fetch RHE list
            $rheResponse = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/shifting-allotment/cs/rhe-list');

            $rhes = [];
            if ($rheResponse->successful()) {
                $rheData = $rheResponse->json('data', []);
                $rhes = [0 => '- Select -'] + collect($rheData)->pluck('label', 'value')->toArray();
            }

            $selectedRheId = $request->input('rhe_id', 0);
            $vacancyCount = 0;
            $applicantCount = 0;

            if ($selectedRheId > 0) {
                // Fetch vacancy count
                $vacancyResponse = Http::withToken($token)
                    ->acceptJson()
                    ->get($this->backend . '/shifting-allotment/cs/vacancy-count', [
                        'rhe_id' => $selectedRheId
                    ]);

                if ($vacancyResponse->successful()) {
                    $vacancyCount = $vacancyResponse->json('data.vacancy_count', 0);
                }

                // Fetch applicant count
                $applicantResponse = Http::withToken($token)
                    ->acceptJson()
                    ->get($this->backend . '/shifting-allotment/cs/applicant-count', [
                        'rhe_id' => $selectedRheId
                    ]);

                if ($applicantResponse->successful()) {
                    $applicantCount = $applicantResponse->json('data.applicant_count', 0);
                }
            }

            return view('housingTheme.shifting-allotment.cs-allotment', [
                'rhes' => $rhes,
                'selectedRheId' => $selectedRheId,
                'vacancyCount' => $vacancyCount,
                'applicantCount' => $applicantCount
            ]);

        } catch (\Exception $e) {
            Log::error('CS Allotment Form Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to load CS allotment form.');
        }
    }

    /**
     * Process CS Allotment
     * POST /cs_allotment
     */
    public function processCsAllotment(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $request->validate([
            'rhe_id' => 'required|integer|min:1'
        ]);

        try {
            $token = $request->session()->get('api_token');
            $user = $request->session()->get('user');
            $uid = $user['uid'] ?? 1;

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($this->backend . '/shifting-allotment/cs/process', [
                    'rhe_id' => $request->input('rhe_id'),
                    'uid' => $uid
                ]);

            if (!$response->successful()) {
                $error = $response->json('message', 'Failed to process CS allotment');
                return redirect()->back()
                    ->withInput()
                    ->with('error', $error);
            }

            $data = $response->json();
            $message = $data['message'] ?? 'Successfully done allotment';

            return redirect()->route('shifting-allotment.cs')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Process CS Allotment Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process CS allotment.');
        }
    }

    /**
     * AJAX: Get VS vacancy and applicant counts
     */
    public function getVsCountsAjax(Request $request)
    {
        if (!$request->session()->has('user')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'rhe_id' => 'required|integer|min:1'
        ]);

        try {
            $token = $request->session()->get('api_token');

            // Fetch vacancy count
            $vacancyResponse = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/shifting-allotment/vs/vacancy-count', [
                    'rhe_id' => $request->input('rhe_id')
                ]);

            // Fetch applicant count
            $applicantResponse = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/shifting-allotment/vs/applicant-count', [
                    'rhe_id' => $request->input('rhe_id')
                ]);

            $vacancyCount = 0;
            $applicantCount = 0;

            if ($vacancyResponse->successful()) {
                $vacancyCount = $vacancyResponse->json('data.vacancy_count', 0);
            }

            if ($applicantResponse->successful()) {
                $applicantCount = $applicantResponse->json('data.applicant_count', 0);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'vacancy_count' => $vacancyCount,
                    'applicant_count' => $applicantCount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get VS Counts AJAX Error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch counts'
            ], 500);
        }
    }

    /**
     * AJAX: Get CS vacancy and applicant counts
     */
    public function getCsCountsAjax(Request $request)
    {
        if (!$request->session()->has('user')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'rhe_id' => 'required|integer|min:1'
        ]);

        try {
            $token = $request->session()->get('api_token');

            // Fetch vacancy count
            $vacancyResponse = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/shifting-allotment/cs/vacancy-count', [
                    'rhe_id' => $request->input('rhe_id')
                ]);

            // Fetch applicant count
            $applicantResponse = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/shifting-allotment/cs/applicant-count', [
                    'rhe_id' => $request->input('rhe_id')
                ]);

            $vacancyCount = 0;
            $applicantCount = 0;

            if ($vacancyResponse->successful()) {
                $vacancyCount = $vacancyResponse->json('data.vacancy_count', 0);
            }

            if ($applicantResponse->successful()) {
                $applicantCount = $applicantResponse->json('data.applicant_count', 0);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'vacancy_count' => $vacancyCount,
                    'applicant_count' => $applicantCount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get CS Counts AJAX Error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch counts'
            ], 500);
        }
    }
}
