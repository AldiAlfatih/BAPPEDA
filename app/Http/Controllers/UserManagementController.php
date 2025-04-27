<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProfileSKPD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Hanya admin yang bisa melihat daftar pengguna
        if (!auth()->user()->can('view akun pengguna')) {
            abort(403, 'Unauthorized');
        }

        $users = User::with(['roles', 'ProfileSKPD'])
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['pd', 'operator']);
            })
            ->get();

        return Inertia::render('UserManagement/Index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Hanya admin yang bisa membuat akun
        if (!auth()->user()->can('add akun pengguna')) {
            abort(403, 'Unauthorized');
        }

        $roles = Role::whereIn('name', ['pd', 'operator'])->get();
        return Inertia::render('UserManagement/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|exists:roles,name',
            'nama_kepala_skpd' => 'required|string|max:255',
            'kode_urusan' => 'required|string|max:100',
            'nama' => 'required|string|max:255',
            'kode_organisasi' => 'required|string|max:100',
            'nip' => 'required|string|max:50',
        ]);

        // Cek apakah admin yang membuat user
        if (!auth()->user()->can('add akun pengguna')) {
            abort(403, 'Unauthorized');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign Role
        $user->assignRole($request->role);

        // Buat profil SKPD
        $user->ProfileSKPD()->create([
            'nama_kepala_skpd' => $request->nama_kepala_skpd,
            'kode_urusan' => $request->kode_urusan,
            'nama_skpd' => $request->nama,
            'kode_organisasi' => $request->kode_organisasi,
            'nip' => $request->nip,
        ]);

        return redirect()->route('user-management.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with('ProfileSKPD', 'roles')->findOrFail($id);

        // Cek apakah admin yang mengedit
        if (!auth()->user()->can('edit akun pengguna')) {
            abort(403, 'Unauthorized');
        }

        $roles = Role::whereIn('name', ['pd', 'operator'])->get();

        return Inertia::render('UserManagement/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
    
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|exists:roles,name',
            'nama_kepala_skpd' => 'required|string|max:255',
            'kode_urusan' => 'required|string|max:100',
            'nama' => 'required|string|max:255',
            'kode_organisasi' => 'required|string|max:100',
            'nip' => 'required|string|max:50',
        ];
    
        // Kalau Admin, password juga di-validate
        if (Auth::user()->hasRole('admin')) {
            $rules['password'] = 'nullable|string|min:6|confirmed';
        }
    
        $validated = $request->validate($rules);

        // Cek apakah admin yang mengedit
        if (!auth()->user()->can('edit akun pengguna')) {
            abort(403, 'Unauthorized');
        }
    
        // Update data user
        $user->name = $validated['name'];
        $user->email = $validated['email'];
    
        // Update password hanya kalau admin yang mengisi password baru
        if (Auth::user()->hasRole('admin') && !empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
    
        $user->save();
    
        // Update role
        $user->syncRoles($validated['role']);
    
        // Update Profile SKPD
        if ($user->ProfileSKPD) {
            $user->ProfileSKPD->update([
                'nama_kepala_skpd' => $validated['nama_kepala_skpd'],
                'kode_urusan' => $validated['kode_urusan'],
                'nama_skpd' => $validated['nama'],
                'kode_organisasi' => $validated['kode_organisasi'],
                'nip' => $validated['nip'],
            ]);
        }
    
        return redirect()->route('user-management.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Cek apakah admin yang menghapus
        if (!auth()->user()->can('delete akun pengguna')) {
            abort(403, 'Unauthorized');
        }

        if ($user->ProfileSKPD) {
            $user->ProfileSKPD->delete();
        }

        $user->delete();

        return redirect()->route('user-management.index')->with('success', 'User deleted successfully.');
    }
}
