<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check administrator
        if(!$request->user()->isSuperAdmin()) {
            // Unauthorized response
            return response()->json([
                'message' => "You don't authorize for this action."
            ],403);
        }
        
        return $next($request);
    }
}
