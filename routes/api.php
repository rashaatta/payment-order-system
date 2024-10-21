<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Facades\JWTAuth;

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

Route::prefix('orders')->group(function () {
    Route::middleware(['auth:api'])->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'createOrder']);
        Route::patch('/{id}/status', [OrderController::class, 'updateStatus']);
    });

    Route::post('/{id}/pay', [PaymentController::class, 'processPayment']);
    Route::post('/webhook', [WebhookController::class, 'handleWebhook']);
});


Route::post('login', LoginController::class);
