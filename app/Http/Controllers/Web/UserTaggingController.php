<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class UserTaggingController extends Controller
{
    /**
     * Show User Tagging Form
     * GET /user-tagging
     */
    public function create(Request $request)
    {
        // Check if user is logged in
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $hrmsId = $user['name'];
        $uid = $user['uid'];

        // Check if user has already submitted a tagging request
        $hasSubmitted = $this->checkExistingSubmission($uid);
        // dd($user);

        // Get HRMS login log data (if available)
        $hrmsData = $this->getHRMSLogData($hrmsId);

        return view('userSso.user-tagging-form', [
            'hrmsData' => $hrmsData,
            'user' => $user,
            'hasSubmitted' => $hasSubmitted
        ]);
    }

    /**
     * Submit User Tagging Form
     * POST /user-tagging
     */
    public function store(Request $request)
    {
        $request->validate([
            'applicant_name' => 'required|string|max:255',
            'mobile_no' => ['required', 'regex:/^[0-9]{10}$/'],
            'email' => 'required|email|max:255',
            'license_no' => 'required|string|max:255',
            'license_issue_date' => 'required|date_format:d/m/Y',
            'license_expiry_date' => 'nullable|date_format:d/m/Y',
            'physical_application_vs_cs' => 'required|in:yes,no',
            'physical_application_no' => 'nullable|string',
            'application_type' => 'nullable|in:VS,CS',
            'rhe_name' => 'required|integer',
            'flat_type' => 'required|integer',
            'block_name' => 'required|integer',
            'floor_no' => 'required',
            'flat_no' => 'required|integer',
            'flat_id' => 'required|integer',
        ], [
            'mobile_no.regex' => 'Mobile number must be exactly 10 digits.',
        ]);

        // Custom validation for license expiry date (max 3 years from issue date)
        if ($request->filled('license_expiry_date') && $request->filled('license_issue_date')) {
            $issueDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->license_issue_date);
            $expiryDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->license_expiry_date);

            // Check if expiry is after issue
            if ($expiryDate->lte($issueDate)) {
                return back()->withErrors(['license_expiry_date' => 'License expiry date must be after issue date.'])->withInput();
            }

            // Calculate difference
            $interval = $issueDate->diff($expiryDate);
            
            // Check if more than 3 years OR exactly 3 years with additional months/days
            if ($interval->y > 3 || ($interval->y == 3 && ($interval->m > 0 || $interval->d > 0))) {
                return back()->withErrors(['license_expiry_date' => 'The license expiry date cannot be more than 3 years after the issue date.'])->withInput();
            }
        }

        $user = $request->session()->get('user');

        // Convert dates to Y-m-d format
        $licenseIssueDate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->license_issue_date)->format('Y-m-d');
        $licenseExpiryDate = $request->license_expiry_date ? 
            \Carbon\Carbon::createFromFormat('d/m/Y', $request->license_expiry_date)->format('Y-m-d') : null;

        try {
            // Call backend API
            $response = Http::post(config('services.api.base_url') . '/user-tagging/submit', [
                'applicant_name' => $request->applicant_name,
                'mobile_no' => $request->mobile_no,
                'email' => $request->email,
                'license_no' => $request->license_no,
                'license_issue_date' => $licenseIssueDate,
                'license_expiry_date' => $licenseExpiryDate,
                'physical_application_vs_cs' => $request->physical_application_vs_cs,
                'physical_application_no' => $request->physical_application_no,
                'application_type' => $request->application_type,
                'flat_id' => $request->flat_id,
                'uid' => $user['uid'],
                'hrms_id' => $user['name'],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return back()->with('success', $data['message']);
            } else {
                $error = $response->json()['message'] ?? 'Failed to submit tagging request';
                return back()->with('error', $error)->withInput();
            }

        } catch (\Exception $e) {
            Log::error('User Tagging Submit Error', [
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Something went wrong. Please try again.')->withInput();
        }
    }

    /**
     * Show Flat-wise User Info List (Admin)
     * GET /flat-wise-user-info
     */
    public function flatWiseUserInfo(Request $request)
    {
        try {
            // Call backend API
            $response = Http::get(config('services.api.base_url') . '/user-tagging/list');

            if ($response->successful()) {
                $data = $response->json();
                return view('userSso.flat-wise-user-info', [
                    'data' => $data['data'] ?? []
                ]);
            } else {
                return view('userSso.flat-wise-user-info', [
                    'data' => [],
                    'error' => 'Failed to fetch data'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Flat-wise User Info Error', [
                'error' => $e->getMessage()
            ]);
            return view('userSso.flat-wise-user-info', [
                'data' => [],
                'error' => 'Something went wrong'
            ]);
        }
    }

    /**
     * Show Flat-wise User Details (Admin Approval Form)
     * GET /flat-wise-user-info-details/{flat_id}
     */
    public function flatWiseUserDetails(Request $request, $encryptedFlatId)
    {
        try {
            // Decrypt flat ID
            $flatId = UrlEncryptionHelper::decryptUrl($encryptedFlatId);

            // Call backend API
            $response = Http::get(config('services.api.base_url') . '/user-tagging/details/' . $flatId);

            if ($response->successful()) {
                $data = $response->json();
                return view('userSso.flat-wise-user-details', [
                    'data' => $data['data'],
                    'flatId' => $flatId,
                    'encryptedFlatId' => $encryptedFlatId
                ]);
            } else {
                return redirect()->route('user-tagging.flat-wise-user-info')
                    ->with('error', 'Failed to fetch details');
            }

        } catch (\Exception $e) {
            Log::error('Flat-wise User Details Error', [
                'error' => $e->getMessage()
            ]);
            return redirect()->route('user-tagging.flat-wise-user-info')
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Update Tagging Status (Admin Action)
     * POST /flat-wise-user-info-details/{flat_id}
     */
    public function updateStatus(Request $request, $encryptedFlatId)
    {
        $request->validate([
            'action' => 'required|in:tagged,reject,pending',
            'remarks' => 'required|string',
            'flat_id' => 'required|integer',
            'housing_user_tagging_id' => 'required|integer',
        ]);

        try {
            // Prepare form info array from hidden fields
            $formInfoArray = [
                'name' => $request->input('applicant_name'),
                'mobile' => $request->input('mobile_no'),
                'hrmsid' => $request->input('hrms_id'),
                'user_id' => $request->input('user_id'),
                'housing_existing_occupant_draft_id' => $request->input('housing_existing_occupant_draft_id'),
                'license_no' => $request->input('license_no'),
                'license_issue_date' => $request->input('license_issue_date'),
                'license_expiry_date' => $request->input('license_expiry_date'),
                'authorised_or_not' => $request->input('authorised_or_not'),
                'draft_ddo_id' => $request->input('draft_ddo_id'),
                'draft_pay_band_id' => $request->input('draft_pay_band_id'),
            ];

            // Call backend API
            $response = Http::post(config('services.api.base_url') . '/user-tagging/update-status', [
                'action' => $request->action,
                'flat_id' => $request->flat_id,
                'housing_user_tagging_id' => $request->housing_user_tagging_id,
                'remarks' => $request->remarks,
                'form_info_array' => $formInfoArray,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return redirect()->route('user-tagging.flat-wise-user-info')
                    ->with('success', $data['message']);
            } else {
                $error = $response->json()['message'] ?? 'Failed to update status';
                return back()->with('error', $error);
            }

        } catch (\Exception $e) {
            Log::error('Update Tagging Status Error', [
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Something went wrong');
        }
    }

    /**
     * Get HRMS Log Data
     */
    private function getHRMSLogData($hrmsId)
    {
        try {
            // This would fetch from housing_hrms_applicant_login_log table
            // For now, return empty array
            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Check if user has already submitted a tagging request
     */
    private function checkExistingSubmission($uid)
    {
        try {
            // Call backend API to check for existing submission
            $response = Http::get(config('services.api.base_url') . '/user-tagging/check-submission/' . $uid);

            if ($response->successful()) {
                $data = $response->json();
                return $data['has_submitted'] ?? false;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Check Existing Submission Error', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

