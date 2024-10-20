<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\OrderController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', LoginController::class);
Route::get('pay', function () {
    return view('pay');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'createOrder']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::get('/orders', [OrderController::class, 'listOrders']);
});

Route::post('/orders/{id}/pay', [PaymentController::class, 'processPayment']);
Route::post('/payment/webhook', [WebhookController::class, 'handleWebhook']);
