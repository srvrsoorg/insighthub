<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Summary\Device;
use App\Models\{Server, Application};
use App\Http\Helper;

class DeviceController extends Controller
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
     * Retrieve summary data for devices.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Retrieve devices summary data by application ID, devices name, and total count, grouped and ordered
            $devices = Device::whereIn('devices.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'devices.application_id', 'applications.id')
                ->select('devices.application_id', 'applications.name as application_name', 'devices.device', \DB::raw('SUM(devices.count) as total_count'))
                ->groupBy('devices.application_id', 'devices.device', 'applications.name')
                ->whereBetween('devices.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->orderByDesc('total_count')
                ->take($this->perPage)
                ->get();

            // Return the retrieved devices summary data in a JSON response
            return response()->json([
                'devices' => $devices
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
