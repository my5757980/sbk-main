<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Support\Facades\Route;

class Admin extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'image',
        'email',
        'password',
        'is_active',
        'admin_role_id',
        'chat_visible',     // live chat: null = by role, 'yes' / 'no' (ChatService::personOf)
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function fullName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function role()
    {
        return $this->belongsTo(AdminRole::class, 'admin_role_id');
    }

    public function hasAdminPermission(string $permission): bool
    {
        if ((int) $this->is_system_admin === 1) {
            return true;
        }

        return $this->role()
            ->whereHas('permissions', fn ($query) => $query->where('name', $permission))
            ->exists();
    }

    /** "See all customers"; without it only their own (User::scopeVisibleTo). */
    public function canSeeAllCustomers(): bool
    {
        return $this->hasAdminPermission('users.view_all');
    }

    public function canAccessAdminModule(string $module): bool
    {
        if ((int) $this->is_system_admin === 1) {
            return true;
        }

        return $this->role()
            ->whereHas('permissions', fn ($query) => $query->where('module', $module))
            ->exists();
    }

    public function firstAccessibleAdminRoute(): string
    {
        if ((int) $this->is_system_admin === 1 || $this->hasAdminPermission('dashboard.view')) {
            return 'admin.dashboard.index';
        }

        foreach (array_keys(config('admin_permissions', [])) as $module) {
            foreach (['index', 'create'] as $action) {
                $permission = $module . '.' . $action;
                $route = 'admin.' . $permission;

                if ($this->hasAdminPermission($permission) && Route::has($route)) {
                    return $route;
                }
            }
        }

        return 'admin.dashboard.index';
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }
}
