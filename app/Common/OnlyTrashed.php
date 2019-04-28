<?php

namespace CodeShopping\Common;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

trait OnlyTrashed
{
    protected function onlyTrashedIfRequest(Request $request, Builder $query)
    {
        if ($request->get('trashed') == 1) {
            $query = $query->onlyTrashed();
        }
        return $query;
    }
}
