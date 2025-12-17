<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\AuthEncryptionHelper;
use Illuminate\Support\Facades\Cookie;

class DashboardController extends Controller
{
    private string $backend;

    public function __construct()
    {
        $this->backend = rtrim(env('BACKEND_API'), '/');
    }

    public function __invoke(Request $request)
    {
        // Check if user is logged in
        if (!$request->session()->has('user')) {
            return redirect()->route('homepage')->with('error', 'Please login first');
        }

        // If coming from user-tagging page, set the cookie server-side (backup if JS cookie didn't work)
        $cookie = null;
        $referer = $request->header('Referer');
        if ($referer && str_contains($referer, 'user-tagging')) {
            // Check if cookie is already set, if not set it server-side
            if (!$request->cookie('user_type')) {
                $cookie = cookie('user_type', 'new', 60 * 24, '/', null, false, false, false, 'Lax');
            }
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];
        $username = $user['name'];

        // Get user role
        $userRole = DB::table('user_role')
            ->where('uid', $uid)
            ->orderBy('rid', 'ASC')
            ->value('rid');

        // Role-based dashboard content
        // Roles 4, 5: Applicant
        // Roles 6, 7, 8, 10, 11, 13, 17: Admin roles
        // Other roles: Default/CMS dashboard

        // Prepare response variable for cookie attachment
        $response = null;
        
        if (in_array($userRole, [4, 5])) {
            // Applicant Dashboard (SSO Dashboard logic)
            $response = $this->applicantDashboard($request, $user, $uid, $username, $userRole);
        } elseif (in_array($userRole, [6, 7, 8, 10, 11, 13, 17])) {
            // Admin Dashboard (SSO Dashboard logic for admin roles)
            $response = $this->adminDashboard($request, $user, $uid, $username, $userRole);
        } else {
            // Default/CMS Dashboard (original logic)
            $response = $this->defaultDashboard($request);
        }

        // Attach cookie if it was set
        if ($cookie !== null) {
            return $response->withCookie($cookie);
        }

        return $response;
    }


