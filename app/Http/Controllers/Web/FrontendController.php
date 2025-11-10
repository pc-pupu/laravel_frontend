<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;

class FrontendController extends Controller
{
    public function showData(){
        // $response = Http::get('http://127.0.0.1:8000/api/users');
        $response = Http::get(config('services.api.base_url') . '/users');
        $data = $response->json();

        // Pass users array to Blade
        return view('data-view', ['users' => $data['users']]);
    }

    public function about_us(){   
        $response = Http::get(config('services.api.base_url') . '/content/about-us');
        $data = $response->json();
        // dd($data);

        // Pass to the view
        return view('outerTheme.pages.about-us', [
            'link_title' => $data['link_title'] ?? [],
            'content_title' => $data['content_title'] ?? [],
            'content_description' => $data['content_description'] ?? []
        ]);
    }

    public function contact_us(){   
        $response = Http::get(config('services.api.base_url') . '/content/contact-us');
        $data = $response->json();
        
        // Pass to the view
        return view('outerTheme.pages.contact-us', [
            'link_title' => $data['link_title'] ?? [],
            'content_title' => $data['content_title'] ?? [],
            'content_description' => $data['content_description'] ?? []
        ]);
    }

    public function faq(){   
        $response = Http::get(config('services.api.base_url') . '/content/faq');
        $allData = collect($response->json());

        $perPage = 1;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $allData->slice(($currentPage - 1) * $perPage, $perPage)->values();

        
        return view('outerTheme.pages.faq', ['faqs' => $allData]);
    }

    public function notice(Request $request){ 
        
        $response = Http::get(config('services.api.base_url') . '/content/notice');
        $allData = collect($response->json());

        $perPage = 1;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $allData->slice(($currentPage - 1) * $perPage, $perPage)->values();

        // Step 3: Create paginator instance
        $paginatedData = new LengthAwarePaginator(
            $currentItems,
            $allData->count(),
            $perPage,
            $currentPage,
            ['path' => url()->current()]
        );

        return view('outerTheme.pages.notice', ['notices' => $paginatedData]);
    }
}
