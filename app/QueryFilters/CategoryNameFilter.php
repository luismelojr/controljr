<?php

namespace App\QueryFilters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class CategoryNameFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Busca case-insensitive com LIKE para maior flexibilidade
        return $query->where('name', 'LIKE', "%{$value}%");
    }
}
