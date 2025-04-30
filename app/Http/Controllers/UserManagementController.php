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
<<<<<<< HEAD
        $users = User::with('roles')->paginate(10);
=======
        $users = User::paginate(10); 
>>>>>>> 9531cbc115fb8e8a23e9b9d16ea3ff53ade95207
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

<<<<<<< HEAD
        if ($validated['role'] === 'perangkat_daerah') {
=======
        if ($validated['role'] == 'perangkat_daerah') {
>>>>>>> 9531cbc115fb8e8a23e9b9d16ea3ff53ade95207
            $user->profileSkpd()->create([
                'nama_kepala_skpd' => $validated['nama_kepala_skpd'],
                'kode_urusan' => $validated['kode_urusan'],
                'nama_skpd' => $validated['nama_skpd'],
                'kode_organisasi' => $validated['kode_organisasi'],
            ]);
        }

        return redirect()->route('usermanagement.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $user)
    {
<<<<<<< HEAD
        $user->loadMissing(['userDetail', 'profileSkpd', 'roles']);
=======

    $user->loadMissing(['userDetail', 'profileSkpd', 'roles']);
>>>>>>> 9531cbc115fb8e8a23e9b9d16ea3ff53ade95207

        if (!$user->userDetail) {
            $user->setRelation('userDetail', new UserDetail());
        }

        if (!$user->profileSkpd) {
            $user->setRelation('profileSkpd', new ProfileSkpd());
        }

        return Inertia::render('usermanagement/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:perangkat_daerah,operator',
            'alamat' => 'required|string|max:255',
            'nip' => 'required|string|max:50',
            'no_hp' => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
<<<<<<< HEAD
            'tgl_lahir' => 'required|date',
=======
            'tgl_lahir' => 'required|date', 
>>>>>>> 9531cbc115fb8e8a23e9b9d16ea3ff53ade95207
            'nama_kepala_skpd' => 'nullable|string|max:255',
            'kode_urusan' => 'nullable|string|max:100',
            'nama_skpd' => 'nullable|string|max:255',
            'kode_organisasi' => 'nullable|string|max:100',
        ]);

<<<<<<< HEAD
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $user->userDetail()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'alamat' => $validated['alamat'],
                'nip' => $validated['nip'],
                'no_hp' => $validated['no_hp'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tgl_lahir' => $validated['tgl_lahir'],
            ]
        );

        $user->syncRoles([$validated['role']]);

        if ($validated['role'] === 'perangkat_daerah') {
            $user->profileSkpd()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_kepala_skpd' => $validated['nama_kepala_skpd'],
                    'kode_urusan' => $validated['kode_urusan'],
                    'nama_skpd' => $validated['nama_skpd'],
                    'kode_organisasi' => $validated['kode_organisasi'],
                ]
            );
        } else {
            if ($user->profileSkpd) {
                $user->profileSkpd()->delete();
            }
=======
       
        if ($validated['password']) {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $user->update([
            'email' => $validated['email'],
        ]);

        $user->userDetail()->update([
            'alamat' => $validated['alamat'],
            'nip' => $validated['nip'],
            'no_hp' => $validated['no_hp'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tgl_lahir' => $validated['tgl_lahir'], 
        ]);

        if ($user->hasRole('perangkat_daerah')) {
            $user->profileSkpd()->update([
                'nama_kepala_skpd' => $validated['nama_kepala_skpd'],
                'kode_urusan' => $validated['kode_urusan'],
                'nama_skpd' => $validated['nama_skpd'],
                'kode_organisasi' => $validated['kode_organisasi'],
            ]);
>>>>>>> 9531cbc115fb8e8a23e9b9d16ea3ff53ade95207
        }

        return redirect()->route('usermanagement.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('usermanagement.index')->with('success', 'Akun berhasil dihapus.');
    }
}