    private function applicantDashboard(Request $request, $user, $uid, $username, $userRole)
    {
        $output = [];
        $output['user_role'] = $userRole;
        /* ======================================================
        | ROLE 4 & 5 (Applicant)
        ======================================================*/
        if (in_array($userRole, [4, 5])) {

            /* ---------------- ROLE 4 ONLY ---------------- */
            if ($userRole == 4) {

                $userType = $request->cookie('user_type');

                if ($userType === 'new') {

                    // clear cookie and continue
                    Cookie::queue(Cookie::forget('user_type'));

                } else {
                    // print_r($user);die;
                    $hrmsTagging = DB::table('housing_user_tagging')
                        ->select('flag')
                        ->where('hrms_id', trim($user['name']))
                        ->first();

                    if (!$hrmsTagging) {

                        $onlineApp = DB::table('housing_applicant_official_detail')
                            ->where('is_active', 1)
                            ->where('hrms_id', $user['name'])
                            ->first();

                        if (!$onlineApp) {
                            return redirect('/user-tagging');
                        }

                    } else {
                        if (in_array($hrmsTagging->flag, ['new', 'pending'])) {
                            return redirect('/user-tagging')
                                ->with('message', 'Please wait for the departmental approval.');
                        }
                    }
                }
            }

            // Always clear cookie (Drupal behavior)
            Cookie::queue(Cookie::forget('user_type'));

            /* ---------------- USER INFO ---------------- */
            $output['user_info'] = $this->getHRMSUserData($username);
            $output['user_info']['email'] = $output['user_info']['email'] ?? 
                DB::table('users')->where('uid', $uid)->value('mail') ?? 'N/A';

            $uid = $user->id;

            /* ---------------- USER STATUS ---------------- */
            $data = DB::table('housing_online_application as hoa')
                ->join('housing_applicant_official_detail as haod',
                    'haod.applicant_official_detail_id', '=', 'hoa.applicant_official_detail_id')
                ->where('haod.uid', $uid)
                ->where('haod.is_active', 1)
                ->whereIn('hoa.status', ['offer_letter_cancel', 'license_cancel'])
                ->select('hoa.status')
                ->first();

            $output['user_status'] = $data->status ?? '';
            $output['all-application-data'] = all_application_details_fetch($uid);

            /* -------- Offer / Allotment Status -------- */
            $output['fetch_current_status'] = DB::table('housing_process_flow as hpf')
                ->join('housing_flat_occupant as hfo',
                    'hfo.online_application_id', '=', 'hpf.online_application_id')
                ->where('hpf.uid', $uid)
                ->where('hpf.short_code', 'applicant_acceptance')
                ->orderByDesc('hpf.online_application_id')
                ->select('hpf.short_code', 'hpf.online_application_id', 'hfo.allotment_no')
                ->first();

            /* -------- License Status -------- */
            $output['fetch_license_status'] = DB::table('housing_process_flow as hpf')
                ->join('housing_online_application as hoa',
                    'hoa.online_application_id', '=', 'hpf.online_application_id')
                ->join('housing_applicant_official_detail as haod',
                    'haod.applicant_official_detail_id', '=', 'hoa.applicant_official_detail_id')
                ->where('hpf.short_code', 'license_generate')
                ->where('haod.is_active', 1)
                ->where('haod.uid', $uid)
                ->select('hpf.short_code', 'hpf.online_application_id')
                ->first();
        }

        /* ======================================================
        | DEPARTMENT / OFFICIAL ROLES
        ======================================================*/
        else if (in_array($userRole, [6,7,8,10,11,13,17])) {

            $roleArr = $user->roles; // same as Drupal

            /* -------- ROLE 11 (DDO) -------- */
            if ($userRole == 11) {

                $result = DB::table('housing_ddo_hrms_mapping')
                    ->where('ddo_code', $user->name)
                    ->where('is_active', 'Y')
                    ->select('hrms_id')
                    ->first();

                if ($result) {
                    $data = getHRMSUserData($result->hrms_id);

                    $output['user_info'] = [
                        'applicantName'        => $user->name . '(' . $data['applicantName'] . ')',
                        'applicantDesignation' => $roleArr[$userRole] . '(' . $data['applicantDesignation'] . ')',
                        'email'                => $user->email,
                        'officeName'           => $data['officeName'],
                        'mobileNo'             => $data['mobileNo'],
                    ];
                }

            } else {

                $output['user_info'] = [
                    'applicantName'        => $user->name,
                    'mobileNo'             => 'N/A',
                    'applicantDesignation' => $roleArr[$userRole],
                    'email'                => $user->email,
                    'officeName'           => 'Housing Department',
                ];
            }

            /* -------- DASHBOARD COUNTS -------- */
            if ($userRole == 11) {
                $output['new-apply']      = application_list_fetch('new-apply','applied')->rowCount();
                $output['vs']             = application_list_fetch('vs','applied')->rowCount();
                $output['cs']             = application_list_fetch('cs','applied')->rowCount();
                $output['allotted-apply'] = application_list_fetch('new-apply','applicant_acceptance')->rowCount();
                $output['allotted-vs']    = application_list_fetch('vs','applicant_acceptance')->rowCount();
                $output['allotted-cs']    = application_list_fetch('cs','applicant_acceptance')->rowCount();

            } elseif ($userRole == 10) {
                $output['new-apply'] = application_list_fetch('new-apply','ddo_verified_1')->rowCount();
                $output['vs']        = application_list_fetch('vs','ddo_verified_1')->rowCount();
                $output['cs']        = application_list_fetch('cs','ddo_verified_1')->rowCount();

            } elseif ($userRole == 13) {
                $output['new-apply'] = application_list_fetch('new-apply','housing_sup_approved_1')->rowCount();

            } elseif ($userRole == 6) {
                $output['all-applications'] = pending_app_list_fetch_secy('allotted')->rowCount();

            } elseif ($userRole == 7) {
                $output['all-exsting-occupant'] = occupant_list_fetch()->rowCount();
                $output['auto-cancellation']    = auto_cancellation_applicant_list_fetch()->rowCount();
                $output['existing_occupant_data'] = fetch_withouthrms_data_count();

            } elseif ($userRole == 8) {
                $output['all-exsting-occupant'] = occupant_list_fetch()->rowCount();

            } elseif ($userRole == 17) {
                $output['all-applications'] = pending_app_list_fetch_secy('allotted')->rowCount();
                $output['special-recommendation-list-data']
                    = fetch_special_recommendation_list_data()->rowCount();
            }
        }

        return view('housingTheme.pages.dashboard', compact('output'));
    }

