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
use Inertia\Inertia;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('Skpd')->paginate(10);
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
            'nama_skpd' => 'nullable|string|max:255',
            'no_dpa' => 'nullable|string|max:255',
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
        ]);

        if ($validated['role'] == 'perangkat_daerah') {
            $user->Skpd()->create([
                'user_id' => $user->id,
                'nama_skpd' => $validated['nama_skpd'],
                'no_dpa' => $validated['no_dpa'],
                'kode_organisasi' => $validated['kode_organisasi'],
            ]);
            if ($validated['role'] == 'perangkat_daerah') {
                $user->skpdKepala()->create([
                    'skpd_id' => $user->Skpd->id,
                    'user_id' => $user->id,
                ]);
            }
            if ($validated['role'] == 'perangkat_daerah') {
                $user->skpdTugas()->create([
                    'skpd_id' => $user->Skpd->id,
                    'user_id' => $user->id,
                    'is_aktif' => 1,
                ]);
            }
            if ($validated['role'] == 'perangkat_daerah') {
                $user->timKerja()->create([
                    'skpd_id' => $user->Skpd->id,
                    'user_id' => $user->id,
                ]);
            }


        }

        return redirect()->route('usermanagement.index')->with('success', 'Akun berhasil dibuat.');
    }
public function edit(User $user)
{
    $user->load(['userDetail', 'skpd', 'roles']);
    
    return Inertia::render('usermanagement/Edit', [
        'user' => $user,
    ]);
}

public function update(Request $request, User $user)
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
            'no_dpa' => 'nullable|string|max:255',
            'kode_organisasi' => 'nullable|string|max:100',
        ]);

    // Update user basic info
    $user->update([
        'nama' => $validated['nama'],
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
            
        ]);
    } else {
        // Create new record with explicit user_id
        UserDetail::create([
            'user_id' => $user->id,
            'alamat' => $validated['alamat'],
            'nip' => $validated['nip'],
            'no_hp' => $validated['no_hp'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
        ]);
    }

    // Update SKPD profile if user has 'perangkat_daerah' role
    if ($user->hasRole('perangkat_daerah')) {
        $skpd = skpd::where('user_id', $user->id)->first();
        
        if ($skpd) {
            // Update existing record
            $skpd->update([
                'nama_kepala_skpd' => $validated['nama_kepala_skpd'],
                'kode_urusan' => $validated['kode_urusan'],
                'nama_skpd' => $validated['nama_skpd'],
                'kode_organisasi' => $validated['kode_organisasi'],
            ]);
        } else {
            // Create new record with explicit user_id
            skpd::create([
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