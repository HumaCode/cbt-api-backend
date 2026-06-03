<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Services\CategoryService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the categories.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return ResponseHelper::success($categories, 'Daftar kategori berhasil diambil.');
    }

    /**
     * Store a newly created category in storage.
     *
     * @param StoreCategoryRequest $request
     * @return JsonResponse
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());
        return ResponseHelper::success($category, 'Kategori berhasil dibuat.', 201);
    }

    /**
     * Display the specified category.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);

        if (!$category) {
            return ResponseHelper::error('Kategori tidak ditemukan.', null, 404);
        }

        return ResponseHelper::success($category, 'Detail kategori berhasil diambil.');
    }

    /**
     * Update the specified category in storage.
     *
     * @param UpdateCategoryRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, string $id): JsonResponse
    {
        $updated = $this->categoryService->updateCategory($id, $request->validated());

        if (!$updated) {
            return ResponseHelper::error('Kategori tidak ditemukan atau gagal diperbarui.', null, 404);
        }

        $category = $this->categoryService->getCategoryById($id);
        return ResponseHelper::success($category, 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified category from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->categoryService->deleteCategory($id);

        if (!$deleted) {
            return ResponseHelper::error('Kategori tidak ditemukan atau gagal dihapus.', null, 404);
        }

        return ResponseHelper::success(null, 'Kategori berhasil dihapus.');
    }
}
