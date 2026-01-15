<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;
use Barryvdh\DomPDF\Facade\Pdf;

class ApplicationListController extends Controller
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
     * Show application list for applicants (their own applications)
     * GET /application-list
     */
    public function index(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];

        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/application-list', [
                    'uid' => $uid,
                ]);
                // print('<pre>');print_r($response->json());die;
            if (!$response->successful()) {
                return redirect()->route('dashboard')
                    ->with('error', 'Failed to load application list.');
            }

            $applications = $response->json('data') ?? [];

            return view('housingTheme.application-list.index', [
                'applications' => $applications,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            Log::error('Application List Index Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load application list.');
        }
    }

    /**
     * Show application list for admins/officials
     * GET /view-application-list/{status}/{entity}
     */
    public function adminList(Request $request, $status, $entity, $pageStatus = null)
    {
        
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $userRole = $user['role'] ?? null;
        $ddoCode = $user['name'] ?? null; // DDO code is stored in name field
        

        try {
            // Decrypt status and entity if encrypted
            $status = $this->decryptIfEncrypted($status);
            $entity = $this->decryptIfEncrypted($entity);
            
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/application-list/admin', [
                    'status' => $status,
                    'entity' => $entity,
                    'page_status' => $pageStatus,
                    'user_role' => $userRole,
                    'ddo_code' => $ddoCode,
                ]);
                // print('<pre>');print_r($response->json());die;
            if (!$response->successful()) {
                return redirect()->route('dashboard')
                    ->with('error', 'Failed to load application list.');
            }

            $data = $response->json('data') ?? [];
            $counts = $response->json('counts') ?? [];
            // print('<pre>');print_r($counts);die;
            // Get status message
            $statusMsg = $this->getStatusMessage($status);
            $entityMsg = $this->getEntityMessage($entity);

            // Get verified and rejected statuses for action buttons
            $verifiedStatus = $this->getVerifiedStatus($status, $userRole);
            $rejectedStatus = $this->getRejectedStatus($status, $userRole);

            return view('housingTheme.application-list.admin-list', [
                'applications' => $data,
                'counts' => $counts,
                'status' => $status,
                'entity' => $entity,
                'pageStatus' => $pageStatus,
                'statusMsg' => $statusMsg,
                'entityMsg' => $entityMsg,
                'userRole' => $userRole,
                'user' => $user,
                'verifiedStatus' => $verifiedStatus,
                'rejectedStatus' => $rejectedStatus,
            ]);

        } catch (\Exception $e) {
            Log::error('Admin Application List Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load application list.');
        }
    }

    /**
     * View application detail (for applicants)
     * GET /view-application/{id}
     */
    public function view(Request $request, $id)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];

        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($id);

            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/application-list/' . $applicationId, [
                    'uid' => $uid,
                ]);
                
                
            if (!$response->successful() || !$response->json('data')) {
                return redirect()->route('application-list.index')
                    ->with('error', 'Application not found.');
            }

            $application = $response->json('data');
            // print('<pre>');print_r($application);die;
            return view('housingTheme.application-list.view', [
                'application' => $application,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            Log::error('View Application Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('application-list.index')
                ->with('error', 'Failed to load application details.');
        }
    }

    /**
     * View application detail (for admins/officials)
     * GET /application-detail/{id}/{page_status}/{status}
     */
    public function adminView(Request $request, $id, $pageStatus = '', $status = '')
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];

        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($id);
            $decryptedStatus = $status ? UrlEncryptionHelper::decryptUrl($status) : '';

            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/application-list/' . $applicationId, [
                    'uid' => $uid,
                    'page_status' => $pageStatus,
                    'status' => $decryptedStatus,
                ]);

            if (!$response->successful() || !$response->json('data')) {
                return redirect()->back()
                    ->with('error', 'Application not found.');
            }

            $application = $response->json('data');
            // print_r($application);die;
            
            return view('housingTheme.application-list.admin-view', [
                'application' => $application,
                'pageStatus' => $pageStatus,
                'status' => $decryptedStatus,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            Log::error('Admin View Application Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to load application details.');
        }
    }

    /**
     * Update application status (approve/reject)
     * POST /update-status/{id}/{new_status}/{status}/{entity}
     */
    public function updateStatus(Request $request, $id, $newStatus, $status, $entity, $computerSerialNo = '')
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];

        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($id);
            $decryptedNewStatus = UrlEncryptionHelper::decryptUrl($newStatus);
            $decryptedStatus = UrlEncryptionHelper::decryptUrl($status);
            $decryptedEntity = UrlEncryptionHelper::decryptUrl($entity);
            $decryptedComputerSerialNo = $computerSerialNo ? UrlEncryptionHelper::decryptUrl($computerSerialNo) : '';

            $response = $this->authorizedRequest()
                ->post($this->backend . '/api/application-list/' . $applicationId . '/update-status', [
                    'new_status' => $decryptedNewStatus,
                    'entity' => $decryptedEntity,
                    'current_status' => $decryptedStatus,
                    'computer_serial_no' => $decryptedComputerSerialNo,
                    'remarks' => $request->input('remarks', ''),
                    'uid' => $uid,
                ]);

            if (!$response->successful()) {
                $message = $response->json('message') ?? 'Failed to update application status.';
                return redirect()->back()
                    ->with('error', $message);
            }

            $encryptedStatus = UrlEncryptionHelper::encryptUrl($decryptedStatus);
            $encryptedEntity = UrlEncryptionHelper::encryptUrl($decryptedEntity);

            return redirect()->route('aapplication-list.admin-list-with-status', [
                'status' => $encryptedStatus,
                'entity' => $encryptedEntity,
                'page_status' => 'action-list',
            ])->with('success', 'Application status updated successfully.');

        } catch (\Exception $e) {
            Log::error('Update Status Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update application status.');
        }
    }

    /**
     * Generate license
     * GET /generate-license/{id}/{page_status}/{status}
     */
    public function generateLicense(Request $request, $id, $pageStatus = '', $status = '')
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];

        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($id);

            $response = $this->authorizedRequest()
                ->post($this->backend . '/api/license/generate', [
                    'online_application_id' => $applicationId,
                    'uid' => $uid,
                ]);

            if (!$response->successful()) {
                $message = $response->json('message') ?? 'Failed to generate license.';
                return redirect()->back()
                    ->with('error', $message);
            }

            $message = $response->json('message') ?? 'License generated successfully.';

            return redirect()->route('dashboard')
                ->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Generate License Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to generate license.');
        }
    }

    /**
     * View license list
     * GET /view-generated-license
     */
    public function licenseList(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $userRole = $user['role'] ?? null;
        $ddoCode = $user['name'] ?? null;

        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/license/list', [
                    'user_role' => $userRole,
                    'ddo_code' => $ddoCode,
                ]);

            if (!$response->successful()) {
                return redirect()->route('dashboard')
                    ->with('error', 'Failed to load license list.');
            }

            $licenses = $response->json('data') ?? [];

            return view('housingTheme.application-list.license-list', [
                'licenses' => $licenses,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            Log::error('License List Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load license list.');
        }
    }

    /**
     * View flat possession taken list
     * GET /view-flat-possession-taken-ddo
     */
    public function flatPossessionTaken(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $userRole = $user['role'] ?? null;
        $ddoCode = $user['name'] ?? null;

        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/license/flat-possession-taken', [
                    'user_role' => $userRole,
                    'ddo_code' => $ddoCode,
                ]);

            if (!$response->successful()) {
                return redirect()->route('dashboard')
                    ->with('error', 'Failed to load list.');
            }

            $list = $response->json('data') ?? [];

            return view('housingTheme.application-list.flat-possession-taken', [
                'list' => $list,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            Log::error('Flat Possession Taken List Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load list.');
        }
    }

    /**
     * View flat released list
     * GET /view-flat-released-ddo
     */
    public function flatReleased(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $userRole = $user['role'] ?? null;
        $ddoCode = $user['name'] ?? null;

        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/license/flat-released', [
                    'user_role' => $userRole,
                    'ddo_code' => $ddoCode,
                ]);

            if (!$response->successful()) {
                return redirect()->route('dashboard')
                    ->with('error', 'Failed to load list.');
            }

            $list = $response->json('data') ?? [];

            return view('housingTheme.application-list.flat-released', [
                'list' => $list,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            Log::error('Flat Released List Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load list.');
        }
    }

    /**
     * Decrypt if encrypted, otherwise return as is
     */
    private function decryptIfEncrypted($value)
    {
        try {
            return UrlEncryptionHelper::decryptUrl($value);
        } catch (\Exception $e) {
            // If decryption fails, assume it's not encrypted
            return $value;
        }
    }

    /**
     * Get status message
     */
    private function getStatusMessage($status)
    {
        $messages = [
            'applied' => 'Submitted',
            'ddo_verified_1' => 'DDO (Level-1) Verified',
            'ddo_rejected_1' => 'DDO (Level-1) Rejected',
            'housing_sup_approved_1' => 'Housing Supervisor(Level-1) Verified',
            'housing_sup_reject_1' => 'Housing Supervisor(Level-1) Rejected',
            'housingapprover_approved_1' => 'Housing Approver Verified',
            'housing_approver_reject_1' => 'Housing Approver Rejected',
            'housing_official_approved' => 'Housing Official Verified',
            'housing_official_rejected' => 'Housing Official Rejected',
            'ddo_verified_2' => 'DDO (Level-2) Verified',
            'ddo_rejected_2' => 'DDO (Level-2) Rejected',
            'housing_sup_approved_2' => 'Housing Supervisor(Level-2) Verified',
            'housing_sup_reject_2' => 'Housing Supervisor(Level-2) Rejected',
            'applicant_acceptance' => 'Allotted',
            'housingapprover_approved_2' => 'Housing Approver Verified',
            'housing_approver_reject_2' => 'Housing Approver Rejected',
        ];

        return $messages[$status] ?? $status;
    }

    /**
     * Get entity message
     */
    private function getEntityMessage($entity)
    {
        $messages = [
            'new-apply' => 'New Allotment',
            'vs' => 'Vertical Shifting',
            'cs' => 'Category Shifting',
            'new_license' => 'New Licence',
            'vs_licence' => 'VS Licence',
            'cs_licence' => 'CS Licence',
            'renew_license' => 'Renew Licence',
        ];

        return $messages[$entity] ?? $entity;
    }

    /**
     * Get verified status based on current status and user role
     */
    private function getVerifiedStatus($status, $userRole)
    {
        $statusMap = [
            '11' => [ // DDO
                'applied' => 'ddo_verified_1',
                'applicant_acceptance' => 'ddo_verified_2',
            ],
            '10' => [ // Housing Supervisor
                'ddo_verified_1' => 'housing_sup_approved_1',
                'ddo_verified_2' => 'housing_sup_approved_2',
            ],
            '13' => [ // Housing Approver
                'housing_sup_approved_1' => 'housingapprover_approved_1',
                'housing_sup_approved_2' => 'housingapprover_approved_2',
            ],
            '6' => [ // Housing Official
                'housingapprover_approved_1' => 'housing_official_approved',
                'housingapprover_approved_2' => 'housing_official_approved',
            ],
        ];

        return $statusMap[$userRole][$status] ?? null;
    }

    /**
     * Get rejected status based on current status and user role
     */
    private function getRejectedStatus($status, $userRole)
    {
        $statusMap = [
            '11' => [ // DDO
                'applied' => 'ddo_rejected_1',
                'applicant_acceptance' => 'ddo_rejected_2',
            ],
            '10' => [ // Housing Supervisor
                'ddo_verified_1' => 'housing_sup_reject_1',
                'ddo_verified_2' => 'housing_sup_reject_2',
            ],
            '13' => [ // Housing Approver
                'housing_sup_approved_1' => 'housing_approver_reject_1',
                'housing_sup_approved_2' => 'housing_approver_reject_2',
            ],
            '6' => [ // Housing Official
                'housing_official_approved' => 'housing_official_reject',
            ],
        ];

        return $statusMap[$userRole][$status] ?? null;
    }

    /**
     * Dashboard page for view_application_list
     * GET /view_application_list/{status}/{url}
     */
    public function dashboard(Request $request, $status, $url = '')
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $userRole = $user['role'] ?? null;
        $ddoCode = $user['name'] ?? null;

        try {
            // Decrypt status and url if encrypted
            $status = $this->decryptIfEncrypted($status);
            $url = $url ? $this->decryptIfEncrypted($url) : 'new-apply';
            
            // Get dashboard counts
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/view-application-list/dashboard', [
                    'status' => $status,
                    'entity' => $url,
                    'user_role' => $userRole,
                    'ddo_code' => $ddoCode,
                ]);

            if (!$response->successful()) {
                return redirect()->route('dashboard')
                    ->with('error', 'Failed to load dashboard.');
            }
            // print_r($response->json());die;
            $data = $response->json('data') ?? [];
            $verifiedStatus = $data['verified_status'] ?? null;
            $rejectedStatus = $data['rejected_status'] ?? null;

            return view('housingTheme.view-application-list.dashboard', [
                'status' => $status,
                'url' => $url,
                'actionCount' => $data['action_count'] ?? 0,
                'verifiedCount' => $data['verified_count'] ?? 0,
                'rejectedCount' => $data['rejected_count'] ?? 0,
                'verifiedStatus' => $verifiedStatus,
                'rejectedStatus' => $rejectedStatus,
                'userRole' => $userRole,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            Log::error('View Application List Dashboard Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to load dashboard.');
        }
    }

    /**
     * Show approve application form
     * GET /application-approve/{id}/{status}/{entity}/{page_status}/{computer_serial_no}/{flat_type}
     */
    public function showApproveForm(Request $request, $id, $status, $entity, $pageStatus, $computerSerialNo, $flatType)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];

        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($id);
            $decryptedStatus = UrlEncryptionHelper::decryptUrl($status);
            $decryptedEntity = UrlEncryptionHelper::decryptUrl($entity);
            $decryptedPageStatus = $pageStatus;
            $decryptedComputerSerialNo = UrlEncryptionHelper::decryptUrl($computerSerialNo);
            $decryptedFlatType = UrlEncryptionHelper::decryptUrl($flatType);

            // Get application details
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/application-list/' . $applicationId);

                
            if (!$response->successful() || !$response->json('data')) {
                return redirect()->back()
                    ->with('error', 'Application not found.');
            }

            $application = $response->json('data');

            // Get entity type
            $entityResponse = $this->authorizedRequest()
                ->get($this->backend . '/api/view-application-list/' . $applicationId . '/entity-type');

            $entityType = $entityResponse->json('data') ?? ['type' => 'Application'];

            return view('housingTheme.view-application-list.approve-form', [
                'application' => $application,
                'applicationId' => $applicationId,
                'statusNew' => $this->getVerifiedStatus($decryptedStatus, $user['role']),
                'status' => $decryptedStatus,
                'entity' => $decryptedEntity,
                'pageStatus' => $decryptedPageStatus,
                'computerSerialNo' => $decryptedComputerSerialNo,
                'flatType' => $decryptedFlatType,
                'entityType' => $entityType,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            Log::error('Show Approve Form Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to load approve form.');
        }
    }

    /**
     * Store approve application
     * POST /application-approve/{id}/{status}/{entity}/{page_status}/{computer_serial_no}/{flat_type}
     */
    public function ddoAcceptStore(Request $request)
    { 
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];
        // print_r($user);die;
        $id = $request->input('id');
        $status = $request->input('status');
        $entity = $request->input('entity');
        $computerSerialNo = $request->input('computer_serial_no');
        $flatType = $request->input('flat_type');
        $pageStatus = $request->input('page_status');

        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($id);
            $decryptedStatus = UrlEncryptionHelper::decryptUrl($status);
            $decryptedEntity = UrlEncryptionHelper::decryptUrl($entity);
            $decryptedComputerSerialNo = UrlEncryptionHelper::decryptUrl($computerSerialNo);
            $decryptedFlatType = UrlEncryptionHelper::decryptUrl($flatType);

            $statusNew = $this->getVerifiedStatus($decryptedStatus, $user['role']);

            // Prepare multipart form data for file upload
            $dataArr = [
                'online_application_id' => $applicationId,
                'status_new' => $statusNew,
                'entity' => $decryptedEntity,
                'status' => $decryptedStatus,
                'computer_serial_no' => $decryptedComputerSerialNo,
                'flat_type' => $decryptedFlatType,
                'uid' => $uid,
                'role' => $user['role'],
                'userName' => $user['name'],    
            ];

            $response = $this->authorizedRequest()
                ->post($this->backend . '/api/view-application-list/approve', $dataArr);

            
          
            if ($response->json('status') !== 'success') {
                $message = $response->json('message') ?? 'Failed to approve application.';
                return redirect()->back()
                    ->withInput()
                    ->with('error', $message);
            }

            $encryptedStatus = UrlEncryptionHelper::encryptUrl($decryptedStatus);
            $encryptedEntity = UrlEncryptionHelper::encryptUrl($decryptedEntity);
            // echo $encryptedStatus;die;
            return redirect()->route('view_application', [
                'status' => $encryptedStatus,
                'entity' => $encryptedEntity,
                'page_status' => 'action-list',
            ])->with('success', 'Application approved successfully.');

        } catch (\Exception $e) {
            Log::error('Store Approve Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to approve application.');
        }
    }

    /**
     * Reject application
     * POST /reject-application
     */
    public function rejectApplication(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $user = $request->session()->get('user');
        $uid = $user['uid'];

        $request->validate([
            'online_application_id' => 'required|string',
            'rejected_status' => 'required|string',
            'computer_serial_no' => 'nullable|string',
            'entity' => 'required|string',
            'status' => 'required|string',
            'reject_remarks' => 'nullable|string',
        ]);

       
        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($request->input('online_application_id'));
            $rejectedStatus = UrlEncryptionHelper::decryptUrl($request->input('rejected_status'));
            $status = UrlEncryptionHelper::decryptUrl($request->input('status'));
            $entity = UrlEncryptionHelper::decryptUrl($request->input('entity'));
            $computerSerialNo = $request->input('computer_serial_no') 
                ? UrlEncryptionHelper::decryptUrl($request->input('computer_serial_no')) 
                : $applicationId;
            $remarks = $request->input('reject_remarks', '');
            // echo $remarks;die;
            $response = $this->authorizedRequest()
                ->post($this->backend . '/api/application-list/' . $applicationId . '/update-status', [
                    'new_status' => $rejectedStatus,
                    'entity' => $entity,
                    'current_status' => $status,
                    'computer_serial_no' => $computerSerialNo,
                    'remarks' => $remarks,
                    'uid' => $uid,
                ]);
 
            if (!$response->successful()) {
                $message = $response->json('message') ?? 'Failed to reject application.';
                return redirect()->back()
                    ->withInput()
                    ->with('error', $message);
            }

            $encryptedStatus = UrlEncryptionHelper::encryptUrl($status);
            $encryptedEntity = UrlEncryptionHelper::encryptUrl($entity);

            return redirect()->route('view_application', [
                'status' => $encryptedStatus,
                'entity' => $encryptedEntity,
                'page_status' => 'action-list',
            ])->with('success', 'Application rejected successfully.');

        } catch (\Exception $e) {
            Log::error('Reject Application Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to reject application.');
        }
    }

    /**
     * Generate application detail PDF
     * GET /application_detail_pdf/{id}/{status}
     */
    public function generateApplicationPdf(Request $request, $id, $status)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $applicationId = UrlEncryptionHelper::decryptUrl($id);
            $decryptedStatus = UrlEncryptionHelper::decryptUrl($status);
            $user = $request->session()->get('user');
            $uid = $user['uid'];

            // Fetch application data from API
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/application-list/' . $applicationId, [
                    'uid' => $uid,
                    'page_status' => '',
                    'status' => $decryptedStatus,
                ]);

            if (!$response->successful() || !$response->json('data')) {
                return redirect()->back()
                    ->with('error', 'Application not found.');
            }

            $application = $response->json('data');
            
            // Generate filename
            $filename = 'App_Details_' . ($application['application_no'] ?? $applicationId) . '.pdf';

            // Load PDF view and generate PDF
            $pdf = Pdf::loadView('housingTheme.application-list.pdf', [
                'application' => $application,
                'user' => $user,
            ]);

            // Set PDF options
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOption('enable-local-file-access', true);
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);

            // Download PDF
            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Generate PDF Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Download license PDF
     * GET /download_licence_pdf/{id}
     */
    public function downloadLicensePdf(Request $request, $id)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $licenseId = UrlEncryptionHelper::decryptUrl($id);

            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/license/download-pdf/' . $licenseId);

            if (!$response->successful()) {
                return redirect()->back()
                    ->with('error', 'Failed to download license PDF.');
            }

            // If the response is a file, return it
            if ($response->header('Content-Type') === 'application/pdf') {
                return response($response->body(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="license_' . $licenseId . '.pdf"',
                ]);
            }

            // Otherwise, redirect with message
            return redirect()->back()
                ->with('info', 'License PDF download feature coming soon.');

        } catch (\Exception $e) {
            Log::error('Download License PDF Error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to download license PDF.');
        }
    }
}

