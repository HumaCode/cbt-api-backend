<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display listing of groups.
     */
    public function index(): JsonResponse
    {
        $groups = Group::withCount('users')->latest()->get();
        return ResponseHelper::success($groups, 'Daftar grup berhasil diambil.');
    }

    /**
     * Store new group.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:groups,name|max:255',
            'description' => 'nullable|string',
        ]);

        $group = Group::create($request->only(['name', 'description']));
        return ResponseHelper::success($group, 'Grup berhasil dibuat.', 201);
    }

    /**
     * Update group.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $group = Group::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $group->id,
            'description' => 'nullable|string',
        ]);

        $group->update($request->only(['name', 'description']));
        return ResponseHelper::success($group, 'Grup berhasil diperbarui.');
    }

    /**
     * Delete group.
     */
    public function destroy(string $id): JsonResponse
    {
        $group = Group::findOrFail($id);
        $group->users()->detach();
        $group->assessments()->detach();
        $group->delete();

        return ResponseHelper::success(null, 'Grup berhasil dihapus.');
    }

    /**
     * Sync group members.
     */
    public function syncMembers(Request $request, string $id): JsonResponse
    {
        $group = Group::findOrFail($id);
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $group->users()->sync($request->user_ids);
        return ResponseHelper::success($group->load('users'), 'Anggota grup berhasil diperbarui.');
    }
}
