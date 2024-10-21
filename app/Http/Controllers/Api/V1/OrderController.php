<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['status']);
        $user = auth()->guard('api')->user();

        $orders = $user->orders()
            ->when($filters['status'], function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })->get();

        return OrderResource::collection($orders);
    }

    public function createOrder(OrderRequest $request)
    {
        $user = auth()->guard('api')->user();
        $order = $user->orders()->create($request->validated());

        return new OrderResource($order->refresh());
    }

    public function updateStatus(UpdateOrderStatusRequest $request, $id)
    {
        $user = auth()->guard('api')->user();
        $order = $user->orders()->find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        $order->update(['status' => $request->validated()['status']]);

        return new OrderResource($order);
    }

}
