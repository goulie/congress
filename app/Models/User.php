<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Services\OtpMailService;
use App\Traits\GeneratesOtp;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Otp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends \TCG\Voyager\Models\User implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, GeneratesOtp;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Boot the model and hook into lifecycle events
     */
    protected static function booted()
    {
       static::creating(function ($user) {
            $user->user_id = 'USR-' . strtoupper(uniqid());

            // Génération OTP à la création
            //$code = $user->generateOtp($user->email, 4, 15);

            // Envoi de l'OTP par email
            //$result = new otpMailServices::($user->email, 'fr', $code['code']);
            //app(OtpMailService::class)->sendOtp($user->email, 'fr', $code['code']);
            //Log::info('OTP envoyé à ' . $user->email);
        });
    }
}
