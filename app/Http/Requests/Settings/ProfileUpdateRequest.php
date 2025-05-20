<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Tentukan aturan validasi untuk permintaan pembaruan profil.
     */
    public function rules(): array
    {
        return [
            // Validasi data user
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],

            // Validasi data SKPD
            'nama_skpd' => ['nullable', 'string', 'max:255'],
            'nama_operator' => ['nullable', 'string', 'max:255'],
            'nama_dinas' => ['nullable', 'string', 'max:255'],
            'no_dpa' => ['nullable', 'string', 'max:255'],
            'kode_organisasi' => ['nullable', 'string', 'max:255'],

            // Validasi data user_detail
            'alamat' => ['nullable', 'string', 'max:255'],
            'nip' => ['nullable', 'string', 'max:20'],
            'no_hp' => ['nullable', 'string', 'max:15'],
            'jenis_kelamin' => ['nullable', 'string', Rule::in(['Laki-laki', 'Perempuan'])],
        ];
    }
}
