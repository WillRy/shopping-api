<?php

namespace CodeShopping\Http\Filters;

use Mnabialek\LaravelEloquentFilter\Filters\SimpleQueryFilter;

class ProductFilter extends SimpleQueryFilter
{

    protected $simpleFilters = ['search'];

    protected $simpleSorts = ['id','name','price','created_at'];

    protected function applySearch($value)
    {
        $this->query->where('name','like',"%$value%")->orWhere('description','like',"%$value%");
    }

    public function hasFilterParameter()
    {
        $contais = $this->parser->getFilters()->contains(function($filter) {
            return $filter->getField() === 'search' && !empty($this->getValue());
        });
        return $contais;
    }

}
