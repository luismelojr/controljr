<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class WalletTypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Filter transactions by wallet type
        return $query->whereHas('wallet', function (Builder $query) use ($value) {
            $query->where('type', $value);
        });
    }
}
