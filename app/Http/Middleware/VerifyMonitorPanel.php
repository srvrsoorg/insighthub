<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\{BasicDetail, UserServer};
use App\Http\Helper;

class VerifyMonitorPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        // Get the currently authenticated user
        $user = auth()->user();

        if($server = $request->route('server')) {
            // Find the user's server entry for the requested server
            $userServer = $user->userServers()->where('server_id', $server->id)->first();

            // Check if the user has permission for the requested server
            if (!$userServer) {
                return response()->json([
                    "message" => "You don't have server permission."
                ], 500);
            }
        }   

        if($application = $request->route('application')) {
            // Find the application within the user's server
            $application = $userServer->applications()->find($application->id);

            if (!$application) {
                return response()->json([
                    "message" => "You don't have application permission."
                ], 500);
            }
        }

        return $next($request);
    }
}
