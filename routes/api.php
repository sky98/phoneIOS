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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/login', 'LoginController@login');
Route::post('/recovery', 'LoginController@recovery');
Route::post('/user', 'UserController@store');

Route::middleware('auth:sanctum')->group(function () {
	Route::apiResource('contact', 'ContactController');
	Route::post('/updatePassword/{user}', 'UserController@update');
	Route::get('/delete', 'UserController@destroy');
});