<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddFlatBlockController extends Controller
{
    public function addFlatBlock(Request $request) // Modified by Subham dt.12-03-2026
    {
        if (!$request->session()->has('user')) {
            abort(403, 'Authentication required');
        }

        $blocks = collect(); // default empty collection

        try {
            $token   = $request->session()->get('api_token');
            $backend = rtrim(env('BACKEND_API'), '/');

            $response = Http::withToken($token)->get($backend . '/api/block/list');

            if ($response->successful()) {
                $blocks = collect($response->json());
            }
        } catch (\Exception $e) {
            Log::error('Fetch Blocks Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return view('housingTheme.add-block.add-block', compact('blocks'));
    }
    
    public function storeFlatBlock(Request $request)
    {
        // print_r($request->all());die;
        $request->validate([
            'block_name' => 'required|string|max:255',
        ]);

        try {
            $token   = $request->session()->get('api_token');
            $backend = rtrim(env('BACKEND_API'), '/');

            $response = Http::withToken($token)
                ->post($backend . '/api/block/add', [
                    'block_name' => $request->block_name,
                ]);

            if (!$response->successful()) {
                Log::error('Add Block API Error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                abort($response->status(), 'Failed to add block');
            }

            return redirect()->back()->with('success', 'Block added successfully');

        } catch (\Exception $e) {
            Log::error('Add Block Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            abort(500, 'Failed to add block: ' . $e->getMessage());
        }
    }
}