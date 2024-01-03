<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Summary\Status;
use App\Models\{Server, Application};
use App\Http\Helper;
use DB;

class StatusController extends Controller
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
     * @return \Illuminate\Http\JsonResponse
     */
    // http status details
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    // status code combine chart
    public function getLineChartData()
    {
        try {

            $statusValues = [
                'Informational',
                'Successful',
                'Redirection',
                'Client_error',
                'Server_error',
                'Unknown',
            ];

            // Create a dictionary to store the status values as keys with initial counts set to 0
            $statusCounts = array_fill_keys($statusValues, 0);

            $results = collect([]);

            $statuses = Status::whereIn('application_id', $this->permissionIds['applicationIds'])
                ->select([
                    DB::raw('DATE_FORMAT(created_at, "%m/%d/%Y") as datetime'),
                    'status',
                    'count'
                ])->whereBetween('created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->orderBy('created_at', 'ASC')->get();

            // Group the results by date and count status values
            $groupedLogs = $statuses->groupBy('datetime');
            foreach ($groupedLogs as $date => $logs) {
                $dateStatusCounts = $statusCounts;

                foreach ($logs as $log) {
                    $status = $this->getStatusCategory($log->status);
                    $dateStatusCounts[$status] += $log->count;
                }

                if (array_sum($dateStatusCounts) > 0) {
                    $results->push([
                        'datetime' => $date,
                        'status_counts' => $dateStatusCounts,
                    ]);
                }
            }

            return response()->json(['datas' => $results], 200);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    private function getStatusCategory($status)
    {
        if ($status >= 100 && $status <= 199) {
            return 'Informational';
        } elseif ($status >= 200 && $status <= 299) {
            return 'Successful';
        } elseif ($status >= 300 && $status <= 399) {
            return 'Redirection';
        } elseif ($status >= 400 && $status <= 499) {
            return 'Client_error';
        } elseif ($status >= 500 && $status <= 599) {
            return 'Server_error';
        }

        return 'Unknown';
    }

    /**
     * Retrieve summary data for status.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPieChartData() {
        try {
            $statusData = Status::whereIn('application_id', $this->permissionIds['applicationIds'])
                ->select('status', DB::raw('SUM(count) as total_count'))
                ->whereBetween('created_at', $this->dateRange)
                ->when(request()->get('bot'), function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })->when(request()->get('error_summary'), function($query) {
                    $query->whereBetween('status', [400, 599]);
                })
                ->groupBy('status')
                ->orderByDesc('total_count')
                ->when(request()->has('per_page'), function ($query) {
                    return $query->take($this->perPage);
                })
                ->get();

            // Calculate total count for percentage calculation
            $totalCount = $statusData->sum('total_count');

            // Prepare data with percentage count
            $formattedData = $statusData->map(function ($item) use ($totalCount) {
                $percentage = $totalCount > 0 ? ($item->total_count / $totalCount) * 100 : 0;
                return [
                    'status' => $item->status,
                    'total_count' => $item->total_count,
                    'percentage' => round($percentage, 2), // Rounding to two decimal places
                ];
            });

            return response()->json($formattedData);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    /**
     * Retrieve summary data for a specific status range to generate pie chart data.
     *
     * @param string $status The status code range identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPieChartAppData($status) {
        try {
            // Determine the status range based on the input
            if($status == "1xx") {
                $statusRange = [100,199];
            } elseif($status == "2xx") {
                $statusRange = [200, 299];
            } elseif($status == "3xx") {
                $statusRange = [300, 399];
            } elseif($status == "4xx") {
                $statusRange = [400, 499];
            } else {
                $statusRange = [500, 599];
            }

            // Retrieve status data based on the specified status range
            $statusData = Status::whereIn('statuses.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'statuses.application_id', 'applications.id')
                ->select('applications.name as application_name', \DB::raw('SUM(statuses.count) as total_count'))
                ->groupBy('applications.name')
                ->whereBetween('statuses.status', $statusRange)
                ->whereBetween('statuses.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->get();

            // Return the status data in a JSON response
            return response()->json($statusData);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    /**
     * Retrieve status details to categorize statuses and their hit counts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statusDetails()
    {
        try {
            // Retrieve status details categorized based on status code ranges
            $datas = Status::whereIn('application_id', $this->permissionIds['applicationIds'])
                ->select(
                    DB::raw("CASE 
                        WHEN status BETWEEN 100 AND 199 THEN 'Informational'
                        WHEN status BETWEEN 200 AND 299 THEN 'Successful'
                        WHEN status BETWEEN 300 AND 399 THEN 'Redirection'
                        WHEN status BETWEEN 400 AND 499 THEN 'Client_error'
                        WHEN status BETWEEN 500 AND 599 THEN 'Server_error'
                        ELSE 'Other' 
                        END as status_alias"), // Alias the raw expression
                    \DB::raw('SUM(count) as hits')
                )
                ->groupBy('status_alias') // Use the alias in the groupBy
                ->whereBetween('created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot'));
                })
                ->get();

            // Return the status details in a JSON response
            return response()->json(['datas' => $datas], 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }
}