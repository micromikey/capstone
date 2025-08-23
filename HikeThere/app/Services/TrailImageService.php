<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TrailImageService
{
    protected $unsplashKey;
    protected $pexelsKey;
    protected $pixabayKey;
    protected $googleMapsKey;

    public function __construct()
    {
        $this->unsplashKey = config('services.unsplash.access_key');
        $this->pexelsKey = config('services.pexels.api_key');
        $this->pixabayKey = config('services.pixabay.api_key');
        $this->googleMapsKey = config('services.google.maps_api_key');
    }

    /**
     * Get trail images with organization images having priority
     */
    public function getTrailImages($trail, $limit = 5)
    {
        $images = [];

        // 1. First priority: Organization uploaded images (but skip placeholders)
        if ($trail->images && $trail->images->count() > 0) {
            foreach ($trail->images as $orgImage) {
                // Skip placeholder/demo images
                if (!$this->isPlaceholderImage($orgImage->url)) {
                    $images[] = [
                        'url' => $orgImage->url,
                        'source' => 'organization',
                        'caption' => $orgImage->caption ?? $trail->trail_name,
                        'photographer' => $trail->organization->name ?? 'Trail Organization'
                    ];
                }
            }
        }

        // 2. If we need more images, fetch from APIs
        $remainingSlots = $limit - count($images);
        if ($remainingSlots > 0) {
            $apiImages = $this->fetchApiImages($trail, $remainingSlots);
            $images = array_merge($images, $apiImages);
        }

        return array_slice($images, 0, $limit);
    }

    /**
     * Get primary trail image (organization first, then API)
     */
    public function getPrimaryTrailImage($trail)
    {
        // Check for organization's primary image first, but skip placeholder images
        if ($trail->images && $trail->images->count() > 0) {
            $primaryImage = $trail->images->where('is_primary', true)->first() 
                         ?? $trail->images->first();
            
            // Skip placeholder/demo images (Picsum, Lorem Picsum, etc.)
            if ($primaryImage && !$this->isPlaceholderImage($primaryImage->url)) {
                return [
                    'url' => $primaryImage->url,
                    'source' => 'organization',
                    'caption' => $primaryImage->caption ?? $trail->trail_name
                ];
            }
        }

        // Fall back to API images
        $apiImages = $this->fetchApiImages($trail, 1);
        return $apiImages[0] ?? [
            'url' => '/img/default-trail.jpg',
            'source' => 'default',
            'caption' => $trail->trail_name
        ];
    }

    /**
     * Fetch images from external APIs
     */
    protected function fetchApiImages($trail, $limit = 3)
    {
        $searchTerms = $this->generateSearchTerms($trail);
        $images = [];

        foreach ($searchTerms as $term) {
            if (count($images) >= $limit) break;

            // Try different APIs in order of preference
            $apiMethods = [
                'fetchUnsplashImages',
                'fetchGooglePlacesImages', 
                'fetchPexelsImages',
                'fetchPixabayImages'
            ];

            foreach ($apiMethods as $method) {
                if (count($images) >= $limit) break;
                
                try {
                    $newImages = $this->$method($term, $limit - count($images));
                    $images = array_merge($images, $newImages);
                } catch (\Exception $e) {
                    Log::warning("Failed to fetch images from {$method}: " . $e->getMessage());
                }
            }
        }

        return array_slice($images, 0, $limit);
    }

    /**
     * Generate search terms for the trail
     */
    protected function generateSearchTerms($trail)
    {
        $terms = [];
        
        // Primary terms
        if ($trail->mountain_name) {
            $terms[] = $trail->mountain_name . ' hiking';
            $terms[] = $trail->mountain_name . ' mountain';
        }
        
        if ($trail->trail_name) {
            $terms[] = $trail->trail_name;
        }

        // Location-based terms
        if ($trail->location && $trail->location->name) {
            $terms[] = $trail->location->name . ' hiking';
            $terms[] = $trail->location->name . ' nature';
        }

        // Generic terms based on features
        if ($trail->features) {
            foreach ($trail->features as $feature) {
                $terms[] = $feature . ' hiking philippines';
            }
        }

        // Fallback terms
        $terms[] = 'hiking trail philippines';
        $terms[] = 'mountain hiking philippines';

        return array_unique($terms);
    }

    /**
     * Fetch images from Unsplash API
     */
    protected function fetchUnsplashImages($query, $limit = 3)
    {
        if (!$this->unsplashKey) {
            return [];
        }

        $cacheKey = "unsplash_images_" . md5($query . $limit);
        
        return Cache::remember($cacheKey, 3600, function () use ($query, $limit) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Client-ID ' . $this->unsplashKey
                ])->get('https://api.unsplash.com/search/photos', [
                    'query' => $query,
                    'per_page' => $limit,
                    'orientation' => 'landscape'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $images = [];

                    foreach ($data['results'] as $photo) {
                        $images[] = [
                            'url' => $photo['urls']['regular'],
                            'thumb_url' => $photo['urls']['small'],
                            'source' => 'unsplash',
                            'caption' => $photo['alt_description'] ?? $query,
                            'photographer' => $photo['user']['name'] ?? 'Unknown',
                            'photographer_url' => $photo['user']['links']['html'] ?? null
                        ];
                    }

                    return $images;
                }
            } catch (\Exception $e) {
                Log::error('Unsplash API error: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Fetch images from Google Places API
     */
    protected function fetchGooglePlacesImages($query, $limit = 2)
    {
        if (!$this->googleMapsKey) {
            return [];
        }

        $cacheKey = "google_places_images_" . md5($query . $limit);
        
        return Cache::remember($cacheKey, 3600, function () use ($query, $limit) {
            try {
                // First, search for places
                $response = Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
                    'query' => $query,
                    'key' => $this->googleMapsKey,
                    'type' => 'natural_feature|tourist_attraction'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $images = [];

                    foreach (array_slice($data['results'], 0, 2) as $place) {
                        if (isset($place['photos']) && count($place['photos']) > 0) {
                            foreach (array_slice($place['photos'], 0, 1) as $photo) {
                                $photoUrl = "https://maps.googleapis.com/maps/api/place/photo?" . http_build_query([
                                    'maxwidth' => 800,
                                    'photo_reference' => $photo['photo_reference'],
                                    'key' => $this->googleMapsKey
                                ]);

                                $images[] = [
                                    'url' => $photoUrl,
                                    'thumb_url' => str_replace('maxwidth=800', 'maxwidth=400', $photoUrl),
                                    'source' => 'google_places',
                                    'caption' => $place['name'] ?? $query,
                                    'photographer' => 'Google Places',
                                    'attribution' => $photo['html_attributions'][0] ?? null
                                ];

                                if (count($images) >= $limit) break 2;
                            }
                        }
                    }

                    return $images;
                }
            } catch (\Exception $e) {
                Log::error('Google Places API error: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Fetch images from Pexels API
     */
    protected function fetchPexelsImages($query, $limit = 3)
    {
        if (!$this->pexelsKey) {
            return [];
        }

        $cacheKey = "pexels_images_" . md5($query . $limit);
        
        return Cache::remember($cacheKey, 3600, function () use ($query, $limit) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $this->pexelsKey
                ])->get('https://api.pexels.com/v1/search', [
                    'query' => $query,
                    'per_page' => $limit,
                    'orientation' => 'landscape'
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $images = [];

                    foreach ($data['photos'] as $photo) {
                        $images[] = [
                            'url' => $photo['src']['large'],
                            'thumb_url' => $photo['src']['medium'],
                            'source' => 'pexels',
                            'caption' => $photo['alt'] ?? $query,
                            'photographer' => $photo['photographer'] ?? 'Unknown',
                            'photographer_url' => $photo['photographer_url'] ?? null
                        ];
                    }

                    return $images;
                }
            } catch (\Exception $e) {
                Log::error('Pexels API error: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Fetch images from Pixabay API
     */
    protected function fetchPixabayImages($query, $limit = 3)
    {
        if (!$this->pixabayKey) {
            return [];
        }

        $cacheKey = "pixabay_images_" . md5($query . $limit);
        
        return Cache::remember($cacheKey, 3600, function () use ($query, $limit) {
            try {
                $response = Http::get('https://pixabay.com/api/', [
                    'key' => $this->pixabayKey,
                    'q' => $query,
                    'image_type' => 'photo',
                    'orientation' => 'horizontal',
                    'category' => 'nature',
                    'per_page' => $limit,
                    'min_width' => 800
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $images = [];

                    foreach ($data['hits'] as $photo) {
                        $images[] = [
                            'url' => $photo['largeImageURL'],
                            'thumb_url' => $photo['webformatURL'],
                            'source' => 'pixabay',
                            'caption' => $photo['tags'] ?? $query,
                            'photographer' => $photo['user'] ?? 'Pixabay User'
                        ];
                    }

                    return $images;
                }
            } catch (\Exception $e) {
                Log::error('Pixabay API error: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Check if an image URL is a placeholder/demo image
     */
    protected function isPlaceholderImage($url)
    {
        $placeholderDomains = [
            'picsum.photos',
            'lorem.picsum',
            'placeholder.com',
            'placehold.it',
            'placekitten.com',
            'via.placeholder.com',
            'dummyimage.com'
        ];

        foreach ($placeholderDomains as $domain) {
            if (strpos($url, $domain) !== false) {
                return true;
            }
        }

        return false;
    }
}