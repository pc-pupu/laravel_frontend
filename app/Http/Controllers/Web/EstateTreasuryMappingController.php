<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Helpers\UrlEncryptionHelper;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class EstateTreasuryMappingController extends Controller
{
    private string $backend;

    public function __construct()
    {
        $this->backend = rtrim(env('BACKEND_API'), '/');
    }

    /**
     * List all estate treasury mappings
     */
    public function index(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/estate-treasury-mapping', $request->query());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to load estate treasury mappings.');
        }

        $payload = $response->json('data') ?? [];
        $collection = new Collection($payload['data'] ?? []);

        $mappings = new LengthAwarePaginator(
            $collection,
            $payload['total'] ?? $collection->count(),
            $payload['per_page'] ?? 15,
            $payload['current_page'] ?? 1,
            [
                'path'  => url()->current(),
                'query' => $request->query(),
            ]
        );

        return view('housingTheme.estate-treasury-mapping.index', [
            'mappings' => $mappings,
            'filters' => $request->only(['search', 'is_active']),
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        // Fetch estates and treasuries for dropdowns
        $estatesResponse = $this->authorizedRequest()
            ->get($this->backend . '/api/estate-treasury-mapping-helpers/estates');

        $treasuriesResponse = $this->authorizedRequest()
            ->get($this->backend . '/api/estate-treasury-mapping-helpers/treasuries');

        $estates = [];
        $treasuries = [];

        if ($estatesResponse->successful()) {
            $estates = $estatesResponse->json('data') ?? [];
        }

        if ($treasuriesResponse->successful()) {
            $treasuries = $treasuriesResponse->json('data') ?? [];
        }

        return view('housingTheme.estate-treasury-mapping.create', [
            'estates' => $estates,
            'treasuries' => $treasuries,
        ]);
    }

    /**
     * Store new estate treasury mapping
     */
    public function store(Request $request)
    {
        $response = $this->authorizedRequest()
            ->post($this->backend . '/api/estate-treasury-mapping', $request->all());

        if (!$response->successful()) {
            $errors = $response->json('errors') ?? [];
            $message = $response->json('message') ?? 'Failed to create estate treasury mapping.';
            return back()->withInput()->withErrors($errors)->with('error', $message);
        }

        return redirect()->route('estate-treasury-selection.index')
            ->with('success', $response->json('message') ?? 'Estate treasury mapping created successfully.');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $decryptedId = UrlEncryptionHelper::decryptUrl($id);

        $mappingResponse = $this->authorizedRequest()
            ->get($this->backend . '/api/estate-treasury-mapping/' . $decryptedId);

        if (!$mappingResponse->successful()) {
            return redirect()->route('estate-treasury-selection.index')
                ->with('error', 'Estate treasury mapping not found.');
        }

        $mapping = $mappingResponse->json('data');

        // Fetch estates and treasuries for dropdowns
        $estatesResponse = $this->authorizedRequest()
            ->get($this->backend . '/api/estate-treasury-mapping-helpers/estates');

        $treasuriesResponse = $this->authorizedRequest()
            ->get($this->backend . '/api/estate-treasury-mapping-helpers/treasuries');

        $estates = [];
        $treasuries = [];

        if ($estatesResponse->successful()) {
            $estates = $estatesResponse->json('data') ?? [];
        }

        if ($treasuriesResponse->successful()) {
            $treasuries = $treasuriesResponse->json('data') ?? [];
        }

        return view('housingTheme.estate-treasury-mapping.edit', [
            'mapping' => $mapping,
            'estates' => $estates,
            'treasuries' => $treasuries,
            'encryptedId' => $id,
        ]);
    }

    /**
     * Update estate treasury mapping
     */
    public function update(Request $request, $id)
    {
        $decryptedId = UrlEncryptionHelper::decryptUrl($id);

        $response = $this->authorizedRequest()
            ->put($this->backend . '/api/estate-treasury-mapping/' . $decryptedId, $request->all());

        if (!$response->successful()) {
            $errors = $response->json('errors') ?? [];
            $message = $response->json('message') ?? 'Failed to update estate treasury mapping.';
            return back()->withInput()->withErrors($errors)->with('error', $message);
        }

        return redirect()->route('estate-treasury-selection.index')
            ->with('success', $response->json('message') ?? 'Estate treasury mapping updated successfully.');
    }

    /**
     * Delete estate treasury mapping
     */
    public function destroy($id)
    {
        $decryptedId = UrlEncryptionHelper::decryptUrl($id);

        $response = $this->authorizedRequest()
            ->delete($this->backend . '/api/estate-treasury-mapping/' . $decryptedId);

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to delete estate treasury mapping.');
        }

        return redirect()->route('estate-treasury-selection.index')
            ->with('success', $response->json('message') ?? 'Estate treasury mapping deleted successfully.');
    }

    protected function authorizedRequest()
    {
        $token = session('api_token');
        return Http::acceptJson()->withToken($token);
    }
}

