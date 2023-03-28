<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
Route::get('/run/migrations' , function() {
    return view('migration');
});
Route::post('/run/migrations' , [\App\Http\Controllers\UserController::class , 'runMigration']);
Route::post('/clear/cache/config' , [\App\Http\Controllers\UserController::class , 'clearCache']);
Route::post('/migrations/reset-1-steps' , [\App\Http\Controllers\UserController::class , 'resetMigration']);
Route::post('/migrations/reset-all' , [\App\Http\Controllers\UserController::class , 'refreshMigration']);

Route::get('/generate-order-product-relationships' , [\App\Http\Controllers\OrderController::class , 'generateOrderProductRelationShips']);
Route::get('/refresh-users-delivery-prices' , [\App\Http\Controllers\UserController::class , 'refreshDeliveryPrice']);

*/

