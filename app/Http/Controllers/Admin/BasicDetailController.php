<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BasicDetail;

class BasicDetailController extends Controller
{
    /**
     * Retrieve and return basic details.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Get all basic detail data (includes 'key' and 'value')
            $basicDetails = BasicDetail::select('key', 'value')->get();

            $allData = [];

            // Restructure the data into an associative array
            foreach ($basicDetails as $basicDetail) {
                $data[$basicDetail->key] = $basicDetail->value;
                $allData[] = $data;
            }

            return response()->json([
                'basic_details' => $allData
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