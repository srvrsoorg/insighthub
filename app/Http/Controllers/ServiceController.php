<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Server, Service};
use App\Http\Helper;
use DB;

class ServiceController extends Controller
{
    // Apply middleware only to specific controller methods
    public function __construct() {
        $this->middleware('verifyPermission')->only('services', 'getServiceDataForChart');
    }

    /**
     * Retrieve the live service status for a server.
     *
     * @param Server $server
     * @return \Illuminate\Http\JsonResponse
     */
    public function services(Server $server) {
        try {
            // Make an external request to fetch server data
            $response = Helper::serveravatarClient("organizations/$server->sa_organization_id/servers/$server->sa_server_id/services", 'GET');

            if (isset($response['error'])) {
                // ❌ Error response: Handle and respond to any external errors
                \Log::info($response['message']);
                return response()->json([
                    'message' => "Unable to connect with server."
                ], 500);
            }   

            // Return JSON response with fetched services
            return response()->json([
                'services' => $response['services']
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
     * Retrieve service data for generating a chart.
     *
     * @param Server $server
     * @param string $service
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServiceDataForChart(Server $server, $service) {
        try {
            // Determine the date range based on the user's selection
            $dateRange = Helper::getDateRange();

            // Retrieve the latest service data for the specified service and date range
            $services = $server->services()
                ->select(
                    DB::raw('MAX(cpu_usage) as cpu_usage'),
                    DB::raw('MAX(memory_usage) as memory_usage'),
                    DB::raw("DATE_FORMAT(created_at,'%m/%d/%Y %H:%i') as datetime")
                )
                ->where('name', $service)
                ->whereBetween('created_at', $dateRange)
                ->groupBy('datetime')
                ->orderBy('created_at')
                ->get();

            // Respond with the latest services
            return response()->json(['services' => $services], 200);

        } catch (\Exception $e) {
            // Error handling: Log the exception and return an error response
            report($e);
            return response()->json(['message' => "Something went wrong."], 500);
        }
    }
}