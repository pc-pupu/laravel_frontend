<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;

class DocumentController extends Controller
{

    public function download(Request $request)
    {
        // Check authentication
        if (!$request->session()->has('user')) {
            abort(403, 'Authentication required');
        }
        
        $encryptedPath = $request->query('path');
        
        if (empty($encryptedPath)) {
            abort(403, 'Missing document path');
        }

        try {
            $token = $request->session()->get('api_token');
            $backend = rtrim(env('BACKEND_API'), '/');
            
            // Call backend API to download document
            $response = Http::withToken($token)
                ->withOptions([
                    'stream' => true, // Stream the file
                ])
                ->get($backend . '/api/document/download', [
                    'path' => $encryptedPath
                ]);

            if (!$response->successful()) {
                $error = $response->json();
                Log::error('Document Download API Error', [
                    'status' => $response->status(),
                    'error' => $error
                ]);
                abort($response->status(), $error['error'] ?? 'Failed to download document');
            }

            // Get file name from response headers or use default
            $contentDisposition = $response->header('Content-Disposition');
            $fileName = 'document.pdf';
            if ($contentDisposition && preg_match('/filename="?([^"]+)"?/', $contentDisposition, $matches)) {
                $fileName = $matches[1];
            }

            // Return the file as download
            return response()->streamDownload(function () use ($response) {
                echo $response->body();
            }, $fileName, [
                'Content-Type' => $response->header('Content-Type') ?? 'application/pdf',
            ]);

        } catch (\Exception $e) {
            Log::error('Document Download Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Failed to download document: ' . $e->getMessage());
        }
    }

    /**
     * View document securely without exposing the original path
     */
    public function view($path)
    {
        try {
            $filePath = Crypt::decryptString($path);
        } catch (\Exception $e) {
            abort(403, 'Invalid document link');
        }

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'Document not found');
        }

        $fullPath = Storage::disk('public')->path($filePath);
        $mimeType = mime_content_type($fullPath);
        $fileName = basename($filePath);

        // Determine if document can be displayed inline
        $canDisplayInline = in_array($mimeType, [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'text/plain',
            'text/html'
        ]);

        if ($canDisplayInline) {
            return response()->file($fullPath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }

        // For unsupported types, force download
        return Storage::disk('public')->download($filePath);
    }

}