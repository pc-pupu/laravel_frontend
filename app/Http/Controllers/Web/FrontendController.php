<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

/**
 * Public-facing CMS and static pages (no auth).
 * Fetches content from backend API and renders outerTheme views.
 */
class FrontendController extends Controller
{
    private function getApiBaseUrl(): string
    {
        return rtrim(config('services.api.base_url', ''), '/');
    }

    public function showData()
    {
        $response = Http::get($this->getApiBaseUrl() . '/users');
        $data = $response->json();

        return view('data-view', ['users' => $data['users'] ?? []]);
    }

    /** About Us: single content block from CMS (content_type = about_us). */
    public function about_us()
    {
        $data = $this->fetchPublicContent('about-us');

        return view('outerTheme.pages.about-us', [
            'link_title'         => $data['link_title'] ?? 'About Us',
            'content_title'      => $data['content_title'] ?? '',
            'content_description'=> $data['content_description'] ?? '',
        ]);
    }

    /** Contact Us: single content block + map iframe. */
    public function contact_us()
    {
        $data = $this->fetchPublicContent('contact-us');

        return view('outerTheme.pages.contact-us', [
            'link_title'         => $data['link_title'] ?? 'Contact Us',
            'content_title'      => $data['content_title'] ?? '',
            'content_description'=> $data['content_description'] ?? '',
        ]);
    }

    /** FAQ: list of items, rendered as accordion. */
    public function faq()
    {
        $items = $this->fetchPublicContentList('faq');

        return view('outerTheme.pages.faq', ['faqs' => $items]);
    }

    /** Notice: list with title, download link, date of notification. */
    public function notice(Request $request)
    {
        $items = $this->fetchPublicContentList('notice');

        return view('outerTheme.pages.notice', ['notices' => $items]);
    }

    /** User Manual: list with description and download link (CMS content_type = user_manual). */
    public function user_manual()
    {
        $items = $this->fetchPublicContentList('user-manual');

        return view('outerTheme.pages.user-manual', ['manuals' => $items]);
    }

    /** Fetch single public CMS content (about_us, contact_us, what_is_new). */
    private function fetchPublicContent(string $type): array
    {
        $response = Http::get($this->getApiBaseUrl() . '/content/' . $type);
        $data = $response->json();

        return is_array($data) ? $data : [];
    }

    /** Fetch list of public CMS content (faq, notice, user_manual). */
    private function fetchPublicContentList(string $type): Collection
    {
        $response = Http::get($this->getApiBaseUrl() . '/content/' . $type);
        $data = $response->json();

        return collect(is_array($data) ? $data : []);
    }
}
