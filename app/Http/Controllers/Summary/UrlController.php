<?php

namespace App\Http\Controllers\Summary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Summary\Url;
use App\Models\{Server, Application};
use App\Http\Helper;
use DB;

class UrlController extends Controller
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
     * Retrieve summary data for urls.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function urls()
    {
        try {
            // Retrieve urls summary data by application ID, urls name, and total count, grouped and ordered
            $urls = Url::whereIn('urls.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'urls.application_id', 'applications.id')
                ->whereBetween('urls.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->select('urls.application_id', 'applications.name as application_name', 'urls.title', 'urls.url', DB::raw('SUM(urls.count) as total_count'))
                ->groupBy('urls.application_id', 'application_name','urls.url', 'urls.title')
                ->orderByDesc('total_count')
                ->take($this->perPage)
                ->get();

            // Return the retrieved urls summary data in a JSON response
            return response()->json([
                'urls' => $urls
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
     * Retrieve summary data for URLs categorized as given type URLs.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function specificUrls($type)
    {
        try {

            // Check is type is valid
            if(!in_array($type, ['is_sitemap_url', 'is_xmlrpc_request'])) {
                return response()->json([
                    "message" => 'Invalid type.'
                ],500);
            }

            // Retrieve URLs summary data by application ID, URL names, and total count, grouped and ordered
            $urls = Url::whereIn('urls.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'urls.application_id', 'applications.id')
                ->where("urls.$type", 1)
                ->whereBetween('urls.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->select('urls.application_id', 'applications.name as application_name', 'urls.url', 'urls.browser', 'urls.method', 'urls.status', 'urls.bot_name', 'urls.created_at')
                ->orderByDesc('urls.created_at')
                ->take($this->perPage)
                ->get();

            // Return the retrieved URLs summary data in a JSON response
            return response()->json([
                'urls' => $urls
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
     * Retrieve URL summary data combined with a specific column's total count.
     *
     * @param string $column The column to retrieve total counts for
     * @return \Illuminate\Http\JsonResponse
     */
    public function urlCombineData($column)
    {
        try {
            // Retrieve URLs summary data by application ID, URL names, and total count based on the specified column, grouped and ordered
            $urls = Url::whereIn('urls.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'urls.application_id', 'applications.id')
                ->whereBetween('urls.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->select('urls.application_id', 'applications.name as application_name', 'urls.url', "urls.$column", DB::raw('SUM(urls.count) as total_count'))
                ->groupBy('urls.application_id', 'application_name','urls.url', "urls.$column")
                ->orderByDesc('total_count')
                ->take($this->perPage)
                ->get();

            // Return the retrieved URLs summary data in a JSON response
            return response()->json([
                'urls' => $urls
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
     * Retrieve URL summary data with method and status information.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function urlWithMethodStatus()
    {
        try {
            // Retrieve URLs summary data by application ID, URL names, method, status, and total count, grouped and ordered
            $urls = Url::whereIn('urls.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'urls.application_id', 'applications.id')
                ->whereBetween('urls.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->select('urls.application_id', 'applications.name as application_name', 'urls.url', 'urls.method', 'urls.status', \DB::raw('SUM(urls.count) as total_count'))
                ->groupBy('urls.application_id', 'applications.name', 'urls.url', 'urls.method', 'urls.status', 'urls.url')
                ->orderByDesc('total_count')
                ->take($this->perPage)
                ->get();

            // Return the retrieved URLs summary data in a JSON response
            return response()->json([
                'urls' => $urls
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
     * Retrieve URL count based on a specific status range.
     *
     * @param string $status The status code range identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUrlCount($status) {
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

            // Retrieve URL data based on the specified status range
            $statusData = Url::whereIn('urls.application_id', $this->permissionIds['applicationIds'])
                ->join('applications', 'urls.application_id', 'applications.id')
                ->select('applications.name as application_name', 'urls.url', \DB::raw('SUM(urls.count) as total_count'), DB::raw('MAX(urls.created_at) as last_visit'))
                ->groupBy('applications.name', 'urls.url')
                ->whereBetween('urls.status', $statusRange)
                ->whereBetween('urls.created_at', $this->dateRange)
                ->when(request()->get('bot') != "", function ($query) {
                    $query->where('is_bot_request', request()->get('bot')); // Apply bot status filter if provided
                })
                ->orderByDesc('total_count')
                ->take($this->perPage)
                ->get();

            // Return the URL data in a JSON response
            return response()->json($statusData);
        } catch (\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }
}