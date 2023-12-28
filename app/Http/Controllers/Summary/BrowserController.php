<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Summary\Browser;
use App\Models\{Server, Application};
use App\Http\Helper;
use DB;

class BrowserController extends Controller
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
            return $next($request);
        });
    }

    /**
     * Retrieve summary data for browser.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPieChartData() {
        try {
            $documentTypesData = Browser::whereIn('application_id', $this->permissionIds['applicationIds'])
                ->select('browser', DB::raw('SUM(count) as total_count'))
                ->whereBetween('created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->groupBy('browser')
                ->get();

            return response()->json($documentTypesData);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }
}
