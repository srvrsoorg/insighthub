<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Summary\PlatformVersion;
use App\Models\{Server, Application};
use App\Http\Helper;

class PlatformVersionController extends Controller
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
     * Retrieve summary data for platform_versions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Retrieve platform_versions summary data by application ID, platform_versions name, and total count, grouped and ordered
            $platform_versions = PlatformVersion::whereIn('platform_versions.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'platform_versions.application_id', 'applications.id')
                ->select('platform_versions.application_id', 'applications.name as application_name', 'platform_versions.platform_version', \DB::raw('SUM(platform_versions.count) as total_count'))
                ->groupBy('platform_versions.application_id', 'platform_versions.platform_version', 'applications.name')
                ->whereBetween('platform_versions.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->orderByDesc('total_count')
                ->take($this->perPage)
                ->get();

            // Return the retrieved platform_versions summary data in a JSON response
            return response()->json([
                'platform_versions' => $platform_versions
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