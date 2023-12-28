<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Summary\Platform;
use App\Models\{Server, Application};
use App\Http\Helper;

class PlatformController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Retrieve the user's permission IDs using Helper class and store them for later use
        $this->middleware(function ($request, $next) {
            $this->permissionIds = Helper::permissionIds();
            $this->dateRange = Helper::getDateRange();
            $this->perPage = Helper::perPage();
            return $next($request);
        });
    }

    /**
     * Retrieve summary data for platforms.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Retrieve platforms summary data by application ID, platforms name, and total count, grouped and ordered
            $platforms = Platform::whereIn('platforms.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'platforms.application_id', 'applications.id')
                ->select('platforms.application_id', 'applications.name as application_name', 'platforms.platform', \DB::raw('SUM(platforms.count) as total_count'))
                ->groupBy('platforms.application_id', 'platforms.platform', 'applications.name')
                ->whereBetween('platforms.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->orderByDesc('total_count')
                ->take($this->perPage)
                ->get();

            // Return the retrieved platforms summary data in a JSON response
            return response()->json([
                'platforms' => $platforms
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