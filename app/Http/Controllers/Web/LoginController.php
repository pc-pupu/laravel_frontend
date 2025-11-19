<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm(){
        return view('outerTheme.pages.login')->with('title', 'Login');
    }


    public function login(Request $request){
        $request->validate([
            'username' => 'required|string',
            'pass' => 'required|string',
            'captcha' => 'required|captcha'
        ]);
        
        $response = Http::post(config('services.api.base_url').'/login', [
            'name' => $request->username,
            'password' => $request->pass,
        ]);

        if($response->successful()){
            $data = $response->json();
            
            // Validate response data
            if (!isset($data['token']) || empty($data['token'])) {
                Log::error('Login response missing token', ['data' => $data]);
                return back()->withErrors(['login_error' => 'Login failed: Token not received from server.'])->withInput();
            }
            
            $token = $data['token'];
            $user = $data['user'] ?? null;
            
            // Store user data and token in session
            $request->session()->put('user', $user);
            $request->session()->put('api_token', $token);
            
            // Ensure session is saved
            $request->session()->save();
            
            // Verify token was stored
            if ($request->session()->get('api_token') !== $token) {
                Log::error('Token not stored in session properly');
            }
            
            // Store token in cookie for JavaScript access
            // Set SameSite to 'None' if needed for cross-origin, otherwise 'Lax'
            $cookie = cookie('api_token', $token, 60 * 24 * 7, '/', null, false, false, false, 'Lax'); // 7 days, httpOnly=false so JS can access, path=/, SameSite=Lax
            
            $response = Http::withToken($token)
                ->get(config('services.api.base_url') . '/sidebar-menus');

            $menus = $response->json('data') ?? [];
            // Log::info('Sidebar menus fetched', ['menus' => $menus]);

            session([
                'user' => $user,
                'api_token' => $token,
                'sidebar_menus' => $menus,
            ]);


            // Check if user is admin (uid 1 and name 'admin')
            if ($user && isset($user['uid']) && (int)$user['uid'] === 1 && isset($user['name']) && strtolower($user['name']) === 'admin') {
                // Redirect to admin dashboard with token in cookie
                return redirect()->route('admin.dashboard')->withCookie($cookie);
            }
            
            if ($user && isset($user['uid']) && isset($user['name']) && strtolower($user['name']) !== 'admin') {
                // Redirect to CMS content manager dashboard with token in cookie
                return redirect()->route('dashboard')->withCookie($cookie);
            }

            // return redirect()->route('dashboard')->withCookie($cookie);
        } else {
            // Handle different error scenarios
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? 'Login failed. Please check your credentials.';
            
            // Log error for debugging
            Log::error('Login failed', [
                'username' => $request->username,
                'response' => $errorData,
                'status' => $response->status()
            ]);
            
            return back()->withErrors(['login_error' => $errorMessage])->withInput();
        }
    }


    public function logout(Request $request)
    {
        // Clear session
        $request->session()->forget('user');
        $request->session()->forget('api_token');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear API token cookie
        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.')
            ->withCookie(cookie()->forget('api_token'));
    }
}