<?php

namespace App\Http\Controllers;

use App\Services\RajaOngkirService;
use App\Services\WilayahIndonesiaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    public function __construct(
        private RajaOngkirService $rajaOngkirService,
        private WilayahIndonesiaService $wilayahService
    ) {}

    /**
     * Search cities/destinations (NEW - for API V2)
     */
    public function searchDestinations(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string', 'min:2'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $results = $this->rajaOngkirService->searchDestination(
            $request->query,
            $request->limit ?? 10
        );

        return response()->json([
            'success' => true,
            'data' => $results,
        ]);
    }

    /**
     * Get subdistrict ID from city name (NEW - for API V2)
     */
    public function getSubdistrictId(Request $request)
    {
        $request->validate([
            'city_name' => ['required', 'string'],
            'subdistrict_name' => ['nullable', 'string'],
        ]);

        Log::info('Getting subdistrict ID', $request->all());

        try {
            $subdistrictId = $this->rajaOngkirService->getSubdistrictId(
                $request->city_name,
                $request->subdistrict_name
            );

            if (!$subdistrictId) {
                // Try to find suggestions
                $suggestions = $this->rajaOngkirService->searchDestination($request->city_name, 5);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Lokasi tidak ditemukan. Pastikan nama kota sudah benar.',
                    'suggestions' => $suggestions,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'subdistrict_id' => $subdistrictId,
            ]);
        } catch (\Exception $e) {
            // Check if it's a rate limit error
            if (str_contains($e->getMessage(), 'Quota') || str_contains($e->getMessage(), 'limit')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error_type' => 'rate_limit',
                ], 429);
            }
            
            Log::error('Error getting subdistrict ID', [
                'error' => $e->getMessage(),
                'city_name' => $request->city_name,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari lokasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate shipping cost (NEW - for API V2)
     */
    public function calculateCost(Request $request)
    {
        $request->validate([
            'origin' => ['required', 'string'],
            'destination' => ['required', 'string'],
            'weight' => ['required', 'integer', 'min:1'],
            'couriers' => ['nullable', 'string'],
        ]);

        Log::info('Calculating shipping cost', $request->all());

        $couriers = $request->couriers ?? 'jne:sicepat:jnt:tiki:pos';

        $costs = $this->rajaOngkirService->calculateCost(
            $request->origin,
            $request->destination,
            $request->weight,
            $couriers
        );

        if (empty($costs)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghitung ongkir. Pastikan data sudah benar.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $costs,
        ]);
    }

    // ============ WILAYAH DATA METHODS (using Wilayah.id API) ============
    // These methods use free API to avoid RajaOngkir quota consumption

    /**
     * Get all provinces (using Wilayah.id - FREE, no quota limit)
     */
    public function getProvinces()
    {
        $provinces = $this->wilayahService->getProvinces();

        return response()->json([
            'success' => true,
            'data' => $provinces,
        ]);
    }

    /**
     * Get regencies (kabupaten/kota) by province ID (using Wilayah.id - FREE)
     */
    public function getCities(Request $request)
    {
        $request->validate([
            'province_id' => ['required', 'string'],
        ]);

        $regencies = $this->wilayahService->getRegencies($request->province_id);

        return response()->json([
            'success' => true,
            'data' => $regencies,
        ]);
    }

    /**
     * Get districts (kecamatan) by regency ID (using Wilayah.id - FREE)
     */
    public function getDistricts(Request $request)
    {
        $request->validate([
            'regency_id' => ['required', 'string'],
        ]);

        $districts = $this->wilayahService->getDistricts($request->regency_id);

        return response()->json([
            'success' => true,
            'data' => $districts,
        ]);
    }

    /**
     * Search provinces by name (using Wilayah.id - FREE)
     */
    public function searchProvinces(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string', 'min:2'],
        ]);

        $provinces = $this->wilayahService->searchProvinces($request->query);

        return response()->json([
            'success' => true,
            'data' => array_values($provinces), // Re-index array
        ]);
    }

    /**
     * Search regencies by name (using Wilayah.id - FREE)
     */
    public function searchRegencies(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string', 'min:2'],
            'province_id' => ['nullable', 'string'],
        ]);

        $regencies = $this->wilayahService->searchRegencies(
            $request->query,
            $request->province_id
        );

        return response()->json([
            'success' => true,
            'data' => $regencies,
        ]);
    }

    public function searchCities(Request $request)
    {
        $request->validate([
            'query' => ['required', 'string', 'min:2'],
        ]);

        $cities = $this->rajaOngkirService->searchCities($request->query);

        return response()->json([
            'success' => true,
            'data' => $cities,
        ]);
    }

    public function checkCost(Request $request)
    {
        $request->validate([
            'origin' => ['required', 'string'],
            'destination' => ['required', 'string'],
            'weight' => ['required', 'integer', 'min:1'],
            'courier' => ['nullable', 'string'],
        ]);

        Log::info('Checking shipping cost (legacy)', $request->all());

        $costs = $this->rajaOngkirService->checkCost(
            $request->origin,
            $request->destination,
            $request->weight,
            $request->courier ?? 'jne'
        );

        if (empty($costs)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghitung ongkir. Pastikan data sudah benar.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $costs,
        ]);
    }

    public function getCityId(Request $request)
    {
        $request->validate([
            'city_name' => ['required', 'string'],
            'province_name' => ['nullable', 'string'],
        ]);

        Log::info('Getting city ID (legacy)', $request->all());

        $cityId = $this->rajaOngkirService->getCityId(
            $request->city_name,
            $request->province_name
        );

        if (!$cityId) {
            $suggestions = $this->rajaOngkirService->searchCities($request->city_name, 5);
            
            return response()->json([
                'success' => false,
                'message' => 'Kota tidak ditemukan',
                'suggestions' => $suggestions,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'city_id' => $cityId,
        ]);
    }
}