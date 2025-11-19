<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    private string $backend;

    public function __construct()
    {
        $this->backend = rtrim(env('BACKEND_API'), '/');
    }

    public function __invoke(Request $request)
    {
        $stats = [
            'existing_with_hrms' => $this->fetchPaginatedTotal('/api/admin/existing-occupants/with-hrms'),
            'existing_without_hrms' => $this->fetchPaginatedTotal('/api/admin/existing-occupants/without-hrms'),
            'cms_items' => $this->fetchPaginatedTotal('/api/admin/cms-content'),
        ];

        return view('housingTheme.dashboard.index', [
            'user' => session('user'),
            'stats' => $stats,
        ]);
    }

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


