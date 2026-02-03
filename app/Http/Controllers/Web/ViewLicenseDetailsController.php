<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;
use Barryvdh\DomPDF\Facade\Pdf;

class ViewLicenseDetailsController extends Controller
{
    protected $backend;

    public function __construct()
    {
        $this->backend = config('app.backend_url');
    }

    /**
     * Show View License Details page
     * GET /view-license-details
     */
    public function index(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $licenseType = $request->input('license_type'); // 'new', 'vs', 'cs'

        try {
            $user = $request->session()->get('user');
            $uid = $user['uid'] ?? null;
            $token = $request->session()->get('api_token');
            $licenses = [];

            if ($licenseType) {
                $response = Http::withToken($token)
                    ->acceptJson()
                    ->get($this->backend . '/api/view-license-details/list', [
                        'license_type' => $licenseType,
                        'uid' => $uid
                    ]);

                if ($response->successful()) {
                    $licenses = $response->json('data') ?? [];
                }
            }

            return view('housingTheme.view-license-details.index', [
                'licenses' => $licenses,
                'selected_type' => $licenseType
            ]);

        } catch (\Exception $e) {
            Log::error('View License Details Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('housingTheme.view-license-details.index', [
                'licenses' => [],
                'selected_type' => null,
                'error' => 'An error occurred while loading the page.'
            ]);
        }
    }

    /**
     * Download License PDF
     * GET /view-license-details/download-pdf/{encrypted_license_type}/{encrypted_app_id}/{encrypted_flat_occupant_id}/{encrypted_license_app_id}
     */
    public function downloadPdf($encryptedLicenseType, $encryptedAppId, $encryptedFlatOccupantId, $encryptedLicenseAppId)
    {
        try {
            $licenseType = UrlEncryptionHelper::decryptUrl($encryptedLicenseType, false);
            $appId = UrlEncryptionHelper::decryptUrl($encryptedAppId, false);
            $flatOccupantId = UrlEncryptionHelper::decryptUrl($encryptedFlatOccupantId, false);
            $licenseAppId = UrlEncryptionHelper::decryptUrl($encryptedLicenseAppId, false);

            $token = request()->session()->get('api_token');

            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/api/view-license-details/details', [
                    'license_type' => $licenseType,
                    'online_application_id' => $appId,
                    'flat_occupant_id' => $flatOccupantId,
                    'license_application_id' => $licenseAppId
                ]);

            if (!$response->successful()) {
                return redirect()->back()
                    ->with('error', 'Failed to fetch license details.');
            }

            $licenseData = $response->json('data');

            if (!$licenseData) {
                return redirect()->back()
                    ->with('error', 'License data not found.');
            }

            // Generate PDF based on license type
            $pdfView = 'pdf.license'; // Default
            if ($licenseType == 'vs') {
                $pdfView = 'pdf.vs-license';
            } elseif ($licenseType == 'cs') {
                $pdfView = 'pdf.cs-license';
            }

            $html = view($pdfView, [
                'licenseDetails' => (object)$licenseData
            ])->render();

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $fileName = $licenseType . '_licence_' . $licenseData['license_no'] . '_' . time() . '.pdf';

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            Log::error('Download License PDF Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to download license PDF.');
        }
    }
}
