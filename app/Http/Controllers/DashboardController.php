<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Helper;
use App\Models\Server;
use Illuminate\Support\Str;
use App\Models\Summary\{Ip, Status, Referrer, Url, Browser};
use App\Models\{Throughput, ErrorRate};

class DashboardController extends Controller
{
    public $permissionIds;

    public function __construct() {

        // Users permission ids
        $this->middleware(function ($request, $next) {
            $this->permissionIds = Helper::permissionIds();
            $this->dateRange = Helper::getDateRange();
            return $next($request);
        });
    }

    /**
     * Verify API key.
     *
     * This method verifies the API key by making a request to an external API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify() {

        try {

            // Attempt to fetch data from an external API
            $response = Helper::serveravatarClient("monitoring-panel", 'get');

            // Check if an error occurred in the response
            if (isset($response['error'])) {
                // ❌ Error response: Return an error message and HTTP status code 500 (Internal Server Error)
                return response()->json([
                    'message' => $response['message']
                ], 500);
            }

            // Extract the 'monitoring_panel' data from the response
            $monitoringPanel = $response['monitoring_panel'];

            // Check if 'monitoringPanel' is empty, indicating no access to the monitoring panel
            if (empty($monitoringPanel)) {
                return response()->json([
                    'message' => "The user does not have access to the monitoring panel."
                ], 500);
            }

            // If all checks pass, return a JSON response indicating successful verification
            return response()->json(["verify" => true], 200);

        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    /**
     * Provides an overview of server and application counts based on permissions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function overview()
    {
        try {
            $permittedServerIds = $this->permissionIds['serverIds'] ?? [];
            $permittedApplicationIds = $this->permissionIds['applicationIds'] ?? [];

            // Count servers with related user
            $serverCount = count($permittedServerIds);

            // Count applications associated with user
            $applicationCount = count($permittedApplicationIds);

            // Fetch web server data with counts for servers and applications
            $webserverData = $this->fetchWebServerData($permittedServerIds, $permittedApplicationIds);

            // Create an array to hold all variables
            $data = [
                'serverCount' => $serverCount,
                'applicationCount' => $applicationCount,
                'webserverData' => $webserverData,
            ];

            // Return all variables in a JSON response
            return response()->json($data, 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    /**
     * Fetches web server data with counts for servers and applications.
     *
     * @param array $serverIds Array of permitted server IDs
     * @param array $applicationIds Array of permitted application IDs
     * @return \Illuminate\Support\Collection
     */
    private function fetchWebServerData($serverIds, $applicationIds)
    {
        // Query to retrieve web server data with counts for servers and applications
        return Server::whereIn('servers.id', $serverIds)
            ->join('applications', 'servers.id', '=', 'applications.server_id')
            ->select(
                'servers.web_server',
                \DB::raw('count(DISTINCT servers.id) as server_count'),
                \DB::raw('count(applications.id) as application_count')
            )
            ->whereIn('applications.id', $applicationIds)
            ->groupBy('servers.web_server')
            ->get();
    }

    /**
     * Get top insights of a specific models.
     */
    public function topInsights()
    {
        try {
            $relationshipModels = ["ip", "url", "status", "referrer", "browser"];

            $topValues = [];

            foreach ($relationshipModels as $relationshipName) {
                
                $modelName = ucfirst($relationshipName);
                $modelClass = "\App\Models\Summary\\" . $modelName;

                // Correct relationship names and column names
                $columnName = ($relationshipName == "referrer") ? "referrer_url" : $relationshipName;
                $relationshipName = ($relationshipName == "status") ? "statuse" : $relationshipName;

                // Use model relationships and eager loading to optimize queries
                $topValue = $modelClass::whereIn("application_id", $this->permissionIds['applicationIds'])
                    ->select(
                        'applications.id as application_id',
                        'applications.name as application_name',
                        $relationshipName . 's.' . $columnName,
                        DB::raw('SUM(' . $relationshipName . 's.count) as count'),
                    )
                    ->leftJoin('applications', 'applications.id', '=', $relationshipName . 's.application_id')
                    ->whereBetween($relationshipName . "s.created_at", $this->dateRange)
                    ->when(request()->get('bot') != "", function ($query) {
                        $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                    })
                    ->groupBy('applications.id', 'applications.name', $relationshipName . 's.' . $columnName)
                    ->orderByDesc('count')
                    ->first();

                $topValues[$modelName] = $topValue;
            }

            // Return the data as a JSON response
            return response()->json($topValues, 200);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }
}
