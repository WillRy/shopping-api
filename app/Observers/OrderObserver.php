<?php

namespace CodeShopping\Observers;

use CodeShopping\Models\Order;
use CodeShopping\Mail\OrderCreated;
use Illuminate\Support\Facades\Log;
use CodeShopping\Firebase\CloudMessagingFb;
use CodeShopping\Firebase\NotificationType;


class OrderObserver
{

    public function created(Order $order)
    {
        if (!$this->runningInTerminal()) {
            $user = $order->user;
            \Mail::to($user)->send(new OrderCreated($order));
        }
    }

    public function updated(Order $order)
    {
        $this->handleIfPending($order);
        $this->handleIfCancel($order);
        $this->handleIfApproved($order);
        $this->handleIfSent($order);
    }

    private function handleIfPending(Order $order)
    {
        if (Order::STATUS_PENDING != $order->status) {
            return;
        }
        $token = $order->user->profile->device_token;

        if (!$token || $this->runningInTerminal()) {
            return;
        }

        $oldStatus = $order->getOriginal('status');

        if ($oldStatus !== $order->status) {
            $messaging = app(CloudMessagingFb::class);
            $messaging->setTitle("Link de pagamento do pedido")
                ->setBody('Acesse o app para pagar o pedido feito')
                ->setTokens([$token])
                ->setData([
                    'type' => NotificationType::ORDER_DO_PAYMENT,
                    'order' => $order->id
                ])
                ->send();
        }
    }

    private function handleIfApproved(Order $order)
    {
        if (Order::STATUS_APPROVED != $order->status) {
            return;
        }
        $token = $order->user->profile->device_token;

        if (!$token || $this->runningInTerminal()) {
            return;
        }

        $oldPaymentLink = $order->getOriginal('payment_link');

        $messaging = app(CloudMessagingFb::class);
        $messaging->setTitle("Seu pedido foi aprovado")
            ->setBody("Em breve o produto {$order->product->name} será enviado")
            ->setTokens([$token])
            ->setData([
                'type' => NotificationType::ORDER_APPROVED,
                'order' => $order->id
            ])
            ->send();
    }

    private function handleIfCancel(Order $order)
    {
        if (Order::STATUS_CANCELLED != $order->status) {
            return;
        }
    }


    private function handleIfSent(Order $order)
    {
        if (Order::STATUS_SENT != $order->status) {
            return;
        }
    }

    public function runningInTerminal()
    {
        return app()->runningInConsole() || app()->runningUnitTests();
    }
}
