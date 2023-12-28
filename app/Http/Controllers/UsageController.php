<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usage;
use App\Models\Server;
use App\Http\Helper;
use DB;

class UsageController extends Controller
{
    // Constructor with middleware for permission and common data retrieval
    public function __construct()
    {
        $this->middleware('verifyPermission')->only('highUsages', 'usage', 'usageChart', 'loadChart');

        // Fetch permission IDs, date range, and pagination value
        $this->middleware(function ($request, $next) {
            $this->permissionIds = Helper::permissionIds();
            $this->dateRange = Helper::getDateRange();
            $this->perPage = Helper::perPage();
            return $next($request);
        });
    }

    /**
     * Retrieve high usage statistics for servers.
     *
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function highUsages($type)
    {
        try {
            // Get the latest load usages with server names
            $usages = Usage::whereIn('usages.server_id', $this->permissionIds['serverIds'])
                ->select('servers.id as server_id', 'servers.name as server_name', "usages.$type as $type")
                ->join('servers', 'usages.server_id', '=', 'servers.id')
                ->join(DB::raw('(SELECT MAX(created_at) AS max_created_at, server_id FROM usages GROUP BY server_id) AS latest_usages'), function ($join) {
                    $join->on('usages.server_id', '=', 'latest_usages.server_id')
                        ->whereColumn('usages.created_at', '=', 'latest_usages.max_created_at');
                })
                ->orderByDesc("usages.$type") // Sort by $type column in descending order
                ->take($this->perPage)
                ->get();

            // Return server usage statistics as JSON response
            return response()->json([
                'usages' => $usages
            ], 200);
        } catch (\Exception $e) {
            // Handle and respond to exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    /**
     * Retrieve usage statistics for a specific server.
     *
     * @param Server $server
     * @return \Illuminate\Http\JsonResponse
     */
    public function usage(Server $server)
    {
        try {
            // Fetch server usage statistics using an external request
            $response = Helper::serveravatarClient("organizations/$server->sa_organization_id/servers/$server->sa_server_id/usage", 'GET');

            if (isset($response['error'])) {
                // Handle and respond to external errors
                \Log::info($response['message']);
                return response()->json([
                    'message' => "Unable to connect with server."
                ], 500);
            }

            // Return server usage data as JSON response
            return response()->json([
                'usage' => $response
            ], 200);

        } catch (\Exception $e) {
            // Handle and respond to exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    /**
     * Retrieve server usage data for generating a chart.
     *
     * @param Server $server
     * @return \Illuminate\Http\JsonResponse
     */
    public function usageChart(Server $server)
    {
        try {
            // Fetch server usage within the date range for chart data
            $usages = $server->usages()
                ->selectRaw("MAX(fifteen_min_load) as cpu_usage, MAX(memory_in_pr) as memory_usage, MAX(disk_in_pr) as disk_usage, MAX(swap_in_pr) as swap_usage, DATE_FORMAT(created_at,'%m/%d/%Y %H:%i') as datetime")
                ->whereBetween('created_at', $this->dateRange)
                ->groupBy(DB::raw('DATE_FORMAT(created_at, "%m/%d/%Y %H:%i")'))
                ->orderBy('datetime', 'ASC')
                ->get();

            // Return server usage chart data as JSON response
            return response()->json($usages, 200);
        } catch (\Exception $e) {
            // Handle and respond to exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    /**
     * Retrieve server load chart data.
     *
     * @param Server $server
     * @return \Illuminate\Http\JsonResponse
     */
    public function loadChart(Server $server)
    {
        try {
            // Fetch server load chart data within the date range
            $usages = $server->usages()
                ->selectRaw("MAX(five_min_load) * $server->cores / 100 as calculated_five_min_load")
                ->selectRaw("MAX(fifteen_min_load) * $server->cores / 100 as calculated_fifteen_min_load")
                ->selectRaw("DATE_FORMAT(created_at, '%m/%d/%Y %H:%i') as datetime")
                ->whereBetween('created_at', $this->dateRange)
                ->groupBy('datetime')
                ->orderBy('datetime', 'ASC')
                ->get();

            // Simplified mapping logic for chart data
            $data = $usages->map(function ($usage) use($server) {
                return [
                    'five_min_load' => $usage->calculated_five_min_load,
                    'fifteen_min_load' => $usage->calculated_fifteen_min_load,
                    'cores' => $server->cores,
                    'datetime' => $usage->datetime,
                ];
            });

            // Return server load chart data as JSON response
            return response()->json($data, 200);
        } catch (\Exception $e) {
            // Handle and respond to exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }
}