<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPropertyType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->type == 1) {
            return $next($request);
        }
        return response()->json(['error' => 'Unauthorized'], 401);

        // If not authenticated or user type is not 0, redirect or return error response
//        return redirect('/')->with('error', 'Unauthorized access');
    }
}
