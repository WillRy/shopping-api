<?php

namespace CodeShopping\Http\Filters;

use Mnabialek\LaravelEloquentFilter\Filters\SimpleQueryFilter;

class OrderFilter extends SimpleQueryFilter
{
    protected $simpleSorts = ['id','amount', 'price', 'created_at'];
}
