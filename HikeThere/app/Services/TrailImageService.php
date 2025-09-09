<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrailImageService
{
    protected $googleMapsKey;

    public function __construct()
    {
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
                if (! $this->isPlaceholderImage($orgImage->url)) {
                    $images[] = [
                        'url' => $orgImage->url,
                        'source' => 'organization',
                        'caption' => $orgImage->caption ?? $trail->trail_name,
                        'photographer' => $trail->user->display_name ?? 'Trail Organization',
                    ];
                }
            }
        }

        // 2. If we need more images, fetch from Google Places API
        $remainingSlots = $limit - count($images);
        if ($remainingSlots > 0) {
            $googleImages = $this->fetchGooglePlacesImagesForTrail($trail, $remainingSlots);
            $images = array_merge($images, $googleImages);
        }

        return array_slice($images, 0, $limit);
    }

    /**
     * Get primary trail image (organization first, then Google Places)
     */
    public function getPrimaryTrailImage($trail)
    {
        // Check for organization's primary image first, but skip placeholder images
        if ($trail->images && $trail->images->count() > 0) {
            $primaryImage = $trail->images->where('is_primary', true)->first()
                           ?? $trail->images->first();

            // Skip placeholder/demo images (Picsum, Lorem Picsum, etc.)
            if ($primaryImage && ! $this->isPlaceholderImage($primaryImage->url)) {
                return [
                    'url' => $primaryImage->url,
                    'source' => 'organization',
                    'caption' => $primaryImage->caption ?? $trail->trail_name,
                ];
            }
        }

        // Fall back to Google Places API images
        $googleImages = $this->fetchGooglePlacesImagesForTrail($trail, 1);

        return $googleImages[0] ?? [
            'url' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            'source' => 'default',
            'caption' => $trail->trail_name,
        ];
    }

    /**
     * Enhanced Google Places API integration for trail-specific images
     */
    protected function fetchGooglePlacesImagesForTrail($trail, $limit = 3)
    {
        if (! $this->googleMapsKey) {
            return [];
        }

        $cacheKey = 'google_places_trail_images_'.md5($trail->id.$trail->trail_name.$limit);

        return Cache::remember($cacheKey, 7200, function () use ($trail, $limit) {
            $images = [];

            try {
                // Search queries for Google Places API
                $searchQueries = $this->generateGooglePlacesSearchQueries($trail);

                foreach ($searchQueries as $query) {
                    if (count($images) >= $limit) {
                        break;
                    }

                    // Search for places
                    $response = Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
                        'query' => $query,
                        'key' => $this->googleMapsKey,
                        'type' => 'natural_feature|tourist_attraction|establishment',
                        'region' => 'ph', // Philippines
                        'language' => 'en',
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();

                        if (isset($data['results']) && count($data['results']) > 0) {
                            foreach (array_slice($data['results'], 0, 2) as $place) {
                                if (count($images) >= $limit) {
                                    break;
                                }

                                if (isset($place['photos']) && count($place['photos']) > 0) {
                                    foreach (array_slice($place['photos'], 0, 1) as $photo) {
                                        $photoUrl = 'https://maps.googleapis.com/maps/api/place/photo?'.http_build_query([
                                            'maxwidth' => 800,
                                            'photo_reference' => $photo['photo_reference'],
                                            'key' => $this->googleMapsKey,
                                        ]);

                                        $images[] = [
                                            'url' => $photoUrl,
                                            'thumb_url' => str_replace('maxwidth=800', 'maxwidth=400', $photoUrl),
                                            'source' => 'google_places',
                                            'caption' => $place['name'] ?? $trail->trail_name,
                                            'photographer' => 'Google Places',
                                            'attribution' => $photo['html_attributions'][0] ?? null,
                                            'place_id' => $place['place_id'] ?? null,
                                            'rating' => $place['rating'] ?? null,
                                        ];

                                        if (count($images) >= $limit) {
                                            break 2;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                Log::info('Google Places API fetched '.count($images).' images for trail: '.$trail->trail_name);

            } catch (\Exception $e) {
                Log::error('Google Places API error for trail '.$trail->id.': '.$e->getMessage());
            }

            return $images;
        });
    }

    /**
     * Generate optimized search queries for Google Places API
     */
    protected function generateGooglePlacesSearchQueries($trail)
    {
        $queries = [];

        // Primary location-based queries
        if ($trail->location && $trail->location->name) {
            $queries[] = $trail->location->name.' hiking trail';
            $queries[] = $trail->location->name.' mountain';
            $queries[] = $trail->location->name.' nature park';
        }

        // Mountain-specific queries
        if ($trail->mountain_name) {
            $queries[] = $trail->mountain_name.' philippines';
            $queries[] = $trail->mountain_name.' hiking';
            $queries[] = $trail->mountain_name.' mountain trail';
        }

        // Trail-specific queries
        if ($trail->trail_name) {
            $queries[] = $trail->trail_name.' trail philippines';
            $queries[] = $trail->trail_name.' hiking';
        }

        // Combined queries
        if ($trail->mountain_name && $trail->location && $trail->location->name) {
            $queries[] = $trail->mountain_name.' '.$trail->location->name;
        }

        // Generic hiking queries for the region
        if ($trail->location && $trail->location->region) {
            $queries[] = $trail->location->region.' hiking trails';
            $queries[] = $trail->location->region.' mountains';
        }

        // Remove duplicates and limit to top 5 queries
        return array_slice(array_unique($queries), 0, 5);
    }

    /**
     * Get trail image - wrapper method for backward compatibility
     *
     * @param  $trail  Trail model
     * @param  string  $type  'primary' or 'gallery'
     * @param  string  $size  'small', 'medium', 'large' (ignored for now)
     * @return string Image URL
     */
    public function getTrailImage($trail, $type = 'primary', $size = 'medium')
    {
        if ($type === 'primary') {
            $imageData = $this->getPrimaryTrailImage($trail);

            return $imageData['url'];
        }

        // For gallery type, return first image from getTrailImages
        $images = $this->getTrailImages($trail, 1);

        return $images[0]['url'] ?? 'https://images.unsplash.com/photo-1551632811-561732d1e306?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
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
            'dummyimage.com',
        ];

        foreach ($placeholderDomains as $domain) {
            if (strpos($url, $domain) !== false) {
                return true;
            }
        }

        return false;
    }
}
