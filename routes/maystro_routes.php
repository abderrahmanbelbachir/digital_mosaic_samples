<?php

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

use App\Http\Controllers\Maystro\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Maystro\OrderController;

Route::middleware('api')
    ->post('auth/store/{id}', [ProductController::class, 'authenticateStore'])
    ->name('authenticateStore');

Route::middleware('api')
    ->post('/products/generate/{storeId}', [ProductController::class, 'generateMaystroProducts'])
    ->name('generateMaystroProducts');

Route::middleware('api')
    ->post('/products/get/{storeId}', [ProductController::class, 'getMaystroProducts'])
    ->name('getMaystroProducts');

Route::middleware('api')
    ->post('/products/premium/send/{storeId}', [ProductController::class, 'sendPremiumProductsToMaystro'])
    ->name('sendPremiumProductsToMaystro');

Route::middleware('api')
    ->post('/one-product/premium/send/{productId}', [ProductController::class, 'sendOnePremiumProductsToMaystro'])
    ->name('sendOnePremiumProductsToMaystro');

Route::middleware('api')
    ->post('/update-product-id/{productId}', [ProductController::class, 'updateMaytroProductId'])
    ->name('updateMaytroProductId');

Route::middleware('api')
    ->post('/products/starter/send/{storeId}', [ProductController::class, 'sendProductsToMaystro'])
    ->name('sendProductsToMaystro');

Route::middleware('api')
    ->post('/products/premium/update-quantity/{storeId}', [ProductController::class, 'updatePremiumProductsQuantity'])
    ->name('updatePremiumProductsQuantity');

Route::middleware('api')
    ->post('/orders/premium/send/{orderId}', [OrderController::class, 'sendPremiumOrderToMaystro'])
    ->name('sendPremiumOrderToMaystro');

Route::middleware('api')
    ->post('/orders/starter/send/{orderId}', [OrderController::class, 'sendOrderToMaystro'])
    ->name('sendOrderToMaystro');

Route::middleware('api')
    ->post('/cancel-maystro-order/{magasinId}/{orderId}', [OrderController::class, 'cancelMaystroOrder'])
    ->name('cancelMaystroOrder');

Route::middleware('api')
    ->get('/refresh-maystro-db-id', [OrderController::class, 'refreshOrdersId'])
    ->name('refreshOrdersId');

Route::get('/refresh-maystro-order-status', [\App\Http\Controllers\Maystro\OrderController::class, 'refreshOrdersStatus'])
    ->name('refreshMaystroOrderStatus');

/*Route::middleware('api')
    ->get('/all-maystro-orders', [OrderController::class, 'getAllMaystroOrders'])
    ->name('getAllMaystroOrders');*/
