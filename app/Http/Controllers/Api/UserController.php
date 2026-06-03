<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of candidates.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'group_id']);
        $users = $this->userService->getCandidates($filters);
        return ResponseHelper::success($users, 'Daftar peserta berhasil diambil.');
    }

    /**
     * Store a newly created candidate.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6',
            'telp' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id'
        ]);

        $data = $request->only(['name', 'username', 'email', 'password', 'telp', 'gender']);
        $groupIds = $request->input('group_ids');

        $user = $this->userService->createCandidate($data, $groupIds);

        return ResponseHelper::success($user, 'Peserta berhasil dibuat.', 201);
    }

    /**
     * Update candidate info.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|string|min:6',
            'telp' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id'
        ]);

        $data = $request->only(['name', 'username', 'email', 'password', 'telp', 'gender']);
        $groupIds = $request->input('group_ids');

        $user = $this->userService->updateCandidate($id, $data, $groupIds);

        if (!$user) {
            return ResponseHelper::error('Peserta tidak ditemukan.', null, 404);
        }

        return ResponseHelper::success($user, 'Peserta berhasil diperbarui.');
    }

    /**
     * Remove candidate.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->userService->deleteCandidate($id);

        if (!$deleted) {
            return ResponseHelper::error('Peserta tidak ditemukan.', null, 404);
        }

        return ResponseHelper::success(null, 'Peserta berhasil dihapus.');
    }

    /**
     * Import users from Excel spreadsheet.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt',
        ]);

        try {
            $file = $request->file('file');
            
            $import = new \App\Imports\UsersImport;
            $data = \Maatwebsite\Excel\Facades\Excel::toArray($import, $file);
            
            if (empty($data) || empty($data[0])) {
                return ResponseHelper::error('File kosong atau format tidak valid.', null, 422);
            }

            $result = $this->userService->importCandidates($data[0]);

            return ResponseHelper::success(
                $result, 
                "Berhasil memproses data peserta: {$result['imported_count']} peserta baru diimpor, {$result['updated_count']} peserta diperbarui."
            );
        } catch (\Exception $e) {
            return ResponseHelper::error('Gagal mengimpor file: ' . $e->getMessage(), null, 500);
        }
    }
}
