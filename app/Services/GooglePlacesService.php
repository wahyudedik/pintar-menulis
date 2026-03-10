<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Google Places Service
 * 
 * Optional service for real Google data
 * Only used when user wants accurate/real-time data
 */
class GooglePlacesService
{
    private string $apiKey;
    private bool $enabled;
    
    public function __construct()
    {
        // Disabled - fokus ke ML free
        $this->apiKey = '';
        $this->enabled = false;
    }
    
    /**
     * Check if Google API is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
    
    /**
     * Get trending places by industry
     */
    public function getTrendingPlaces(string $industry, string $location = 'Indonesia', int $limit = 20): array
    {
        if (!$this->enabled) {
            return [];
        }
        
        $cacheKey = "google_places_{$industry}_{$location}";
        
        return Cache::remember($cacheKey, 3600, function () use ($industry, $location, $limit) {
            try {
                $type = $this->mapIndustryToPlaceType($industry);
                
                $response = Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
                    'query' => "{$type} in {$location}",
                    'key' => $this->apiKey,
                    'language' => 'id',
                ]);
                
                if ($response->successful()) {
                    $results = $response->json()['results'] ?? [];
                    
                    return collect($results)
                        ->take($limit)
                        ->map(function ($place) {
                            return [
                                'name' => $place['name'],
                                'rating' => $place['rating'] ?? 0,
                                'user_ratings_total' => $place['user_ratings_total'] ?? 0,
                                'types' => $place['types'] ?? [],
                                'vicinity' => $place['vicinity'] ?? '',
                            ];
                        })
                        ->toArray();
                }
                
                return [];
            } catch (\Exception $e) {
                Log::error('Google Places API error: ' . $e->getMessage());
                return [];
            }
        });
    }
    
    /**
     * Get keyword suggestions from Google Autocomplete
     */
    public function getKeywordSuggestions(string $query, string $location = 'Indonesia'): array
    {
        if (!$this->enabled) {
            return [];
        }
        
        $cacheKey = "google_autocomplete_{$query}_{$location}";
        
        return Cache::remember($cacheKey, 3600, function () use ($query, $location) {
            try {
                $response = Http::get('https://maps.googleapis.com/maps/api/place/autocomplete/json', [
                    'input' => $query,
                    'components' => 'country:id',
                    'key' => $this->apiKey,
                    'language' => 'id',
                ]);
                
                if ($response->successful()) {
                    $predictions = $response->json()['predictions'] ?? [];
                    
                    return collect($predictions)
                        ->map(function ($prediction) {
                            return [
                                'keyword' => $prediction['description'],
                                'place_id' => $prediction['place_id'],
                                'types' => $prediction['types'] ?? [],
                            ];
                        })
                        ->toArray();
                }
                
                return [];
            } catch (\Exception $e) {
                Log::error('Google Autocomplete API error: ' . $e->getMessage());
                return [];
            }
        });
    }
    
    /**
     * Get place details for competitor analysis
     */
    public function getPlaceDetails(string $placeId): ?array
    {
        if (!$this->enabled) {
            return null;
        }
        
        $cacheKey = "google_place_details_{$placeId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($placeId) {
            try {
                $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
                    'place_id' => $placeId,
                    'fields' => 'name,rating,user_ratings_total,reviews,types,website,formatted_phone_number',
                    'key' => $this->apiKey,
                    'language' => 'id',
                ]);
                
                if ($response->successful()) {
                    return $response->json()['result'] ?? null;
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error('Google Place Details API error: ' . $e->getMessage());
                return null;
            }
        });
    }
    
    /**
     * Get trending hashtags from Google Trends (simulated)
     */
    public function getTrendingHashtags(string $industry, string $location = 'Indonesia'): array
    {
        // Note: Google Trends doesn't have official API
        // This is a placeholder for future implementation
        // Could use unofficial libraries or web scraping
        
        return [];
    }
    
    /**
     * Map industry to Google Place type
     */
    private function mapIndustryToPlaceType(string $industry): string
    {
        $mapping = [
            'fashion' => 'clothing store',
            'food' => 'restaurant',
            'beauty' => 'beauty salon',
            'printing' => 'print shop',
            'photography' => 'photographer',
            'catering' => 'catering service',
            'home_decor' => 'home goods store',
            'handmade' => 'craft store',
            'digital_service' => 'marketing agency',
            'automotive' => 'car repair',
        ];
        
        return $mapping[$industry] ?? $industry;
    }
    
    /**
     * Get API usage info
     */
    public function getUsageInfo(): array
    {
        return [
            'enabled' => $this->enabled,
            'api_key_configured' => !empty($this->apiKey),
            'features' => [
                'trending_places' => $this->enabled,
                'keyword_suggestions' => $this->enabled,
                'competitor_analysis' => $this->enabled,
                'location_based_hashtags' => false, // Future feature
            ],
        ];
    }
}
