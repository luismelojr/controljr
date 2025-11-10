<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Categories\DTO\CreateCategoryData;
use App\Domain\Categories\DTO\UpdateCategoryData;
use App\Domain\Categories\Services\CategoryService;
use App\Facades\Toast;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoriesController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Category::class);

        $categories = $this->categoryService->getAllForUser(
            user: auth()->user(),
            perPage: request()->input('perPage', 10),
        );

        return Inertia::render('dashboard/categories/index', [
            'categories' => CategoryResource::collection($categories)->resolve(),
            'filters' => request()->only(['filter', 'sort']), // Send current filters to frontend
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Category::class);

        return Inertia::render('dashboard/categories/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', Category::class);

        $data = CreateCategoryData::fromRequest($request);

        $this->categoryService->create($data, auth()->user());

        Toast::success('Categoria criada com sucesso!');

        return redirect()->route('dashboard.categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): Response
    {
        $this->authorize('update', $category);

        return Inertia::render('dashboard/categories/edit', [
            'category' => new CategoryResource($category),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);

        try {
            $data = UpdateCategoryData::fromRequest($request);

            $this->categoryService->update($category, $data);

            Toast::success('Categoria atualizada com sucesso!');

            return redirect()->route('dashboard.categories.index');
        } catch (\Exception $e) {
            Toast::error($e->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        try {
            $this->categoryService->delete($category);

            Toast::success('Categoria excluída com sucesso!');

            return redirect()->route('dashboard.categories.index');
        } catch (\Exception $e) {
            Toast::error($e->getMessage());

            return back();
        }
    }
}
