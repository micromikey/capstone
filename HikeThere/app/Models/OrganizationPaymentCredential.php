<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

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
     * Check if organization is using manual payment
     */
    public function isManualPayment(): bool
    {
        return $this->payment_method === 'manual';
    }
}
