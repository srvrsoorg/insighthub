<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{PublicController, UserController, ServerController, ApplicationController, ServiceController, UsageController, DashboardController, ErrorRateController, ThroughputController};
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Summary\{BotController, BrowserController};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Route for getting the authenticated user (requires sanctum authentication)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes
Route::controller(PublicController::class)->group(function () {
    Route::post('/login', 'login'); // User login
    Route::post('/forgot-password', 'forgotPassword'); // Forgot password
    Route::post('/reset-password', 'resetPassword'); // Reset password
    Route::get('/installation-steps', 'installationSteps'); // Provides installation steps
});

Route::controller(SiteSettingController::class)->group(function () {
    Route::get('/site-setting', 'index'); // Retrieve site settings
});

// User routes (requires authentication)
Route::middleware(['auth:api'])->group(function () {
    // User-related routes
    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'show');
        Route::patch('/user/update', 'update');
        Route::get('/user/logout', 'logout');
    });

    // Dashboard routes
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/verify', 'verify'); // Verify user
        Route::get('/overview', 'overview');
        Route::get('/top-insights', 'topInsights');
    });

    // Server-related routes
    Route::prefix('/servers')->group(function () {
        // Define routes for ServerController
        Route::controller(ServerController::class)->group(function () {
            // Define routes for listing all servers and showing a specific server
            Route::get('/', 'index'); // List all servers
            Route::get('/{server}', 'show'); // Show a specific server
        });

        // Define routes for ServiceController
        Route::controller(ServiceController::class)->prefix('/{server}/services')->group(function () {
            Route::get('/', 'services'); // List services
            Route::get('/{service}/chart', 'getServiceDataForChart'); // Get service data for chart
        });

        // Define routes for UsageController
        Route::get('/high-usages/{type}', [UsageController::class, 'highUsages']);

        Route::controller(UsageController::class)->prefix('/{server}')->group(function () {
            // Define a route for live usage of a specific server
            Route::get('/live-usage', 'usage'); // Live server usage
            Route::get('/usage-chart', 'usageChart'); // Server usage chart
            Route::get('/load-chart', 'loadChart'); // Server load chart
        });

        // Define routes for ApplicationController, which operates on applications of a specific server
        Route::controller(ApplicationController::class)->prefix('/{server}/applications/{application}')->group(function () {
            // Define routes for listing all applications and showing a specific application
            Route::get('/', 'show'); // Show a specific application
            Route::get('/overview', 'overview');
            Route::get('/access-logs', 'getAccessLogs');
            Route::get('/access-logs-chart', 'getAccessLogsChart');
        });
    });

    // Application related routes
    Route::controller(ApplicationController::class)->group(function () {
        Route::get('applications', 'index');
    });

    Route::get('/throughputs', [ThroughputController::class, 'getThroughputChartData']);
    Route::get('/error-rates', [ErrorRateController::class, 'getErrorRateChartData']);
});