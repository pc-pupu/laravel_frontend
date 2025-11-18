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
        
        // Get cache version to invalidate when menus change
        $cacheVersion = Cache::get('sidebar_menus_version', 0);
        
        // Cache for 1 minute per user (reduced from 5 minutes for better responsiveness)
        // Include version in cache key so it invalidates when menus change
        $cacheKey = 'sidebar_menus_user_' . $userId . '_v' . $cacheVersion;
        
        return Cache::remember($cacheKey, 60, function () use ($token) {
            try {
                $response = Http::withToken($token)
                    ->timeout(5)
                    ->get(config('services.api.base_url') . '/sidebar-menus');

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['status']) && $data['status'] === 'success') {
                        return $data['data'] ?? [];
                    }
                } else {
                    \Log::warning('Failed to fetch sidebar menus: ' . $response->status() . ' - ' . $response->body());
                }
            } catch (\Exception $e) {
                // Log error if needed
                \Log::error('Failed to fetch sidebar menus: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Clear all sidebar menu caches for all users
     */
    public static function clearAllCaches(): void
    {
        // Store a version/timestamp that invalidates all sidebar menu caches
        // When this changes, all cached menus become invalid
        Cache::put('sidebar_menus_version', time(), 86400); // 24 hours
    }

    /**
     * Clear sidebar menu cache for a specific user
     */
    public static function clearUserCache($userId): void
    {
        $cacheKey = 'sidebar_menus_user_' . $userId;
        Cache::forget($cacheKey);
    }
}

