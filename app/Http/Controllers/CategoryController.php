<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $type = $request->input('type');

        $query = $request->user()->categories()
            ->withCount('transactions')
            ->with(['children' => fn ($q) => $q->withCount('transactions')->orderBy('sort_order')])
            ->parents()
            ->orderBy('sort_order');

        if ($type) {
            $query->ofType($type);
        }

        return $this->success(CategoryResource::collection($query->get()));
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if (! empty($data['parent_id'])) {
            $parent = Category::where('user_id', $user->id)->findOrFail($data['parent_id']);
            abort_if($parent->parent_id !== null, 422, '子分类不能再添加下级分类');
        }

        $sortOrder = $user->categories()
            ->where('type', $data['type'])
            ->where('parent_id', $data['parent_id'] ?? null)
            ->max('sort_order') + 1;

        $category = $user->categories()->create([
            ...$data,
            'sort_order' => $sortOrder,
        ]);

        $category->loadCount('transactions');

        if ($category->parent_id) {
            $category->load('parent');
        } else {
            $category->load(['children' => fn ($q) => $q->withCount('transactions')]);
        }

        return $this->success(new CategoryResource($category), '添加成功');
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $this->authorizeCategory($request, $category);

        $category->update($request->validated());
        $category->loadCount('transactions');

        return $this->success(new CategoryResource($category), '修改成功');
    }

    public function destroy(Request $request, Category $category): JsonResponse
    {
        $this->authorizeCategory($request, $category);

        if ($category->isParent()) {
            $category->children()->delete();
        }

        $category->delete();

        return $this->success(null, '删除成功');
    }

    private function authorizeCategory(Request $request, Category $category): void
    {
        abort_if($category->user_id !== $request->user()->id, 403, '无权操作此分类');
    }
}
