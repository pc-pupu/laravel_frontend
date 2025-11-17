<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SidebarMenuComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $menus = $this->getMenus();
        $view->with('sidebarMenus', $menus);
    }

    /**
     * Get sidebar menus for current user
     */
    private function getMenus()
    {
        $token = session('api_token');
        
        if (!$token) {
            return [];
        }

        // Get user from session to create unique cache key per user
        $user = session('user');
        $userId = $user['uid'] ?? 'guest';
        
        // Cache for 5 minutes per user
        $cacheKey = 'sidebar_menus_user_' . $userId;
        
        return Cache::remember($cacheKey, 300, function () use ($token) {
            try {
                $response = Http::withToken($token)
                    ->timeout(5)
                    ->get(config('services.api.base_url') . '/sidebar-menus');

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['status']) && $data['status'] === 'success') {
                        return $data['data'] ?? [];
                    }
                }
            } catch (\Exception $e) {
                // Log error if needed
                \Log::error('Failed to fetch sidebar menus: ' . $e->getMessage());
            }

            return [];
        });
    }
}

