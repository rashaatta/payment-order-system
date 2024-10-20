<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function processPayment( $orderId)
    {
        $order = Order::find($orderId);
        if (!$order) return response()->json(['message' => 'Order not found'], 404);

        $result = $this->paymentService->processPayment($order);

        return response()->json(['clientSecret' => $result]);
    }
}
