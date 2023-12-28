<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Summary\Method;
use App\Models\{Server, Application};
use App\Http\Helper;
use DB;

class MethodController extends Controller
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
     * Retrieve summary data for methods.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Retrieve methods summary data by application ID, methods name, and total count, grouped and ordered
            $methods = Method::whereIn('methods.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'methods.application_id', 'applications.id')
                ->whereBetween('methods.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->select('methods.application_id', 'applications.name as application_name', 'methods.method', \DB::raw('SUM(methods.count) as hits'))
                ->groupBy('methods.application_id', 'methods.method', 'applications.name')
                ->orderByDesc('hits')
                ->take($this->perPage)
                ->get();

            // Return the retrieved methods summary data in a JSON response
            return response()->json([
                'methods' => $methods
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
     * Retrieve pie chart data for method.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPieChartData() {
        try {
            $methodData = Method::whereIn('methods.application_id', $this->permissionIds['applicationIds'])
                ->select('method', DB::raw('SUM(count) as total_count'))
                ->whereBetween('created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->groupBy('method')
                ->get();

            return response()->json($methodData);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }
}