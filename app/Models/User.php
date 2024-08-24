<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Paddle\Billable;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    use HasFactory, Notifiable, Billable, \OwenIt\Auditing\Auditable;

    protected $auditStrict = true; //Audit the hidden properties
    protected $auditTimestamps = true; //Audit the timestamps


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'google_account_info',
        'is_signup_successful',
        'card_type',
        'card_last_4',
        'card_expiry_date',
        'previous_billing_date',
        'current_billing_date',
        'current_month_failed_renewal_attempts',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
        'google_account_info',
        'subscriptions'
    ];

    protected $appends = ['is_subscribed'];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'previous_billing_date' => 'datetime',
            'current_billing_date' => 'datetime',
        ];
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiToken::class);
    }

    /**
     * Get the subscribed status
     *
     * @return bool
     */
    public function getIsSubscribedAttribute(): bool
    {
        return !is_null($this->subscription());
    }
}
