<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VacancyListController extends Controller
{
    private string $backend;

    public function __construct()
    {
        $this->backend = rtrim(env('BACKEND_API'), '/');
    }

    /**
     * District-wise Vacancy List page (for higher officials)
     * Corresponds to Drupal 'vacany_list' when accessed by district users.
     */
    public function districtWise(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $districtId = (int) $request->input('district_id', 0);
        $flatTypeId = (int) $request->input('flat_type_id', 0);

        try {
            $token = session('api_token');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($this->backend . '/api/vacancy-list/district-wise', [
                'district_id' => $districtId ?: null,
                'flat_type_id' => $flatTypeId ?: null,
            ]);

            if (!$response->successful()) {
                $message = $response->json('message') ?? 'Failed to load vacancy list.';
                return view('housingTheme.vacancy-list.district-wise', [
                    'districts' => [],
                    'flatTypes' => [],
                    'rows' => [],
                    'selectedDistrictId' => $districtId,
                    'selectedFlatTypeId' => $flatTypeId,
                    'totalVacantFlats' => 0,
                    'error' => $message,
                ]);
            }

            $data = $response->json('data') ?? [];

            return view('housingTheme.vacancy-list.district-wise', [
                'districts' => $data['districts'] ?? [],
                'flatTypes' => $data['flat_types'] ?? [],
                'rows' => $data['rows'] ?? [],
                'selectedDistrictId' => $districtId,
                'selectedFlatTypeId' => $flatTypeId,
                'totalVacantFlats' => $data['total_vacant_flats'] ?? 0,
                'error' => null,
            ]);

        } catch (\Exception $e) {
            Log::error('Vacancy List District-wise Page Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('housingTheme.vacancy-list.district-wise', [
                'districts' => [],
                'flatTypes' => [],
                'rows' => [],
                'selectedDistrictId' => $districtId,
                'selectedFlatTypeId' => $flatTypeId,
                'totalVacantFlats' => 0,
                'error' => 'An error occurred while loading vacancy list.',
            ]);
        }
    }

    /**
     * RHE-wise Vacancy List page (for Sub-Division / RHE users)
     * Mirrors Drupal show_vacancy_list() usage.
     */
    public function rheWise(Request $request)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $estateId = (int) $request->input('estate_id', 0);
        $flatTypeId = (int) $request->input('flat_type_id', 0);

        try {
            $token = session('api_token');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($this->backend . '/api/vacancy-list/rhe-wise', [
                'estate_id' => $estateId ?: null,
                'flat_type_id' => $flatTypeId ?: null,
            ]);

            if (!$response->successful()) {
                $message = $response->json('message') ?? 'Failed to load RHE-wise vacancy list.';
                return view('housingTheme.vacancy-list.rhe-wise', [
                    'rheList' => [],
                    'flatTypes' => [],
                    'rows' => [],
                    'selectedEstateId' => $estateId,
                    'selectedFlatTypeId' => $flatTypeId,
                    'totalVacantFlats' => 0,
                    'error' => $message,
                ]);
            }

            $data = $response->json('data') ?? [];

            return view('housingTheme.vacancy-list.rhe-wise', [
                'rheList' => $data['rhe_list'] ?? [],
                'flatTypes' => $data['flat_types'] ?? [],
                'rows' => $data['rows'] ?? [],
                'selectedEstateId' => $estateId,
                'selectedFlatTypeId' => $flatTypeId,
                'totalVacantFlats' => $data['total_vacant_flats'] ?? 0,
                'error' => null,
            ]);

        } catch (\Exception $e) {
            Log::error('Vacancy List RHE-wise Page Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('housingTheme.vacancy-list.rhe-wise', [
                'rheList' => [],
                'flatTypes' => [],
                'rows' => [],
                'selectedEstateId' => $estateId,
                'selectedFlatTypeId' => $flatTypeId,
                'totalVacantFlats' => 0,
                'error' => 'An error occurred while loading RHE-wise vacancy list.',
            ]);
        }
    }
}

