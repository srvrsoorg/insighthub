<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ErrorRate;
use App\Http\Helper;
use DB;

class ErrorRateController extends Controller
{
    public function __construct() {
        // Middleware to retrieve user permission IDs and date range
        $this->middleware(function ($request, $next) {
            $this->permissionIds = Helper::permissionIds();
            $this->dateRange = Helper::getDateRange();
            return $next($request);
        });
    }
    
    /**
     * Error rate for application.
     */
    public function getErrorRateChartData()
    {
        try {
            // Fetch top 5 applications with the highest error rates within a specific date range
            $topApplications = ErrorRate::whereIn('application_id', $this->permissionIds['applicationIds'])
                ->whereBetween('log_time', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->with(['application:id,name'])
                ->select(
                    'application_id',
                    DB::raw('AVG(error_rate) as avg_error_rate')
                )
                ->groupBy('application_id')
                ->orderByDesc('avg_error_rate')
                ->limit(5)
                ->get();

            // Get all dates within the specified date range
            $datesInRange = Helper::getDatesInRange($this->dateRange[0], $this->dateRange[1]);

            // Prepare data for the line chart
            $lineChartData = [];
            foreach ($topApplications as $application) {
                $applicationData = ErrorRate::where('application_id', $application->application_id)
                    ->selectRaw('log_time as date, IFNULL(AVG(error_rate), 0) as error_rate')
                    ->whereBetween('log_time', $this->dateRange)
                    ->when(request()->get('bot') != "", function ($query) {
                        $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                    })
                    ->groupBy('date')
                    ->get()
                    ->pluck('error_rate', 'date')
                    ->toArray();

                // Fill missing dates with 0 error rate
                /*$missingDates = array_diff($datesInRange, array_keys($applicationData));
                foreach ($missingDates as $missingDate) {
                    $applicationData[$missingDate] = 0; // Fixed the typo here ('datesInRange' to '$missingDate')
                }*/

                ksort($applicationData); // Sort the array by date

                $lineChartData[] = [
                    'application_name' => $application->application->name, // Replace 'name' with the actual field name for the application name
                    'avg_error_rate' => round($application->avg_error_rate, 2),
                    'error_rates' => $applicationData,
                ];
            }

            // $lineChartData now holds data for the top 5 applications' error rates over time (line chart data)
            return response()->json([
                "lineChartData" => $lineChartData
            ], 200);
        } catch (\Exception $e) {
            report($e);
            // Handle exceptions here
            return response()->json([
                "message" => "An error occurred while fetching error rate data."
            ], 500);
        }
    }
}