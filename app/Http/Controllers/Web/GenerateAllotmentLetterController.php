<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateAllotmentLetterController extends Controller
{
    protected $backend;

    public function __construct()
    {
        $this->backend = config('services.api.base_url');
    }

    /**
     * Display the generate allotment letter form
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
            $flatTypesResponse = $httpClient->get($this->backend . '/api/generate-allotment-letter/flat-types');
            $flatTypes = $flatTypesResponse->successful() ? $flatTypesResponse->json('data') : [];

            $flatTypeId = $request->get('flat_type_id');
            $waitingList = [];

            $flatTypeName = null;
            if ($flatTypeId) {
                // Get waiting list
                $waitingListResponse = $httpClient->get($this->backend . '/api/generate-allotment-letter/waiting-list', [
                    'flat_type_id' => $flatTypeId
                ]);
                $waitingList = $waitingListResponse->successful() ? $waitingListResponse->json('data') : [];
                $flatTypeName = $waitingListResponse->successful() ? $waitingListResponse->json('flat_type') : null;
            }

            return view('housingTheme.generate-allotment-letter.index', [
                'flatTypes' => $flatTypes,
                'flatTypeId' => $flatTypeId,
                'flatTypeName' => $flatTypeName,
                'waitingList' => $waitingList
            ]);

        } catch (\Exception $e) {
            Log::error('Generate Allotment Letter Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')->with('error', 'Failed to load generate allotment letter page.');
        }
    }

    /**
     * Generate allotment letter
     */
    public function generate(Request $request)
    {
        try {
            $request->validate([
                'flat_id' => 'required|integer',
                'online_application_id' => 'required|integer',
                'flat_type' => 'required|string',
                'roaster_counter' => 'required|integer',
                'list_no' => 'required|integer',
                'flat_type_id' => 'required|integer'
            ]);

            $token = $request->session()->get('api_token');
            
            if (!$token) {
                return redirect()->back()->with('error', 'Authentication required.');
            }

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($this->backend . '/api/generate-allotment-letter/generate', [
                    'flat_id' => $request->flat_id,
                    'online_application_id' => $request->online_application_id,
                    'flat_type' => $request->flat_type,
                    'roaster_counter' => $request->roaster_counter,
                    'list_no' => $request->list_no
                ]);

            if ($response->successful()) {
                return redirect()->route('generate-allotment-letter.index', ['flat_type_id' => $request->flat_type_id])
                    ->with('success', 'Allotment letter generated successfully!');
            } else {
                $errorMessage = $response->json('message') ?? 'Failed to generate allotment letter.';
                return redirect()->back()->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('Generate Allotment Letter Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'An error occurred while generating the allotment letter.');
        }
    }
}

