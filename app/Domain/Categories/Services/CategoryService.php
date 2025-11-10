<?php

namespace App\Domain\Categories\Services;

use App\Domain\Categories\DTO\CreateCategoryData;
use App\Domain\Categories\DTO\UpdateCategoryData;
use App\Models\Category;
use App\Models\User;
use App\QueryFilters\CategoryNameFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryService
{
    /**
     * Get all categories for a user with filters and sorting.
     * Applies Spatie Query Builder with allowed filters and sorts.
     */
    public function getAllForUser(User $user, int $perPage = 2): LengthAwarePaginator
    {
        $baseQuery = Category::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('is_default', true);
            });

        return QueryBuilder::for($baseQuery)
            ->allowedFilters([
                AllowedFilter::custom('name', new CategoryNameFilter()),
                AllowedFilter::exact('is_default'),
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts([
                'name',
                'created_at',
                AllowedSort::field('default', 'is_default'),
            ])
            ->defaultSort('-is_default', 'name') // Default categories first, then alphabetically
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Create a new category for a user.
     */
    public function create(CreateCategoryData $data, User $user): Category
    {
        return $user->categories()->create($data->toArray());
    }

    /**
     * Update an existing category.
     * Only allows updating non-default categories.
     */
    public function update(Category $category, UpdateCategoryData $data): Category
    {
        if ($category->is_default) {
            throw new \Exception('Categorias padrão não podem ser editadas.');
        }

        $category->update($data->toArray());

        return $category->fresh();
    }

    /**
     * Delete a category.
     * Only allows deleting non-default categories.
     */
    public function delete(Category $category): bool
    {
        if ($category->is_default) {
            throw new \Exception('Categorias padrão não podem ser excluídas.');
        }

        return $category->delete();
    }

    /**
     * Toggle category status.
     * Only allows toggling non-default categories.
     */
    public function toggleStatus(Category $category): Category
    {
        if ($category->is_default) {
            throw new \Exception('Categorias padrão não podem ter o status alterado.');
        }

        $category->update(['status' => !$category->status]);

        return $category->fresh();
    }
}
