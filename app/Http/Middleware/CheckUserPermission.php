<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPermission
{
    /**
     * Allow the request when the admin has ANY of the given permission flags,
     * e.g. `check.permission:all_property,featured_image`.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = Auth::guard('admin')->user();

        if (!$user || !$user->hasPermission(...$permissions)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
