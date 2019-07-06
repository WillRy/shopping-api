<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Http\Request;
use CodeShopping\Models\Order;
use CodeShopping\Http\Filters\OrderFilter;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Resources\OrderResource;

class OrderController extends Controller
{

    public function index()
    {
        $filter = app(OrderFilter::class);
        $filterQuery = Order::with(['product', 'user'])->filtered($filter);
        $orders = $filterQuery->paginate();
        return OrderResource::collection($orders);
    }

    public function show(Order $order)
    {
        return new OrderResource($order);
    }
}
