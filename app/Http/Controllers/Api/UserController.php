<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of candidates.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $groupId = $request->input('group_id');

        $query = User::role('Peserta');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if (!empty($groupId)) {
            $query->whereHas('groups', function ($q) use ($groupId) {
                $q->where('groups.id', $groupId);
            });
        }

        $users = $query->with('groups')->latest()->get();
        return ResponseHelper::success($users, 'Daftar peserta berhasil diambil.');
    }

    /**
     * Store a newly created candidate.
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

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telp' => $request->telp,
            'gender' => $request->gender,
            'is_active' => '1',
        ]);

        $user->assignRole('Peserta');

        if ($request->has('group_ids')) {
            $user->groups()->sync($request->group_ids);
        }

        return ResponseHelper::success($user->load('groups'), 'Peserta berhasil dibuat.', 201);
    }

    /**
     * Update candidate info.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'telp' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id'
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'telp' => $request->telp,
            'gender' => $request->gender,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->has('group_ids')) {
            $user->groups()->sync($request->group_ids);
        }

        return ResponseHelper::success($user->load('groups'), 'Peserta berhasil diperbarui.');
    }

    /**
     * Remove candidate.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->groups()->detach();
        $user->delete();

        return ResponseHelper::success(null, 'Peserta berhasil dihapus.');
    }
}
