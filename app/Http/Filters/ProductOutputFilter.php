<?php

namespace CodeShopping\Http\Filters;

use Mnabialek\LaravelEloquentFilter\Filters\SimpleQueryFilter;

class ProductOutputFilter extends SimpleQueryFilter
{

    protected $simpleFilters = ['search'];

    protected $simpleSorts = ['id', 'product_name', 'created_at'];

    protected function applySearch($value)
    {
        $this->query->where('name', 'like', "%$value%");
    }

    public function applySortProductName($order)
    {
        $this->query->orderBy('name', $order);
    }

    public function applySortCreatedAt($order)
    {
        $this->query->orderBy('product_outputs.created_at', $order);
    }

    public function apply($query)
    {
        $query = $query->select('product_outputs.*')->join('products', 'products.id', '=', 'product_outputs.product_id');

        return parent::apply($query);
    }
}
