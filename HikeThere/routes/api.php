<?php

use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\TrailController;
use App\Http\Controllers\Api\TrailSegmentController;
use App\Http\Controllers\Api\GPXController;
use App\Http\Controllers\Api\WeatherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MountainController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Mountains dataset endpoints
Route::get('/mountains', [MountainController::class, 'index']);
Route::get('/mountains/{slug}', [MountainController::class, 'show']);

// Weather API endpoints
Route::get('/weather/current', [WeatherController::class, 'getCurrentWeather']);

// Trail API endpoints
Route::prefix('trails')->group(function () {
    Route::get('/', [TrailController::class, 'index']);
    Route::get('/search', [TrailController::class, 'search']);
    Route::get('/search-trails', [TrailController::class, 'searchTrails']); // New frontend search endpoint
    Route::get('/nearby', [TrailController::class, 'getNearbyTrails']); // New nearby trails endpoint
    Route::get('/debug', [TrailController::class, 'debugTrails']); // Debug endpoint
    Route::post('/search-osm', [TrailController::class, 'searchOSM']);
    // Favorite/unfavorite a trail (authenticated)
    Route::post('/favorite/toggle', [App\Http\Controllers\Api\TrailFavoriteController::class, 'toggle'])->middleware('auth:sanctum');
    Route::get('/favorites', [App\Http\Controllers\Api\TrailFavoriteController::class, 'index'])->middleware('auth:sanctum');
    Route::get('/map-data', [TrailController::class, 'getMapData']);
    Route::get('/{trail}', [TrailController::class, 'show']);
    Route::get('/{trail}/details', [TrailController::class, 'getDetails']);
    Route::get('/{trail}/elevation', [TrailController::class, 'getElevation']);
    Route::get('/{trail}/route', [TrailController::class, 'getTrailRoute']);
    Route::get('/paths', [TrailController::class, 'getTrailPaths']);
    Route::post('/search-nearby', [TrailController::class, 'searchNearby']);
});

// Header unified search endpoint (frontend header/search inputs use this)
Route::get('/header-search', [\App\Http\Controllers\SearchController::class, 'headerSearch']);

// Organization search endpoint (for organization users to search their own content)
// Using web middleware to access session-based auth
Route::middleware(['web'])->get('/org-search', [\App\Http\Controllers\SearchController::class, 'orgSearch']);

// Recommender proxy endpoint (calls local ML service)
Route::get('/recommender/user/{id}', [App\Http\Controllers\Api\RecommenderController::class, 'forUser']);

// AllTrails OSM Derivative Database API - Trail Segments
Route::prefix('trail-segments')->group(function () {
    Route::post('/generate', [TrailSegmentController::class, 'generateSegments']);
    Route::post('/find-trail', [TrailSegmentController::class, 'findTrailSegments']);
    Route::get('/stored', [TrailSegmentController::class, 'getStoredSegments']);
    Route::post('/build-route', [TrailSegmentController::class, 'buildOptimizedRoute']);
    Route::get('/intersections/nearby', [TrailSegmentController::class, 'getNearbyIntersections']);
});

// Location API endpoints
Route::prefix('locations')->group(function () {
    Route::get('/', [LocationController::class, 'index']);
    Route::get('/search', [LocationController::class, 'search']);
    Route::post('/google-places', [LocationController::class, 'handleGooglePlacesLocation']);
    Route::get('/{location}', [LocationController::class, 'show']);
});

// Weather API endpoint
Route::get('/weather', [App\Http\Controllers\Api\WeatherController::class, 'getWeather']);
Route::get('/weather/forecast', [App\Http\Controllers\Api\WeatherController::class, 'getForecast']);
Route::post('/weather/trail-conditions', [App\Http\Controllers\Api\WeatherController::class, 'getTrailConditions']);

