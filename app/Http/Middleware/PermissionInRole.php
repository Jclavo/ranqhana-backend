<?php

namespace App\Http\Middleware;

use Closure;

class PermissionInRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if (app('auth')->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        foreach ($permissions as $permission) {

            $permission = $permission . '#' . '2';

            $hasPermission = app('auth')->user()::
                join('model_has_roles','users.id','=','model_has_roles.model_id')
                ->join('roles','model_has_roles.role_id','=','roles.id')
                ->join('role_has_permissions','roles.id','=','role_has_permissions.role_id')
                ->join('permissions','role_has_permissions.permission_id','=','permissions.id')
                ->where('permissions.name','=',$permission)
                ->where('users.id','=',app('auth')->user()->id)
                ->count();

            if ($hasPermission > 0) {
                return $next($request);
            }
        }

        // throw UnauthorizedException::forPermissions($permissions);
        abort(200, 'User does not have permission to this action.');  
    }
}
