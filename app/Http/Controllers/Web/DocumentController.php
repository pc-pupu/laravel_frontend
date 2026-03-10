<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
                Log::error('Document Download API Error', ['status' => $response->status()]);
                abort($response->status(), 'Failed to download document');
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
            Log::error('Document Download Error', ['error' => $e->getMessage()]);
            abort(500, 'Failed to download document');
        }
    }

    /**
     * View document (auth required). Proxies through backend so access control is enforced.
     */
    public function view(Request $request, $path)
    {
        if (!$request->session()->has('user') || !$request->session()->has('api_token')) {
            abort(403, 'Authentication required');
        }

        $encryptedPath = $path;
        if (empty($encryptedPath)) {
            abort(403, 'Missing document path');
        }

        try {
            $token = $request->session()->get('api_token');
            $backend = rtrim(env('BACKEND_API'), '/');
            $response = Http::withToken($token)->withOptions(['stream' => true])
                ->get($backend . '/api/document/download', ['path' => $encryptedPath]);

            if (!$response->successful()) {
                Log::warning('Document View API Error', ['status' => $response->status()]);
                abort($response->status(), 'Failed to view document');
            }

            $contentType = $response->header('Content-Type') ?? 'application/octet-stream';
            $contentDisposition = $response->header('Content-Disposition');
            $fileName = 'document';
            if ($contentDisposition && preg_match('/filename="?([^"]+)"?/', $contentDisposition, $m)) {
                $fileName = $m[1];
            }
            $inlineTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'text/plain'];
            $mimeType = strtolower(trim(explode(';', $contentType)[0]));
            $disposition = in_array($mimeType, $inlineTypes) ? 'inline' : 'attachment';

            return response()->stream(function () use ($response) {
                echo $response->body();
            }, 200, [
                'Content-Type' => $contentType,
                'Content-Disposition' => $disposition . '; filename="' . $fileName . '"',
            ]);
        } catch (\Exception $e) {
            Log::error('Document View Error', ['error' => $e->getMessage()]);
            abort(500, 'Failed to view document');
        }
    }

}