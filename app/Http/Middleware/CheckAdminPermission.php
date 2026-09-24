<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminPermission
{
    public function handle(Request $request, Closure $next)
    {
        $admin = auth('admin')->user();

        if (!$admin) {
            return $next($request);
        }

        $routeName = optional($request->route())->getName();

        if (!$routeName || !$this->shouldCheck($routeName)) {
            return $next($request);
        }

        $permission = $this->permissionFromRoute($routeName);

        if (!$permission || $admin->hasAdminPermission($permission)) {
            return $next($request);
        }

        abort(403, 'You do not have permission to access this admin section.');
    }

    private function shouldCheck(string $routeName): bool
    {
        if (!str_starts_with($routeName, 'admin.')) {
            return false;
        }

        $exempt = [
            'admin.auth.login',
            'admin.auth.logout',
            'admin.password.email',
            'admin.password.request',
            'admin.password.reset',
            'admin.update-profile',
        ];

        return !in_array($routeName, $exempt, true);
    }

    private function permissionFromRoute(string $routeName): ?string
    {
        $name = substr($routeName, strlen('admin.'));

        if ($name === 'dashboard.index') {
            return 'dashboard.view';
        }

        if ($name === 'update-status') {
            return 'users.edit';
        }

        $parts = explode('.', $name);

        if (count($parts) < 2) {
            return null;
        }

        $module = $parts[0];
        $action = $parts[1];

        $action = match ($action) {
            'store' => 'create',
            'update' => 'edit',
            default => $action,
        };

        return $module . '.' . $action;
    }
}
