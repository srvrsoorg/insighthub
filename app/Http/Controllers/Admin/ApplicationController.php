<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\Application;
use Illuminate\Validation\Rule;

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

    /**
     * Update the 'enable' column for a specific application.
     *
     * @param  Request      $request
     * @param  Application  $application
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEnable(Request $request, Server $server, Application $application)
    {
        // Validate the request data
        $request->validate([
            'enable' => 'required|boolean',
        ]);

        try {

            // Update the 'enable' column
            $application->update([
                'enable' => $request->input('enable'),
            ]);

            // Return a JSON response for success
            return response()->json([
                'message' => 'Enable status updated successfully.',
            ],200);
        } catch (\Exception $e) {
            // Return a JSON response for error
            return response()->json([
                'message' => 'Failed to update enable status.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the 'priority' column for a specific application.
     *
     * @param  Request      $request
     * @param  Application  $application
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePriority(Request $request, Server $server, Application $application)
    {
        // Validate the request data
        $request->validate([
            'priority' => ['required', Rule::in(['high', 'medium', 'low'])],
        ]);

        try {
            // Update the 'priority' column
            $application->update([
                'priority' => $request->input('priority'),
            ]);

            // Return a JSON response for success
            return response()->json([
                'message' => 'Priority updated successfully.',
            ],200);
        } catch (\Exception $e) {
            // Return a JSON response for error
            return response()->json([
                'message' => 'Failed to update priority.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}