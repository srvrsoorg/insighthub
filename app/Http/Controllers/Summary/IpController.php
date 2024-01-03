<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Summary\Ip;
use App\Models\{Server, Application};
use App\Http\Helper;
use DB;

class IpController extends Controller
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
     * Retrieve summary data for ips.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Retrieve ips summary data by application ID, ips name, and total count, grouped and ordered
            $ips = Ip::whereIn('ips.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'ips.application_id', 'applications.id')
                ->whereBetween('ips.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->select('ips.application_id', 'applications.name as application_name', 'ips.ip', \DB::raw('SUM(ips.count) as total_count'))
                ->groupBy('ips.application_id', 'ips.ip', 'applications.name')
                ->orderByDesc('total_count')
                ->take($this->perPage)
                ->get();

            // Return the retrieved ips summary data in a JSON response
            return response()->json([
                'ips' => $ips
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
     * Get aggregated ip data based on total counts grouped by ip names.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing ip data
     */
    public function getChartData()
    {
        // Retrieve bot data based on permission application IDs
        $ipData = Ip::whereIn('application_id', $this->permissionIds['applicationIds'])
            ->select('ip', \DB::raw('SUM(count) as total_count'))
            ->whereBetween('created_at', $this->dateRange)
            ->when(request()->get('bot') != "", function ($query) {
                $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
            })
            ->groupBy('ip')
            ->orderByDesc('total_count')
            ->take($this->perPage)
            ->get();

        // Return the retrieved ip data in a JSON response
        return response()->json([
            'ipData' => $ipData
        ], 200);
    }

    /**
     * Get line chart data by fetching IP creation counts grouped by date.
     *
     * @return \Illuminate\Support\Collection Collection of IP creation counts grouped by date
     */
    public function getLineChartData()
    {
        // Fetch IP creation counts grouped by date
        $ipData = Ip::whereIn('application_id', $this->permissionIds['applicationIds'])
            ->selectRaw("DATE_FORMAT(created_at, '%m/%d/%Y %H:00') as date, COUNT(DISTINCT ip) as visitors, SUM(count) as hits")
            ->whereBetween('created_at', $this->dateRange)
            ->when(request()->get('bot') != "", function ($query) {
                $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
            })
            ->groupBy('date')
            ->orderBy('created_at')
            ->get();

        // Return the retrieved IP data in a JSON response
        return response()->json([
            'ipData' => $ipData
        ], 200);
    }

    /**
     * Retrieve IP data with associated URL.
     *
     * @return \Illuminate\Http\JsonResponse JSON response with IP data and URLs
     */
    public function ipWithUrl()
    {
        try {
            // Retrieve IPs summary data by application ID, IP name, URL, and total count, grouped and ordered
            $ips = Ip::whereIn('ips.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'ips.application_id', 'applications.id')
                ->whereBetween('ips.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->select('ips.application_id', 'applications.name as application_name', 'ips.ip', 'ips.url', \DB::raw('SUM(ips.count) as total_count'))
                ->groupBy('ips.application_id', 'applications.name', 'ips.ip', 'ips.url')
                ->orderByDesc('total_count')
                ->take($this->perPage)
                ->get();

            // Return the retrieved IPs summary data in a JSON response
            return response()->json([
                'ips' => $ips
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
     * Retrieve IP data with associated bandwidth information.
     *
     * @return \Illuminate\Http\JsonResponse JSON response with IP data and bandwidth details
     */
    public function ipWithBandwidth()
    {
        try {
            // Retrieve IPs summary data by application ID, IP name, bandwidth, and total count, grouped and ordered
            $ips = Ip::whereIn('ips.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'ips.application_id', 'applications.id')
                ->whereBetween('ips.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->select(
                    'ips.application_id',
                    'applications.name as application_name',
                    'ips.ip',
                    \DB::raw('SUM(ips.bandwidth) / (1024 * 1024) as bandwidth_in_MB'), // Calculate bandwidth in MB
                    \DB::raw('SUM(ips.count) as total_count'),
                    \DB::raw('MAX(ips.created_at) as last_visit')
                )
                ->groupBy('ips.application_id', 'applications.name', 'ips.ip', 'ips.bandwidth')
                ->orderByDesc('total_count')
                ->take($this->perPage)
                ->get();

            // Return the retrieved IPs summary data in a JSON response
            return response()->json([
                'ips' => $ips
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
