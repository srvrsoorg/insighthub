<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Summary\{BandwidthController,BrowserController,IpController,MimeTypeController,PlatformVersionController,StatusController,BotController,DeviceController,MethodController,PlatformController,ProtocolController,UrlController, ReferrerController};

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

// Define route group for BandwidthController
Route::middleware(['auth:api'])->controller(BandwidthController::class)->prefix('bandwidths/')->group(function () {
    Route::get('server-statistics', 'serverStatistics');
    Route::get('application-statistics', 'applicationStatistics');
    Route::get('top-document-types', 'topDocumentTypes');
    Route::get('top-urls','topBandwidthUrls');
    Route::get('top-app-document-types', 'topAppDocumentTypes');
    Route::get('line-chart', 'getLineChartData');
});

// Define route group for BrowserController
Route::middleware(['auth:api'])->controller(BrowserController::class)->group(function () {
    Route::get('browsers/pie-chart', 'getPieChartData');
});

// Define route group for IpController
Route::middleware(['auth:api'])->controller(IpController::class)->group(function () {
    Route::get('ips', 'index');
    Route::get('ips/chart', 'getChartData');
    Route::get('ips/line-chart', 'getLineChartData');
    Route::get('ip-with-url', 'ipWithUrl');
    Route::get('ip-with-bandwidth', 'ipWithBandwidth');
});

// Define route group for MimeTypeController
Route::middleware(['auth:api'])->controller(MimeTypeController::class)->group(function () {
    Route::get('document-types/pie-chart', 'getPieChartData');
});

// Define route group for PlatformVersionController
Route::middleware(['auth:api'])->controller(PlatformVersionController::class)->group(function () {
    Route::get('platform-versions', 'index');
});

// Define route group for StatusController
Route::middleware(['auth:api'])->prefix('statuses')->controller(StatusController::class)->group(function () {
    Route::get('/line-chart', 'getLineChartData');
    Route::get('/pie-chart', 'getPieChartData');
    Route::get('/pie-chart/{status}', 'getPieChartAppData');
    Route::get('/details', 'statusDetails');
});

// Define route group for BotController
Route::middleware(['auth:api'])->controller(BotController::class)->prefix('bots/')->group(function () {
    Route::get('pie-chart', 'getPieChartData');
});

// Define route group for DeviceController
Route::middleware(['auth:api'])->controller(DeviceController::class)->group(function () {
    Route::get('devices', 'index');
});

// Define route group for MethodController
Route::middleware(['auth:api'])->prefix('methods')->controller(MethodController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/pie-chart', 'getPieChartData');
});

// Define route group for PlatformController
Route::middleware(['auth:api'])->controller(PlatformController::class)->group(function () {
    Route::get('platforms', 'index');
});

// Define route group for ProtocolController
Route::middleware(['auth:api'])->controller(ProtocolController::class)->group(function () {
    Route::get('protocols', 'index');
});

// Define route group for UrlController
Route::middleware(['auth:api'])->controller(UrlController::class)->group(function () {
    Route::get('urls', 'urls');
    Route::get('specific-urls/{type}', 'specificUrls');
    Route::get('url-combine-data/{column}', 'urlCombineData');
    Route::get('url-with-method-status', 'urlWithMethodStatus');
    Route::get('url-count-by-status/{status}', 'getUrlCount');
});

// Define route group for ReferrerController
Route::middleware(['auth:api'])->controller(ReferrerController::class)->group(function () {
    Route::get('application-referrer-statistics', 'applicationStatistics');
    Route::get('referrer-bandwidth-statistics', 'referrerBandwidthStatistics');
    Route::get('referrer-line-chart', 'getLineChartData');
});