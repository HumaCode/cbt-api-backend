<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GroupService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    protected GroupService $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * Display listing of groups.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $groups = $this->groupService->getGroups();
        return ResponseHelper::success($groups, 'Daftar grup berhasil diambil.');
    }

    /**
     * Store new group.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:groups,name|max:255',
            'description' => 'nullable|string',
        ]);

        $group = $this->groupService->createGroup($request->only(['name', 'description']));
        return ResponseHelper::success($group, 'Grup berhasil dibuat.', 201);
    }

    /**
     * Update group.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $group = $this->groupService->getGroupById($id);
        if (!$group) {
            return ResponseHelper::error('Grup tidak ditemukan.', null, 404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $group->id,
            'description' => 'nullable|string',
        ]);

        $this->groupService->updateGroup($id, $request->only(['name', 'description']));
        $updatedGroup = $this->groupService->getGroupById($id);

        return ResponseHelper::success($updatedGroup, 'Grup berhasil diperbarui.');
    }

    /**
     * Delete group.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->groupService->deleteGroup($id);
        if (!$deleted) {
            return ResponseHelper::error('Grup tidak ditemukan.', null, 404);
        }

        return ResponseHelper::success(null, 'Grup berhasil dihapus.');
    }

    /**
     * Sync group members.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function syncMembers(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $group = $this->groupService->syncGroupMembers($id, $request->user_ids);
        if (!$group) {
            return ResponseHelper::error('Grup tidak ditemukan.', null, 404);
        }

        return ResponseHelper::success($group, 'Anggota grup berhasil diperbarui.');
    }
}
