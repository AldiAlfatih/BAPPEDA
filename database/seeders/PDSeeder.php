<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Skpd;
use App\Models\SkpdTugas;
use App\Models\Kodenomenklatur;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class PDSeeder extends Seeder
{
    public function run(): void
    {
        // Buat role jika belum ada
        Role::firstOrCreate(['name' => 'perangkat_daerah']);
        Role::firstOrCreate(['name' => 'operator']);

        // Data perangkat daerah + nama dinas
        $pdData = [
            ['name' => 'Andi Rahmat',       'email' => 'andirahmat@gmail.com',       'nama_dinas' => 'Dinas Pendidikan'],
            ['name' => 'Siti Marlina',      'email' => 'sitimarlina@gmail.com',      'nama_dinas' => 'Dinas Kebudayaan'],
            ['name' => 'Budi Santoso',      'email' => 'budisantoso@gmail.com',      'nama_dinas' => 'Dinas Perencanaan'],
            ['name' => 'Nurul Hidayat',     'email' => 'nurulhidayat@gmail.com',     'nama_dinas' => 'Dinas Kesenian'],
            ['name' => 'Agus Salim',        'email' => 'agussalim@gmail.com',        'nama_dinas' => 'Dinas Pendidikan Dasar'],
            ['name' => 'Rina Oktaviani',    'email' => 'rinaoktaviani@gmail.com',    'nama_dinas' => 'Dinas Kesehatan'],
            ['name' => 'Yusuf Maulana',     'email' => 'yusufmaulana@gmail.com',     'nama_dinas' => 'Dinas Perhubungan'],
            ['name' => 'Dewi Kartika',      'email' => 'dewikartika@gmail.com',      'nama_dinas' => 'Dinas Sosial'],
            ['name' => 'Hasan Basri',       'email' => 'hasanbasri@gmail.com',       'nama_dinas' => 'Dinas Kominfo'],
            ['name' => 'Maya Sari',         'email' => 'mayasari@gmail.com',         'nama_dinas' => 'Dinas Perumahan'],
            ['name' => 'Teguh Wibowo',      'email' => 'teguhwibowo@gmail.com',      'nama_dinas' => 'Dinas PU'],
            ['name' => 'Ayu Lestari',       'email' => 'ayulestari@gmail.com',       'nama_dinas' => 'Dinas Pariwisata'],
            ['name' => 'Dedi Supriyadi',    'email' => 'dedisupriyadi@gmail.com',    'nama_dinas' => 'Dinas Perindustrian'],
            ['name' => 'Nina Fitriani',     'email' => 'ninafitriani@gmail.com',     'nama_dinas' => 'Dinas Pertanian'],
            ['name' => 'Rizal Maulana',     'email' => 'rizalmaulana@gmail.com',     'nama_dinas' => 'Dinas Ketahanan Pangan'],
            ['name' => 'Sarah Wijaya',      'email' => 'sarahwijaya@gmail.com',      'nama_dinas' => 'Dinas LH'],
            ['name' => 'Tomy Prasetyo',     'email' => 'tomyprasetyo@gmail.com',     'nama_dinas' => 'Dinas Tenaga Kerja'],
            ['name' => 'Lina Marlina',      'email' => 'linamarlina@gmail.com',      'nama_dinas' => 'Dinas Perdagangan'],
            ['name' => 'Ahmad Zulfikar',    'email' => 'ahmadzulfikar@gmail.com',    'nama_dinas' => 'Dinas Koperasi'],
            ['name' => 'Diana Kusuma',      'email' => 'dianakusuma@gmail.com',      'nama_dinas' => 'Dinas Penanaman Modal'],
        ];

        // Nama-nama operator masing-masing dinas (urutan harus sama)
        $operatorNames = [
            'Ridwan Hidayat', 'Lisa Maulida', 'Joko Wibisono', 'Anisa Rahma', 'Dian Suryani',
            'Hendra Putra', 'Lina Kartika', 'Fahmi Zulfikar', 'Nur Aisyah', 'Satria Nugraha',
            'Wulan Ayu', 'Bayu Saputra', 'Yulianti Ningsih', 'Reza Fahlevi', 'Putri Amelia',
            'Andika Pratama', 'Melati Rizki', 'Gilang Ramadhan', 'Vina Oktavia', 'Yusuf Kurniawan',
        ];

        // Buat semua user perangkat daerah dulu (tapi jangan buat skpd pakai ini)
    foreach ($pdData as $item) {
        $pdUser = User::create([
            'name' => $item['name'],
            'email' => $item['email'],
            'password' => \Hash::make('password123'),
        ]);
        $pdUser->assignRole('perangkat_daerah');
    }

    // Buat user operator dulu, simpan di array untuk nanti dibuat skpd
    $operatorUsers = [];
    foreach ($operatorNames as $index => $operatorName) {
        $operatorEmail = Str::slug($operatorName) . '@gmail.com';

        $operatorUser = User::create([
            'name' => $operatorName,
            'email' => $operatorEmail,
            'password' => \Hash::make('password123'),
        ]);
        $operatorUser->assignRole('operator');

        $operatorUsers[] = $operatorUser;
    }

    // Sekarang buat skpd sesuai urutan user operator yang tadi dibuat
    foreach ($operatorUsers as $index => $operatorUser) {
        $namaDinas = $pdData[$index]['nama_dinas']; // asumsi jumlah sama dan urutan cocok
        $namaOperator = $operatorUser->name;

        $skpd = Skpd::create([
            'user_id' => $operatorUser->id,
            'nama_skpd' => $namaDinas,
            'nama_operator' => $namaOperator,
            'nama_dinas' => $namaDinas,
            'no_dpa' => 'DPA-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
            'kode_organisasi' => 'ORG-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
        ]);

        // Tambahkan 2 entri urusan ke tabel skpd_tugas (jenis_nomenklatur = 1)
        $kodeUrusan = KodeNomenklatur::where('jenis_nomenklatur', 1)
                        ->inRandomOrder()
                        ->limit(2)
                        ->pluck('id');

        foreach ($kodeUrusan as $kodeId) {
            SkpdTugas::create([
                'skpd_id' => $skpd->id,
                'kode_nomenklatur_id' => $kodeId,
                'is_aktif' => true,
            ]);
        }
    }
    }
}
