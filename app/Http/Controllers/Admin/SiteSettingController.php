<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Http\Helper;
use Storage;
use Str;
use Http;

class SiteSettingController extends Controller
{
    // Retrieve site settings
    public function index()
    {
        try {
            return Helper::siteSetting();
        } catch(\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return null;
        }
    }

    // Store or update site settings
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'app_name' => 'required|alpha',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,ico',
            'icon' => 'nullable|image|mimes:jpeg,jpg,png,ico',
            'favicon' => 'nullable|mimes:jpeg,jpg,png,ico',
            'color_code' => ['required','regex:/^([a-f0-9]{6}|[a-f0-9]{3})$/i'],
            'retention_period' => 'nullable|numeric|min:7',
            'redis_password' => 'required|string'
        ]);

        try {
            // Check if a SiteSetting exists or create a new one
            if(!$siteSetting = SiteSetting::first()) {
                $siteSetting = new SiteSetting;
            }

            // Update site setting properties
            $siteSetting->app_name = $request->app_name;
            $siteSetting->color_code = $request->color_code;
            $siteSetting->retention_period = $request->retention_period;

            // Retrieve color palette information from an API
            $colorPalette = Http::get(config('app.color_palette_url').$request->color_code);
            $colorPalette = json_decode($colorPalette);

            if($colorPalette && isset($colorPalette->brand)) {
                $siteSetting->color_palette = $colorPalette->brand;
            }

            // Store logo file
            if(isset($request->logo)) {
                $logo = $request->file('logo');

                if ($logo && $logo->isValid()) {
                    $logoName = Str::uuid().'.'.$logo->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('logo', $logo, $logoName);
                    $siteSetting->logo = $logoName;
                } else {
                    // ❌ Error response: Invalid logo
                    return response()->json([
                        'message' => "Logo is invalid!"
                    ],500);
                }
            }

            // Store only icon file
            if(isset($request->icon)) {
                $icon = $request->file('icon');

                if ($icon && $icon->isValid()) {
                    $smallLogoName = Str::uuid().'.'.$icon->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('icon', $icon, $smallLogoName);
                    $siteSetting->icon = $smallLogoName;
                } else {
                    // ❌ Error response: Invalid icon
                    return response()->json([
                        'message' => "Icon is invalid!"
                    ],500);
                }
            }

            // Store favicon file
            if(isset($request->favicon)) {
                $favicon = $request->file('favicon');

                if ($favicon && $favicon->isValid()) {
                    $faviconName = Str::uuid().'.'.$favicon->getClientOriginalExtension();
                    Storage::disk('public')->putFileAs('favicon', $favicon, $faviconName);
                    $siteSetting->favicon = $faviconName;
                } else {
                    // ❌ Error response: Invalid favicon icon
                    return response()->json([
                        'message' => "Favicon icon is invalid!"
                    ],500);
                }
            }

            // Save the site setting
            $siteSetting->save();

            // Update environment variable for app name and redis
            \Artisan::call("env:set app_name {$request->app_name}");
            \Artisan::call("env:set redis_password {$request->redis_password}");
            \Artisan::call("env:set cache_driver redis");
            \Artisan::call("env:set queue_connection redis");
            \Artisan::call("queue:restart");
            \Artisan::call("horizon:terminate");

            // ✅ Success response: Return site setting and a success message
            return response()->json([
                "site_setting" => $siteSetting,
                "message" => "Final setup successfully."
            ],200);

        } catch(\Exception $e) {
            // ❌ Error response: Handle and respond to any exceptions
            report($e);
            return response()->json([
                'message' => "Something went wrong."
            ], 500);
        }
    }
}