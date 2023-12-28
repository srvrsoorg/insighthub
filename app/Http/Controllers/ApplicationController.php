<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Server, Application};
use App\Http\Helper;
use DB;

class ApplicationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Apply the 'verifyPermission' middleware to the 'index' and 'show' methods.
        $this->middleware('verifyPermission')->only('index', 'show', 'overview');

        // Users permission ids
        $this->middleware(function ($request, $next) {
            $this->permissionIds = Helper::permissionIds();
            $this->dateRange = Helper::getDateRange();
            $this->perPage = Helper::perPage();
            return $next($request);
        });
    }

    /**
     * Get a list of applications for a specific server.
     *
     * @param Server $server
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {            

            // Get the list of applications for the user's server
            $applications =Application::whereIn('id', $this->permissionIds['applicationIds'])
                ->with(['server:id,name,ip,agent_status'])
                ->when(request()->has('search'), function ($query) {
                    // Apply a search filter based on application name
                    $query->where('name', 'like', '%' . request('search') . '%');
                })
                ->select('id', 'server_id', 'name', 'framework', 'php_version', 'primary_domain', 'ssl', 'active', 'size')
                ->orderBy('created_at','desc')
                ->when(request()->has('pagination'), fn ($query) => $query->paginate($this->perPage), fn ($query) => $query->get());

            // Respond with the list of applications for the user's server
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
     * Get details of a specific application.
     *
     * @param Server $server
     * @param Application $application
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Server $server, Application $application)
    {
        try {

            // Respond with the details of the user's server
            return response()->json([
                'application' => $application
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
     * Get overview of a specific application.
     *
     * @param Server $server
     * @param Application $application
     * @return \Illuminate\Http\JsonResponse
     */
    public function overview(Server $server, Application $application)
    {
        try {
            $dateRange = $this->dateRange; // Assuming $this->dateRange is previously defined

            // Check if a bot filter is provided in the request
            $botFilter = request()->get('bot') != "" ?
                ['is_bot_request', request()->get('bot')] : [];

            // Retrieve various statistics using helper methods
            $statistics = [
                'total_requests' => $this->getCount($application->accessLogs(), $dateRange, $botFilter),
                'failed_requests' => $this->getFailedRequestsCount($application, $dateRange, $botFilter),
                'top_browser_agent' => $this->getTopItem($application->browsers(), 'browser', $dateRange, $botFilter),
                'most_used_device' => $this->getTopItem($application->devices(), 'device', $dateRange, $botFilter),
                'most_used_platform' => $this->getTopItem($application->platforms(), 'platform', $dateRange, $botFilter),
                'most_common_protocol' => $this->getTopItem($application->protocols(), 'protocol', $dateRange, $botFilter, 'protocol_count'),
            ];

            // Return the gathered statistics in a JSON response
            return response()->json($statistics, 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json(['message' => "Something went wrong."], 500);
        }
    }

    // Helper method to get the count based on provided conditions
    private function getCount($model, $dateRange, $botFilter)
    {
        return $model->whereBetween('created_at', $dateRange)
            ->when(!empty($botFilter), function ($query) use ($botFilter) {
                $query->where($botFilter[0], $botFilter[1]);
            })
            ->count();
    }

    // Helper method to get the count of failed requests
    private function getFailedRequestsCount($application, $dateRange, $botFilter)
    {
        return $application->statuses()
            ->whereBetween('status', [400, 599])
            ->whereBetween('created_at', $dateRange)
            ->when(!empty($botFilter), function ($query) use ($botFilter) {
                $query->where($botFilter[0], $botFilter[1]);
            })
            ->selectRaw("SUM(count) as count")
            ->value('count');
    }

    // Helper method to retrieve the top item based on provided conditions
    private function getTopItem($model, $field, $dateRange, $botFilter, $countField = 'count')
    {
        return $model->select($field, DB::raw("SUM(count) as $countField"))
            ->whereBetween('created_at', $dateRange)
            ->when(!empty($botFilter), function ($query) use ($botFilter) {
                $query->where($botFilter[0], $botFilter[1]);
            })
            ->groupBy($field)
            ->orderByDesc($countField)
            ->first();
    }

    /**
     * Get details of a specific application access logs.
     *
     * @param Server $server
     * @param Application $application
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAccessLogs(Server $server, Application $application)
    {
        try {

            $accessLogs = $application->accessLogs()
                ->select('ip', 'time', 'method', 'url', 'status', 'bytes', 'referrer_url', 'browser', 'bot_name', 'is_bot_request', 'created_at')
                ->whereBetween('created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })->take($this->perPage)
                ->get();

            // Respond with the details of the user's server
            return response()->json([
                'accessLogs' => $accessLogs
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
     * Get chart of a specific application access logs.
     *
     * @param Server $server
     * @param Application $application
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAccessLogsChart(Server $server, Application $application)
    {
        try {

            $accessLogsData = $application->accessLogs()
                ->selectRaw("DATE_FORMAT(created_at, '%m/%d/%Y %H:00') as date, COUNT(id) as count")
                ->whereBetween('created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Respond with the details of the user's server
            return response()->json([
                'accessLogsData' => $accessLogsData
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
