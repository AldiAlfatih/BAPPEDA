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
        // $search = $request->input('search');

        // $users = User::query()
        //     ->whereHas('roles', function ($q) {
        //         $q->whereIn('name', ['perangkat_daerah', 'operator']);
        //     })
        //     ->when($search, function ($query, $search) {
        //         $query->where(function ($q) use ($search) {
        //             $q->where('name', 'like', "%{$search}%")
        //                 ->orWhere('email', 'like', "%{$search}%")
        //                 ->orWhereHas('userDetail', function ($q2) use ($search) {
        //                     $q2->where('nip', 'like', "%{$search}%");
        //                 });
        //         });
        //     })
        //     ->with(['roles', 'userDetail', 'profileSkpd'])
        //     ->latest()
        //     ->paginate(10);

        return Inertia::render('UserManagement', [
            'users' => $users,
            // 'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return Inertia::render('UserManagement/Create');
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
            'tanggal_lahir' => 'required|date',
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
            'tanggal_lahir' => $validated['tanggal_lahir'],
        ]);

        if ($validated['role'] == 'perangkat_daerah') {
            $user->profileSkpd()->create([
                'nama_kepala_skpd' => $validated['nama_kepala_skpd'],
                'kode_urusan' => $validated['kode_urusan'],
                'nama_skpd' => $validated['nama_skpd'],
                'kode_organisasi' => $validated['kode_organisasi'],
            ]);
        }

        return redirect()->route('user-management.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $user)
    {
        if (!auth()->user()->hasRole('admin') && auth()->id() !== $user->id) {
            abort(403);
        }

        return Inertia::render('UserManagement/Edit', [
            'user' => $user->load('userDetail', 'profileSkpd', 'roles'),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'alamat' => 'required|string|max:255',
            'nip' => 'required|string|max:50',
            'no_hp' => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'nama_kepala_skpd' => 'nullable|string|max:255',
            'kode_urusan' => 'nullable|string|max:100',
            'nama_skpd' => 'nullable|string|max:255',
            'kode_organisasi' => 'nullable|string|max:100',
        ]);

        if (auth()->user()->hasRole('admin')) {
            $user->update([
                'email' => $validated['email'],
                'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
            ]);
        }

        $user->userDetail()->update([
            'alamat' => $validated['alamat'],
            'nip' => $validated['nip'],
            'no_hp' => $validated['no_hp'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'nama_kepala_skpd' => $validated['nama_kepala_skpd'],
            'kode_urusan' => $validated['kode_urusan'],
            'nama_skpd' => $validated['nama_skpd'],
        ]);

        // if ($user->hasRole('perangkat_daerah') && $user->profileSkpd) {
        //     $user->profileSkpd()->update([
        //         'nama_kepala_skpd' => $validated['nama_kepala_skpd'],
        //         'kode_urusan' => $validated['kode_urusan'],
        //         'nama_skpd' => $validated['nama_skpd'],
        //         'kode_organisasi' => $validated['kode_organisasi'],
        //     ]);
        // }

        return redirect()->route('user-management.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user-management.index')->with('success', 'Akun berhasil dihapus.');
    }
}
