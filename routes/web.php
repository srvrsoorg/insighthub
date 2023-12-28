<?php

use Illuminate\Support\Facades\Route;
use App\Http\Helper;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/{any?}', function () {
    $siteSetting = Helper::siteSetting();
    unset($siteSetting['redis_password']);

    return view('app')->with([
        'siteSettings' => $siteSetting
    ]);
})->where('any', '^(?!api|insighthub).*$');

Route::prefix("insighthub")->group(function(){
    Route::redirect('/', 'insighthub/login');
    
    Route::redirect('home', '/insighthub');

    \Auth::routes([
        'register'=>false,
        'reset'=>false,
        'login'=>true
    ]);

    Route::get('/', function(){
        return view('prompt');
    })->middleware("auth");

    Route::post("logout", function(){
        \Auth::logout();
        return redirect("insighthub/login");
    })->name('insighthub.logout');
});