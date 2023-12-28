<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Summary\Protocol;
use App\Models\{Server, Application};
use App\Http\Helper;

class ProtocolController extends Controller
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
     * Retrieve summary data for protocols.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Retrieve protocols summary data by application ID, protocols name, and total count, grouped and ordered
            $protocols = Protocol::whereIn('protocols.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'protocols.application_id', 'applications.id')
                ->select('protocols.application_id', 'applications.name as application_name', 'protocols.protocol', \DB::raw('SUM(protocols.count) as total_count'))
                ->groupBy('protocols.application_id', 'protocols.protocol', 'applications.name')
                ->whereBetween('protocols.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->orderByDesc('total_count')
                ->take($this->perPage)
                ->get();

            // Return the retrieved protocols summary data in a JSON response
            return response()->json([
                'protocols' => $protocols
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