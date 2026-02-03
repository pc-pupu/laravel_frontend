<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;
use Barryvdh\DomPDF\Facade\Pdf;

class LicenseListController extends Controller
{
    protected $backend;

    public function __construct()
    {
        $this->backend = config('app.backend_url');
    }

    /**
     * Show Licensee List page
     * GET /license-list
     */
    public function index(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $licenseeType = $request->input('licensee_type');

        try {
            $token = $request->session()->get('api_token');
            $licenses = [];

            if ($licenseeType) {
                $response = Http::withToken($token)
                    ->acceptJson()
                    ->get($this->backend . '/api/license-list/list', [
                        'licensee_type' => $licenseeType
                    ]);

                if ($response->successful()) {
                    $licenses = $response->json('data') ?? [];
                }
            }

            return view('housingTheme.license-list.index', [
                'licenses' => $licenses,
                'selected_type' => $licenseeType
            ]);

        } catch (\Exception $e) {
            Log::error('License List Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('housingTheme.license-list.index', [
                'licenses' => [],
                'selected_type' => null,
                'error' => 'An error occurred while loading the page.'
            ]);
        }
    }

    /**
     * Download Licensee List PDF
     * GET /license-list/download-pdf/{encrypted_licensee_type}
     */
    public function downloadPdf($encryptedLicenseeType)
    {
        try {
            $licenseeType = UrlEncryptionHelper::decryptUrl($encryptedLicenseeType, false);
            $token = request()->session()->get('api_token');

            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/api/license-list/list', [
                    'licensee_type' => $licenseeType
                ]);

            if (!$response->successful()) {
                return redirect()->back()
                    ->with('error', 'Failed to fetch license list.');
            }

            $licenses = $response->json('data') ?? [];

            if (empty($licenses)) {
                return redirect()->back()
                    ->with('error', 'No licenses found.');
            }

            // Determine license type name
            $typeName = '';
            $typeForFile = '';
            if ($licenses[0]['type_of_application'] == 'new') {
                $typeName = 'New Allotment';
                $typeForFile = 'new_alot';
            } elseif ($licenses[0]['type_of_application'] == 'vs') {
                $typeName = 'Floor Shifting';
                $typeForFile = 'fllor_shifting';
            } elseif ($licenses[0]['type_of_application'] == 'cs') {
                $typeName = 'Category Shifting';
                $typeForFile = 'category_shifting';
            }

            // Generate PDF
            $html = view('pdf.licensee-list', [
                'licenses' => $licenses,
                'typeName' => $typeName
            ])->render();

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');

            $fileName = $typeForFile . '_licensee_list_' . time() . '.pdf';

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            Log::error('Download Licensee List PDF Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to download PDF.');
        }
    }

    /**
     * Show RHE Wise Licensee List page
     * GET /license-list/rhe-wise
     */
    public function rheWise(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $rheId = $request->input('rhe_id');

        try {
            $user = $request->session()->get('user');
            $token = $request->session()->get('api_token');
            $licenses = [];
            $rhes = [];

            // Get RHE list
            $rheResponse = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/api/license-list/rhe-list', [
                    'user_role' => $user['role'] ?? 0,
                    'uid' => $user['uid'] ?? null
                ]);

            if ($rheResponse->successful()) {
                $rhes = $rheResponse->json('data') ?? [];
            }

            // Get licenses if RHE selected
            if ($rheId) {
                $response = Http::withToken($token)
                    ->acceptJson()
                    ->get($this->backend . '/api/license-list/list', [
                        'rhe_id' => $rheId
                    ]);

                if ($response->successful()) {
                    $licenses = $response->json('data') ?? [];
                }
            }

            return view('housingTheme.license-list.rhe-wise', [
                'licenses' => $licenses,
                'rhes' => $rhes,
                'selected_rhe_id' => $rheId,
                'user_role' => $user['role'] ?? 0
            ]);

        } catch (\Exception $e) {
            Log::error('RHE Wise License List Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('housingTheme.license-list.rhe-wise', [
                'licenses' => [],
                'rhes' => [],
                'selected_rhe_id' => null,
                'error' => 'An error occurred while loading the page.'
            ]);
        }
    }

    /**
     * Download RHE Wise Licensee List PDF
     * GET /license-list/rhe-wise/download-pdf/{encrypted_rhe_id}
     */
    public function downloadRheWisePdf($encryptedRheId)
    {
        try {
            $rheId = UrlEncryptionHelper::decryptUrl($encryptedRheId, false);
            $token = request()->session()->get('api_token');

            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/api/license-list/list', [
                    'rhe_id' => $rheId
                ]);

            if (!$response->successful()) {
                return redirect()->back()
                    ->with('error', 'Failed to fetch license list.');
            }

            $licenses = $response->json('data') ?? [];

            if (empty($licenses)) {
                return redirect()->back()
                    ->with('error', 'No licenses found.');
            }

            $estateName = $licenses[0]['estate_name'] ?? 'RHE';
            $replaceItems = [',', '/', ' ', '.', '(', ')'];
            $fileName = str_replace($replaceItems, '_', trim($estateName)) . '_Licensee_List';

            // Generate PDF
            $html = view('pdf.rhe-wise-licensee-list', [
                'licenses' => $licenses,
                'estateName' => $estateName
            ])->render();

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');

            return $pdf->download($fileName . '.pdf');

        } catch (\Exception $e) {
            Log::error('Download RHE Wise Licensee List PDF Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to download PDF.');
        }
    }

    /**
     * Download signed license (unified for all license types)
     * GET /download-signed-license/{encrypted_occupant_license_id}
     */
    public function downloadSignedLicense($encryptedOccupantLicenseId)
    {
        if (!request()->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $occupantLicenseId = UrlEncryptionHelper::decryptUrl($encryptedOccupantLicenseId, false);
            $token = request()->session()->get('api_token');

            $response = Http::withToken($token)
                ->get($this->backend . '/api/generate-vs-cs-license/download-signed/' . $occupantLicenseId);

            if ($response->successful()) {
                // Get content type from response
                $contentType = $response->header('Content-Type') ?? 'application/pdf';
                
                // Get filename from Content-Disposition header if available
                $contentDisposition = $response->header('Content-Disposition') ?? 'attachment; filename="signed_licence.pdf"';
                
                return response($response->body(), 200, [
                    'Content-Type' => $contentType,
                    'Content-Disposition' => $contentDisposition,
                ]);
            }

            // If response is not successful, check for error message
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? 'Failed to download signed license.';

            return redirect()->back()
                ->with('error', $errorMessage);

        } catch (\Exception $e) {
            Log::error('Download Signed License Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while downloading the file.');
        }
    }
}
