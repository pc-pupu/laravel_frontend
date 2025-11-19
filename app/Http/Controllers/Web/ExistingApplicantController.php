<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ExistingApplicantController extends Controller
{
    private string $backend;

    public function __construct()
    {
        $this->backend = rtrim(env('BACKEND_API'), '/');
    }

    public function index(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/admin/existing-applicants', $request->query());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to load existing applicants.');
        }

        $payload = $response->json('data') ?? [];
        $collection = new Collection($payload['data'] ?? []);

        $applicants = new LengthAwarePaginator(
            $collection,
            $payload['total'] ?? $collection->count(),
            $payload['per_page'] ?? 15,
            $payload['current_page'] ?? 1,
            [
                'path'  => url()->current(),
                'query' => $request->query(),
            ]
        );

        return view('housingTheme.existing-applicant.index', [
            'applicants' => $applicants,
            'filters'   => $request->only(['search', 'has_hrms']),
        ]);
    }

    public function withHrms(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/admin/existing-applicants/with-hrms', $request->query());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to load applicants.');
        }

        $payload = $response->json('data') ?? [];
        $collection = new Collection($payload['data'] ?? []);

        $applicants = new LengthAwarePaginator(
            $collection,
            $payload['total'] ?? $collection->count(),
            $payload['per_page'] ?? 15,
            $payload['current_page'] ?? 1,
            [
                'path'  => url()->current(),
                'query' => $request->query(),
            ]
        );

        return view('housingTheme.existing-applicant.index', [
            'applicants' => $applicants,
            'filters'   => $request->only(['search']),
            'title'     => 'Legacy Applicant List with HRMS ID',
        ]);
    }

    public function withoutHrms(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/admin/existing-applicants/without-hrms', $request->query());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to load applicants.');
        }

        $payload = $response->json('data') ?? [];
        $collection = new Collection($payload['data'] ?? []);

        $applicants = new LengthAwarePaginator(
            $collection,
            $payload['total'] ?? $collection->count(),
            $payload['per_page'] ?? 15,
            $payload['current_page'] ?? 1,
            [
                'path'  => url()->current(),
                'query' => $request->query(),
            ]
        );

        return view('housingTheme.existing-applicant.index', [
            'applicants' => $applicants,
            'filters'   => $request->only(['search']),
            'title'     => 'Legacy Applicant List without HRMS ID',
        ]);
    }

    public function search()
    {
        return view('housingTheme.existing-applicant.search');
    }

    public function searchSubmit(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/admin/existing-applicants/search', $request->only(['search_type', 'search_value']));

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'No matching application found.');
        }

        $data = $response->json('data');
        return redirect()->route('existing-applicant.view', encrypt($data['online_application_id']));
    }

    public function view($id)
    {
        $decryptedId = decrypt($id);
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/admin/existing-applicants/' . $decryptedId);

        if (!$response->successful()) {
            return redirect()->route('existing-applicant.index')
                ->with('error', $response->json('message') ?? 'Applicant not found.');
        }

        return view('housingTheme.existing-applicant.view', [
            'applicant' => $response->json('data'),
        ]);
    }

    public function create()
    {
        return view('housingTheme.existing-applicant.create');
    }

    public function edit($id)
    {
        $decryptedId = decrypt($id);
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/admin/existing-applicants/' . $decryptedId);

        if (!$response->successful()) {
            return redirect()->route('existing-applicant.index')
                ->with('error', $response->json('message') ?? 'Applicant not found.');
        }

        return view('housingTheme.existing-applicant.edit', [
            'applicant' => $response->json('data'),
        ]);
    }

    protected function authorizedRequest()
    {
        $token = session('api_token');
        return Http::acceptJson()->withToken($token);
    }
}

