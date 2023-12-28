<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Throughput;
use App\Http\Helper;
use DB;

class ThroughputController extends Controller
{
    public function __construct() {
        // Middleware to retrieve user permission IDs and date range
        $this->middleware(function ($request, $next) {
            $this->permissionIds = Helper::permissionIds();
            $this->dateRange = Helper::getDateRange();
            return $next($request);
        });
    }

    public function getThroughputChartData()
    {
        try {
            // Fetch top 5 applications with the highest throughput within a specific date range
            $topApplications = Throughput::whereIn('application_id', $this->permissionIds['applicationIds'])
                ->whereBetween('log_time', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->with(['application:id,name'])
                ->select(
                    'application_id',
                    DB::raw('SUM(request_per) as total_request_per')
                )
                ->groupBy('application_id')
                ->orderByDesc('total_request_per')
                ->limit(5)
                ->get();

            // Get all dates within the specified date range
            $datesInRange = Helper::getDatesInRange($this->dateRange[0], $this->dateRange[1]);

            // Prepare data for the line chart
            $lineChartData = [];
            foreach ($topApplications as $application) {
                $applicationData = Throughput::where('application_id', $application->application_id)
                    ->selectRaw('log_time as date, IFNULL(SUM(request_per), 0) as request_per')
                    ->whereBetween('log_time', $this->dateRange)
                    ->when(request()->get('bot') != "", function ($query) {
                        $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                    })
                    ->groupBy('date')
                    ->get()
                    ->pluck('request_per', 'date')
                    ->toArray();

                // Fill missing dates with 0 request_per
                /*$missingDates = array_diff($datesInRange, array_keys($applicationData));
                foreach ($missingDates as $missingDate) {
                    $applicationData[$missingDate ." 00:00:00"] = 0;
                }*/

                ksort($applicationData); // Sort the array by date

                // Prepare the final data structure
                $lineChartData[] = [
                    'application_name' => $application->application->name,
                    'total_request_per' => round($application->total_request_per, 2),
                    'request_per' => $applicationData,
                ];
            }

            // $lineChartData now holds data for the top 5 applications' throughput over time (line chart data)
            return response()->json([
                "lineChartData" => $lineChartData
            ], 200);
        } catch (\Exception $e) {
            report($e);
            // Handle exceptions here
            return response()->json([
                "message" => "An error occurred while fetching throughput data."
            ], 500);
        }
    }
}