<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Helpers\UrlEncryptionHelper;

class OnlineApplicationController extends Controller
{
    private string $backend;

    public function __construct()
    {
        $this->backend = rtrim(env('BACKEND_API'), '/');
    }

    /**
     * Authenticated HTTP client
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
     * Landing page for online application selection.
     * GET /online_application/{url?}
     */
    public function index(Request $request, $url = null)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $selected = $url ? UrlEncryptionHelper::decryptUrl($url) : 'new-apply';

        $user = $request->session()->get('user');
        $uid = $user['uid'] ?? null;

        $statuses = [];
        try {
            $response = $this->authorizedRequest()
                ->get($this->backend . '/api/online-application/statuses', [
                    'uid' => $uid,
                ]);

            if ($response->successful()) {
                $statuses = $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Online Application status fetch failed', [
                'error' => $e->getMessage(),
            ]);
        }

        return view('housingTheme.online-application.index', [
            'selected' => $selected,
            'statuses' => $statuses,
        ]);
    }
}

