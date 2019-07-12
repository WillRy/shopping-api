<?php

namespace CodeShopping\Http\Controllers\Api\Open;

use CodeShopping\Models\Order;
use CodeShopping\Http\Controllers\Controller;
use CodeShopping\Http\Resources\Open\OrderResource as OpenOrderResource;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Http\Request;


class OrderController extends Controller
{

    public function index()
    {
        $userId = \Auth::guard('api')->user()->id;
        $orders = Order::where('user_id', $userId)->orderBy('created_at','desc')->paginate();
        return OpenOrderResource::collection($orders);
    }

    public function show(Order $order)
    {
        $this->assertOrder($order);
        return new OpenOrderResource($order);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'product_id' => [
                'required',
                (new Exists('products', 'id'))->where(function ($query) {
                    return $query->where('stock', '>', 0)->where('active', true);
                }),
            ],
            'amount' => 'required|integer|min:1'
        ]);
        $order = Order::createWithProduct([
            'user_id' => \Auth::guard('api')->user()->id,
            'product_id' => $request->product_id,
            'amount' => $request->amount
        ]);
        // forçar a atualização do status, pois ele vem como valor default no banco de dados
        $order->refresh();
        return new OpenOrderResource($order);
    }

    public function update(Order $order)
    {
        $this->assertOrder($order);

        if($order->status != Order::STATUS_PENDING){
            abort(403, "Alteração de status em pedido, não autorizado");
        }

        $order->status = Order::STATUS_CANCELLED;
        $order->save();

        return new OpenOrderResource($order);
    }

    public function assertOrder(Order $order)
    {
        $userId = \Auth::guard('api')->user()->id;
        if ($order->user->id !== $userId) {
            abort(404);
        }
    }
}
