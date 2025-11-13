<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    private $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = config('services.api.base_url', 'http://localhost:8000/api');
    }

    /**
     * Admin Dashboard
     */
    public function dashboard(Request $request)
    {
        // Ensure token is in session
        $token = $request->session()->get('api_token');
        if (!$token) {
            return redirect()->route('login')->with('error', 'Please login to access admin panel.');
        }
        
        return view('admin.dashboard', ['api_token' => $token]);
    }

    /**
     * Users Management
     */
    public function users(Request $request)
    {
        $token = $request->session()->get('api_token');
        if (!$token) {
            return redirect()->route('login')->with('error', 'Please login to access admin panel.');
        }
        
        return view('admin.users', ['api_token' => $token]);
    }

    /**
     * Roles Management
     */
    public function roles(Request $request)
    {
        $token = $request->session()->get('api_token');
        if (!$token) {
            return redirect()->route('login')->with('error', 'Please login to access admin panel.');
        }
        
        return view('admin.roles', ['api_token' => $token]);
    }

    /**
     * Permissions Management
     */
    public function permissions(Request $request)
    {
        $token = $request->session()->get('api_token');
        if (!$token) {
            return redirect()->route('login')->with('error', 'Please login to access admin panel.');
        }
        
        return view('admin.permissions', ['api_token' => $token]);
    }

    /**
     * Error Logs
     */
    public function errorLogs(Request $request)
    {
        $token = $request->session()->get('api_token');
        if (!$token) {
            return redirect()->route('login')->with('error', 'Please login to access admin panel.');
        }
        
        return view('admin.error-logs', ['api_token' => $token]);
    }
}

