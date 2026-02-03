<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateVsLicenseController extends Controller
{
    protected $backend;

    public function __construct()
    {
        $this->backend = config('app.backend_url');
    }

    /**
     * Show VS License list
     * GET /generate-vs-license
     */
    public function index(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $user = $request->session()->get('user');
            $uid = $user['uid'] ?? null;
            $token = $request->session()->get('api_token');

            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/api/generate-vs-cs-license/list', [
                    'type' => 'vs',
                    'uid' => $uid
                ]);

            if (!$response->successful()) {
                Log::error('VS License List Error', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return view('housingTheme.generate-vs-license.index', [
                    'licenses' => [],
                    'error' => 'Failed to load license list.'
                ]);
            }

            $data = $response->json('data') ?? [];

            return view('housingTheme.generate-vs-license.index', [
                'licenses' => $data,
                'user_role' => $user['role'] ?? 0
            ]);

        } catch (\Exception $e) {
            Log::error('VS License Index Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('housingTheme.generate-vs-license.index', [
                'licenses' => [],
                'error' => 'An error occurred while loading the page.'
            ]);
        }
    }

    /**
     * Generate VS License
     * POST /generate-vs-license/generate
     */
    public function generate(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $request->validate([
            'online_application_id' => 'required|string',
            'flat_occupant_id' => 'required|string',
            'license_application_id' => 'required|string'
        ]);

        try {
            $token = $request->session()->get('api_token');

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($this->backend . '/api/generate-vs-cs-license/generate', [
                    'online_application_id' => UrlEncryptionHelper::decryptUrl($request->online_application_id, false),
                    'flat_occupant_id' => UrlEncryptionHelper::decryptUrl($request->flat_occupant_id, false),
                    'license_application_id' => UrlEncryptionHelper::decryptUrl($request->license_application_id, false),
                    'type' => 'vs'
                ]);

            if ($response->successful()) {
                return redirect()->route('generate-vs-license.index')
                    ->with('success', 'VS License generated successfully.');
            } else {
                $errorMessage = $response->json('message') ?? 'Failed to generate license.';
                return redirect()->route('generate-vs-license.index')
                    ->with('error', $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('Generate VS License Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('generate-vs-license.index')
                ->with('error', 'An error occurred while generating the license.');
        }
    }

    /**
     * Download VS License PDF
     * GET /generate-vs-license/download-pdf/{encrypted_app_id}/{encrypted_flat_occupant_id}/{encrypted_license_app_id}
     */
    public function downloadPdf($encryptedAppId, $encryptedFlatOccupantId, $encryptedLicenseAppId)
    {
        try {
            $appId = UrlEncryptionHelper::decryptUrl($encryptedAppId, false);
            $flatOccupantId = UrlEncryptionHelper::decryptUrl($encryptedFlatOccupantId, false);
            $licenseAppId = UrlEncryptionHelper::decryptUrl($encryptedLicenseAppId, false);

            $token = request()->session()->get('api_token');

            $response = Http::withToken($token)
                ->acceptJson()
                ->get($this->backend . '/api/generate-vs-cs-license/details', [
                    'online_application_id' => $appId,
                    'flat_occupant_id' => $flatOccupantId,
                    'license_application_id' => $licenseAppId,
                    'type' => 'vs'
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

            // Generate PDF
            $html = view('pdf.vs-license', [
                'licenseDetails' => (object)$licenseData
            ])->render();

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'portrait');

            $fileName = 'vs_licence_' . $licenseData['license_no'] . '.pdf';

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            Log::error('Download VS License PDF Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to download license PDF.');
        }
    }

    /**
     * Show upload signed license form
     * GET /generate-vs-license/upload-signed/{encrypted_occupant_license_id}/{encrypted_license_no}
     */
    public function showUploadForm($encryptedOccupantLicenseId, $encryptedLicenseNo)
    {
        if (!request()->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $occupantLicenseId = UrlEncryptionHelper::decryptUrl($encryptedOccupantLicenseId, false);
            $licenseNo = UrlEncryptionHelper::decryptUrl($encryptedLicenseNo, false);

            return view('housingTheme.generate-vs-license.upload-signed', [
                'occupant_license_id' => $encryptedOccupantLicenseId,
                'license_no' => $licenseNo,
                'license_type' => 'Floor Shifting Licence'
            ]);

        } catch (\Exception $e) {
            Log::error('Show Upload Form Error', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('generate-vs-license.index')
                ->with('error', 'Invalid license information.');
        }
    }

    /**
     * Upload signed license
     * POST /generate-vs-license/upload-signed
     */
    public function uploadSigned(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $request->validate([
            'occupant_license_id' => 'required|string',
            'license_no' => 'required|string',
            'signed_licence_file' => 'required|file|mimes:pdf|max:1024'
        ]);

        try {
            $token = $request->session()->get('api_token');
            $occupantLicenseId = UrlEncryptionHelper::decryptUrl($request->occupant_license_id, false);

            $response = Http::withToken($token)
                ->attach('signed_licence_file', file_get_contents($request->file('signed_licence_file')->getRealPath()), 
                    $request->file('signed_licence_file')->getClientOriginalName())
                ->post($this->backend . '/api/generate-vs-cs-license/upload-signed', [
                    'occupant_license_id' => $occupantLicenseId,
                    'license_no' => $request->license_no,
                    'type' => 'vs'
                ]);

            if ($response->successful()) {
                return redirect()->route('generate-vs-license.index')
                    ->with('success', 'Signed Floor Shifting Licence uploaded successfully.');
            } else {
                $errorMessage = $response->json('message') ?? 'Failed to upload signed license.';
                return redirect()->back()
                    ->with('error', $errorMessage)
                    ->withInput();
            }

        } catch (\Exception $e) {
            Log::error('Upload Signed VS License Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while uploading the file.')
                ->withInput();
        }
    }

    /**
     * Download signed license (for applicants)
     * GET /generate-vs-license/download-signed/{encrypted_occupant_license_id}
     */
    public function downloadSigned($encryptedOccupantLicenseId)
    {
        if (!request()->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $occupantLicenseId = UrlEncryptionHelper::decryptUrl($encryptedOccupantLicenseId, false);
            $token = request()->session()->get('api_token');

            $response = Http::withToken($token)
                ->get($this->backend . '/api/generate-vs-cs-license/download-signed/' . $occupantLicenseId);

            if ($response->successful() && $response->header('Content-Type') === 'application/pdf') {
                return response($response->body(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="vs_signed_licence.pdf"',
                ]);
            }

            return redirect()->back()
                ->with('error', 'Failed to download signed license.');

        } catch (\Exception $e) {
            Log::error('Download Signed VS License Error', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while downloading the file.');
        }
    }
}
