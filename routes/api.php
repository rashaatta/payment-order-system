<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('pay', function () {
    return view('pay');
});


Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {

    Route::prefix('orders')->middleware(['role:customer'])->group(function () {
        Route::get('/', [OrderController::class, 'index'])->middleware('permission:view orders');
        Route::post('/', [OrderController::class, 'createOrder'])->middleware('permission:create orders');
        Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->middleware('permission:view orders');
    });

    Route::prefix('admin')->middleware(['role:admin'])->group(function () {
        Route::get('orders', [AdminOrderController::class, 'index'])->middleware('permission:view all orders');
    });

    Route::post('/{id}/pay', [PaymentController::class, 'processPayment']);
    Route::post('/webhook', [WebhookController::class, 'handleWebhook']);
});


Route::post('login', LoginController::class);
