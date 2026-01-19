<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Helpers\AuthEncryptionHelper;

class SsoDashboardController extends Controller
{
    /**
     * SSO User Dashboard (matching Drupal dashboard_content logic)
     * GET /sso-dashboard
     */
    public function index(Request $request)
    {
        // Check if user is logged in
        if (!$request->session()->has('user')) {
            return redirect()->route('homepage')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];
        $username = $user['name'];

        // Get user role
        $userRole = DB::table('user_role')
            ->where('uid', $uid)
            ->orderBy('rid', 'ASC')
            ->value('rid');

        $output = [];
        $output['user_role'] = $userRole;

        // Role 4 or 5: Applicant
        if (in_array($userRole, [4, 5])) {
            if ($userRole == 4) {
                // Check user_type cookie
                $userType = $request->cookie('user_type');
                
                if ($userType != 'new') {
                    // Check HRMS tagging
                    $hrmsTagging = DB::table('housing_user_tagging')
                        ->where('hrms_id', $username)
                        ->first();

                    if (empty($hrmsTagging)) {
                        // Check if any online application exists
                        $onlineApp = DB::table('housing_applicant_official_detail')
                            ->where('is_active', 1)
                            ->where('hrms_id', $username)
                            ->first();

                        if (empty($onlineApp)) {
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

            // Fetch HRMS user data
            $output['user_info'] = $this->getHRMSUserData($username);
            $output['user_info']['email'] = $output['user_info']['email'] ?? 
                DB::table('users')->where('uid', $uid)->value('mail') ?? 'N/A';

            // Fetch user status
            $userStatus = DB::table('housing_online_application as hoa')
                ->join('housing_applicant_official_detail as haod', 'haod.applicant_official_detail_id', '=', 'hoa.applicant_official_detail_id')
                ->where('haod.uid', $uid)
                ->where('haod.is_active', 1)
                ->whereIn('hoa.status', ['offer_letter_cancel', 'license_cancel'])
                ->select('hoa.status')
                ->first();

            $output['user_status'] = $userStatus->status ?? '';

            // Fetch all applications
            $output['all-application-data'] = DB::table('housing_applicant_official_detail as haod')
                ->join('housing_applicant as ha', 'ha.housing_applicant_id', '=', 'haod.housing_applicant_id')
                ->join('housing_online_application as hoa', 'hoa.applicant_official_detail_id', '=', 'haod.applicant_official_detail_id')
                ->join('housing_allotment_status_master as hasm', 'hasm.short_code', '=', 'hoa.status')
                ->where('haod.uid', $uid)
                ->select(
                    'hoa.application_no',
                    'hoa.date_of_application',
                    'hoa.online_application_id',
                    'haod.applicant_designation',
                    'ha.applicant_name',
                    'hasm.status_description'
                )
                ->orderBy('hoa.status', 'ASC')
                ->get();

            // Fetch current status
            $output['fetch_current_status'] = DB::table('housing_process_flow as hpf')
                ->join('housing_flat_occupant as hfo', 'hfo.online_application_id', '=', 'hpf.online_application_id')
                ->where('hpf.uid', $uid)
                ->where('hpf.short_code', 'applicant_acceptance')
                ->select('hpf.short_code', 'hpf.online_application_id', 'hfo.allotment_no')
                ->orderBy('hpf.online_application_id', 'DESC')
                ->first();

            // Fetch license status
            $output['fetch_license_status'] = DB::table('housing_process_flow as hpf')
                ->join('housing_online_application as hoa', 'hoa.online_application_id', '=', 'hpf.online_application_id')
                ->join('housing_applicant_official_detail as haod', 'haod.applicant_official_detail_id', '=', 'hoa.applicant_official_detail_id')
                ->where('hpf.short_code', 'license_generate')
                ->where('haod.is_active', 1)
                ->where('haod.uid', $uid)
                ->select('hpf.short_code', 'hpf.online_application_id')
                ->first();
        }
        // Admin roles: 6, 7, 8, 10, 11, 13, 17
        elseif (in_array($userRole, [6, 7, 8, 10, 11, 13, 17])) {
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
            }
        }

        return view('userSso.sso-dashboard', compact('output'));
    }

    /**
     * Fetch HRMS User Data
     * For local: Returns dummy data
     * For live: Fetches from HRMS API and decrypts response
     */
    private function getHRMSUserData($hrmsId)
    {
        try {

            // HRMS API URL
            $hrmsApiUrl = config(
                'services.hrms.api_url',
                'https://uat.wbifms.gov.in/hrms-External/housing/fetchEmployeeDetails'
            );
            // $hrmsApiUrl = config('services.hrms.api_url', 'https://172.17.2.45/hrms-External/housing/fetchEmployeeDetails'); // Internal IP (for Live)

            
            // if (in_array(config('app.env'), ['local', 'development'])) {

            //     $hrmsApiUrl = [
            //         'hrmsId' => $hrmsId,
            //         'applicantName' => 'PRADIP KUMAR HANSDA',
            //         'dateOfBirth' => '15/04/1980',
            //         'dateOfJoining' => '10/06/2005',
            //         'dateOfRetirement' => '30/04/2040',
            //         'mobileNo' => '7278587028',
            //         'gender' => 'Male',
            //         'applicantDesignation' => 'Upper Division Assistant',
            //         'officeName' => 'PANCHAYATS & RURAL DEVELOPMENT DEPARTMENT',
            //         'ddoId' => 'CAFPNA001',
            //         'permanentStreet' => 'Flat No R-5/1,Bidhan Abasan',
            //         'permanentCityTownVillage' => 'F B Block,Sector-III',
            //         'permanentPostOffice' => 'Bidhannagar',
            //         'permanentPincode' => '700106',
            //         'permanentDistrictCode' => '5',
            //         'permanentPresentSame' => 'Y',
            //         'presentStreet' => 'Flat No R-5/1,Bidhan Abasan',
            //         'presentCityTownVillage' => 'F B Block,Sector-III',
            //         'presentPostOffice' => 'Bidhannagar',
            //         'presentPincode' => '700106',
            //         'presentDistrictCode' => '5',
            //         'guardianName' => 'Sri Nabin Chandra Hansda',
            //         'applicantHeadquarter' => 'L1-DEPARTMENT',
            //         'gradePay' => '3600',
            //         'payBandId' => '3',
            //         'payScaleId' => '',
            //         'payInThePayBand' => '10600',
            //         'applicantPostingPlace' => 'Salt Lake City',
            //         'officePinCode' => '700106',
            //         'officeDistrict' => '5',
            //         'email' => '',
            //     ];
            // }

            
            $requestData = [
                'req' => [
                    'hrmsId' => $hrmsId,
                ],
            ];

            $response = Http::timeout(30)
                ->withOptions(['verify' => false])
                ->post($hrmsApiUrl, $requestData);

            if (!$response->successful()) {
                Log::error('HRMS API Error', [
                    'hrms_id' => $hrmsId,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                ]);

                return $this->getDefaultData($hrmsId);
            }

            $responseData = $response->json();

            if (
                empty($responseData['resp']['status']) ||
                strtolower($responseData['resp']['status']) !== 's' ||
                empty($responseData['resp']['data'])
            ) {
                Log::error('HRMS Invalid Response', [
                    'hrms_id' => $hrmsId,
                    'response' => $responseData,
                ]);

                return $this->getDefaultData($hrmsId);
            }

            /**
             * ------------------------
             * DECRYPT & PARSE
             * ------------------------
             */
            $encryptedData = $responseData['resp']['data'];
            $decryptedJson = AuthEncryptionHelper::decrypt($encryptedData);
            $userDataArray = json_decode($decryptedJson, true);

            if (empty($userDataArray[0])) {
                Log::error('HRMS Decryption Failed', [
                    'hrms_id' => $hrmsId,
                    'data' => $decryptedJson,
                ]);

                return $this->getDefaultData($hrmsId);
            }

            return $userDataArray[0];

        } catch (\Throwable $e) {

            Log::error('HRMS Fetch Exception', [
                'hrms_id' => $hrmsId,
                'error' => $e->getMessage(),
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
}

