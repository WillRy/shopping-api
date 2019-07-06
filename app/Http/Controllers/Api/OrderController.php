<?php

namespace CodeShopping\Http\Controllers\Api;

use Illuminate\Http\Request;
use CodeShopping\Models\Order;
use CodeShopping\Rules\OrderStatusChange;
use CodeShopping\Http\Filters\OrderFilter;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Resources\OrderResource;
use CodeShopping\Rules\OrderPaymentLinkChange;

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

    public function update(Request $request, Order $order)
    {
        $this->validate($request, [
            'status' => [
                'nullable',
                'in:' . Order::STATUS_APPROVED . ',' . Order::STATUS_SENT . ',' . Order::STATUS_CANCELLED,
                new OrderStatusChange($order->status)
            ],
            'payment_link' => [
                "nullable",
                "url",
                new OrderPaymentLinkChange($order->status)
            ]
        ]);
        $order->status = $request->get('status') ?? $order->status;
        $order->obs = $request->get('obs') ?? $order->obs;
        $order->payment_link = $request->get('payment_link') ?? $order->payment_link;
        $order->save();
        return new OrderResource($order);
    }
}