    /**
     * Admin Dashboard (Roles 6, 7, 8, 10, 11, 13, 17)
     */
    private function adminDashboard(Request $request, $user, $uid, $username, $userRole)
    {
        $output = [];
        $output['user_role'] = $userRole;

        $roleName = DB::table('roles')->where('id', $userRole)->value('name') ?? 'User';

        // Role 11: DDO
        if ($userRole == 11) {
            $ddoMapping = DB::table('housing_ddo_hrms_mapping')
                ->where('ddo_code', $username)
                ->where('is_active', 'Y')
                ->first();

            if (!empty($ddoMapping)) {
                $hrmsData = $this->getHRMSUserData($ddoMapping->hrms_id);
                $output['user_info'] = [
                    'applicantName' => $username . ' (' . ($hrmsData['applicantName'] ?? '') . ')',
                    'applicantDesignation' => $roleName . ' (' . ($hrmsData['applicantDesignation'] ?? '') . ')',
                    'email' => DB::table('users')->where('uid', $uid)->value('mail'),
                    'officeName' => $hrmsData['officeName'] ?? 'N/A',
                    'mobileNo' => $hrmsData['mobileNo'] ?? 'N/A',
                ];
            } else {
                $output['user_info'] = [
                    'applicantName' => $username,
                    'mobileNo' => 'N/A',
                    'applicantDesignation' => $roleName,
                    'email' => DB::table('users')->where('uid', $uid)->value('mail'),
                    'officeName' => 'Housing Department',
                ];
            }

            // Fetch application counts
            $output = array_merge($output, $this->getApplicationCounts($userRole));
        } else {
            $output['user_info'] = [
                'applicantName' => $username,
                'mobileNo' => 'N/A',
                'applicantDesignation' => $roleName,
                'email' => DB::table('users')->where('uid', $uid)->value('mail'),
                'officeName' => 'Housing Department',
            ];

            // Fetch role-specific data
            $output = array_merge($output, $this->getApplicationCounts($userRole));
            
            // For Housing Official (Role 6), add flat type counts
            if ($userRole == 6) {
                $output['flatTypeCounts'] = $this->getFlatTypeCounts();
            }
        }

        return view('housingTheme.pages.dashboard', compact('output'));
    }

    /**
     * Default/CMS Dashboard
     */
    private function defaultDashboard(Request $request)
    {
        $stats = [
            'existing_with_hrms' => $this->fetchPaginatedTotal('/api/existing-occupants/with-hrms'),
            'existing_without_hrms' => $this->fetchPaginatedTotal('/api/existing-occupants/without-hrms'),
            'cms_items' => $this->fetchPaginatedTotal('/api/cms-content'),
        ];

        return view('housingTheme.dashboard.index', [
            'user' => session('user'),
            'stats' => $stats,
        ]);
    }

    /**
     * Get application counts based on role
     */
    private function getApplicationCounts($userRole)
    {
        $counts = [];

        if ($userRole == 11) { // DDO
            $counts['new-apply'] = $this->countApplications('new-apply', 'applied');
            $counts['vs'] = $this->countApplications('vs', 'applied');
            $counts['cs'] = $this->countApplications('cs', 'applied');
            $counts['allotted-apply'] = $this->countApplications('new-apply', 'applicant_acceptance');
            $counts['allotted-vs'] = $this->countApplications('vs', 'applicant_acceptance');
            $counts['allotted-cs'] = $this->countApplications('cs', 'applicant_acceptance');
        } elseif ($userRole == 10) { // Housing Supervisor
            $counts['new-apply'] = $this->countApplications('new-apply', 'ddo_verified_1');
            $counts['vs'] = $this->countApplications('vs', 'ddo_verified_1');
            $counts['cs'] = $this->countApplications('cs', 'ddo_verified_1');
            $counts['allotted-apply'] = $this->countApplications('new-apply', 'ddo_verified_2');
            $counts['allotted-vs'] = $this->countApplications('vs', 'ddo_verified_2');
            $counts['allotted-cs'] = $this->countApplications('cs', 'ddo_verified_2');
        } elseif ($userRole == 13) { // Housing Approver
            $counts['new-apply'] = $this->countApplications('new-apply', 'housing_sup_approved_1');
            $counts['vs'] = $this->countApplications('vs', 'housing_sup_approved_1');
            $counts['cs'] = $this->countApplications('cs', 'housing_sup_approved_1');
            $counts['allotted-apply'] = $this->countApplications('new-apply', 'housing_sup_approved_2');
            $counts['allotted-vs'] = $this->countApplications('vs', 'housing_sup_approved_2');
            $counts['allotted-cs'] = $this->countApplications('cs', 'housing_sup_approved_2');
        } elseif ($userRole == 6) { // Housing Official
            $counts['all-applications'] = DB::table('housing_online_application')
                ->where('status', 'allotted')->count();
            $counts['all-license'] = DB::table('housing_online_application')
                ->where('status', 'housingapprover_approved_2')->count();
        } elseif (in_array($userRole, [7, 8])) { // Occupant Manager
            $counts['all-exsting-occupant'] = DB::table('housing_online_application')
                ->where('status', 'existing_occupant')->count();
            if ($userRole == 7) {
                $counts['auto-cancellation'] = DB::table('housing_online_application')
                    ->whereIn('status', ['offer_letter_cancel', 'license_cancel', 'offer_letter_extended', 'license_extended'])
                    ->count();
                $counts['existing_occupant_data'] = DB::table('housing_existing_occupant_draft')->count();
            }
        } elseif ($userRole == 17) { // Special Recommendations
            $counts['all-applications'] = DB::table('housing_online_application')
                ->where('status', 'allotted')->count();
            $counts['special-recommendation-list-data'] = DB::table('housing_special_recommended')->count();
        }

        return $counts;
    }

