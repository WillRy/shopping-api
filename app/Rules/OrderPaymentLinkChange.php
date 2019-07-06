<?php

namespace CodeShopping\Rules;

use CodeShopping\Models\Order;
use Illuminate\Contracts\Validation\Rule;

class OrderPaymentLinkChange implements Rule
{

    private $status;

    public function __construct($status)
    {
        $this->status = $status;
    }


    public function passes($attribute, $value)
    {
        return $this->status === Order::STATUS_PENDING;
    }


    public function message()
    {
        return 'O status precisa ser '.Order::STATUS_PENDING.' para atualizar o link de pagamento';
    }
}
