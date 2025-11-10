<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    private string $apiKey;
    private string $baseUrl = 'https://rajaongkir.komerce.id/api/v1';

    public function __construct()
    {
        $this->apiKey = config('services.rajaongkir.api_key');
    }

    public function getProvinces(): array
    {
        try {
            // New API endpoint - try API key in header first
            $headers = ['accept' => 'application/json'];
            
            // Try API key in header (common for new APIs)
            if (!empty($this->apiKey)) {
                $headers['key'] = $this->apiKey;
                // Also try Authorization header format
                // $headers['Authorization'] = 'Bearer ' . $this->apiKey;
            }

            $response = Http::withHeaders($headers)->get($this->baseUrl . '/destination/province');

            $data = $response->json();
            
            if ($response->successful()) {
                // Handle new API response format: { "meta": {...}, "data": [...] }
                if (isset($data['data']) && is_array($data['data'])) {
                    return $data['data'];
                } elseif (isset($data['meta']['status']) && $data['meta']['status'] === 'success' && isset($data['data'])) {
                    return is_array($data['data']) ? $data['data'] : [];
                } elseif (is_array($data) && isset($data[0])) {
                    // Jika response langsung array
                    return $data;
                } elseif (isset($data['results']) && is_array($data['results'])) {
                    return $data['results'];
                }
            } else {
                // Check for error in meta
                if (isset($data['meta']['message'])) {
                    \Log::error('RajaOngkir API Error', [
                        'status' => $response->status(),
                        'message' => $data['meta']['message'],
                        'code' => $data['meta']['code'] ?? null,
                    ]);
                } else {
                    \Log::error('RajaOngkir HTTP Error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            }

            return [];
        } catch (\Exception $e) {
            \Log::error('RajaOngkir Exception', [
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }

    public function getCities(string $provinceId = null): array
    {
        try {
            $headers = ['accept' => 'application/json'];
            
            // API key in header (as per documentation)
            if (!empty($this->apiKey)) {
                $headers['key'] = $this->apiKey;
            }
            
            // According to documentation: /destination/city/{province_id}
            // If province_id is provided, use it as path parameter
            if ($provinceId) {
                $url = $this->baseUrl . '/destination/city/' . $provinceId;
            } else {
                // If no province_id, get all cities
                $url = $this->baseUrl . '/destination/city';
            }

            $response = Http::withHeaders($headers)->get($url);

            $data = $response->json();
            
            if ($response->successful()) {
                // Handle new API response format: { "meta": {...}, "data": [...] }
                if (isset($data['data']) && is_array($data['data'])) {
                    return $data['data'];
                } elseif (isset($data['meta']['status']) && $data['meta']['status'] === 'success' && isset($data['data'])) {
                    return is_array($data['data']) ? $data['data'] : [];
                } elseif (is_array($data) && isset($data[0])) {
                    return $data;
                } elseif (isset($data['results']) && is_array($data['results'])) {
                    return $data['results'];
                }
            } else {
                // Check for error in meta
                if (isset($data['meta']['message'])) {
                    \Log::error('RajaOngkir API Error - Get Cities', [
                        'status' => $response->status(),
                        'message' => $data['meta']['message'],
                        'code' => $data['meta']['code'] ?? null,
                    ]);
                } else {
                    \Log::error('RajaOngkir HTTP Error - Get Cities', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function checkCost(string $origin, string $destination, int $weight, string $courier = 'jne'): array
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
            ])->post($this->baseUrl . '/cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['rajaongkir']['results'][0]['costs'])) {
                    return $data['rajaongkir']['results'][0]['costs'];
                }
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getCityId(string $cityName, string $provinceName = null): ?string
    {
        $cities = $this->getCities();
        
        foreach ($cities as $city) {
            if (strtolower($city['city_name']) === strtolower($cityName)) {
                if ($provinceName) {
                    // Check province if provided
                    $provinces = $this->getProvinces();
                    $province = collect($provinces)->firstWhere('province_id', $city['province_id']);
                    if ($province && strtolower($province['province']) === strtolower($provinceName)) {
                        return $city['city_id'];
                    }
                } else {
                    return $city['city_id'];
                }
            }
        }

        return null;
    }
}

