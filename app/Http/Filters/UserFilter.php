<?php

namespace CodeShopping\Http\Filters;

use CodeShopping\Models\User;
use Mnabialek\LaravelEloquentFilter\Filters\SimpleQueryFilter;

class UserFilter extends SimpleQueryFilter
{

    protected $simpleFilters = ['search', 'role'];

    protected $simpleSorts = ['id', 'name', 'email', 'created_at'];

    protected function applySearch($value)
    {
        $this->query->where('name', 'like', "%$value%");
    }

    protected function applyRole($value)
    {
        $role = $value == 'customer' ? User::ROLE_CUSTOMER : User::ROLE_SELLER;
        $this->query->where('role', $role);
    }

    public function hasFilterParameter()
    {
        $contais = $this->parser->getFilters()->contains(function ($filter) {
            return $filter->getField() === 'search' && !empty($filter->getValue());
        });
        return $contais;
    }
}
