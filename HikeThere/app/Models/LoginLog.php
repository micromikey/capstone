<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{

      use HasFactory;

    protected $fillable = [
        'user_id', 'ip_address', 'user_agent', 'login_successful', 'logged_in_at'
    ];

    protected $casts = [
        'login_successful' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   

    public $timestamps = true; // keeps created_at / updated_at
    
}
