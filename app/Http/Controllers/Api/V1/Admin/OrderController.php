<?php

namespace App\Http\Controllers\Api\V1\Admin;

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
        $user = auth()->guard('api')->user();

        $filters = $request->only(['status']);

        $orders = Order::when($filters['status'], function ($query) use ($filters) {
            $query->where('status', $filters['status']);
        })->get();

        return OrderResource::collection($orders);
    }
}
