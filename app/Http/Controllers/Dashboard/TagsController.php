<?php

namespace App\Http\Controllers\Dashboard;

use App\Domain\Tags\Services\TagService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Facades\Toast;

class TagsController extends Controller
{
    public function __construct(
        protected TagService $tagService
    ) {}

    public function index()
    {
        $tags = $this->tagService->getUserTags(auth()->user());

        return inertia('dashboard/tags/index', [
            'tags' => TagResource::collection($tags),
        ]);
    }

    public function store(StoreTagRequest $request)
    {
        $currentCount = $request->user()->tags()->count();

        if (\App\Http\Middleware\CheckPlanFeature::hasReachedLimit($request, 'max_tags', $currentCount)) {
            Toast::create('Você atingiu o limite de tags do seu plano.')
                ->error()
                ->action('Fazer Upgrade', route('dashboard.subscription.plans'))
                ->persistent()
                ->flash();

            return back();
        }

        $this->tagService->create($request->user(), $request->validated());

        Toast::create('Tag criada com sucesso!')
            ->success()
            ->flash();

        return back();
    }

    public function update(UpdateTagRequest $request, Tag $tag)
    {
        $this->authorize('update', $tag);

        $this->tagService->update($tag, $request->validated());

        Toast::create('Tag atualizada com sucesso!')
            ->success()
            ->flash();

        return back();
    }

    public function destroy(Tag $tag)
    {
        $this->authorize('delete', $tag);

        $this->tagService->delete($tag);

        Toast::create('Tag excluída com sucesso!')
            ->success()
            ->flash();

        return back();
    }
}
