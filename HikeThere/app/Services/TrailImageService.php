<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TrailImageService
{
    protected $unsplashApiKey;
    protected $pexelsApiKey;
    protected $pixabayApiKey;

    public function __construct()
    {
        $this->unsplashApiKey = config('services.unsplash.access_key');
        $this->pexelsApiKey = config('services.pexels.api_key');
        $this->pixabayApiKey = config('services.pixabay.api_key');
    }

    /**
     * Get trail image from various sources
     */
    public function getTrailImage($trail, $type = 'primary', $size = 'medium')
    {
        $cacheKey = "trail_image_{$trail->id}_{$type}_{$size}";
        
        return Cache::remember($cacheKey, 3600, function () use ($trail, $type, $size) {
            // Try to get from existing trail images first
            if ($trail->images && $trail->images->count() > 0) {
                $image = $type === 'primary' ? $trail->images->first() : $trail->images->random();
                if ($image && $image->image_url) {
                    return $image->image_url;
                }
            }

            // Fallback to API images
            return $this->getApiImage($trail, $type, $size);
        });
    }

    /**
     * Get image from API based on trail information
     */
    protected function getApiImage($trail, $type, $size)
    {
        $keywords = $this->buildSearchKeywords($trail, $type);
        
        // Try different APIs in order of preference
        $image = $this->getUnsplashImage($keywords, $size);
        if ($image) {
            \Log::info("Using Unsplash image for trail {$trail->id}");
            return $image;
        }
        
        $image = $this->getPexelsImage($keywords, $size);
        if ($image) {
            \Log::info("Using Pexels image for trail {$trail->id}");
            return $image;
        }
        
        $image = $this->getPixabayImage($keywords, $size);
        if ($image) {
            \Log::info("Using Pixabay image for trail {$trail->id}");
            return $image;
        }
        
        \Log::info("Using fallback image for trail {$trail->id}");
        return $this->getFallbackImage($trail, $type);
    }

    /**
     * Build search keywords for image search
     */
    protected function buildSearchKeywords($trail, $type)
    {
        $keywords = [];
        
        // Add mountain name (cleaned)
        if ($trail->mountain_name) {
            $keywords[] = str_replace(['Mt.', 'Mount'], '', $trail->mountain_name);
        }
        
        // Add location context (Philippines)
        $keywords[] = 'Philippines';
        
        // Add type-specific keywords
        if ($type === 'primary') {
            $keywords[] = 'hiking trail';
            $keywords[] = 'mountain';
            $keywords[] = 'summit';
            $keywords[] = 'landscape';
        } elseif ($type === 'map') {
            $keywords[] = 'trail map';
            $keywords[] = 'hiking route';
            $keywords[] = 'topographic map';
        } else {
            $keywords[] = 'mountain hiking';
            $keywords[] = 'trail';
            $keywords[] = 'nature';
        }
        
        return implode(' ', array_unique(array_filter($keywords)));
    }

    /**
     * Get image from Unsplash API
     */
    protected function getUnsplashImage($keywords, $size)
    {
        if (!$this->unsplashApiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Client-ID ' . $this->unsplashApiKey
            ])->get('https://api.unsplash.com/search/photos', [
                'query' => $keywords,
                'orientation' => 'landscape',
                'per_page' => 1,
                'order_by' => 'relevant'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['results'][0])) {
                    $photo = $data['results'][0];
                    return $this->getUnsplashImageUrl($photo, $size);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Unsplash API error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get image from Pexels API
     */
    protected function getPexelsImage($keywords, $size)
    {
        if (!$this->pexelsApiKey) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->pexelsApiKey
            ])->get('https://api.pexels.com/v1/search', [
                'query' => $keywords,
                'orientation' => 'landscape',
                'per_page' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['photos'][0])) {
                    $photo = $data['photos'][0];
                    return $this->getPexelsImageUrl($photo, $size);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Pexels API error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get image from Pixabay API
     */
    protected function getPixabayImage($keywords, $size)
    {
        if (!$this->pixabayApiKey) {
            return null;
        }

        try {
            $response = Http::get('https://pixabay.com/api/', [
                'key' => $this->pixabayApiKey,
                'q' => $keywords,
                'orientation' => 'horizontal',
                'per_page' => 1,
                'safesearch' => 'true'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['hits'][0])) {
                    $photo = $data['hits'][0];
                    return $this->getPixabayImageUrl($photo, $size);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Pixabay API error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get Unsplash image URL with appropriate size
     */
    protected function getUnsplashImageUrl($photo, $size)
    {
        $sizes = [
            'small' => 'small',
            'medium' => 'regular',
            'large' => 'full'
        ];

        $sizeKey = $sizes[$size] ?? 'regular';
        return $photo['urls'][$sizeKey] ?? $photo['urls']['regular'];
    }

    /**
     * Get Pexels image URL with appropriate size
     */
    protected function getPexelsImageUrl($photo, $size)
    {
        $sizes = [
            'small' => 'small',
            'medium' => 'medium',
            'large' => 'large'
        ];

        $sizeKey = $sizes[$size] ?? 'medium';
        return $photo['src'][$sizeKey] ?? $photo['src']['medium'];
    }

    /**
     * Get Pixabay image URL with appropriate size
     */
    protected function getPixabayImageUrl($photo, $size)
    {
        $sizes = [
            'small' => 'previewURL',
            'medium' => 'webformatURL',
            'large' => 'largeImageURL'
        ];

        $sizeKey = $sizes[$size] ?? 'webformatURL';
        return $photo[$sizeKey] ?? $photo['webformatURL'];
    }

    /**
     * Get fallback image when APIs fail
     */
    protected function getFallbackImage($trail, $type)
    {
        // Use Lorem Picsum with nature-themed seed based on trail ID
        $seed = $trail->id + ($type === 'map' ? 1000 : 0);
        $width = 800;
        $height = 600;
        
        if ($type === 'primary') {
            // Use Lorem Picsum with blur effect for beautiful nature photos
            return "https://picsum.photos/seed/{$seed}/{$width}/{$height}";
        } elseif ($type === 'map') {
            // Different seed for map-style images
            return "https://picsum.photos/seed/" . ($seed + 500) . "/{$width}/{$height}";
        }
        
        return "https://picsum.photos/seed/{$seed}/{$width}/{$height}";
    }

    /**
     * Get multiple images for a trail
     */
    public function getTrailImages($trail, $count = 3)
    {
        $images = [];
        
        // Get primary image
        $images[] = $this->getTrailImage($trail, 'primary', 'large');
        
        // Get additional images
        for ($i = 1; $i < $count; $i++) {
            $images[] = $this->getTrailImage($trail, 'secondary', 'medium');
        }
        
        return array_filter($images);
    }

    /**
     * Refresh cached images for a trail
     */
    public function refreshTrailImages($trail)
    {
        $cacheKeys = [
            "trail_image_{$trail->id}_primary_small",
            "trail_image_{$trail->id}_primary_medium",
            "trail_image_{$trail->id}_primary_large",
            "trail_image_{$trail->id}_map_medium",
            "trail_image_{$trail->id}_secondary_medium"
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        return true;
    }
}
