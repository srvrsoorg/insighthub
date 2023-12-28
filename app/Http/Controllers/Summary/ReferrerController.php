<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Summary\Referrer;
use App\Http\Helper;

class ReferrerController extends Controller
{
    /**
     * Create a new controller instance.
     * Retrieve the user's permission IDs and date range using Helper class and store them for later use.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->permissionIds = Helper::permissionIds();
            $this->dateRange = Helper::getDateRange();
            $this->perPage = Helper::perPage();
            return $next($request);
        });
    }

    /**
     * Retrieve statistics for each application regarding referrers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function applicationStatistics()
    {
        try {
            // Retrieve referrers summary data by application ID, referrer URL, and total count, grouped and ordered
            $applicationStatistics = Referrer::whereIn('referrers.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'referrers.application_id', 'applications.id')
                ->whereBetween('referrers.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->select(
                    'referrers.application_id',
                    'applications.name as application_name',
                    'referrers.referrer_url',
                    \DB::raw('SUM(referrers.count) as total_count'),
                )
                ->groupBy('referrers.application_id', 'applications.name', 'referrers.referrer_url')
                ->orderByDesc('total_count')
                ->take($this->perPage)
                ->get();

            // Return the retrieved referrers summary data in a JSON response
            return response()->json([
                'applicationStatistics' => $applicationStatistics
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
     * Retrieve referrers with their bandwidth statistics for each application.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function referrerBandwidthStatistics()
    {
        try {
            // Retrieve referrers summary data by application ID, referrer URL, bandwidth, and total count, grouped and ordered
            $referrers = Referrer::whereIn('referrers.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'referrers.application_id', 'applications.id')
                ->whereBetween('referrers.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->select(
                    'referrers.application_id',
                    'applications.name as application_name',
                    'referrers.referrer_url',
                    \DB::raw('SUM(referrers.bandwidth) / (1024 * 1024) as bandwidth_in_MB'), // Calculate bandwidth in MB
                    \DB::raw('SUM(referrers.count) as total_count'),
                )
                ->groupBy('referrers.application_id', 'applications.name', 'referrers.referrer_url', 'referrers.bandwidth')
                ->orderByDesc('total_count')
                ->take($this->perPage)
                ->get();

            // Return the retrieved referrers summary data in a JSON response
            return response()->json([
                'referrersBandwidthStatistics' => $referrers
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
     * Get line chart data by fetching Referrer creation counts grouped by date.
     *
     * @return \Illuminate\Support\Collection Collection of Referrer creation counts grouped by date
     */
    public function getLineChartData()
    {
        // Fetch Referrer creation counts grouped by date
        $referrerData = Referrer::whereIn('application_id', $this->permissionIds['applicationIds'])
            ->selectRaw("DATE_FORMAT(created_at, '%m/%d/%Y %H:00') as date, SUM(count) as hits")
            ->whereBetween('created_at', $this->dateRange)
            ->when(request()->get('bot') != "", function ($query) {
                $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Return the retrieved Referrer data in a JSON response
        return response()->json([
            'referrerData' => $referrerData
        ], 200);
    }
}