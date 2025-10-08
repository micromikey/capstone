<?php
/**
 * Simple script to upload OG image to Google Cloud Storage
 * 
 * Run this script: php upload-og-image.php
 */

require __DIR__.'/vendor/autoload.php';

// Load environment variables
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Storage;

echo "🚀 Uploading OG Image to Google Cloud Storage\n";
echo str_repeat("=", 50) . "\n\n";

// Check if source file exists
$sourceFile = __DIR__ . '/public/img/og-image.png';

if (!file_exists($sourceFile)) {
    echo "❌ Error: og-image.png not found at: public/img/og-image.png\n";
    echo "\n";
    echo "Please:\n";
    echo "1. Open: http://localhost/generate-og-image.html\n";
    echo "2. Generate and download the image\n";
    echo "3. Save it as: public/img/og-image.png\n";
    exit(1);
}

echo "✓ Found og-image.png\n";
echo "  Size: " . number_format(filesize($sourceFile) / 1024, 2) . " KB\n\n";

// Read file contents
$fileContents = file_get_contents($sourceFile);

try {
    echo "📤 Uploading to Google Cloud Storage...\n";
    
    // Upload to GCS
    $gcsPath = 'assets/og-image.png';
    
    Storage::disk('gcs')->put($gcsPath, $fileContents, [
        'visibility' => 'public',
        'CacheControl' => 'public, max-age=86400',
        'ContentType' => 'image/png',
    ]);
    
    $url = Storage::disk('gcs')->url($gcsPath);
    
    echo "✅ Success! Image uploaded to GCS\n\n";
    echo str_repeat("=", 50) . "\n";
    echo "PUBLIC URL:\n";
    echo $url . "\n";
    echo str_repeat("=", 50) . "\n\n";
    
    echo "📝 Next Steps:\n";
    echo "1. Copy the URL above\n";
    echo "2. Test it in browser to make sure it loads\n";
    echo "3. Update your .env file:\n";
    echo "   OG_IMAGE_URL={$url}\n\n";
    echo "4. Use in your meta tags:\n";
    echo "   <meta property=\"og:image\" content=\"{{ env('OG_IMAGE_URL') }}\">\n\n";
    
    // Optional: Also upload icon1.png
    $iconFile = __DIR__ . '/public/img/icon1.png';
    if (file_exists($iconFile)) {
        echo "🎨 Also uploading icon1.png...\n";
        $iconContents = file_get_contents($iconFile);
        Storage::disk('gcs')->put('assets/icon1.png', $iconContents, [
            'visibility' => 'public',
            'CacheControl' => 'public, max-age=86400',
            'ContentType' => 'image/png',
        ]);
        $iconUrl = Storage::disk('gcs')->url('assets/icon1.png');
        echo "✓ Icon uploaded: {$iconUrl}\n\n";
    }
    
    echo "✨ All done! Your images are now hosted on Google Cloud Storage.\n";
    
    // Upload additional tool card images
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "📦 Uploading Hiking Tools Card Images...\n";
    echo str_repeat("=", 50) . "\n\n";
    
    $toolImages = [
        '1.png' => 'Build Itineraries',
        '2.png' => 'Self Assessment',
        '3.png' => 'Bookings'
    ];
    
    foreach ($toolImages as $filename => $title) {
        $imagePath = __DIR__ . '/public/img/' . $filename;
        
        if (file_exists($imagePath)) {
            echo "📤 Uploading {$title} image ({$filename})...\n";
            $imageContents = file_get_contents($imagePath);
            
            Storage::disk('gcs')->put('assets/' . $filename, $imageContents, [
                'visibility' => 'public',
                'CacheControl' => 'public, max-age=86400',
                'ContentType' => 'image/png',
            ]);
            
            $imageUrl = Storage::disk('gcs')->url('assets/' . $filename);
            echo "✓ {$title}: {$imageUrl}\n\n";
        } else {
            echo "⚠️  Skipped {$title} ({$filename}) - file not found\n\n";
        }
    }
    
    echo str_repeat("=", 50) . "\n";
    echo "✅ Upload Complete!\n";
    echo str_repeat("=", 50) . "\n\n";
    
    echo "📝 Summary:\n";
    echo "- OG Image: Uploaded for social media previews\n";
    echo "- Icon Image: Uploaded for logo/favicon\n";
    echo "- Tool Cards: Uploaded for hiking tools page\n\n";
    
    echo "🌐 All images are now accessible via:\n";
    echo "https://storage.googleapis.com/" . env('GCS_BUCKET') . "/assets/[filename]\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    echo "Please check:\n";
    echo "1. GCS_BUCKET is set in .env\n";
    echo "2. GCS credentials are valid\n";
    echo "3. Bucket permissions allow public access\n";
    exit(1);
}
