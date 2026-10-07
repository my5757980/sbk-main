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

        // A role with no section ticked still gets in (their own profile, the chat
        // desk) instead of a "no permission" page straight after signing in.
        return 'admin.update-profile';
    }

    /**
     * The admin-panel account fields copied from a staff member's Users row,
     * cut to this table's column sizes (MySQL refuses a longer value outright,
     * which used to make the account - and so the login - fail).
     */
    public static function profileFrom(User $user): array
    {
        $cut = fn ($value, int $length) => mb_substr(trim((string) $value), 0, $length);

        return [
            'first_name' => $cut($user->first_name, 32),
            'last_name' => $cut($user->last_name, 32),
            'phone' => $cut($user->phone, 24),
            'email' => $user->email,
        ];
    }

    /**
     * The admin role a Users row stands for: the role with its slug, or - for a
     * role renamed back when renaming also changed its slug - the role its admin
     * account still has, and the Users row is put right.
     */
    public static function roleFor(User $user): ?AdminRole
    {
        $role = AdminRole::where('slug', $user->role)->first();
        if ($role || in_array($user->role, [null, '', 'customer'], true)) {
            return $role;
        }

        $role = optional(self::where('email', $user->email)->first())->role;
        if ($role) {
            $user->forceFill(['role' => $role->slug])->save();
        }

        return $role;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPasswordNotification($token));
    }
}
