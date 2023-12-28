<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Server;
use App\Http\Helper;
use DB;

class ServerController extends Controller
{
    public function __construct() {
        $this->middleware('verifyPermission')->only('index', 'show', 'overview', 'topDetails', 'combineDetails');

        // Users permission ids
        $this->middleware(function ($request, $next) {
            $this->permissionIds = Helper::permissionIds();
            $this->perPage = Helper::perPage();
            return $next($request);
        });
    }

    /**
     * Get a list of servers that the authenticated user has access to.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Retrieve the search query from the request
            $search = request('search');

            // Build a single query to retrieve servers with related user servers
            $servers = Server::whereIn('id', $this->permissionIds['serverIds'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('ip', 'like', '%' . $search . '%');
                });
            })->orderBy('created_at','desc')
            ->when(request()->has('pagination'), fn ($query) => $query->paginate($this->perPage), fn ($query) => $query->get());

            // Respond with the list of servers
            return response()->json([
                'servers' => $servers
            ], 200);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    /**
     * Get details of a specific server.
     *
     * @param Server $server
     * @return \Illuminate\Http\JsonResponse
     */
   public function show(Server $server)
    {
        try {

            $user = auth()->user();

            // Check user server permission
            $userServer = $user->userServers()->where('server_id', $server->id)->first();
    
            // Add the application count to the server object
            $server->applicationCount = $userServer->applications()->count();

            // Respond with the details of the user's server including the application count
            return response()->json([
                'server' => $server
            ], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }
}