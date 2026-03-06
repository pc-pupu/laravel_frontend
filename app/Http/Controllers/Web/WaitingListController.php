<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class WaitingListController extends Controller
{
    private string $backend;

    public function __construct()
    {
        $this->backend = rtrim(env('BACKEND_API'), '/');
    }

    /**
     * Flat Type Wise Waiting List page (RHE Allotment -> "Flat Type Wise Waiting List")
     * GET /flat_type_waiting_list
     */
    public function flatTypeWaitingList(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $selectedFlatTypeId = (int) $request->input('flat_type_id', 0);

        try {
            $token = session('api_token');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($this->backend . '/api/waiting-list/flat-type', [
                'flat_type_id' => $selectedFlatTypeId ?: null,
            ]);

            if (!$response->successful()) {
                $message = $response->json('message') ?? 'Failed to load waiting list.';
                return view('housingTheme.waiting-list.flat-type', [
                    'flatTypes' => [],
                    'rows' => [],
                    'selectedFlatTypeId' => $selectedFlatTypeId,
                    'error' => $message,
                ]);
            }

            $data = $response->json('data') ?? [];
            $flatTypes = $data['flat_types'] ?? [];
            $rows = $data['rows'] ?? [];

            // Pre-encrypt online_application_id for links
            foreach ($rows as &$row) {
                if (!empty($row['online_application_id'])) {
                    $row['encrypted_online_application_id'] = UrlEncryptionHelper::encryptUrl($row['online_application_id']);
                }
            }

            return view('housingTheme.waiting-list.flat-type', [
                'flatTypes' => $flatTypes,
                'rows' => $rows,
                'selectedFlatTypeId' => $selectedFlatTypeId,
                'error' => null,
            ]);

        } catch (\Exception $e) {
            Log::error('Flat Type Waiting List Page Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('housingTheme.waiting-list.flat-type', [
                'flatTypes' => [],
                'rows' => [],
                'selectedFlatTypeId' => $selectedFlatTypeId,
                'error' => 'An error occurred while loading the waiting list.',
            ]);
        }
    }
}

