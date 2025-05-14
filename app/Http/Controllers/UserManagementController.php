<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Skpd;
use App\Models\SkpdKepala;
use App\Models\SkpdTugas;
use App\Models\TimKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('roles')->paginate(10);
        return Inertia::render('UserManagement', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return Inertia::render('usermanagement/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:perangkat_daerah,operator',
            'alamat' => 'required|string|max:255',
            'nip' => 'required|string|max:50',
            'no_hp' => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        $user->userDetail()->create([
            'alamat' => $validated['alamat'],
            'nip' => $validated['nip'],
            'no_hp' => $validated['no_hp'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
        ]);

        return redirect()->route('usermanagement.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $user->load('userDetail');

        $roles = $user->getRoleNames()->toArray();

        return Inertia::render('usermanagement/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $roles,
                'userDetail' => $user->userDetail ? [
                    'alamat' => $user->userDetail->alamat,
                    'nip' => $user->userDetail->nip,
                    'no_hp' => $user->userDetail->no_hp,
                    'jenis_kelamin' => $user->userDetail->jenis_kelamin,
                ] : null,
            ],
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => [
                'string',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'nullable|in:perangkat_daerah,operator',
            'alamat' => 'nullable|string|max:255',
            'nip' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('user_details', 'nip')->ignore($user->userDetail->id ?? 0),
            ],
            'no_hp' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
        ]);

        $user->fill([
            'name' => $validatedData['name'] ?? $user->name,
            'email' => $validatedData['email'],
        ])->save();

        if (!empty($validatedData['password'])) {
            $user->update([
                'password' => Hash::make($validatedData['password'])
            ]);
        }

        if (!empty($validatedData['role'])) {
            $user->syncRoles([$validatedData['role']]);
        }

        $userDetailFields = array_filter(
            array_intersect_key($validatedData, array_flip(['alamat', 'nip', 'no_hp', 'jenis_kelamin'])),
            function ($value) { return !is_null($value); }
        );

        if (!empty($userDetailFields)) {
            if ($user->userDetail) {
                $user->userDetail->update($userDetailFields);
            }
        }

        return redirect()->route('usermanagement.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('usermanagement.index')->with('success', 'Akun berhasil dihapus.');
    }
}
