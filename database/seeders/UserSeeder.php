<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Skpd;
use App\Models\SkpdTugas;
use App\Models\SkpdKepala;
use App\Models\TimKerja;
use App\Models\Kodenomenklatur;
use App\Models\UserDetail;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'super_admin']);
        Role::firstOrCreate(['name' => 'perangkat_daerah']);
        Role::firstOrCreate(['name' => 'operator']);

        // Buat user admin dan superadmin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => \Hash::make('12345678'),
        ]);
        $admin->assignRole('admin');
        $this->createUserDetail($admin, 1);

        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => \Hash::make('12345678'),
        ]);
        $superadmin->assignRole('super_admin');
        $this->createUserDetail($superadmin, 2);

    // Data perangkat daerah + nama dinas
     $pdData = [
            ['name' => 'Andi Rahmat',    'email' => 'andirahmat@gmail.com',    'nama_dinas' => 'Dinas Pendidikan dan Kebudayaan'],
            ['name' => 'Siti Marlina',   'email' => 'sitimarlina@gmail.com',   'nama_dinas' => 'Dinas Kesehatan'],
            ['name' => 'Budi Santoso',   'email' => 'budisantoso@gmail.com',   'nama_dinas' => 'RS Hasri Ainun Habibie'],
            ['name' => 'Nurul Hidayat',  'email' => 'nurulhidayat@gmail.com',  'nama_dinas' => 'RSUD Andi Makkasau'],
            ['name' => 'Agus Salim',     'email' => 'agussalim@gmail.com',     'nama_dinas' => 'Dinas Pekerjaan Umum dan Penataan'],
            ['name' => 'Rina Oktaviani', 'email' => 'rinaoktaviani@gmail.com', 'nama_dinas' => 'Dinas Perumahan, Kawasan Perkotaan'],
            ['name' => 'Yusuf Maulana',  'email' => 'yusufmaulana@gmail.com',  'nama_dinas' => 'Dinas Satuan Polisi Pamong Praja'],
            ['name' => 'Dewi Kartika',   'email' => 'dewikartika@gmail.com',   'nama_dinas' => 'Dinas Pemadam Kebakaran dan Penyelamatan'],
            ['name' => 'Hasan Basri',    'email' => 'hasanbasri@gmail.com',    'nama_dinas' => 'Badan Penanggulangan Bencana'],
            ['name' => 'Maya Sari',      'email' => 'mayasari@gmail.com',      'nama_dinas' => 'Dinas Sosial'],
            ['name' => 'Teguh Wibowo',   'email' => 'teguhwibowo@gmail.com',   'nama_dinas' => 'Dinas Tenaga Kerja'],
            ['name' => 'Ayu Lestari',    'email' => 'ayulestari@gmail.com',    'nama_dinas' => 'Dinas Pemberdayaan Perempuan'],
            ['name' => 'Dedi Supriyadi', 'email' => 'dedisupriyadi@gmail.com', 'nama_dinas' => 'Dinas Ketahanan Pangan'],
            ['name' => 'Nina Fitriani',  'email' => 'ninafitriani@gmail.com',  'nama_dinas' => 'Dinas Lingkungan Hidup'],
            ['name' => 'Rizal Maulana',  'email' => 'rizalmaulana@gmail.com',  'nama_dinas' => 'Dinas Kependudukan dan Pencatatan Sipil'],
            ['name' => 'Sarah Wijaya',   'email' => 'sarahwijaya@gmail.com',   'nama_dinas' => 'Dinas Pengendalian Penduduk'],
            ['name' => 'Tomy Prasetyo',  'email' => 'tomyprasetyo@gmail.com',  'nama_dinas' => 'Dinas Perhubungan'],
            ['name' => 'Lina Marlina',   'email' => 'linamarlina@gmail.com',   'nama_dinas' => 'Dinas Komunikasi dan Informatika'],
            ['name' => 'Ahmad Zulfikar', 'email' => 'ahmadzulfikar@gmail.com', 'nama_dinas' => 'Dinas Penanaman Modal dan Perizinan'],
            ['name' => 'Diana Kusuma',   'email' => 'dianakusuma@gmail.com',   'nama_dinas' => 'Dinas Kepemudaan, Olahraga dan Pariwisata'],
            ['name' => 'Rico Santoso',   'email' => 'ricosantoso@gmail.com',   'nama_dinas' => 'Dinas Perpustakaan'],
            ['name' => 'Mila Kurnia',    'email' => 'milakurnia@gmail.com',    'nama_dinas' => 'Dinas Pertanian, Kelautan, dan Perikanan'],
            ['name' => 'Eka Prasetya',  'email' => 'ekaprasetya@gmail.com',   'nama_dinas' => 'Dinas Perdagangan'],
            ['name' => 'Fajar Nugroho', 'email' => 'fajarnugroho@gmail.com',  'nama_dinas' => 'Sekretariat Daerah Kota'],
            ['name' => 'Sari Melati',   'email' => 'sarimelati@gmail.com',    'nama_dinas' => 'Sekretariat DPRD'],
            ['name' => 'Joko Widodo',   'email' => 'jokowidodo@gmail.com',    'nama_dinas' => 'Badan Perencanaan Pembangunan Daerah'],
            ['name' => 'Lestari Putri', 'email' => 'lestariputri@gmail.com',  'nama_dinas' => 'Badan Keuangan Daerah'],
            ['name' => 'Rian Saputra',  'email' => 'riansaputra@gmail.com',   'nama_dinas' => 'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia'],
            ['name' => 'Dewi Anggraeni', 'email' => 'dewianggraeni@gmail.com', 'nama_dinas' => 'Inspektorat Daerah'],
            ['name' => 'Bambang Hartono', 'email' => 'bambanghartono@gmail.com', 'nama_dinas' => 'Kecamatan Bacukiki'],
            ['name' => 'Rini Permatasari', 'email' => 'rinipermatasari@gmail.com', 'nama_dinas' => 'Kecamatan Bacukiki Barat'],
            ['name' => 'Andika Pratama', 'email' => 'andikapratama@gmail.com', 'nama_dinas' => 'Kecamatan Soreang'],
            ['name' => 'Nina Kartika',  'email' => 'ninakartika@gmail.com',   'nama_dinas' => 'Kecamatan Ujung'],
            ['name' => 'Yusuf Kurniawan','email' => 'yusufkurniawan@gmail.com', 'nama_dinas' => 'Badan Kesatuan Bangsa dan Politik'],
        ];

        // Operator names (34 entries)
        $operatorNames = [
            'Ridwan Hidayat', 'Lisa Maulida', 'Joko Wibisono',
            'Anisa Rahma', 'Dian Suryani', 'Hendra Putra',
        ];

        // Buat semua user perangkat daerah
        $pdUsers = [];
        $nipCounter = 3; // Lanjut setelah admin dan superadmin
        foreach ($pdData as $item) {
            $pdUser = User::create([
                'name' => $item['name'],
                'email' => $item['email'],
                'password' => \Hash::make('12345678'),
            ]);
            $pdUser->assignRole('perangkat_daerah');
            $this->createUserDetail($pdUser, $nipCounter++);
            $pdUsers[] = $pdUser;
        }

        // Buat user operator
        $operatorUsers = [];
        foreach ($operatorNames as $operatorName) {
            $operatorEmail = Str::slug($operatorName) . '@gmail.com';

            $operatorUser = User::create([
                'name' => $operatorName,
                'email' => $operatorEmail,
                'password' => \Hash::make('12345678'),
            ]);
            $operatorUser->assignRole('operator');
            $this->createUserDetail($operatorUser, $nipCounter++);
            $operatorUsers[] = $operatorUser;
        }

        // Bagi perangkat daerah ke 6 kelompok operator
        $pdGroups = array_chunk($pdUsers, ceil(count($pdUsers) / count($operatorUsers)));

        foreach ($pdGroups as $groupIndex => $pdGroup) {
            $operatorUser = $operatorUsers[$groupIndex];

            foreach ($pdGroup as $index => $pdUser) {
                $pdIndex = array_search($pdUser, $pdUsers);
                $namaDinas = $pdData[$pdIndex]['nama_dinas']; 

                // Buat SKPD dengan struktur baru
                $skpd = Skpd::create([
                    'nama_skpd' => $namaDinas,
                    'kode_organisasi' => 'ORG-' . str_pad($pdIndex + 1, 3, '0', STR_PAD_LEFT),
                ]);

                // Buat SKPD Kepala
                SkpdKepala::create([
                    'skpd_id' => $skpd->id,
                    'user_id' => $pdUser->id,
                    'is_aktif' => true,
                ]);

                // Buat Tim Kerja dengan struktur baru
                TimKerja::create([
                    'skpd_id' => $skpd->id,
                    'operator_id' => $operatorUser->id,
                    'is_aktif' => true,
                ]);

                // Khusus hanya untuk dua dinas berikut, isi tugasnya:
                if ($namaDinas === 'Dinas Pendidikan dan Kebudayaan') {
                    $relevantKode = Kodenomenklatur::whereIn('nomor_kode', [
                        '1', '1.01', '1.01.01', '1.01.01.2.01', '1.01.01.2.01.0001', '1.01.01.2.01.0002',
                        '2', '2.22', '2.22.02', '2.22.02.2.01', '2.22.02.2.01.0001',
                    ])->pluck('id');
                } elseif ($namaDinas === 'RS Hasri Ainun Habibie') {
                    $relevantKode = Kodenomenklatur::whereIn('nomor_kode', [
                        '1', '1.02', '1.02.01', '1.02.01.2.01', '1.02.01.2.01.0001',
                    ])->pluck('id');
                } else {
                    $relevantKode = collect();
                }

                foreach ($relevantKode as $kodeId) {
                    SkpdTugas::create([
                        'skpd_id' => $skpd->id,
                        'kode_nomenklatur_id' => $kodeId,
                        'is_aktif' => true,
                    ]);
                }
            }
        }
    }

    private function createUserDetail(User $user, int $nipIndex)
    {
        UserDetail::create([
            'user_id' => $user->id,
            'alamat' => 'Jl. Contoh Alamat No. ' . $nipIndex,
            'nip' => '1987' . str_pad($nipIndex, 6, '0', STR_PAD_LEFT),
            'no_hp' => '0812' . rand(10000000, 99999999),
            'jenis_kelamin' => rand(0, 1) ? 'Laki-laki' : 'Perempuan',
        ]);
    }
}
