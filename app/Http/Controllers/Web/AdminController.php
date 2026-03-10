<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    private $backend;

    public function __construct()
    {
        $this->backend = $this->normalizeBackendUrl(rtrim(env('BACKEND_API', ''), '/'));
    }

    /**
     * Ensure backend URL has a valid port so cURL does not reject it.
     * Fixes: "URL rejected: Port number was not a decimal number between 0 and 65535"
     */
    private function normalizeBackendUrl(string $base): string
    {
        if ($base === '') {
            return '';
        }
        $parsed = parse_url($base);
        if ($parsed === false || !isset($parsed['host'])) {
            return $base;
        }
        $scheme = $parsed['scheme'] ?? 'http';
        $host = $parsed['host'];
        $port = $parsed['port'] ?? null;
        $path = $parsed['path'] ?? '';
        $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
        $validPort = null;
        if ($port !== null && $port !== '') {
            $p = is_numeric($port) ? (int) $port : null;
            if ($p !== null && $p >= 0 && $p <= 65535) {
                $validPort = $p;
            }
        }
        if ($validPort === null) {
            $validPort = ($scheme === 'https') ? 443 : 80;
        }
        $defaultPort = ($scheme === 'https') ? 443 : 80;
        $showPort = ($validPort !== $defaultPort);
        $url = $scheme . '://' . $host . ($showPort ? ':' . $validPort : '') . $path . $query;
        return rtrim($url, '/');
    }

    /***********************************************************
     * UNIVERSAL BACKEND API CALLER
     ***********************************************************/
    private function apiRequest($method, $endpoint, $data = [])
    {
        if ($this->backend === '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Backend API URL is not configured. Set BACKEND_API in .env.',
            ], 500);
        }

        $url = $this->backend .'/api'. '/' . ltrim($endpoint, '/');

        // For GET requests, append query parameters to URL
        if (strtoupper($method) === 'GET' && !empty($data)) {
            $queryString = http_build_query($data);
            $url .= '?' . $queryString;
        }

        $token = session('api_token');

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
        ];

        if ($token) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (in_array(strtoupper($method), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Proxy Error: ' . curl_error($ch),
            ], 500);
        }

        curl_close($ch);

        return response($response)->header('Content-Type', 'application/json');
    }


    /***********************************************************
     * PAGE VIEWS
     ***********************************************************/
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function usersPage()
    {
        return view('admin.users');
    }

    public function rolesPage()
    {
        return view('admin.roles');
    }

    public function permissionsPage()
    {
        return view('admin.permissions');
    }

    public function errorLogsPage()
    {
        return view('admin.error-logs');
    }

    public function sidebarMenusPage()
    {
        return view('admin.sidebar-menus');
    }

    /**
     * Cache & Optimize page (clear cache, view, optimize for this Laravel app).
     */
    public function cachePage()
    {
        return view('admin.cache');
    }

    /**
     * Run cache/view/optimize clear on both frontend and backend. POST with action: cache|view|optimize|config|route|all.
     */
    public function clearCache(Request $request)
    {
        $action = $request->input('action', 'all');
        $allowed = ['cache', 'view', 'optimize', 'config', 'route', 'all'];
        if (!in_array($action, $allowed, true)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid action.'], 422);
        }

        $commands = [
            'cache'    => ['cache:clear', 'Application cache cleared'],
            'view'     => ['view:clear', 'Compiled views cleared'],
            'config'   => ['config:clear', 'Configuration cache cleared'],
            'route'    => ['route:clear', 'Route cache cleared'],
            'optimize' => ['optimize:clear', 'Optimize (bootstrap) cache cleared'],
        ];

        // ---- Frontend (this app) ----
        $frontendResults = [];
        if ($action === 'all') {
            foreach ($commands as $key => [$cmd, $label]) {
                try {
                    Artisan::call($cmd);
                    $frontendResults[$key] = ['ok' => true, 'message' => $label];
                } catch (\Throwable $e) {
                    $frontendResults[$key] = ['ok' => false, 'message' => $e->getMessage()];
                }
            }
        } else {
            [$cmd, $label] = $commands[$action];
            try {
                Artisan::call($cmd);
                $frontendResults[$action] = ['ok' => true, 'message' => $label];
            } catch (\Throwable $e) {
                $frontendResults[$action] = ['ok' => false, 'message' => $e->getMessage()];
            }
        }

        // ---- Backend (API) ----
        $backendResults = null;
        $backendError = null;
        if ($this->backend !== '') {
            try {
                $backendResponse = $this->apiRequest('POST', 'admin/cache/clear', ['action' => $action]);
                $content = $backendResponse->getContent();
                $data = json_decode($content, true);
                $backendResults = $data['results'] ?? [];
                $backendError = ($data['status'] ?? '') === 'error' ? ($data['message'] ?? 'Backend returned error') : null;
            } catch (\Throwable $e) {
                $backendResults = ['_error' => ['ok' => false, 'message' => $e->getMessage()]];
                $backendError = $e->getMessage();
            }
        } else {
            $backendResults = ['_error' => ['ok' => false, 'message' => 'Backend API not configured (BACKEND_API).']];
            $backendError = 'Backend not configured';
        }

        $frontendOk = !in_array(false, array_column($frontendResults, 'ok'));
        $backendOk = $backendError === null && (is_array($backendResults) && !isset($backendResults['_error']));
        $allOk = $frontendOk && $backendOk;

        return response()->json([
            'status'  => $allOk ? 'success' : 'error',
            'message' => $allOk ? 'Frontend and backend cleared.' : ($frontendOk ? 'Frontend cleared; backend had issues.' : 'Some commands failed.'),
            'results' => [
                'frontend' => $frontendResults,
                'backend'  => $backendResults,
            ],
        ], $allOk ? 200 : 500);
    }


    /***********************************************************
     * USERS PROXY (ALL FIXED)
     ***********************************************************/
    public function listUsers(Request $req)
    {
        return $this->apiRequest('GET', "admin/users", $req->query());
    }

    public function getUser($id)
    {
        return $this->apiRequest('GET', "admin/users/$id");
    }

    public function createUser(Request $req)
    {
        return $this->apiRequest('POST', "admin/users", $req->all());
    }

    public function updateUser(Request $req, $id)
    {
        return $this->apiRequest('PUT', "admin/users/$id", $req->all());
    }

    public function deleteUser($id)
    {
        return $this->apiRequest('DELETE', "admin/users/$id");
    }


    /***********************************************************
     * ROLES PROXY
     ***********************************************************/
    public function listRoles(Request $req)
    {
        return $this->apiRequest('GET', "admin/roles", $req->query());
    }

    public function getRole($id)
    {
        return $this->apiRequest('GET', "admin/roles/$id");
    }

    public function createRole(Request $req)
    {
        return $this->apiRequest('POST', "admin/roles", $req->all());
    }

    public function updateRole(Request $req, $id)
    {
        return $this->apiRequest('PUT', "admin/roles/$id", $req->all());
    }

    public function deleteRole($id)
    {
        return $this->apiRequest('DELETE', "admin/roles/$id");
    }


    /***********************************************************
     * PERMISSIONS PROXY
     ***********************************************************/
    public function listPermissions(Request $req)
    {
        return $this->apiRequest('GET', "admin/permissions", $req->query());
    }

    public function getPermission($id)
    {
        return $this->apiRequest('GET', "admin/permissions/$id");
    }

    public function createPermission(Request $req)
    {
        return $this->apiRequest('POST', "admin/permissions", $req->all());
    }

    public function updatePermission(Request $req, $id)
    {
        return $this->apiRequest('PUT', "admin/permissions/$id", $req->all());
    }

    public function deletePermission($id)
    {
        return $this->apiRequest('DELETE', "admin/permissions/$id");
    }


    /***********************************************************
     * ERROR LOGS PROXY
     ***********************************************************/
    public function listErrorLogs(Request $req)
    {
        return $this->apiRequest('GET', "admin/error-logs", $req->query());
    }

    public function getErrorLog($id)
    {
        return $this->apiRequest('GET', "admin/error-logs/$id");
    }

    public function deleteErrorLog($id)
    {
        return $this->apiRequest('DELETE', "admin/error-logs/$id");
    }

    public function clearAllErrorLogs()
    {
        return $this->apiRequest('DELETE', "admin/error-logs");
    }

    public function clearErrorLogsByTime(Request $req)
    {
        return $this->apiRequest('DELETE', "admin/error-logs/clear-by-time", $req->all());
    }


    /***********************************************************
     * SIDEBAR MENUS PROXY
     ***********************************************************/
    public function listSidebarMenus(Request $req)
    {
        return $this->apiRequest('GET', "admin/sidebar-menus/all", $req->query());
    }

    public function getSidebarMenu($id)
    {
        return $this->apiRequest('GET', "admin/sidebar-menus/$id");
    }

    public function createSidebarMenu(Request $req)
    {
        $response = $this->apiRequest('POST', "admin/sidebar-menus", $req->all());
        
        // Clear sidebar menu cache after creating menu
        \App\Http\ViewComposers\SidebarMenuComposer::clearAllCaches();
        
        return $response;
    }

    public function updateSidebarMenu(Request $req, $id)
    {
        $response = $this->apiRequest('PUT', "admin/sidebar-menus/$id", $req->all());
        
        // Clear sidebar menu cache after updating menu
        \App\Http\ViewComposers\SidebarMenuComposer::clearAllCaches();
        
        return $response;
    }

    public function deleteSidebarMenu($id)
    {
        $response = $this->apiRequest('DELETE', "admin/sidebar-menus/$id");
        
        // Clear sidebar menu cache after deleting menu
        \App\Http\ViewComposers\SidebarMenuComposer::clearAllCaches();
        
        return $response;
    }
}
