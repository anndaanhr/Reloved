<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RajaOngkirService
{
    private string $apiKey;
    private string $baseUrl = 'https://rajaongkir.komerce.id/api/v1';

    public function __construct()
    {
        $this->apiKey = config('services.rajaongkir.api_key');
    }

    /**
     * Search domestic destinations (cities/districts) using the new API
     * This is the recommended method for API V2
     */
    public function searchDestination(string $query, int $limit = 10): array
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
                'accept' => 'application/json',
            ])->get($this->baseUrl . '/destination/domestic-destination', [
                'search' => $query,
                'limit' => $limit,
                'offset' => 0,
            ]);

            $data = $response->json();
            
            Log::info('RajaOngkir Search Destination Response', [
                'query' => $query,
                'status' => $response->status(),
                'full_response' => $data,
                'data_structure' => isset($data['data']) ? 'has_data_key' : 'no_data_key',
                'first_item' => isset($data['data'][0]) ? $data['data'][0] : null,
            ]);
            
            if ($response->successful()) {
                // API V2 returns data in 'data' key
                if (isset($data['data']) && is_array($data['data'])) {
                    return $data['data'];
                }
                // Fallback: if response is directly an array
                if (is_array($data)) {
                    return $data;
                }
            } else {
                // Check for rate limit error
                if ($response->status() === 429) {
                    $errorMessage = $data['meta']['message'] ?? 'Daily limit exceeded';
                    Log::warning('RajaOngkir Rate Limit Exceeded - Search Destination', [
                        'status' => $response->status(),
                        'message' => $errorMessage,
                        'query' => $query,
                    ]);
                    // Return empty array but we'll handle this in getSubdistrictId
                    return [];
                }
                
                Log::error('RajaOngkir API Error - Search Destination', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Exception - Search Destination', [
                'message' => $e->getMessage(),
                'query' => $query,
                'trace' => $e->getTraceAsString(),
            ]);
            return [];
        }
    }

    /**
     * Get provinces (legacy method - may not work on API V2)
     */
    public function getProvinces(): array
    {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
                'accept' => 'application/json',
            ])->get($this->baseUrl . '/destination/province');

            $data = $response->json();
            
            Log::info('RajaOngkir Provinces Response', [
                'status' => $response->status(),
                'data' => $data
            ]);
            
            if ($response->successful() && isset($data['data'])) {
                return $data['data'];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Exception - Provinces', [
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Calculate shipping cost using the new API V2 endpoint
     * OPTIMIZED: Uses cache to avoid repeated API calls for same route
     */
    public function calculateCost(string $originSubdistrictId, string $destinationSubdistrictId, int $weight, string $couriers = 'jne:sicepat:jnt'): array
    {
        // Cache key: origin_destination_weight_couriers
        $cacheKey = 'rajaongkir_cost_' . md5($originSubdistrictId . '_' . $destinationSubdistrictId . '_' . $weight . '_' . $couriers);
        
        // Cache for 1 day (shipping costs can change, but not frequently)
        return Cache::remember($cacheKey, now()->addDay(), function () use ($originSubdistrictId, $destinationSubdistrictId, $weight, $couriers) {
        try {
            $response = Http::withHeaders([
                'key' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post($this->baseUrl . '/calculate/domestic-cost', [
                'origin' => $originSubdistrictId,
                'destination' => $destinationSubdistrictId,
                'weight' => $weight,
                'courier' => $couriers,
            ]);

            $data = $response->json();
            
            Log::info('RajaOngkir Calculate Cost Response', [
                'origin' => $originSubdistrictId,
                'destination' => $destinationSubdistrictId,
                'weight' => $weight,
                'status' => $response->status(),
                'full_response' => $data,
                'has_data_key' => isset($data['data']),
            ]);

            if ($response->successful()) {
                // Try different response structures
                if (isset($data['data']) && is_array($data['data'])) {
                    return $data['data'];
                }
                // Fallback: if response is directly an array
                if (is_array($data) && !empty($data)) {
                    return $data;
                }
            } else {
                Log::error('RajaOngkir API Error - Calculate Cost', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Exception - Calculate Cost', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [];
        }
        });
    }

    /**
     * Legacy checkCost method - redirects to new calculateCost
     */
    public function checkCost(string $origin, string $destination, int $weight, string $courier = 'jne'): array
    {
        return $this->calculateCost($origin, $destination, $weight, $courier);
    }

    /**
     * Get subdistrict ID from city name using search
     * OPTIMIZED: Uses cache and tries fewer search variations
     */
    public function getSubdistrictId(string $cityName, string $subdistrictName = null): ?string
    {
        // Cache key based on normalized city name
        $cacheKey = 'rajaongkir_subdistrict_' . md5(strtolower(trim($cityName)) . ($subdistrictName ? '_' . strtolower(trim($subdistrictName)) : ''));
        
        // Try to get from cache first (cache for 30 days - subdistrict IDs don't change)
        return Cache::remember($cacheKey, now()->addDays(30), function () use ($cityName, $subdistrictName) {
            // Normalize city name - remove common prefixes
            $cityNormalized = $this->normalizeLocationName($cityName);
            
            // OPTIMIZED: Try only 2 variations (reduced from 3)
            // 1. Normalized name (most likely to match)
            // 2. Name without prefix (fallback)
            $searchTerms = [
                $cityNormalized, // Try normalized first (e.g., "bandar lampung")
                preg_replace('/^(kota|kabupaten|kab\.|kab)\s*/i', '', $cityName), // Remove prefix (e.g., "Bandar Lampung")
            ];
            
            // Remove duplicates
            $searchTerms = array_unique($searchTerms);
            
            $allResults = [];
            $hasRateLimitError = false;
            
            // OPTIMIZED: Stop early if we find results in first search
            foreach ($searchTerms as $term) {
                $results = $this->searchDestination($term, 30);
                
                // Check if we got rate limited
                if (empty($results)) {
                    $hasRateLimitError = true;
                } else {
                    // If we got results, add them
                    $allResults = array_merge($allResults, $results);
                    
                    // OPTIMIZED: If we have enough results (5+), stop searching
                    // This reduces API calls from 2-3x to 1x for most cases
                    if (count($allResults) >= 5) {
                        break;
                    }
                }
            }
        
            // Remove duplicates based on ID
            $uniqueResults = [];
            $seenIds = [];
            foreach ($allResults as $result) {
                $id = $result['subdistrict_id'] ?? $result['id'] ?? $result['location_id'] ?? $result['destination_id'] ?? null;
                if ($id && !in_array($id, $seenIds)) {
                    $seenIds[] = $id;
                    $uniqueResults[] = $result;
                } elseif (!$id) {
                    // If no ID, add anyway (might be duplicate but we'll filter later)
                    $uniqueResults[] = $result;
                }
            }
        
        Log::info('Searching for subdistrict', [
            'city' => $cityName,
                'normalized' => $cityNormalized,
                'search_terms' => $searchTerms,
            'subdistrict' => $subdistrictName,
                'results_count' => count($uniqueResults),
                'first_result' => !empty($uniqueResults) ? $uniqueResults[0] : null,
                'has_rate_limit' => $hasRateLimitError && empty($uniqueResults),
            ]);

            if (empty($uniqueResults)) {
                // If we have rate limit error, throw exception with specific message
                if ($hasRateLimitError) {
                    throw new \Exception('Quota RajaOngkir sudah habis. Silakan coba lagi besok atau hubungi administrator.');
                }
            return null;
        }

        // Try to find the key for subdistrict/location ID
        // Common keys: subdistrict_id, id, location_id, destination_id
        $possibleIdKeys = ['subdistrict_id', 'id', 'location_id', 'destination_id', 'subdistrictId'];
        
        // Try exact match first
            foreach ($uniqueResults as $location) {
            $locationCity = $this->normalizeLocationName($location['city_name'] ?? $location['city'] ?? '');
            
            if ($locationCity === $cityNormalized) {
                // If subdistrict is specified, try to match it
                if ($subdistrictName) {
                    $subdistrictNormalized = $this->normalizeLocationName($subdistrictName);
                    $locationSubdistrict = $this->normalizeLocationName(
                        $location['subdistrict_name'] ?? $location['subdistrict'] ?? $location['district'] ?? ''
                    );
                    
                    if (str_contains($locationSubdistrict, $subdistrictNormalized)) {
                        return $this->extractId($location, $possibleIdKeys);
                    }
                } else {
                    // Return first matching city
                    return $this->extractId($location, $possibleIdKeys);
                }
            }
        }
        
            // Partial match - try with normalized names
            foreach ($uniqueResults as $location) {
            $locationCity = $this->normalizeLocationName($location['city_name'] ?? $location['city'] ?? '');
            
                // More flexible matching
                if (str_contains($locationCity, $cityNormalized) || 
                    str_contains($cityNormalized, $locationCity) ||
                    similar_text(strtolower($locationCity), strtolower($cityNormalized)) / max(strlen($locationCity), strlen($cityNormalized)) > 0.7) {
                Log::info('Found partial match', [
                    'input' => $cityNormalized,
                    'matched' => $locationCity,
                    'location' => $location,
                ]);
                return $this->extractId($location, $possibleIdKeys);
            }
        }
            
            // Last resort: return first result if available
            if (!empty($uniqueResults)) {
                Log::info('Using first result as fallback', [
                    'input' => $cityName,
                    'first_result' => $uniqueResults[0],
                ]);
                return $this->extractId($uniqueResults[0], $possibleIdKeys);
            }

        return null;
        });
    }

    /**
     * Extract ID from location array based on possible key names
     */
    private function extractId(array $location, array $possibleKeys): ?string
    {
        foreach ($possibleKeys as $key) {
            if (isset($location[$key]) && !empty($location[$key])) {
                $id = (string) $location[$key];
                Log::info('Extracted ID', [
                    'key' => $key,
                    'id' => $id,
                    'location' => $location,
                ]);
                return $id;
            }
        }

        Log::warning('Could not extract ID from location', [
            'location' => $location,
            'tried_keys' => $possibleKeys,
        ]);

        return null;
    }

    /**
     * Legacy getCityId method - redirects to getSubdistrictId
     */
    public function getCityId(string $cityName, string $provinceName = null): ?string
    {
        return $this->getSubdistrictId($cityName);
    }

    /**
     * Get cities (legacy - may not work on V2)
     */
    public function getCities(string $provinceId = null): array
    {
        // For V2, use search instead
        if (!$provinceId) {
            return [];
        }

        try {
            $url = $this->baseUrl . '/destination/city/' . $provinceId;

            $response = Http::withHeaders([
                'key' => $this->apiKey,
                'accept' => 'application/json',
            ])->get($url);

            $data = $response->json();
            
            if ($response->successful() && isset($data['data'])) {
                return $data['data'];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('RajaOngkir Exception - Cities', [
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Search cities by name (for autocomplete) - using new search API
     */
    public function searchCities(string $query, int $limit = 10): array
    {
        return $this->searchDestination($query, $limit);
    }

    /**
     * Normalize location name for better matching
     */
    private function normalizeLocationName(string $name): string
    {
        // Convert to lowercase
        $name = strtolower(trim($name));
        
        // Remove common prefixes (more comprehensive)
        $name = preg_replace('/^(kota|kabupaten|kab\.|kab|kabupatén|kec\.|kec)\s*/i', '', $name);
        
        // Remove common suffixes
        $name = preg_replace('/\s+(kota|kabupaten|kab\.|kab)$/i', '', $name);
        
        // Remove extra spaces
        $name = preg_replace('/\s+/', ' ', $name);
        
        // Trim
        $name = trim($name);
        
        return $name;
    }
}