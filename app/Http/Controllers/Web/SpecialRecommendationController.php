<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class SpecialRecommendationController extends Controller
{
    protected $backend;

    public function __construct()
    {
        $this->backend = config('services.api.base_url');
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
     * Housing Approver List
     * GET /housing-approver-list
     */
    public function housingApproverList(Request $request)
    {
        try {
            $typeOfCategory = $request->input('type_of_category');
            $flatType = $request->input('flat_type');

            $response = $this->authorizedRequest()
                ->get($this->backend . '/special-recommendation/housing-approver-list', [
                    'type_of_category' => $typeOfCategory,
                    'flat_type' => $flatType,
                ]);

            $applications = [];
            if ($response->successful()) {
                $applications = $response->json('data') ?? [];
            } else {
                // Log the API error response
                Log::error('Housing Approver List API Error', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                    'body' => $response->body(),
                ]);
                
                return redirect()->route('dashboard')
                    ->with('error', 'Failed to load housing approver list: ' . ($response->json('message') ?? 'API request failed'));
            }

            // Get allotment categories and flat types for filters
            $allotmentCategories = [
                '' => '-Select-',
                'General' => 'General',
                'Judicial Officer On Transfer' => 'Judicial Officer On Transfer',
                'Transfer' => 'Transfer',
                'Recommended' => 'Recommended',
                'Physically Handicaped Or Serious Illness' => 'Physically Handicaped Or Serious Illness',
                'Single Earning Lady' => 'Single Earning Lady',
                'Legal Heir' => 'Legal Heir',
            ];

            $flatTypes = [
                '' => '-Select-',
                '5' => 'A+',
                '1' => 'A',
                '2' => 'B',
                '3' => 'C',
                '4' => 'D',
            ];

            return view('housingTheme.special-recommendation.housing-approver-list', [
                'applications' => $applications,
                'allotmentCategories' => $allotmentCategories,
                'flatTypes' => $flatTypes,
                'selectedCategory' => $typeOfCategory,
                'selectedFlatType' => $flatType,
            ]);

        } catch (\Exception $e) {
            Log::error('Housing Approver List Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load housing approver list: ' . $e->getMessage());
        }
    }

    /**
     * Add to Special Recommendation
     * GET /special-recommendation/{encrypted_online_application_id}/{encrypted_allotment_category}
     */
    public function addToSpecialRecommendation($encryptedOnlineApplicationId, $encryptedAllotmentCategory)
    {
        try {
            $onlineApplicationId = UrlEncryptionHelper::decryptUrl($encryptedOnlineApplicationId);
            $allotmentCategory = UrlEncryptionHelper::decryptUrl($encryptedAllotmentCategory);

            $response = $this->authorizedRequest()
                ->post($this->backend . '/special-recommendation/add', [
                    'online_application_id' => $onlineApplicationId,
                    'allotment_category' => $allotmentCategory,
                ]);

            if ($response->successful()) {
                return redirect()->route('special-recommendation.housing-approver-list')
                    ->with('success', $response->json('message') ?? 'Special Recommendation is Successful.');
            }

            return redirect()->route('special-recommendation.housing-approver-list')
                ->with('error', $response->json('message') ?? 'Failed to add to special recommendation.');

        } catch (\Exception $e) {
            Log::error('Add to Special Recommendation Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('special-recommendation.housing-approver-list')
                ->with('error', 'An error occurred while adding to special recommendation.');
        }
    }

    /**
     * Remove from Special Recommendation
     * GET /special-recommendation-remove/{encrypted_online_application_id}
     */
    public function removeFromSpecialRecommendation($encryptedOnlineApplicationId)
    {
        try {
            $onlineApplicationId = UrlEncryptionHelper::decryptUrl($encryptedOnlineApplicationId);

            $response = $this->authorizedRequest()
                ->post($this->backend . '/special-recommendation/remove', [
                    'online_application_id' => $onlineApplicationId,
                    'action' => 'delete',
                ]);

            if ($response->successful()) {
                return redirect()->route('special-recommendation.housing-approver-list')
                    ->with('success', $response->json('message') ?? 'Special Recommendation is Removed for the Application');
            }

            return redirect()->route('special-recommendation.housing-approver-list')
                ->with('error', $response->json('message') ?? 'Failed to remove from special recommendation.');

        } catch (\Exception $e) {
            Log::error('Remove from Special Recommendation Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('special-recommendation.housing-approver-list')
                ->with('error', 'An error occurred while removing from special recommendation.');
        }
    }

    /**
     * Special Recommendation List View (for editing priority)
     * GET /special-recommendation-list-view
     */
    public function listView()
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/special-recommendation/list-view');

            $applications = [];
            if ($response->successful()) {
                $applications = $response->json('data') ?? [];
            }

            return view('housingTheme.special-recommendation.list-view', [
                'applications' => $applications,
            ]);

        } catch (\Exception $e) {
            Log::error('Special Recommendation List View Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load special recommendation list.');
        }
    }

    /**
     * Update Priority Order
     * POST /special-recommendation-list-view
     */
    public function updatePriorityOrder(Request $request)
    {
        try {
            $request->validate([
                'online_application_ids' => 'required|string',
            ]);

            $response = $this->authorizedRequest()
                ->post($this->backend . '/special-recommendation/update-priority', [
                    'online_application_ids' => $request->online_application_ids,
                ]);

            if ($response->successful()) {
                return redirect()->route('special-recommendation.list-view')
                    ->with('success', $response->json('message') ?? 'Data Saved successfully.');
            }

            return redirect()->route('special-recommendation.list-view')
                ->with('error', $response->json('message') ?? 'Failed to update priority order.');

        } catch (\Exception $e) {
            Log::error('Update Priority Order Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('special-recommendation.list-view')
                ->with('error', 'An error occurred while updating priority order.');
        }
    }

    /**
     * Final Special Recommended List
     * GET /special-recommended-list
     */
    public function finalList()
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/special-recommendation/final-list');

            $applications = [];
            if ($response->successful()) {
                $applications = $response->json('data') ?? [];
            }

            return view('housingTheme.special-recommendation.final-list', [
                'applications' => $applications,
            ]);

        } catch (\Exception $e) {
            Log::error('Final Special Recommended List Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load final special recommended list.');
        }
    }

    /**
     * View Application Details
     * GET /view-app-det/{encrypted_online_application_id}
     */
    public function viewApplicationDetails($encryptedOnlineApplicationId)
    {
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/special-recommendation/view-details/' . $encryptedOnlineApplicationId);

            if (!$response->successful()) {
                return redirect()->back()
                    ->with('error', $response->json('message') ?? 'Failed to load application details.');
            }

            $application = $response->json('data');

            return view('housingTheme.special-recommendation.view-details', [
                'application' => $application,
            ]);

        } catch (\Exception $e) {
            Log::error('View Application Details Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to load application details.');
        }
    }

    /**
     * Convert to General Category
     * GET /convrt-to-gen-cat/{encrypted_online_application_id}
     */
    public function convertToGeneralCategory($encryptedOnlineApplicationId)
    {
        try {
            $onlineApplicationId = UrlEncryptionHelper::decryptUrl($encryptedOnlineApplicationId);

            $response = $this->authorizedRequest()
                ->post($this->backend . '/special-recommendation/convert-to-general', [
                    'online_application_id' => $onlineApplicationId,
                    'old_category' => 'Recommended',
                ]);

            if ($response->successful()) {
                return redirect()->route('special-recommendation.housing-approver-list')
                    ->with('success', $response->json('message') ?? 'Applicant converted to General category with same waiting list');
            }

            return redirect()->route('special-recommendation.housing-approver-list')
                ->with('error', $response->json('message') ?? 'Failed to convert to general category.');

        } catch (\Exception $e) {
            Log::error('Convert to General Category Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('special-recommendation.housing-approver-list')
                ->with('error', 'An error occurred while converting to general category.');
        }
    }

    /**
     * Show Manual Allotment Form
     * GET /special-recommendation-occupant/{encrypted_online_application_id}
     */
    public function showManualAllotmentForm($encryptedOnlineApplicationId)
    {
        try {
            $onlineApplicationId = UrlEncryptionHelper::decryptUrl($encryptedOnlineApplicationId);

            // Get RHE list
            $rheResponse = $this->authorizedRequest()
                ->get($this->backend . '/special-recommendation/helpers/rhe-list');

            $rheList = [];
            if ($rheResponse->successful()) {
                $rheList = $rheResponse->json('data') ?? [];
            }

            return view('housingTheme.special-recommendation.manual-allotment', [
                'onlineApplicationId' => $onlineApplicationId,
                'encryptedOnlineApplicationId' => $encryptedOnlineApplicationId,
                'rheList' => $rheList,
            ]);

        } catch (\Exception $e) {
            Log::error('Show Manual Allotment Form Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('special-recommendation.final-list')
                ->with('error', 'Failed to load manual allotment form.');
        }
    }

    /**
     * Submit Manual Allotment
     * POST /special-recommendation-occupant
     */
    public function submitManualAllotment(Request $request)
    {
        try {
            $request->validate([
                'online_application_id' => 'required|integer',
                'allotment_date' => 'required|string',
                'rhe_id' => 'required|integer',
                'flat_type_id' => 'required|integer',
                'block_id' => 'required|integer',
                'floor_no' => 'required|integer',
                'flat_id' => 'required|integer',
            ]);

            $user = $request->session()->get('user');
            $formData = $request->all();
            $formData['uid'] = $user['uid'] ?? null;

            $response = $this->authorizedRequest()
                ->post($this->backend . '/special-recommendation/manual-allotment', $formData);

            if ($response->successful()) {
                return redirect()->route('special-recommendation.final-list')
                    ->with('success', $response->json('message') ?? 'Flat has been successfully tagged for special recommendation.');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $response->json('message') ?? 'Failed to process manual allotment.');

        } catch (\Exception $e) {
            Log::error('Submit Manual Allotment Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while processing manual allotment.');
        }
    }

    /**
     * Get Flat Types (AJAX)
     */
    public function getFlatTypes(Request $request)
    {
        try {
            $rheId = $request->input('rhe_id');
            if (!$rheId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'RHE ID is required',
                ], 400);
            }

            $response = $this->authorizedRequest()
                ->get($this->backend . "/special-recommendation/helpers/flat-types/{$rheId}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch flat types',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Get Flat Types Error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Get Blocks (AJAX)
     */
    public function getBlocks(Request $request)
    {
        try {
            $rheId = $request->input('rhe_id');
            $flatTypeId = $request->input('flat_type_id');

            if (!$rheId || !$flatTypeId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'RHE ID and Flat Type ID are required',
                ], 400);
            }

            $response = $this->authorizedRequest()
                ->get($this->backend . "/special-recommendation/helpers/blocks/{$rheId}/{$flatTypeId}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch blocks',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Get Blocks Error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Get Floors (AJAX)
     */
    public function getFloors(Request $request)
    {
        try {
            $rheId = $request->input('rhe_id');
            $flatTypeId = $request->input('flat_type_id');
            $blockId = $request->input('block_id');

            if (!$rheId || !$flatTypeId || !$blockId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'RHE ID, Flat Type ID, and Block ID are required',
                ], 400);
            }

            $response = $this->authorizedRequest()
                ->get($this->backend . "/special-recommendation/helpers/floors/{$rheId}/{$flatTypeId}/{$blockId}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch floors',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Get Floors Error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Get Flats (AJAX)
     */
    public function getFlats(Request $request)
    {
        try {
            $rheId = $request->input('rhe_id');
            $flatTypeId = $request->input('flat_type_id');
            $blockId = $request->input('block_id');
            $floorNo = $request->input('floor_no');

            if (!$rheId || !$flatTypeId || !$blockId || !$floorNo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'All parameters are required',
                ], 400);
            }

            $response = $this->authorizedRequest()
                ->get($this->backend . "/special-recommendation/helpers/flats/{$rheId}/{$flatTypeId}/{$blockId}/{$floorNo}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch flats',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Get Flats Error', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
            ], 500);
        }
    }
}
