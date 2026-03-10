<?php

namespace App\Http\Controllers\Web;

use App\Helpers\UrlEncryptionHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

/**
 * CMS Content Management (admin).
 * Proxies CRUD to backend API; mirrors legacy Drupal cms_content admin (add/list/edit/delete).
 */
class CmsContentManagerController extends Controller
{
    private string $backend;

    public function __construct()
    {
        $this->backend = rtrim(config('services.api.base_url', env('BACKEND_API', '')), '/');
    }

    /** List CMS contents with filters and pagination. */
    public function index(Request $request): View|RedirectResponse
    {
        $query = $request->query();
        if (empty($query['per_page'])) {
            $query['per_page'] = 10;
        }

        $response = $this->authorizedRequest()
            ->get($this->backend . '/cms-content', $query);

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
                'query' => $query,
            ]
        );

        return view('housingTheme.cms.content.index', [
            'contents'     => $contents,
            'filters'      => array_merge($request->only(['search', 'content_type', 'is_active']), ['per_page' => $query['per_page']]),
            'contentTypes' => $this->contentTypes(),
        ]);
    }

    /** Show create form with next order_no from API. */
    public function create(): View
    {
        $nextOrder = 1;
        $response = $this->authorizedRequest()->get($this->backend . '/cms-content/meta/stats');
        if ($response->successful()) {
            $nextOrder = data_get($response->json(), 'data.next_order_no', 1);
        }

        return view('housingTheme.cms.content.create', [
            'contentTypes' => $this->contentTypes(),
            'content'      => null,
            'nextOrder'    => $nextOrder,
        ]);
    }

    /** Store new CMS content (with optional PDF upload). */
    public function store(Request $request): RedirectResponse
    {
        $payload = $this->buildPayload($request);

        $builder = $this->authorizedRequest();

        if ($request->hasFile('content_file_upload')) {
            $file = $request->file('content_file_upload');
            $multipart = [
                [
                    'name'     => 'content_file_upload',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ],
            ];
            foreach ($payload as $key => $value) {
                $multipart[] = [
                    'name'     => $key,
                    'contents' => is_array($value) ? json_encode($value) : (string) ($value ?? ''),
                ];
            }
            $response = $builder->asMultipart()
                ->post($this->backend . '/cms-content', $multipart);
        } else {
            $response = $builder->post($this->backend . '/cms-content', $payload);
        }

        if ($response->status() === 422) {
            return $this->handleValidation($response, $request);
        }

        if (!$response->successful()) {
            return back()->withInput()->with('error', $response->json('message') ?? 'Failed to add content.');
        }

        return redirect()->route('cms-content.index')->with('success', 'Content added successfully.');
    }

    /** Show edit form for one CMS content. Id in URL is encrypted. */
    public function edit($id): View|RedirectResponse
    {
        $decryptedId = $this->decryptCmsContentId($id);
        if ($decryptedId === null) {
            return redirect()->route('cms-content.index')->with('error', 'Invalid content id.');
        }

        $response = $this->authorizedRequest()->get($this->backend . '/cms-content/' . $decryptedId);

        if (!$response->successful()) {
            return redirect()->route('cms-content.index')->with('error', $response->json('message') ?? 'Content not found.');
        }

        $data = $response->json('data');
        // Ensure we have a single content record (not paginated list)
        if (is_array($data) && isset($data['data']) && is_array($data['data'])) {
            return redirect()->route('cms-content.index')->with('error', 'Invalid response.');
        }
        $content = is_array($data) ? $data : [];

        return view('housingTheme.cms.content.edit', [
            'contentTypes'  => $this->contentTypes(),
            'content'      => $content,
            'encrypted_id' => UrlEncryptionHelper::encryptUrl((string) $decryptedId),
        ]);
    }

    /** Update CMS content (optional new PDF replaces existing). Id in URL is encrypted. */
    public function update(Request $request, $id): RedirectResponse
    {
        $decryptedId = $this->decryptCmsContentId($id);
        if ($decryptedId === null) {
            return redirect()->route('cms-content.index')->with('error', 'Invalid content id.');
        }

        $payload = $this->buildPayload($request);
        $payload['is_new'] = $request->is_new ? 1 : 0;
        $builder = $this->authorizedRequest();

        if ($request->hasFile('content_file_upload')) {
            $file = $request->file('content_file_upload');
            // Multipart form: each part must have 'name' and 'contents' (Laravel/Guzzle requirement)
            $multipart = [
                [
                    'name'     => 'content_file_upload',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ],
                [
                    'name'     => '_method',
                    'contents' => 'PUT',
                ],
            ];
            foreach ($payload as $key => $value) {
                $multipart[] = [
                    'name'     => $key,
                    'contents' => is_array($value) ? json_encode($value) : (string) ($value ?? ''),
                ];
            }
            $response = $builder->asMultipart()
                ->post($this->backend . '/cms-content/' . $decryptedId, $multipart);
        } else {
            $response = $builder->put($this->backend . '/cms-content/' . $decryptedId, $payload);
        }

        // print_r($response->json()); die;
        if ($response->status() === 422) {
            return $this->handleValidation($response, $request);
        }

        if (!$response->successful()) {
            return back()->withInput()->with('error', $response->json('message') ?? 'Failed to update content.');
        }

        return redirect()->route('cms-content.index')->with('success', 'Content updated successfully.');
    }

    /** Delete CMS content and associated file. Id in URL is encrypted. */
    public function destroy($id): RedirectResponse
    {
        $decryptedId = $this->decryptCmsContentId($id);
        if ($decryptedId === null) {
            return redirect()->route('cms-content.index')->with('error', 'Invalid content id.');
        }

        $response = $this->authorizedRequest()->delete($this->backend . '/cms-content/' . $decryptedId);

        if (!$response->successful()) {
            return redirect()->route('cms-content.index')
                ->with('error', $response->json('message') ?? 'Failed to delete content.');
        }

        return redirect()->route('cms-content.index')->with('success', 'Content deleted successfully.');
    }

    /** Decrypt CMS content id from URL (accepts encrypted or plain numeric). */
    protected function decryptCmsContentId($id): ?int
    {
        if ($id === null || $id === '') {
            return null;
        }
        if (is_numeric($id)) {
            return (int) $id;
        }
        try {
            $decrypted = UrlEncryptionHelper::decryptUrl($id, true);
            return $decrypted !== '' ? (int) $decrypted : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Map API validation errors to redirect with messages and old input. */
    protected function handleValidation($response, Request $request): RedirectResponse
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

    /** Build request payload for store/update (no file). */
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

    /** HTTP client with session Bearer token for backend API. */
    protected function authorizedRequest(): \Illuminate\Http\Client\PendingRequest
    {
        $token = session('api_token');
        return Http::acceptJson()->withToken($token);
    }

    /** Content type options for dropdowns (must match backend CmsContentType). */
    protected function contentTypes(): array
    {
        return [
            'faq'         => 'FAQ',
            'about_us'    => 'About Us',
            'contact_us'  => 'Contact Us',
            'what_is_new' => "What's New",
            'notice'      => 'Notice',
            'user_manual' => 'User Manual',
        ];
    }
}

