<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WilayahIndonesiaService
{
    private string $baseUrl = 'https://wilayah.id/api';

    /**
     * Get all provinces
     * Cached for 30 days (data rarely changes)
     */
    public function getProvinces(): array
    {
        return Cache::remember('wilayah_provinces', now()->addDays(30), function () {
            try {
                $response = Http::timeout(10)->get($this->baseUrl . '/provinces.json');
                
                if ($response->successful()) {
                    $data = $response->json();
                    // API returns: {"data": [{"code": "11", "name": "Aceh"}, ...]}
                    $provinces = $data['data'] ?? $data ?? [];
                    
                    // Normalize: convert "code" to "id" for consistency
                    return array_map(function ($province) {
                        return [
                            'id' => $province['code'] ?? $province['id'] ?? null,
                            'name' => $province['name'] ?? '',
                        ];
                    }, $provinces);
                }
                
                Log::warning('Wilayah.id API Error - Provinces', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return [];
            } catch (\Exception $e) {
                Log::error('Wilayah.id Exception - Provinces', [
                    'message' => $e->getMessage(),
                ]);
                return [];
            }
        });
    }

    /**
     * Get regencies (kabupaten/kota) by province ID
     * Cached for 30 days
     */
    public function getRegencies(string $provinceId): array
    {
        return Cache::remember("wilayah_regencies_{$provinceId}", now()->addDays(30), function () use ($provinceId) {
            try {
                $response = Http::timeout(10)->get($this->baseUrl . "/regencies/{$provinceId}.json");
                
                if ($response->successful()) {
                    $data = $response->json();
                    // API returns: {"data": [{"code": "1101", "province_code": "11", "name": "Aceh Selatan"}, ...]}
                    $regencies = $data['data'] ?? $data ?? [];
                    
                    // Normalize: convert "code" to "id" for consistency
                    return array_map(function ($regency) {
                        return [
                            'id' => $regency['code'] ?? $regency['id'] ?? null,
                            'province_id' => $regency['province_code'] ?? $regency['province_id'] ?? null,
                            'name' => $regency['name'] ?? '',
                        ];
                    }, $regencies);
                }
                
                Log::warning('Wilayah.id API Error - Regencies', [
                    'province_id' => $provinceId,
                    'status' => $response->status(),
                ]);
                
                return [];
            } catch (\Exception $e) {
                Log::error('Wilayah.id Exception - Regencies', [
                    'message' => $e->getMessage(),
                    'province_id' => $provinceId,
                ]);
                return [];
            }
        });
    }

    /**
     * Get districts (kecamatan) by regency ID
     * Cached for 30 days
     */
    public function getDistricts(string $regencyId): array
    {
        return Cache::remember("wilayah_districts_{$regencyId}", now()->addDays(30), function () use ($regencyId) {
            try {
                $response = Http::timeout(10)->get($this->baseUrl . "/districts/{$regencyId}.json");
                
                if ($response->successful()) {
                    $data = $response->json();
                    // API returns: {"data": [{"code": "1101010", "regency_code": "1101", "name": "Bakongan"}, ...]}
                    $districts = $data['data'] ?? $data ?? [];
                    
                    // Normalize: convert "code" to "id" for consistency
                    return array_map(function ($district) {
                        return [
                            'id' => $district['code'] ?? $district['id'] ?? null,
                            'regency_id' => $district['regency_code'] ?? $district['regency_id'] ?? null,
                            'name' => $district['name'] ?? '',
                        ];
                    }, $districts);
                }
                
                return [];
            } catch (\Exception $e) {
                Log::error('Wilayah.id Exception - Districts', [
                    'message' => $e->getMessage(),
                    'regency_id' => $regencyId,
                ]);
                return [];
            }
        });
    }

    /**
     * Get villages (kelurahan/desa) by district ID
     * Cached for 30 days
     */
    public function getVillages(string $districtId): array
    {
        return Cache::remember("wilayah_villages_{$districtId}", now()->addDays(30), function () use ($districtId) {
            try {
                $response = Http::timeout(10)->get($this->baseUrl . "/villages/{$districtId}.json");
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data ?? [];
                }
                
                return [];
            } catch (\Exception $e) {
                Log::error('Wilayah.id Exception - Villages', [
                    'message' => $e->getMessage(),
                    'district_id' => $districtId,
                ]);
                return [];
            }
        });
    }

    /**
     * Search provinces by name
     */
    public function searchProvinces(string $query): array
    {
        $provinces = $this->getProvinces();
        $query = strtolower($query);
        
        return array_filter($provinces, function ($province) use ($query) {
            return str_contains(strtolower($province['name']), $query);
        });
    }

    /**
     * Search regencies by name (across all provinces or within a province)
     */
    public function searchRegencies(string $query, ?string $provinceId = null): array
    {
        $query = strtolower($query);
        $results = [];
        
        if ($provinceId) {
            // Search within specific province
            $regencies = $this->getRegencies($provinceId);
            foreach ($regencies as $regency) {
                if (str_contains(strtolower($regency['name']), $query)) {
                    $results[] = $regency;
                }
            }
        } else {
            // Search across all provinces (slower, but more flexible)
            $provinces = $this->getProvinces();
            foreach ($provinces as $province) {
                $regencies = $this->getRegencies($province['id']);
                foreach ($regencies as $regency) {
                    if (str_contains(strtolower($regency['name']), $query)) {
                        $results[] = $regency;
                    }
                }
            }
        }
        
        return $results;
    }

    /**
     * Get province by ID
     */
    public function getProvinceById(string $provinceId): ?array
    {
        $provinces = $this->getProvinces();
        foreach ($provinces as $province) {
            if ($province['id'] === $provinceId) {
                return $province;
            }
        }
        return null;
    }

    /**
     * Get regency by ID
     */
    public function getRegencyById(string $regencyId): ?array
    {
        // Regency ID format: first 2 digits = province ID
        $provinceId = substr($regencyId, 0, 2);
        $regencies = $this->getRegencies($provinceId);
        
        foreach ($regencies as $regency) {
            if ($regency['id'] === $regencyId) {
                return $regency;
            }
        }
        return null;
    }

    /**
     * Normalize city/regency name for matching with RajaOngkir
     * Removes prefixes like "Kota", "Kabupaten", etc.
     */
    public function normalizeName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/^(kota|kabupaten|kab\.|kab)\s*/i', '', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }

    /**
     * Find regency ID that matches RajaOngkir city name
     * This helps bridge between Wilayah.id and RajaOngkir
     */
    public function findRegencyIdByRajaOngkirName(string $rajaOngkirCityName, ?string $provinceId = null): ?string
    {
        $normalized = $this->normalizeName($rajaOngkirCityName);
        
        if ($provinceId) {
            $regencies = $this->getRegencies($provinceId);
        } else {
            // Search all provinces
            $provinces = $this->getProvinces();
            $regencies = [];
            foreach ($provinces as $province) {
                $regencies = array_merge($regencies, $this->getRegencies($province['id']));
            }
        }
        
        // Try exact match first
        foreach ($regencies as $regency) {
            if ($this->normalizeName($regency['name']) === $normalized) {
                return $regency['id'];
            }
        }
        
        // Try partial match
        foreach ($regencies as $regency) {
            $regencyName = $this->normalizeName($regency['name']);
            if (str_contains($regencyName, $normalized) || str_contains($normalized, $regencyName)) {
                return $regency['id'];
            }
        }
        
        return null;
    }
}

