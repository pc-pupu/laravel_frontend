<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        
        // Get API token (may be null for SSO users who haven't generated token yet)
        $token = $request->session()->get('api_token');
        
        try {
            // Get dashboard data from API (includes all DB queries)
            // Dashboard API accepts uid/username as parameters, so token is optional
            $httpClient = Http::acceptJson();
            if ($token) {
                $httpClient = $httpClient->withToken($token);
            }
            $userType = $_COOKIE['user_type'] ?? null;

            $dashboardResponse = $httpClient->get($this->backend . '/api/dashboard', [
                'uid' => $uid,
                'username' => $username,
                'user_type' => $userType,
            ]);
            
            if (!$dashboardResponse->successful()) {
                // echo 'Dashboard API Error';die;
                Log::error('Failed to fetch dashboard data', [
                    'uid' => $uid,
                    'response' => $dashboardResponse->json()
                ]);
                return redirect()->route('homepage')->with('error', 'Failed to load dashboard data');
            }

            $dashboardData = $dashboardResponse->json();
            $output = $dashboardData['data'] ?? [];
            $userRole = $output['user_role'] ?? null;
            // print_r($output);die;
            // Check for redirect (from applicant dashboard logic)
            if (isset($output['redirect'])) {
                $redirect = redirect($output['redirect']);
                if (isset($output['redirect_message'])) {
                    $redirect->with('message', $output['redirect_message']);
                }
                // Clear user_type cookie if redirecting
                if ($output['redirect'] === '/user-tagging') {
                    Cookie::queue(Cookie::forget('user_type'));
                }
                if ($cookie !== null) {
                    return $redirect->withCookie($cookie);
                }
                return $redirect;
            }

            // Clear user_type cookie after processing (Drupal behavior)
            if ($userRole == 4) {
                Cookie::queue(Cookie::forget('user_type'));
            }

            // Role-based view selection
            if (in_array($userRole, [4, 5])) {

                $menus = session('sidebar_menus', []);

                if (empty($menus) && !empty($token)) {

                    $apiResponse = Http::withToken($token)
                        ->get(config('services.api.base_url') . '/sidebar-menus');

                    if ($apiResponse->successful()) {
                        $menus = $apiResponse->json('data') ?? [];
                        session(['sidebar_menus' => $menus]);
                    }
                }

                // Applicant Dashboard
                $response = view('housingTheme.pages.dashboard', [
                    'output' => $output,
                    'sidebar_menus' => $menus
                ]);

            }
            elseif (in_array($userRole, [6, 7, 8, 10, 11, 13, 17])) {
                // Admin Dashboard
                $response = view('housingTheme.pages.dashboard', compact('output'));
            } else {
                // Default/CMS Dashboard
                $response = $this->defaultDashboard($request);
            }

            // Attach cookie if it was set
            if ($cookie !== null) {
                return $response->withCookie($cookie);
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Dashboard Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('homepage')->with('error', 'An error occurred while loading the dashboard');
        }
    }


    // Removed applicantDashboard method - now handled by API

    // Removed adminDashboard method - now handled by API

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

    // Removed all helper methods - now handled by API

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


