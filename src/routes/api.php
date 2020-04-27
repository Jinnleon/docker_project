<?php

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('registration', 'Auth\AuthorizationController@registration');
Route::put('login', 'Auth\AuthorizationController@login');

//Route::middleware('jwt.auth')->get('users', function () {
//    return auth('api')->user();
//});

//routes that require authorization
Route::group(['middleware' => 'jwt.auth'], function() {
    Route::put('scanDirectory', 'ScanController@scanDirectory');
    Route::get('getScanLogs', 'ScanController@getScanLogs');
    Route::get('searchFiles', 'SearchFilesController@searchFiles');
});


