<?php
// Quick debug script to check QR code configuration
// Access via: /debug-qr-config

use App\Models\OrganizationPaymentCredential;

Route::get('/debug-qr-config', function() {
    $user = auth()->user();
    
    if (!$user || $user->user_type !== 'organization') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    $credentials = OrganizationPaymentCredential::where('user_id', $user->id)->first();
    
    if (!$credentials) {
        return response()->json(['error' => 'No payment credentials found'], 404);
    }
    
    $disk = config('filesystems.default', 'public');
    $bucket = config('filesystems.disks.gcs.bucket');
    
    return response()->json([
        'qr_code_path' => $credentials->qr_code_path,
        'qr_code_url' => $credentials->getQrCodeUrl(),
        'filesystem_disk' => $disk,
        'gcs_bucket' => $bucket,
        'is_gcs' => $disk === 'gcs',
        'has_qr_code' => !empty($credentials->qr_code_path),
        'expected_gcs_url' => $bucket ? 'https://storage.googleapis.com/' . $bucket . '/' . $credentials->qr_code_path : null,
    ]);
})->middleware('auth')->name('debug.qr.config');
