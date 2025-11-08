<?php

namespace App\Domain\Categories\Services;

use App\Domain\Categories\DTO\CreateCategoryData;
use App\Domain\Categories\DTO\UpdateCategoryData;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Get all categories for a user (user's categories + default categories).
     */
    public function getAllForUser(User $user): Collection
    {
        return Category::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('is_default', true);
            })
            ->orderBy('is_default', 'desc') // Default categories first
            ->orderBy('name', 'asc')
            ->get();
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
