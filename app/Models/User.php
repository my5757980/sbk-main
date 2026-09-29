<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_OPTIONS = [
        'agent' => 'Agent',
        'admin' => 'Admin',
        'manager' => 'Manager',
        'social' => 'Social',
        'hr' => 'HR',
        'customer' => 'Customer',
    ];

    protected $appends = ['avator_url', 'coach_avator_url'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'country',
        'email',
        'password',
        'status',
        'role',
        'otp',
        'otp_time',
        'device_id',
        'avator',
        'coach_avator',
        'profile_picture',
        'relation_question',
        'situation_question',
        'habit_question',
        'mood_question'
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
        'password' => 'hashed',
        'crm_source' => 'boolean',
        'crm_summary' => 'array',
        'crm_synced_at' => 'datetime',
        'crm_password_generated_at' => 'datetime',
    ];

    /** The account shows a CRM customer's record (see app/Services/Crm). */
    public function hasCrmRecord(): bool
    {
        return !empty($this->crm_email);
    }

    /** The agents this customer belongs to (by email; see CustomerAgent). */
    public function agents()
    {
        return $this->hasMany(CustomerAgent::class);
    }

    /**
     * The users an admin may see: everyone with "See all customers"
     * (users.view_all), otherwise only the customers that belong to the
     * admin's email - their CRM leads and the ones they added here.
     */
    public function scopeVisibleTo($query, ?Admin $admin)
    {
        if ($admin && $admin->canSeeAllCustomers()) {
            return $query;
        }

        if (!$admin || !CustomerAgent::ready()) {
            return $query->whereRaw('1 = 0');
        }

        $email = CustomerAgent::normalize($admin->email);

        return $query->where('users.role', 'customer')
            ->whereExists(function ($owned) use ($email) {
                $owned->selectRaw('1')
                    ->from('customer_agents')
                    ->whereColumn('customer_agents.user_id', 'users.id')
                    ->where('customer_agents.agent_email', $email);
            });
    }

    public function getAvatorUrlAttribute()
    {
        if (!isset($this->attributes['avator']) || empty($this->attributes['avator'])) {
            return $this->defaultProfilePictureUrl();
        }
        return asset('storage/' . $this->attributes['avator']);
    }


    public function getCoachAvatorUrlAttribute()
    {
        if (!isset($this->attributes['coach_avator']) || empty($this->attributes['coach_avator'])) {
            return $this->defaultProfilePictureUrl();
        }
        return asset('storage/' . $this->attributes['coach_avator']);
    }

    public function getNameAttribute()
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function getProfilePictureUrlAttribute()
    {
        if (!empty($this->profile_picture) && file_exists(uploadsDir('front') . $this->profile_picture)) {
            return asset(uploadsDir('front') . $this->profile_picture);
        }

        return $this->defaultProfilePictureUrl();
    }

    private function defaultProfilePictureUrl()
    {
        return asset('assets/front/img/default-profile.svg');
    }

    public function orders()
    {
        return $this->hasMany(CheckoutOrder::class);
    }

    public function saveAnswers()
    {
        return $this->hasMany(SaveAnswer::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id'); // Adjust according to your foreign key
    }
}
