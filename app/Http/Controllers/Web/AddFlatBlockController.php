<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddFlatBlockController extends Controller
{
    public function addFlatBlock(Request $request)
    {

        if (!$request->session()->has('user')) {
            abort(403, 'Authentication required');
        }
        return view('housingTheme.add-block.add-block');  
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