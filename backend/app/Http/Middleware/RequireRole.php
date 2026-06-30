<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    /**
     * Allow only requests from users with one of the given roles.
     *
     * Usage in routes: ->middleware('role:CEO,ADMIN')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => '未認證'], 401);
        }

        $allowedRoles = array_map(fn(string $r) => Role::from($r), $roles);

        if (! in_array($user->role, $allowedRoles, true)) {
            return response()->json(['message' => '無權限執行此操作'], 403);
        }

        return $next($request);
    }
}
