<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{UserController, BasicDetailController, ServerController, SmtpController, ApplicationController, InstallationController, DatabaseController, SiteSettingController};

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

// Installation routes
Route::prefix('setup')->group(function () {
    Route::controller(InstallationController::class)->group(function() {
        // Registration route
        Route::post('/register','register');

        // Permission Route
        Route::get('/permission', 'checkPermission');
        
        // Basic Details routes
        Route::post('/verify', 'verify'); // Verify basic details
    });

    // SMTP routes
    Route::controller(SmtpController::class)->group(function () {
        Route::get('/smtp', 'index');
        Route::post('/smtp', 'store'); // Store SMTP settings
    });

    // Database routes
    Route::controller(DatabaseController::class)->group(function () {
        Route::post('/database', 'store');   // Store database
        Route::get('/database', 'index');    // Get database
    });
});

// Admin routes (requires authentication and adminOnly middleware)
Route::middleware(['auth:api', 'adminOnly'])->prefix('admin')->group(function () {

    Route::controller(BasicDetailController::class)->prefix('/basic-details')->group(function () {
        Route::get('/','index'); // Get basic details
    });

    Route::controller(SiteSettingController::class)->group(function () {
        Route::post('/site-setting', 'store'); // Store SMTP settings
    });

    // Server routes
    Route::controller(ServerController::class)->group(function () {
        Route::get('/servers', 'index');  // Get server list
        Route::get('/sync', 'syncAll'); // Sync servers & application
    });

    // Application routes within a server context
    Route::controller(ApplicationController::class)->prefix('/servers/{server}/applications')->group(function () {
        Route::get('/', 'index');  // Get applications for a server
        // update enable or priority
        Route::patch('/{application}/update-enable', 'updateEnable');
        Route::patch('/{application}/update-priority', 'updatePriority');
        });

    // User routes (admin-only)
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/', 'index');  // Get list of users
        Route::post('/', 'store'); // Create a user
        Route::patch('/{user}','update'); // Update a user
        Route::delete('/{user}','destory'); // Delete a user
        Route::get('/{user}/permissions', 'permissions'); // Get user permission
        Route::post('/{user}/attach-permission', 'attachPermission'); // Attach permission to a user
        Route::patch('/{user}/update-permission', 'updatePermission'); // Update permission of a user
        Route::delete('/{user}/delete-permission/{permission}', 'deletePermission'); // Delete a user
        Route::get('/{user}/sync-all-permission', 'syncAllPermission'); // Sync all permisison to a user
    });

    // SMTP routes (admin-only)
    Route::controller(SmtpController::class)->prefix('smtp')->group(function () {
        Route::get('/', 'index'); // Get SMTP settings
        Route::post('/', 'store'); // Store SMTP settings
        Route::get('/testMail', 'testMail'); // Test SMTP mail sending
    });
});