    /**
     * Count applications by type and status
     */
    private function countApplications($type, $status)
    {
        $query = DB::table('housing_applicant_official_detail as haod')
            ->join('housing_online_application as hoa', 'hoa.applicant_official_detail_id', '=', 'haod.applicant_official_detail_id')
            ->where('hoa.status', $status);

        if ($type == 'new-apply') {
            $query->leftJoin('housing_new_allotment_application as hna', 'hna.online_application_id', '=', 'hoa.online_application_id')
                ->whereNotNull('hna.online_application_id');
        } elseif ($type == 'vs') {
            $query->leftJoin('housing_vs_application as hva', 'hva.online_application_id', '=', 'hoa.online_application_id')
                ->whereNotNull('hva.online_application_id');
        } elseif ($type == 'cs') {
            $query->leftJoin('housing_cs_application as hca', 'hca.online_application_id', '=', 'hoa.online_application_id')
                ->whereNotNull('hca.online_application_id');
        }

        return $query->count();
    }

    /**
     * Fetch HRMS User Data
     * For local: Returns dummy data
     * For live: Fetches from HRMS API and decrypts response
     */
    private function getHRMSUserData($hrmsId)
    {
        // ========== LOCAL DEVELOPMENT - DUMMY DATA ==========
        // Comment this section when going live
        if (config('app.env') === 'local' || config('app.env') === 'development') {
            return [
                'hrmsId' => $hrmsId,
                'applicantName' => 'PRADIP KUMAR HANSDA',
                'dateOfBirth' => '15/04/1980',
                'dateOfJoining' => '10/06/2005',
                'dateOfRetirement' => '30/04/2040',
                'mobileNo' => '7278587028',
                'gender' => 'Male',
                'applicantDesignation' => 'Upper Division Assistant',
                'officeName' => 'PANCHAYATS & RURAL DEVELOPMENT DEPARTMENT',
                'ddoId' => 'CAFPNA001',
                'permanentStreet' => 'Flat No R-5/1,Bidhan Abasan',
                'permanentCityTownVillage' => 'F B Block,Sector-III',
                'permanentPostOffice' => 'Bidhannagar',
                'permanentPincode' => '700106',
                'permanentDistrictCode' => '5',
                'permanentPresentSame' => 'Y',
                'presentStreet' => 'Flat No R-5/1,Bidhan Abasan',
                'presentCityTownVillage' => 'F B Block,Sector-III',
                'presentPostOffice' => 'Bidhannagar',
                'presentPincode' => '700106',
                'presentDistrictCode' => '5',
                'guardianName' => 'Sri Nabin Chandra Hansda',
                'applicantHeadquarter' => 'L1-DEPARTMENT',
                'gradePay' => '3600',
                'payBandId' => '3',
                'payScaleId' => '',
                'applicantPostingPlace' => 'JOINT ADMINISTRATIVE BUILDING, 6TH - 10TH FLOOR, BLOCK HC-7 SECTOR 3 Salt Lake City BIDHANNAGAR IB MARKET SO Bidhannagar South 24 Parganas( North ) West Bengal',
                'payInThePayBand' => '10600',
                'officeStreetCharacter' => 'JOINT ADMINISTRATIVE BUILDING, 6TH - 10TH FLOOR, BLOCK HC-7 SECTOR 3 Salt Lake City BIDHANNAGAR IB MARKET SO Bidhannagar South 24 Parganas( North ) West Bengal',
                'officeCityTownVillage' => 'Salt Lake City',
                'officePostOffice' => 'BIDHANNAGAR IB MARKET SO',
                'officePinCode' => '700106',
                'officeDistrict' => '5',
                'officePhoneNo' => '',
                'email' => '',
            ];
        }
        // ========== END LOCAL DEVELOPMENT ==========

        // ========== LIVE PRODUCTION - HRMS API CALL ==========
        // Uncomment this section when going live
        try {
            // HRMS API URL (configure in config/services.php)
            $hrmsApiUrl = config('services.hrms.api_url', 'https://uat.wbifms.gov.in/hrms-External/housing/fetchEmployeeDetails');
            
            // Prepare request data
            $requestData = [
                'req' => [
                    'hrmsId' => $hrmsId
                ]
            ];

            // Make API call to HRMS
            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => false, // SSL verification disabled (as in Drupal)
                ])
                ->post($hrmsApiUrl, $requestData);

