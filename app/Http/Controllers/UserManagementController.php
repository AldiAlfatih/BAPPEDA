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
        $query = User::with('roles');

        // Tambahkan filter search jika ada
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('roles', function($subQ) use ($searchTerm) {
                      $subQ->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        $users = $query->paginate(1000);
        
        return Inertia::render('UserManagement', [
            'users' => $users,
            'filters' => [
                'search' => $request->search,
            ],
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

    public function edit($id)
    {
        $user = User::with('userDetail')->find($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }

        return Inertia::render('usermanagement/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames()->toArray(),
                'userDetail' => $user->userDetail ? $user->userDetail->only([
                    'alamat', 'nip', 'no_hp', 'jenis_kelamin',
                ]) : [
                    'alamat' => '',
                    'nip' => '',
                    'no_hp' => '',
                    'jenis_kelamin' => '',
                ],
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::with('userDetail')->find($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }

        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => [
                'required',
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
                Rule::unique('user_detail', 'nip')->ignore($user->userDetail->id ?? null),
            ],
            'no_hp' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
        ]);

        // Update user data
        $userData = [];
        if (!empty($validatedData['name'])) {
            $userData['name'] = $validatedData['name'];
        }
        if (!empty($validatedData['email'])) {
            $userData['email'] = $validatedData['email'];
        }
        if (!empty($validatedData['password'])) {
            $userData['password'] = Hash::make($validatedData['password']);
        }

        if (!empty($userData)) {
            $user->update($userData);
        }

        // Update role
        if (!empty($validatedData['role'])) {
            $user->syncRoles([$validatedData['role']]);
        }

        // Update user detail
        $userDetailFields = array_filter(
            array_intersect_key($validatedData, array_flip(['alamat', 'nip', 'no_hp', 'jenis_kelamin'])),
            function ($value) { return !is_null($value) && $value !== ''; }
        );
        
        if (!empty($userDetailFields)) {
            if ($user->userDetail) {
                $user->userDetail->update($userDetailFields);
            } else {
                // Create user detail if doesn't exist
                $user->userDetail()->create($userDetailFields);
            }
        }

        return redirect()->route('usermanagement.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }

        // Delete user detail first (if exists)
        if ($user->userDetail) {
            $user->userDetail->delete();
        }

        // Delete user
        $user->delete();
        
        return redirect()->route('usermanagement.index')->with('success', 'Akun berhasil dihapus.');
    }
}