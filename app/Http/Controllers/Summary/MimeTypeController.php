<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Summary\MimeType;
use App\Models\{Server, Application};
use App\Http\Helper;
use DB;

class MimeTypeController extends Controller
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
     * Retrieve summary data for document_type.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPieChartData() {
        try {
            $documentTypesData = MimeType::whereIn('application_id', $this->permissionIds['applicationIds'])
                ->select('document_type', DB::raw('SUM(count) as total_count'))
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->whereBetween('created_at', $this->dateRange)
                ->groupBy('document_type')
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