            if (!$response->successful()) {
                Log::error('HRMS API Error', [
                    'hrms_id' => $hrmsId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return $this->getDefaultData($hrmsId);
            }

            $responseData = $response->json();

            // Check if response is valid
            if (!isset($responseData['resp']['status']) || 
                strtolower($responseData['resp']['status']) !== 's' ||
                empty($responseData['resp']['data'])) {
                Log::error('HRMS API Invalid Response', [
                    'hrms_id' => $hrmsId,
                    'response' => $responseData
                ]);
                return $this->getDefaultData($hrmsId);
            }

            // Decrypt the encrypted data
            $encryptedData = $responseData['resp']['data'];
            $decryptedData = AuthEncryptionHelper::decrypt($encryptedData);
            
            // Parse decrypted JSON
            $userDataArray = json_decode($decryptedData, true);
            
            if (empty($userDataArray) || !is_array($userDataArray) || empty($userDataArray[0])) {
                Log::error('HRMS Data Decryption Error', [
                    'hrms_id' => $hrmsId,
                    'decrypted_data' => $decryptedData
                ]);
                return $this->getDefaultData($hrmsId);
            }

            // Return first element (user data)
            return $userDataArray[0];

        } catch (\Exception $e) {
            Log::error('HRMS User Data Fetch Error', [
                'hrms_id' => $hrmsId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->getDefaultData($hrmsId);
        }
        // ========== END LIVE PRODUCTION ==========
    }

    /**
     * Get default/fallback data when HRMS API fails
     */
    private function getDefaultData($hrmsId)
    {
        return [
            'hrmsId' => $hrmsId,
            'applicantName' => $hrmsId,
            'email' => 'N/A',
            'applicantDesignation' => 'N/A',
            'officeName' => 'N/A',
            'mobileNo' => 'N/A',
        ];
    }

    /**
     * Get flat type wise waiting list counts for Housing Official (Role 6)
     * Matching Drupal's flat_type_wise_waiting_detail_for_competent_authority function
     */
    private function getFlatTypeCounts()
    {
        $counts = [];
        
        // Flat types: 1=A, 2=B, 3=C, 4=D, 5=A+
        $flatTypes = [
            1 => 'A',
            2 => 'B',
            3 => 'C',
            4 => 'D',
            5 => 'A+'
        ];

        foreach ($flatTypes as $flatTypeId => $flatTypeName) {
            // Query matching Drupal's flat_type_wise_waiting_detail_for_competent_authority
            // Status should be 'housingapprover_approved_1' (not 'allotted')
            $query = DB::table('housing_applicant as ha')
                ->join('housing_applicant_official_detail as haod', 'haod.housing_applicant_id', '=', 'ha.housing_applicant_id')
                ->join('housing_online_application as hoa', 'hoa.applicant_official_detail_id', '=', 'haod.applicant_official_detail_id')
                ->join('housing_new_allotment_application as hnaa', 'hnaa.online_application_id', '=', 'hoa.online_application_id')
                ->join('housing_flat_type as hft', 'hnaa.flat_type_id', '=', 'hft.flat_type_id')
                ->where('hoa.status', 'housingapprover_approved_1')
                ->where('hft.flat_type_id', $flatTypeId)
                ->select('hoa.online_application_id');

            $counts[$flatTypeName] = $query->count();
        }

        return $counts;
    }

    private function fetchPaginatedTotal(string $endpoint, array $query = []): int
    {
        $query = array_merge(['per_page' => 1], $query);

        $response = $this->authorizedRequest()
            ->get($this->backend . $endpoint, $query);

        if ($response->successful()) {
            $data = $response->json('data');

            if (is_array($data) && isset($data['total'])) {
                return (int) $data['total'];
            }
        }

        return 0;
    }

    private function authorizedRequest()
    {
        $token = session('api_token');

        return Http::acceptJson()->withToken($token);
    }
}


