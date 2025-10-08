<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OrganizationPaymentCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'paymongo_secret_key',
        'paymongo_public_key',
        'xendit_api_key',
        'active_gateway',
        'is_active',
        'qr_code_path',
        'payment_method',
        'manual_payment_instructions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the organization user that owns these credentials
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Encrypt and set PayMongo secret key
     */
    public function setPaymongoSecretKeyAttribute($value)
    {
        $this->attributes['paymongo_secret_key'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Decrypt and get PayMongo secret key
     */
    public function getPaymongoSecretKeyAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Encrypt and set PayMongo public key
     */
    public function setPaymongoPublicKeyAttribute($value)
    {
        $this->attributes['paymongo_public_key'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Decrypt and get PayMongo public key
     */
    public function getPaymongoPublicKeyAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Encrypt and set Xendit API key
     */
    public function setXenditApiKeyAttribute($value)
    {
        $this->attributes['xendit_api_key'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Decrypt and get Xendit API key
     */
    public function getXenditApiKeyAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Check if PayMongo is configured
     */
    public function hasPaymongoConfigured(): bool
    {
        return !empty($this->paymongo_secret_key) && !empty($this->paymongo_public_key);
    }

    /**
     * Check if Xendit is configured
     */
    public function hasXenditConfigured(): bool
    {
        return !empty($this->xendit_api_key);
    }

    /**
     * Check if any payment gateway is configured
     */
    public function hasAnyGatewayConfigured(): bool
    {
        return $this->hasPaymongoConfigured() || $this->hasXenditConfigured();
    }

    /**
     * Check if manual payment is configured
     */
    public function hasManualPaymentConfigured(): bool
    {
        return !empty($this->qr_code_path);
    }

    /**
     * Get the full URL for the QR code
     */
    public function getQrCodeUrl(): ?string
    {
        if (empty($this->qr_code_path)) {
            return null;
        }

        try {
            $disk = config('filesystems.default', 'public');
            
            // For GCS, construct the full URL
            if ($disk === 'gcs') {
                $bucket = config('filesystems.disks.gcs.bucket');
                if ($bucket) {
                    return 'https://storage.googleapis.com/' . $bucket . '/' . $this->qr_code_path;
                }
            }
            
            // For local/public disk, use Storage facade
            return Storage::disk($disk)->url($this->qr_code_path);
        } catch (\Exception $e) {
            Log::error('Failed to get QR code URL', [
                'path' => $this->qr_code_path,
                'disk' => config('filesystems.default'),
                'error' => $e->getMessage()
            ]);
            
            // Fallback: try GCS URL directly if it looks like a GCS path
            $bucket = config('filesystems.disks.gcs.bucket');
            if ($bucket && strpos($this->qr_code_path, 'qr_codes/') !== false) {
                return 'https://storage.googleapis.com/' . $bucket . '/' . $this->qr_code_path;
            }
            
            // Final fallback to asset helper
            return asset('storage/' . $this->qr_code_path);
        }
    }

    /**
     * Check if organization is using manual payment
     */
    public function isManualPayment(): bool
    {
        return $this->payment_method === 'manual';
    }
}
