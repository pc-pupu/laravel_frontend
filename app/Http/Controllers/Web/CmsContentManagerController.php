<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class CmsContentManagerController extends Controller
{
    private string $backend;

    public function __construct()
    {
        $this->backend = rtrim(env('BACKEND_API'), '/');
    }

    public function index(Request $request)
    {
        $response = $this->authorizedRequest()
            ->get($this->backend . '/api/admin/cms-content', $request->query());

        if (!$response->successful()) {
            return back()->with('error', $response->json('message') ?? 'Failed to load CMS contents.');
        }

        $payload = $response->json('data') ?? [];
        $collection = new Collection($payload['data'] ?? []);

        $contents = new LengthAwarePaginator(
            $collection,
            $payload['total'] ?? $collection->count(),
            $payload['per_page'] ?? 15,
            $payload['current_page'] ?? 1,
            [
                'path'  => url()->current(),
                'query' => $request->query(),
            ]
        );

        return view('housingTheme.cms.content.index', [
            'contents'     => $contents,
            'filters'      => $request->only(['search', 'content_type', 'is_active']),
            'contentTypes' => $this->contentTypes(),
        ]);
    }

    public function create()
    {
        $nextOrder = 1;
        $response = $this->authorizedRequest()->get($this->backend . '/api/admin/cms-content/meta/stats');
        if ($response->successful()) {
            $nextOrder = data_get($response->json(), 'data.next_order_no', 1);
        }

        return view('housingTheme.cms.content.create', [
            'contentTypes' => $this->contentTypes(),
            'content'      => null,
            'nextOrder'    => $nextOrder,
        ]);
    }

    public function store(Request $request)
    {
        $payload = $this->buildPayload($request);

        $builder = $this->authorizedRequest();

        if ($request->hasFile('content_file_upload')) {
            $file = $request->file('content_file_upload');
            $builder = $builder->attach(
                'content_file_upload',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            );
        }

        $response = $builder->post($this->backend . '/api/admin/cms-content', $payload);

        if ($response->status() === 422) {
            return $this->handleValidation($response, $request);
        }

        if (!$response->successful()) {
            return back()->withInput()->with('error', $response->json('message') ?? 'Failed to add content.');
        }

        return redirect()->route('cms-content.index')->with('success', 'Content added successfully.');
    }

    public function edit($id)
    {
        $response = $this->authorizedRequest()->get($this->backend . "/api/admin/cms-content/{$id}");

        if (!$response->successful()) {
            return redirect()->route('cms-content.index')->with('error', $response->json('message') ?? 'Content not found.');
        }

        return view('housingTheme.cms.content.edit', [
            'contentTypes' => $this->contentTypes(),
            'content'      => $response->json('data'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $payload = $this->buildPayload($request);
        $builder = $this->authorizedRequest();

        if ($request->hasFile('content_file_upload')) {
            $file = $request->file('content_file_upload');
            $builder = $builder->attach(
                'content_file_upload',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            );
        }

        $response = $builder->put($this->backend . "/api/admin/cms-content/{$id}", $payload);

        if ($response->status() === 422) {
            return $this->handleValidation($response, $request);
        }

        if (!$response->successful()) {
            return back()->withInput()->with('error', $response->json('message') ?? 'Failed to update content.');
        }

        return redirect()->route('cms-content.index')->with('success', 'Content updated successfully.');
    }

    public function destroy($id)
    {
        $response = $this->authorizedRequest()->delete($this->backend . "/api/admin/cms-content/{$id}");

        if (!$response->successful()) {
            return redirect()->route('cms-content.index')
                ->with('error', $response->json('message') ?? 'Failed to delete content.');
        }

        return redirect()->route('cms-content.index')->with('success', 'Content deleted successfully.');
    }

    protected function handleValidation($response, Request $request)
    {
        $errors = $response->json('errors') ?? [];
        $message = $response->json('message');

        if (!$message && !empty($errors)) {
            $flattened = collect($errors)->flatten();
            $message = $flattened->first();
        }

        $message = $message ?: 'Please review the highlighted fields.';

        return back()->withErrors($errors)->withInput()->with('error', $message);
    }

    protected function buildPayload(Request $request): array
    {
        return [
            'content_type'        => $request->input('content_type'),
            'link_title'          => $request->input('link_title'),
            'content_title'       => $request->input('content_title'),
            'content_description' => $request->input('content_description'),
            'order_no'            => $request->input('order_no'),
            'meta_keyword'        => $request->input('meta_keyword'),
            'meta_description'    => $request->input('meta_description'),
            'date_of_notification'=> $request->input('date_of_notification'),
            'is_active'           => $request->input('is_active', 1),
            'is_new'              => $request->boolean('is_new'),
        ];
    }

    protected function authorizedRequest()
    {
        $token = session('api_token');
        return Http::acceptJson()->withToken($token);
    }

    protected function contentTypes(): array
    {
        return [
            'faq'          => 'FAQ',
            'about_us'     => 'About Us',
            'contact_us'   => 'Contact Us',
            'what_is_new'  => "What's New",
            'notice'       => 'Notice',
            'user_manual'  => 'User Manual',
        ];
    }
}

