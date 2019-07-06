<?php

namespace CodeShopping\Rules;

use CodeShopping\Models\Order;
use Illuminate\Contracts\Validation\Rule;

class OrderStatusChange implements Rule
{

    private $rulesChanges = [
        Order::STATUS_APPROVED => [Order::STATUS_SENT, Order::STATUS_CANCELLED],
        Order::STATUS_SENT => [Order::STATUS_CANCELLED],
        Order::STATUS_CANCELLED => [Order::STATUS_CANCELLED]
    ];

    private $oldStatus;

    public function __construct($oldStatus)
    {
        $this->oldStatus = $oldStatus;
    }


    public function passes($attribute, $value)
    {
        if (!array_key_exists($this->oldStatus, $this->rulesChanges)) {
            return true;
        }

        if (!in_array($value, $this->rulesChanges[$this->oldStatus])) {
            return false;
        }

        return true;
    }


    public function message()
    {
        return 'Status invalido. Com este valor é permitido alterar para:' . implode(',', $this->rulesChanges[$this->oldStatus]);
    }
}
