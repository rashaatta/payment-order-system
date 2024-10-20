<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function createOrder(OrderRequest $request)
    {
        $order = Order::create($request->validated());
        return new OrderResource($order->refresh());
    }

    public function updateStatus(UpdateOrderStatusRequest $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        $order->update(['status' => $request->validated()['status']]);
        return new OrderResource($order);
    }

    public function listOrders(Request $request)
    {
        $filters = $request->only(['status']);
        $orders = Order::when($filters['status'], function ($query) use ($filters) {
            $query->where('status', $filters['status']);
        })->get();

        return OrderResource::collection($orders);
    }
}

