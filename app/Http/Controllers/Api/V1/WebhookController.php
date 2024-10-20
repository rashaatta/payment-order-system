<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;


use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct()
    {
        $this->stripeSecret = env('STRIPE_SECRET');
    }

    public function handleWebhook(Request $request)
    {
        $event = $request->all();

        Log::info($event['type']);

        try {
            switch ($event['type']) {
                case 'charge.succeeded':
                    $paymentIntent = $event['data']['object'];
                    $this->updateOrderStatus($paymentIntent['metadata']['order_id'], 'Paid');
                    break;
                case 'payment_intent.payment_failed':
                    $paymentIntent = $event['data']['object'];
                    $this->updateOrderStatus($paymentIntent['metadata']['order_id'], 'Canceled');
                    break;
                default:
                    Log::info('Unhandled event type: ' . $event['type']);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Webhook handling error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook handling failed'], 500);
        }
    }

    private function updateOrderStatus($orderId, $status)
    {
        Order::where('id', $orderId)->update(['status' => $status]);
    }
}
