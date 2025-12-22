<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ExistingOccupantController extends Controller
{
    private string $backend;

    public function __construct()
    {
        $this->backend = rtrim(env('BACKEND_API'), '/');
    }

    public function index(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-occupants', $request->query());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to load existing occupants.');
        }

        $payload = $response->json('data') ?? [];
        $collection = new Collection($payload['data'] ?? []);

        $occupants = new LengthAwarePaginator(
            $collection,
            $payload['total'] ?? $collection->count(),
            $payload['per_page'] ?? 15,
            $payload['current_page'] ?? 1,
            [
                'path'  => url()->current(),
                'query' => $request->query(),
            ]
        );

        return view('housingTheme.existing-occupant.index', [
            'occupants' => $occupants,
            'filters'   => $request->only(['search', 'has_hrms']),
        ]);
    }

    public function withHrms(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-occupants/with-hrms', $request->query());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to load occupants.');
        }

        $payload = $response->json('data') ?? [];
        $collection = new Collection($payload['data'] ?? []);

        $occupants = new LengthAwarePaginator(
            $collection,
            $payload['total'] ?? $collection->count(),
            $payload['per_page'] ?? 15,
            $payload['current_page'] ?? 1,
            [
                'path'  => url()->current(),
                'query' => $request->query(),
            ]
        );

        return view('housingTheme.existing-occupant.index', [
            'occupants' => $occupants,
            'filters'   => $request->only(['search']),
            'title'     => 'Existing Occupant List with HRMS ID',
        ]);
    }

    public function withoutHrms(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-occupants/without-hrms', $request->query());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to load occupants.');
        }

        $payload = $response->json('data') ?? [];
        $collection = new Collection($payload['data'] ?? []);

        $occupants = new LengthAwarePaginator(
            $collection,
            $payload['total'] ?? $collection->count(),
            $payload['per_page'] ?? 15,
            $payload['current_page'] ?? 1,
            [
                'path'  => url()->current(),
                'query' => $request->query(),
            ]
        );

        return view('housingTheme.existing-occupant.index', [
            'occupants' => $occupants,
            'filters'   => $request->only(['search']),
            'title'     => 'Existing Occupant List without HRMS ID',
        ]);
    }

    public function flatList(Request $request)
    {
        // Fetch RHE list for dropdown
        // The backend API should automatically filter by authenticated user's division/subdiv
        // For now, we'll fetch all RHEs - backend can be updated to filter by auth user later
        $rheResponse = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-occupants/meta/rhe-list');

        $rheList = [];
        if ($rheResponse->successful()) {
            $rheList = $rheResponse->json('data') ?? [];
        }

        return view('housingTheme.existing-occupant.flat-list', [
            'rheList' => $rheList,
        ]);
    }

    public function create($flatId = null)
    {
        $flatDetails = null;
        $metaData = [];

        if ($flatId) {
            // flatId might be encrypted or plain, try both
            try {
                $decryptedFlatId = decrypt($flatId);
            } catch (\Exception $e) {
                // If decryption fails, assume it's already plain
                $decryptedFlatId = $flatId;
            }
            
            $flatResponse = $this->authorizedRequest()
                ->get($this->backend . '/api/existing-occupants/flat/' . $decryptedFlatId . '/details');

            if ($flatResponse->successful()) {
                $flatDetails = $flatResponse->json('data');
            }
        }

        // Fetch meta data for dropdowns
        $districtsResponse = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-occupants/meta/districts');
        if ($districtsResponse->successful()) {
            $metaData['districts'] = $districtsResponse->json('data') ?? [];
        }

        $ddoResponse = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-occupants/meta/ddo-list');
        if ($ddoResponse->successful()) {
            $metaData['ddos'] = $ddoResponse->json('data') ?? [];
        }

        $payBandsResponse = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-occupants/meta/pay-bands');
        if ($payBandsResponse->successful()) {
            $metaData['payBands'] = $payBandsResponse->json('data') ?? [];
        }

        return view('housingTheme.existing-occupant.create', [
            'flatDetails' => $flatDetails,
            'flatId' => $flatId,
            'metaData' => $metaData,
        ]);
    }

    public function store(Request $request)
    {
        $payload = $request->except(['_token']);

        $response = $this->authorizedRequest()
            ->post($this->backend . '/api/existing-occupants', $payload);

        if ($response->status() === 422) {
            $errors = $response->json('errors') ?? [];
            $message = $response->json('message') ?? 'Validation failed.';
            return back()->withErrors($errors)->withInput()->with('error', $message);
        }

        if (!$response->successful()) {
            return back()->withInput()->with('error', $response->json('message') ?? 'Failed to add occupant.');
        }

        return redirect()->route('existing-occupant.index')->with('success', 'Occupant added successfully.');
    }

    public function view($id)
    {
        $decryptedId = decrypt($id);
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-occupants/' . $decryptedId);

        if (!$response->successful()) {
            return redirect()->route('existing-occupant.index')
                ->with('error', $response->json('message') ?? 'Occupant not found.');
        }

        return view('housingTheme.existing-occupant.view', [
            'occupant' => $response->json('data'),
        ]);
    }

    public function edit($id)
    {
        $decryptedId = decrypt($id);
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-occupants/' . $decryptedId);

        if (!$response->successful()) {
            return redirect()->route('existing-occupant.index')
                ->with('error', $response->json('message') ?? 'Occupant not found.');
        }

        // Fetch meta data
        $metaData = [];
        $districtsResponse = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-occupants/meta/districts');
        if ($districtsResponse->successful()) {
            $metaData['districts'] = $districtsResponse->json('data') ?? [];
        }

        $ddoResponse = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-occupants/meta/ddo-list');
        if ($ddoResponse->successful()) {
            $metaData['ddos'] = $ddoResponse->json('data') ?? [];
        }

        return view('housingTheme.existing-occupant.edit', [
            'occupant' => $response->json('data'),
            'metaData' => $metaData,
        ]);
    }

    public function update(Request $request, $id)
    {
        $decryptedId = decrypt($id);
        $payload = $request->except(['_token', '_method']);

        $response = $this->authorizedRequest()
            ->put($this->backend . '/api/existing-occupants/' . $decryptedId, $payload);

        if ($response->status() === 422) {
            $errors = $response->json('errors') ?? [];
            $message = $response->json('message') ?? 'Validation failed.';
            return back()->withErrors($errors)->withInput()->with('error', $message);
        }

        if (!$response->successful()) {
            return back()->withInput()->with('error', $response->json('message') ?? 'Failed to update occupant.');
        }

        return redirect()->route('existing-occupant.index')->with('success', 'Occupant updated successfully.');
    }

    public function indexDraft(Request $request)
    {
        // Similar to index but for draft entries
        return $this->index($request);
    }

    public function createDraft($flatId = null)
    {
        // Similar to create but for draft entries
        return $this->create($flatId);
    }

    public function storeDraft(Request $request, $flatId)
    {
        // Similar to store but for draft entries
        return $this->store($request);
    }

    public function viewDraft($id)
    {
        // Similar to view but for draft entries
        return $this->view($id);
    }

    public function editDraft($id)
    {
        // Similar to edit but for draft entries
        return $this->edit($id);
    }

    public function updateDraft(Request $request, $id)
    {
        // Similar to update but for draft entries
        return $this->update($request, $id);
    }

    public function destroy($type, $id, $flatId)
    {
        $decryptedId = decrypt($id);
        $decryptedFlatId = decrypt($flatId);
        
        $response = $this->authorizedRequest()
            ->delete($this->backend . '/api/existing-occupants/' . $decryptedId, [
                'type' => $type,
                'flat_id' => $decryptedFlatId
            ]);

        if (!$response->successful()) {
            $redirectRoute = $type === 'draft' ? 'existing-occupant.without-hrms' : 'existing-occupant.with-hrms';
            return redirect()->route($redirectRoute)
                ->with('error', $response->json('message') ?? 'Failed to delete occupant.');
        }

        $redirectRoute = $type === 'draft' ? 'existing-occupant.without-hrms' : 'existing-occupant.with-hrms';
        return redirect()->route($redirectRoute)->with('success', 'Occupant deleted successfully.');
    }

    protected function authorizedRequest()
    {
        $token = session('api_token');
        return Http::acceptJson()->withToken($token);
    }
}

