<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Server;

class ApplicationController extends Controller
{
    /**
     * Retrieve applications associated with a server.
     *
     * @param Server $server
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Server $server)
    {
        try {
            // Retrieve the search/paginate query
            $applications = $server->applications()
                ->orderBy('created_at','desc')
                ->when(request()->has('search'), function ($query) {
                    $query->where('name', 'like', '%' . request('search') . '%');
                })
                ->when(request()->has('pagination'), function ($query) {
                    // Paginate the results
                    return $query->paginate(10);
                }, function ($query) {
                    // Get all results
                    return $query->get();
                });

            return response()->json([
                'applications' => $applications
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