// Enhanced Hiking-specific API endpoints
Route::prefix('hiking')->group(function () {
    Route::get('/trail-conditions', function () {
        // Enhanced mock trail conditions data
        return response()->json([
            'trails' => [
                [
                    'id' => 1,
                    'name' => 'Mount Pulag Trail',
                    'status' => 'open',
                    'conditions' => 'Excellent - Clear skies and dry trail',
                    'last_updated' => now()->subHours(2)->toISOString(),
                    'hazards' => [],
                    'recommendations' => ['Bring warm clothing', 'Start early to catch sunrise', 'Bring extra water'],
                ],
                [
                    'id' => 2,
                    'name' => 'Mount Apo Trail',
                    'status' => 'caution',
                    'conditions' => 'Fair - Recent rainfall, some muddy sections',
                    'last_updated' => now()->subHours(4)->toISOString(),
                    'hazards' => ['Slippery rocks near stream crossing', 'Limited visibility at summit'],
                    'recommendations' => ['Bring trekking poles', 'Waterproof gear essential', 'Check weather forecast'],
                ],
                [
                    'id' => 3,
                    'name' => 'Mount Mayon Trail',
                    'status' => 'closed',
                    'conditions' => 'Closed - Volcanic activity alert level 2',
                    'last_updated' => now()->subDays(1)->toISOString(),
                    'hazards' => ['Volcanic activity', 'Restricted access'],
                    'recommendations' => ['Trail temporarily closed', 'Monitor PHIVOLCS updates', 'Alternative trails available'],
                ],
            ],
            'general_conditions' => [
                'weather_status' => 'Good',
                'overall_safety' => 'Normal precautions advised',
                'peak_season' => 'Dry season (November to April)',
                'last_updated' => now()->toISOString(),
            ],
        ]);
    });

    Route::get('/safety-info', function () {
        return response()->json([
            'emergency_contacts' => [
                'local_police' => '117',
                'mountain_rescue' => '143',
                'forest_service' => 'Contact local DENR office',
                'emergency' => '911',
                'coast_guard' => '143-247-8727',
                'red_cross' => '143-527-8385',
            ],
            'safety_tips' => [
                'Always inform someone of your hiking plans and expected return',
                'Check weather conditions and trail status before departure',
                'Bring essential supplies: water, food, first aid kit, emergency shelter',
                'Stay on marked trails and follow Leave No Trace principles',
                'Carry a whistle, flashlight, and emergency communication device',
                'Know your limits and turn back if conditions worsen',
                'Travel in groups and never hike alone in remote areas',
                'Bring proper hiking gear and dress in layers',
                'Start early to avoid afternoon thunderstorms',
                'Inform local guides or park rangers of your presence',
            ],
            'current_conditions' => [
                'weather' => 'Generally fair, expect afternoon showers',
                'trail_status' => 'Most trails open, check individual trail conditions',
                'hazards' => 'Standard hiking precautions apply',
                'visibility' => 'Good to excellent in most areas',
                'temperature_range' => '18-28°C at lower elevations, 10-20°C at peaks',
            ],
            'essential_gear' => [
                'navigation' => ['Map', 'Compass', 'GPS device or smartphone with offline maps'],
                'sun_protection' => ['Sunglasses', 'Sunscreen', 'Hat'],
                'insulation' => ['Extra layers', 'Rain gear', 'Emergency shelter'],
                'illumination' => ['Headlamp', 'Backup flashlight', 'Extra batteries'],
                'first_aid' => ['First aid kit', 'Personal medications', 'Emergency whistle'],
                'fire' => ['Waterproof matches', 'Lighter', 'Fire starter'],
                'repair_tools' => ['Multi-tool', 'Duct tape', 'Gear repair kit'],
                'nutrition' => ['Extra food', 'Water', 'Water purification method'],
                'hydration' => ['Water bottles', 'Hydration system', 'Electrolyte supplements'],
                'emergency_shelter' => ['Emergency blanket', 'Tarp', 'Bivy sack'],
            ],
        ]);
    });

    Route::get('/emergency-procedures', function () {
        return response()->json([
            'general_emergency' => [
                'step_1' => 'Stay calm and assess the situation',
                'step_2' => 'Ensure your safety before helping others',
                'step_3' => 'Call for help using emergency numbers: 911, 143, 117',
                'step_4' => 'Provide your exact location using GPS coordinates',
                'step_5' => 'Administer first aid if trained and safe to do so',
                'step_6' => 'Stay with the injured person if possible',
                'step_7' => 'Signal for help using whistle, mirror, or bright colors',
            ],
            'getting_lost' => [
                'stop' => 'Stop moving immediately',
                'think' => 'Think about how you got to this point',
                'observe' => 'Look around for familiar landmarks',
                'plan' => 'Plan your next move carefully',
                'signal' => 'Use whistle (3 sharp blasts) to signal for help',
                'shelter' => 'Find or create shelter if weather deteriorates',
                'conserve' => 'Conserve energy and water',
            ],
            'severe_weather' => [
                'lightning' => 'Avoid peaks, ridges, and isolated trees',
                'rain' => 'Seek shelter and avoid stream crossings',
                'fog' => 'Stay put until visibility improves',
                'wind' => 'Avoid exposed areas and unstable trees',
            ],
            'wildlife_encounters' => [
                'snakes' => 'Back away slowly, do not make sudden movements',
                'wild_boar' => 'Make noise, appear large, back away slowly',
                'hornets' => 'Cover face and run to shelter if attacked',
            ],
        ]);
    });
});

// GPX API endpoints
Route::prefix('gpx')->group(function () {
    Route::post('/process', [GPXController::class, 'processGPX'])->name('api.gpx.process');
    Route::post('/generate', [GPXController::class, 'generateGPX'])->name('api.gpx.generate');
});
