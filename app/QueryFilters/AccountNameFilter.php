<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class AccountNameFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Search for account name with case-insensitive LIKE
        return $query->whereHas('account', function (Builder $query) use ($value) {
            $query->where('name', 'LIKE', "%{$value}%");
        });
    }
}
