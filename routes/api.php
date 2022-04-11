<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['as' => 'api.'],function(){
    Route::get('pusher',function(Request $request){
    return config('broadcasting.connections.pusher');
    })->name('pusher');
    Route::post('pusher', 'PusherController@store')->name('pusher.store');
});


Route::get('test', function(){
    return 'Test';
});
