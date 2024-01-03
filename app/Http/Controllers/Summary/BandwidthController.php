<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\Summary\Bandwidth;
use App\Http\Helper;

class BandwidthController extends Controller
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
     * Retrieve statistics for each server regarding bandwidth, count.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function serverStatistics()
    {
       try {
            $serverStatistics = Bandwidth::whereIn('bandwidths.server_id', $this->permissionIds['serverIds'])
                ->join('servers', 'bandwidths.server_id', '=', 'servers.id')
                ->select(
                    'bandwidths.server_id',
                    'servers.name as server_name',
                    \DB::raw('SUM(bandwidths.bandwidth) / (1024 * 1024) as total_bandwidth_MB'), // Convert bandwidth to MB
                    \DB::raw('SUM(bandwidths.count) as total_count')
                )
                ->groupBy('bandwidths.server_id', 'servers.name')
                ->whereBetween('bandwidths.created_at',$this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->orderByDesc('total_count')
                ->take($this->perPage)->get();

            // Return the retrieved bandwidths summary data in a JSON response
            return response()->json([
                'serverStatistics' => $serverStatistics
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
     * Retrieve summary data for bandwidth.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function applicationStatistics()
    {
        try {
            $applicationStatistics = Bandwidth::whereIn('bandwidths.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'bandwidths.application_id', '=', 'applications.id')
                ->select(
                    'bandwidths.application_id',
                    'applications.name as application_name',
                    \DB::raw('SUM(bandwidths.bandwidth) / (1024 * 1024) as total_bandwidth_MB'), // Convert bandwidth to MB
                    \DB::raw('SUM(bandwidths.count) as total_count')
                )
                ->groupBy('bandwidths.application_id', 'applications.name')
                ->whereBetween('bandwidths.created_at',$this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->orderByDesc('total_count')
                ->take($this->perPage)->get();

            // Return the retrieved bandwidths summary data in a JSON response
            return response()->json([
                'applicationStatistics' => $applicationStatistics
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
     * Retrieve statistics for the top document types based on total bandwidth and count.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function topDocumentTypes()
    {
        try {
            $documentTypeStats = Bandwidth::whereIn('application_id', $this->permissionIds['applicationIds'])
                ->join('servers', 'bandwidths.server_id', '=', 'servers.id') // Join servers table
                ->select(
                    'servers.name as server_name',
                    'bandwidths.document_type',
                    \DB::raw('SUM(bandwidths.bandwidth) / (1024 * 1024) as total_bandwidth_MB'),
                    \DB::raw('SUM(bandwidths.count) as total_count')                    
                )
                ->groupBy('server_name', 'document_type') // Group by server name and document type
                ->whereBetween('bandwidths.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->orderByDesc('total_bandwidth_MB') // Order by total bandwidth in MB (descending)
                ->take($this->perPage) // Get top 5 document types
                ->get();

            // Return the document type statistics as a JSON response
            return response()->json([
                'documentTypeStatistics' => $documentTypeStats,
            ], 200);
        } catch (\Exception $e) {
            // Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    /**
     * Retrieve statistics for the top 5 URLs with the highest bandwidth consumption and their application details.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function topBandwidthUrls()
    {
        try {
            $topBandwidthUrls = Bandwidth::whereIn('bandwidths.application_id', $this->permissionIds['applicationIds'])
                ->select(
                    'applications.name as application_name',
                    'bandwidths.url',
                    'bandwidths.mime_type',
                    \DB::raw('SUM(bandwidths.bandwidth) / (1024 * 1024) as bandwidth_in_MB') // Convert bandwidth to MB
                )
                ->join('applications', 'applications.id', '=', 'bandwidths.application_id')
                ->whereBetween('bandwidths.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })->groupBy('application_name', 'bandwidths.url', 'bandwidths.mime_type')
                ->orderByDesc('bandwidth_in_MB') // Order by total bandwidth in MB (descending)
                ->take($this->perPage) // Get top 5 URLs
                ->get();

            // Return the retrieved data in a JSON response
            return response()->json([
                'topBandwidthUrls' => $topBandwidthUrls
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
     * Retrieve statistics for the top document types with application based on total bandwidth and count.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function topAppDocumentTypes()
    {
        try {
            $documentTypeStats = Bandwidth::whereIn('application_id', $this->permissionIds['applicationIds'])
                ->selectRaw(
                    'applications.name as application_name,
                    bandwidths.document_type,
                    SUM(bandwidths.bandwidth) / (1024 * 1024) as total_bandwidth_MB' // Convert bandwidth to MB in SQL
                )
                ->join('applications', 'applications.id', '=', 'bandwidths.application_id')
                ->whereBetween('bandwidths.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->groupBy('application_name', 'bandwidths.document_type')
                ->orderByDesc('total_bandwidth_MB') // Order by total bandwidth in MB (descending)
                ->take($this->perPage) // Get top 5 document types
                ->get();

            // Return the document type statistics as a JSON response
            return response()->json([
                'documentTypeStatistics' => $documentTypeStats,
            ], 200);
        } catch (\Exception $e) {
            // Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }

    /**
     * Get line chart data by fetching bandwidth creation counts grouped by date.
     *
     * @return \Illuminate\Support\Collection Collection of bandwidth creation counts grouped by date
     */
    public function getLineChartData()
    {
        // Fetch bandwidth creation counts grouped by date
        $bandwidthData = Bandwidth::whereIn('application_id', $this->permissionIds['applicationIds'])
            ->selectRaw("DATE_FORMAT(created_at, '%m/%d/%Y %H:00') as date, SUM(bandwidth) / (1024 * 1024) as total_bandwidth_MB")
            ->whereBetween('created_at', $this->dateRange)
            ->when(request()->get('bot') != "", function ($query) {
                $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
            })
            ->groupBy('date')
            ->orderBy('created_at')
            ->get();

        // Return the retrieved bandwidth data in a JSON response
        return response()->json([
            'bandwidthData' => $bandwidthData
        ], 200);
    }
}
