<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Skpd;
use App\Models\SkpdTugas;
use App\Models\SkpdKepala;
use App\Models\TimKerja;
use App\Models\KodeNomenklatur;
use App\Models\UserDetail;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Roles yang dibutuhkan
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'perangkat_daerah']);
        Role::firstOrCreate(['name' => 'operator']);

        // 2. Buat 1 User Admin
        $admin = User::firstOrCreate([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => \Hash::make('12345678'),
        ]);
        $admin->assignRole('admin');
        $this->createUserDetail($admin, 1);

        // 3. Buat 1 User Operator dengan nama orang
        $operator = User::firstOrCreate([
            'name' => 'Ridwan Hidayat',
            'email' => 'ridwanhidayat@gmail.com', 
            'password' => \Hash::make('12345678'),
        ]);
        $operator->assignRole('operator');
        $this->createUserDetail($operator, 2);

        // 4. Buat 1 User SKPD (BAPPEDA) dengan nama orang
        $bappedaUser = User::firstOrCreate([
            'name' => 'Arya Sunandar', 
            'email' => 'aryasunandar@gmail.com', 
            'password' => \Hash::make('12345678'),
        ]);
        $bappedaUser->assignRole('perangkat_daerah');
        $this->createUserDetail($bappedaUser, 3);

        // 5. Buat data SKPD untuk BAPPEDA
        $bappedaSkpd = Skpd::create([
            'nama_skpd' => 'Badan Perencanaan Pembangunan Daerah',
            'kode_organisasi' => 'ORG-BAPPEDA',
        ]);

        // 6. Hubungkan user Kepala SKPD ke data SKPD
        SkpdKepala::create([
            'skpd_id' => $bappedaSkpd->id,
            'user_id' => $bappedaUser->id,
            'is_aktif' => true,
        ]);

        // 7. Hubungkan user Operator ke data SKPD
        TimKerja::create([
            'skpd_id' => $bappedaSkpd->id,
            'operator_id' => $operator->id,
            'is_aktif' => true,
        ]);

        // 8. Tugaskan Nomenklatur khusus untuk BAPPEDA
        $nomenklaturBappeda = [
            // Urusan & Bidang Urusan
            '5', '5.01',
            // Program
            '5.01.01',
            // Kegiatan
            '5.01.01.2.01', 
            // Sub Kegiatan
            '5.01.01.2.01.01',
        ];

        $relevantKode = KodeNomenklatur::whereIn('nomor_kode', $nomenklaturBappeda)->pluck('id');

        foreach ($relevantKode as $kodeId) {
            SkpdTugas::create([
                'skpd_id' => $bappedaSkpd->id,
                'kode_nomenklatur_id' => $kodeId,
                'is_aktif' => true,
            ]);
        }
    }

    private function createUserDetail(User $user, int $nipIndex)
    {
        UserDetail::create([
            'user_id' => $user->id,
            'alamat' => 'Jl. Contoh Alamat No. ' . $nipIndex,
            'nip' => '1990' . str_pad($nipIndex, 6, '0', STR_PAD_LEFT),
            'no_hp' => '0812' . rand(10000000, 99999999),
            'jenis_kelamin' => rand(0, 1) ? 'Laki-laki' : 'Perempuan',
        ]);
    }
}