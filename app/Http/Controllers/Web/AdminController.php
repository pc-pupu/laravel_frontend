<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private $backend;

    public function __construct()
    {
        $this->backend = rtrim(env('BACKEND_API'), '/');
    }

    /***********************************************************
     * UNIVERSAL BACKEND API CALLER
     ***********************************************************/
    private function apiRequest($method, $endpoint, $data = [])
    {
        $url = $this->backend . '/api/' . ltrim($endpoint, '/');

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
        return $this->apiRequest('POST', "admin/sidebar-menus", $req->all());
    }

    public function updateSidebarMenu(Request $req, $id)
    {
        return $this->apiRequest('PUT', "admin/sidebar-menus/$id", $req->all());
    }

    public function deleteSidebarMenu($id)
    {
        return $this->apiRequest('DELETE', "admin/sidebar-menus/$id");
    }
}
