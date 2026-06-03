<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all(): Collection
    {
        return Category::with(['parent', 'children'])->get();
    }

    public function find(string $id): ?Category
    {
        return Category::with(['parent', 'children'])->find($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(string $id, array $data): bool
    {
        $category = Category::find($id);
        if (!$category) {
            return false;
        }
        return $category->update($data);
    }

    public function delete(string $id): bool
    {
        $category = Category::find($id);
        if (!$category) {
            return false;
        }
        return $category->delete();
    }
}
