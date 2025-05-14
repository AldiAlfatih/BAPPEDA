<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\ProfileSkpd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::paginate(10); 
        return Inertia::render('usermanagement', [
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
            'tgl_lahir' => 'required|date',
            'nama_kepala_skpd' => 'nullable|string|max:255',
            'kode_urusan' => 'nullable|string|max:100',
            'nama_skpd' => 'nullable|string|max:255',
            'kode_organisasi' => 'nullable|string|max:100',
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
            'tgl_lahir' => $validated['tgl_lahir'],
        ]);

        if ($validated['role'] == 'perangkat_daerah') {
            $user->profileSkpd()->create([
                'user_id' => $user->id,
                'nama_kepala_skpd' => $validated['nama_kepala_skpd'],
                'kode_urusan' => $validated['kode_urusan'],
                'nama_skpd' => $validated['nama_skpd'],
                'kode_organisasi' => $validated['kode_organisasi'],
            ]);
        }

        return redirect()->route('usermanagement.index')->with('success', 'Akun berhasil dibuat.');
    }

    // 1. First, let's implement the missing edit method in UserManagementController.php

// 1. First, let's implement the missing edit method in UserManagementController.php

public function edit(User $user)
{
    $user->load(['userDetail', 'profileSkpd', 'roles']);
    
    return Inertia::render('usermanagement/Edit', [
        'user' => $user,
    ]);
}

public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|unique:users,email,'.$user->id,
        'password' => 'nullable|string|min:8|confirmed',
        'alamat' => 'required|string|max:255',
        'nip' => 'required|string|max:50',
        'no_hp' => 'required|string|max:20',
        'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        'tgl_lahir' => 'nullable|date',
        'nama_kepala_skpd' => 'nullable|string|max:255',
        'kode_urusan' => 'nullable|string|max:100',
        'nama_skpd' => 'nullable|string|max:255',
        'kode_organisasi' => 'nullable|string|max:100',
    ]);

    // Update user basic info
    $user->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
    ]);

    // Update password if provided
    if (!empty($validated['password'])) {
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);
    }

    // Check if user detail exists
    $userDetail = UserDetail::where('user_id', $user->id)->first();
    
    if ($userDetail) {
        // Update existing record
        $userDetail->update([
            'alamat' => $validated['alamat'],
            'nip' => $validated['nip'],
            'no_hp' => $validated['no_hp'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tgl_lahir' => $validated['tgl_lahir'] ?? null,
        ]);
    } else {
        // Create new record with explicit user_id
        UserDetail::create([
            'user_id' => $user->id,
            'alamat' => $validated['alamat'],
            'nip' => $validated['nip'],
            'no_hp' => $validated['no_hp'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tgl_lahir' => $validated['tgl_lahir'] ?? null,
        ]);
    }

    // Update SKPD profile if user has 'perangkat_daerah' role
    if ($user->hasRole('perangkat_daerah')) {
        $profileSkpd = ProfileSkpd::where('user_id', $user->id)->first();
        
        if ($profileSkpd) {
            // Update existing record
            $profileSkpd->update([
                'nama_kepala_skpd' => $validated['nama_kepala_skpd'],
                'kode_urusan' => $validated['kode_urusan'],
                'nama_skpd' => $validated['nama_skpd'],
                'kode_organisasi' => $validated['kode_organisasi'],
            ]);
        } else {
            // Create new record with explicit user_id
            ProfileSkpd::create([
                'user_id' => $user->id,
                'nama_kepala_skpd' => $validated['nama_kepala_skpd'],
                'kode_urusan' => $validated['kode_urusan'],
                'nama_skpd' => $validated['nama_skpd'],
                'kode_organisasi' => $validated['kode_organisasi'],
            ]);
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