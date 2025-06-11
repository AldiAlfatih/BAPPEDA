<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil utama (user + skpd + user_detail).
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'user_detail' => $user->userDetail, // relasi userDetail juga harus benar
        ]);
    }

    /**
     * Update informasi profil utama (user, skpd, user_detail).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();

    // Update data user
    $user->fill($request->only(['name', 'email']));
    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }
    $user->save();

    // Update atau buat user_detail
    $userDetailData = $request->only([
        'alamat',
        'nip',
        'no_hp',
        'jenis_kelamin',
    ]);

    if ($user->userDetail) {
        $user->userDetail->update($userDetailData);
    } else {
        $user->userDetail()->create($userDetailData);
    }

    return to_route('profile.edit')->with('status', 'profile-updated');
}

    /**
     * Hapus akun pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Tampilkan halaman profile detail (jika ingin pisahkan user_detail).
     */
    public function editDetail(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('settings/ProfileDetail', [
            'user_detail' => $user->userDetail,
            'status' => session('status'),
            'skpd' => $user->skpd, 
        ]);
    }

    /**
     * Update profile detail (khusus user_detail saja).
     */
    public function updateDetail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'alamat' => ['nullable', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:20'],
            'no_hp' => ['nullable', 'string', 'max:15'],
            'jenis_kelamin' => ['nullable', 'string', 'in:Laki-laki,Perempuan'],
        ]);

        $user = $request->user();

        if ($user->userDetail) {
            $user->userDetail->update($validated);
        } else {
            $user->userDetail()->create($validated);
        }

        return to_route('profile.detail')->with('status', 'profile-detail-updated');
    }
}
