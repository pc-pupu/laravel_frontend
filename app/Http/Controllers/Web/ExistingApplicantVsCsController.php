<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;
use Illuminate\Support\Facades\Crypt;

class ExistingApplicantVsCsController extends Controller
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
     * Flat-wise existing applicant details form
     */
    public function flatWiseForm(Request $request)
    {
        return view('housingTheme.existing-applicant-vs-cs.flat-wise-form');
    }

    /**
     * Get flat applicant details (AJAX)
     */
    public function getFlatDetails(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-applicant-vs-cs/flat-details', $request->query());

        return response()->json($response->json());
    }

    /**
     * VS/CS application form
     */
public function create(Request $request, $uid)
{
    Log::info("UID received", ['uid' => $uid]);

    try {
        $decryptedUid = Crypt::decryptString($uid);
    } catch (\Exception $e) {
        Log::error("Decryption failed", [
            "uid" => $uid,
            "error" => $e->getMessage()
        ]);

        return redirect()
            ->route('existing-applicant-vs-cs.flat-wise-form')
            ->with("error", "Invalid applicant ID.");
    }

    Log::info("Decrypted UID OK", ['decrypted' => $decryptedUid]);

    $response = $this->authorizedRequest()
        ->get($this->backend . '/api/existing-applicants/' . $decryptedUid);

    return view('housingTheme.existing-applicant-vs-cs.create', [
        'uid' => $uid,
        'applicantData' => $response->json('data'),
    ]);
}


    /**
     * Store VS/CS application
     */
    public function store(Request $request, $uid = null)
    {
        // If uid is provided in route, add it to request
        if ($uid) {
            $request->merge(['housing_hidden_uid_or_draft_id' => decrypt($uid)]);
        }
        
        $response = $this->authorizedRequest()
            ->post($this->backend . '/api/existing-applicant-vs-cs', $request->all());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to submit application.')
                ->withInput()
                ->withErrors($response->json('errors') ?? []);
        }

        return redirect()->route('existing-applicant-vs-cs.flat-wise-form')
            ->with('success', 'Application submitted successfully.');
    }

    /**
     * VS List (with HRMS)
     */
    public function vsListWithHrms(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-applicant-vs-cs/vs-list-with-hrms', $request->query());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to load VS applications.');
        }

        $applications = $response->json('data') ?? [];

        return view('housingTheme.existing-applicant-vs-cs.vs-list-with-hrms', [
            'applications' => $applications,
            'filters' => $request->only(['rhe_name', 'flat_type']),
        ]);
    }

    /**
     * VS List (without HRMS)
     */
    public function vsListWithoutHrms(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-applicant-vs-cs/vs-list-without-hrms', $request->query());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to load VS applications.');
        }

        $applications = $response->json('data') ?? [];

        return view('housingTheme.existing-applicant-vs-cs.vs-list-without-hrms', [
            'applications' => $applications,
            'filters' => $request->only(['rhe_name', 'flat_type']),
        ]);
    }

    /**
     * CS List (with HRMS)
     */
    public function csListWithHrms(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-applicant-vs-cs/cs-list-with-hrms', $request->query());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to load CS applications.');
        }

        $applications = $response->json('data') ?? [];

        return view('housingTheme.existing-applicant-vs-cs.cs-list-with-hrms', [
            'applications' => $applications,
            'filters' => $request->only(['rhe_name', 'flat_type']),
        ]);
    }

    /**
     * CS List (without HRMS)
     */
    public function csListWithoutHrms(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-applicant-vs-cs/cs-list-without-hrms', $request->query());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to load CS applications.');
        }

        $applications = $response->json('data') ?? [];

        return view('housingTheme.existing-applicant-vs-cs.cs-list-without-hrms', [
            'applications' => $applications,
            'filters' => $request->only(['rhe_name', 'flat_type']),
        ]);
    }

    /**
     * Edit VS/CS application
     */
    public function edit(Request $request, $id)
    {
        try {
            $decryptedId = decrypt($id);
        } catch (\Exception $e) {
            return back()->with('error', 'Invalid application ID.');
        }

        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/existing-applicant-vs-cs/' . $decryptedId);

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to load application.');
        }

        $application = $response->json('data');

        return view('housingTheme.existing-applicant-vs-cs.edit', [
            'id' => $id,
            'application' => $application,
        ]);
    }

    /**
     * Update VS/CS application
     */
    public function update(Request $request, $id)
    {
        try {
            $decryptedId = decrypt($id);
        } catch (\Exception $e) {
            return back()->with('error', 'Invalid application ID.');
        }

        $response = $this->authorizedRequest()
            ->put($this->backend . '/api/existing-applicant-vs-cs/' . $decryptedId, $request->all());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to update application.')
                ->withInput()
                ->withErrors($response->json('errors') ?? []);
        }

        return redirect()->route('existing-applicant-vs-cs.flat-wise-form')
            ->with('success', 'Application updated successfully.');
    }
}

