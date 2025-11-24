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
use TCG\Voyager\Models\Role;

//class User extends \TCG\Voyager\Models\User,Authenticatable implements MustVerifyEmail
//class User extends \TCG\Voyager\Models\User,Authenticatable implements MustVerifyEmail
class User extends \TCG\Voyager\Models\User implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, GeneratesOtp;


    const ROLE_PARTICIPANT = 'participant';
    const ROLE_SECRETARY = 'secretary';
    const ROLE_FINANCE = 'finance';
    const ROLE_VALIDATOR = 'validator';
    const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'email_verified_at',
        'google_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    public function scopeActive($query)
    {
        if (auth()->user()->role_id == 1) {
            return $query;
        }
        return $query->where('id', auth()->user()->id);
    }
    /**
     * Boot the model and hook into lifecycle events
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            $user->user_id = 'USR-' . strtoupper(uniqid(6));
            $user->locale = app()->getLocale();
        });

        static::updating(function ($user) {
            if (empty($user->user_id)) {
                $user->user_id = 'USR-' . strtoupper(uniqid(6));
            }
        });
    }

    public function isAdmin()
    {
        return $this->role->name === self::ROLE_ADMIN;
    }

    public function isValidator()
    {
        return $this->role->name === self::ROLE_VALIDATOR;
    }
    public function isFinance()
    {
        return $this->role->name === self::ROLE_FINANCE;
    }

    public function isParticipant()
    {
        return $this->role->name === self::ROLE_PARTICIPANT;
    }

    public function isSecretary()
    {
        return $this->role->name === self::ROLE_SECRETARY;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
