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
            'email' => 'admin@pareparekota.go.id',
            'password' => \Hash::make('12345678'),
        ]);
        $admin->assignRole('admin');
        $this->createUserDetail($admin, 1);

        // 3. Buat 4 User Operator
        $operators = [
            [
                'name' => 'ASTI AMALIA',
                'email' => 'astiamaliamayasari@gmail.com',
            ],
            [
                'name' => 'NISA',
                'email' => 'nisa@gmail.com',
            ],
            [
                'name' => 'nita',
                'email' => 'nita@gmail.com',
            ],
            [
                'name' => 'RIDWAN HIDAYAT',
                'email' => 'ridwanhidayat@gmail.com',
            ],
        ];

        $operatorUsers = [];
        $operatorIndex = 2; // Mulai dari index 2 karena admin sudah index 1

        foreach ($operators as $operatorData) {
            $operator = User::firstOrCreate([
                'name' => $operatorData['name'],
                'email' => $operatorData['email'],
                'password' => \Hash::make('12345678'),
            ]);
            $operator->assignRole('operator');
            $this->createUserDetail($operator, $operatorIndex);
            
            $operatorUsers[] = $operator;
            $operatorIndex++;
        }



        // 5. Data SKPD dengan PPK dan email @gmail.com
        $skpdData = [
            [
                'nama_skpd' => 'Dinas Pendidikan dan Kebudayaan',
                'nama_ppk' => 'H. M. MAKMUR, S.Pd., M.M',
                'email' => 'disdikbud@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.01.0000',
            ],
            [
                'nama_skpd' => 'Dinas Kesehatan',
                'nama_ppk' => 'RAHMAWATY, SKM., M. Kes (MARS)',
                'email' => 'dinkes@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.02.0000',
            ],
            [
                'nama_skpd' => 'RS Hasri Ainun Habibie',
                'nama_ppk' => 'dr. Mahyuddin Rasyid, Sp. B FINACS.FICS',
                'email' => 'rshabibie@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.03.0000',
            ],
            [
                'nama_skpd' => 'RSUD Andi Makkasau',
                'nama_ppk' => 'dr.Hj. Renny Anggraeny Sari, M.Kes',
                'email' => 'rsudmakkasau@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.04.0000',
            ],
            [
                'nama_skpd' => 'Dinas Pekerjaan Umum dan Penataan Ruang',
                'nama_ppk' => 'BUDI RUSDI, S.Pi., M.M',
                'email' => 'pupr@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.05.0000',
            ],
            [
                'nama_skpd' => 'Dinas Perumahan, Kawasan Permukiman dan Pertanahan',
                'nama_ppk' => 'NOLDY Y. RENGKUAN',
                'email' => 'perkimtan@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.06.0000',
            ],
            [
                'nama_skpd' => 'Dinas Satuan Polisi Pamong Praja',
                'nama_ppk' => 'ANDI ULFAH, S.STP., M.Si',
                'email' => 'satpolpp@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.07.0000',
            ],
            [
                'nama_skpd' => 'Dinas Pemadam Kebakaran dan Penyelamatan',
                'nama_ppk' => 'ABDUL WARIS MUHIDDIN, S.Pd',
                'email' => 'damkar@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.08.0000',
            ],
            [
                'nama_skpd' => 'Badan Penanggulangan Bencana Daerah',
                'nama_ppk' => 'ILHAM WILLIEM, Skm., M.Kes',
                'email' => 'bpbd@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.09.0000',
            ],
            [
                'nama_skpd' => 'Dinas Sosial',
                'nama_ppk' => 'A.Erwin Pallawarukka, AP., M.Si',
                'email' => 'dinsos@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.10.0000',
            ],
            [
                'nama_skpd' => 'Dinas Tenaga Kerja',
                'nama_ppk' => 'RASUKI BUSRAH, SE, M.Si',
                'email' => 'disnaker@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.11.0000',
            ],
            [
                'nama_skpd' => 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak',
                'nama_ppk' => 'JUMADI M, SE., MM',
                'email' => 'dp3a@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.12.0000',
            ],
            [
                'nama_skpd' => 'Dinas Ketahanan Pangan',
                'nama_ppk' => 'MUHAMMAD IDRIS, SKM, M.Kes',
                'email' => 'dkp@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.13.0000',
            ],
            [
                'nama_skpd' => 'Dinas Lingkungan Hidup',
                'nama_ppk' => 'Susianna, S.SIP',
                'email' => 'dlh@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.14.0000',
            ],
            [
                'nama_skpd' => 'Dinas Kependudukan dan Pencatatan Sipil',
                'nama_ppk' => 'Hj. SURIANI, SH',
                'email' => 'disdukcapil@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.15.0000',
            ],
            [
                'nama_skpd' => 'Dinas Pengendalian Penduduk dan Keluarga Berencana',
                'nama_ppk' => 'AMARUN AGUNG HAMKA, S.STP., M.Si',
                'email' => 'dppkb@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.16.0000',
            ],
            [
                'nama_skpd' => 'Dinas Perhubungan',
                'nama_ppk' => 'FITRIANY, S.STP',
                'email' => 'dishub@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.17.0000',
            ],
            [
                'nama_skpd' => 'Dinas Komunikasi dan Informatika',
                'nama_ppk' => 'M. ANWAR AMIR, S.STP, M.Si',
                'email' => 'diskominfo@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.18.0000',
            ],
            [
                'nama_skpd' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu',
                'nama_ppk' => 'HJ. ST. RAHMAH AMIR, ST., MM',
                'email' => 'dpmptsp@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.19.0000',
            ],
            [
                'nama_skpd' => 'Dinas Kepemudaan, Olahraga dan Pariwisata',
                'nama_ppk' => 'H. M. ISKANDAR NUSU, S.STP., M.Si',
                'email' => 'disporapar@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.20.0000',
            ],
            [
                'nama_skpd' => 'Dinas Perpustakaan',
                'nama_ppk' => 'Drs. H. AHMAD MASDAR, M.Si',
                'email' => 'diperpustakaan@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.21.0000',
            ],
            [
                'nama_skpd' => 'Dinas Pertanian, Kelautan, dan Perikanan',
                'nama_ppk' => 'Ir. WILDANA, SP., MM., IPU',
                'email' => 'dpkp@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.22.0000',
            ],
            [
                'nama_skpd' => 'Dinas Perdagangan',
                'nama_ppk' => 'HJ. A. WISNAH T, SE., M.Si',
                'email' => 'disdag@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.23.0000',
            ],
            [
                'nama_skpd' => 'Sekretariat Daerah Kota',
                'nama_ppk' => 'AMARUN AGUNG HAMKA, S.STP., M.Si',
                'email' => 'setda@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.24.0000',
            ],
            [
                'nama_skpd' => 'Sekretariat DPRD',
                'nama_ppk' => 'Drs. ARIFUDDIN, MP',
                'email' => 'setwan@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.25.0000',
            ],
            [
                'nama_skpd' => 'Badan Perencanaan Pembangunan Daerah',
                'nama_ppk' => 'ZULKARNAEN, S.T, M.Si',
                'email' => 'bappeda@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.26.0000',
            ],
            [
                'nama_skpd' => 'Badan Keuangan Daerah',
                'nama_ppk' => 'PRASETYO CATUR KRISTIANTO, SH, MH',
                'email' => 'bkd@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.27.0000',
            ],
            [
                'nama_skpd' => 'Badan Kepegawaian dan Pengembangan Sumber Daya Manusia Daerah',
                'nama_ppk' => 'ADRIANI IDRUS, SP., MM',
                'email' => 'bkpsdm@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.28.0000',
            ],
            [
                'nama_skpd' => 'Inspektorat Daerah',
                'nama_ppk' => 'AGUSSALIM, S.IP., M.Si',
                'email' => 'inspektorat@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.29.0000',
            ],
            [
                'nama_skpd' => 'Kecamatan Bacukiki',
                'nama_ppk' => 'MUHAMMAD SYAKIR, S.STP.,M.Si',
                'email' => 'bacukiki@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.30.0000',
            ],
            [
                'nama_skpd' => 'Kecamatan Bacukiki Barat',
                'nama_ppk' => 'ARDIANSYAH ARIFUDDIN, S.STP., M.Si',
                'email' => 'bacukikibarat@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.31.0000',
            ],
            [
                'nama_skpd' => 'Kecamatan Soreang',
                'nama_ppk' => 'AWALUDDIN, S. Pd',
                'email' => 'soreang@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.32.0000',
            ],
            [
                'nama_skpd' => 'Kecamatan Ujung',
                'nama_ppk' => 'MUHAMMAD YUSUF AZIZ, SE., MM',
                'email' => 'ujung@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.33.0000',
            ],
            [
                'nama_skpd' => 'Badan Kesatuan Bangsa dan Politik',
                'nama_ppk' => 'H. RUSTAN ASTA, SE, M.Si',
                'email' => 'kesbangpol@gmail.com',
                'kode_organisasi' => '5.01.5.05.0.00.34.0000',
            ],
        ];

        $userDetailIndex = 6; // Mulai dari index 6 karena sudah ada 5 user sebelumnya (1 admin + 4 operator)

        foreach ($skpdData as $index => $data) {
            // Buat user untuk setiap PPK
            $ppkUser = User::firstOrCreate([
                'name' => $data['nama_ppk'],
                'email' => $data['email'],
                'password' => \Hash::make('12345678'),
            ]);
            $ppkUser->assignRole('perangkat_daerah');
            $this->createUserDetail($ppkUser, $userDetailIndex);

            // Buat data SKPD
            $skpd = Skpd::create([
                'nama_skpd' => $data['nama_skpd'],
                'kode_organisasi' => $data['kode_organisasi'],
            ]);

            // Hubungkan PPK sebagai kepala SKPD
            SkpdKepala::create([
                'skpd_id' => $skpd->id,
                'user_id' => $ppkUser->id,
                'is_aktif' => true,
            ]);

            // Tentukan operator berdasarkan pembagian SKPD
            // 34 SKPD dibagi 4 operator: 9-9-8-8
            $assignedOperator = null;
            if ($index <= 8) {
                // SKPD 1-9: ASTI AMALIA
                $assignedOperator = $operatorUsers[0];
            } elseif ($index <= 17) {
                // SKPD 10-18: NISA
                $assignedOperator = $operatorUsers[1];
            } elseif ($index <= 25) {
                // SKPD 19-26: nita
                $assignedOperator = $operatorUsers[2];
            } else {
                // SKPD 27-34: RIDWAN HIDAYAT
                $assignedOperator = $operatorUsers[3];
            }

            // Hubungkan operator yang ditentukan ke SKPD
            TimKerja::create([
                'skpd_id' => $skpd->id,
                'operator_id' => $assignedOperator->id,
                'is_aktif' => true,
            ]);

            $userDetailIndex++;
        }

        // 6. Tambahkan tugas/urusan untuk BAPPEDA
        $this->createBappedaTugas();
    }

    private function createBappedaTugas()
    {
        // Cari SKPD BAPPEDA
        $bappedaSkpd = Skpd::where('nama_skpd', 'Badan Perencanaan Pembangunan Daerah')->first();
        
        if ($bappedaSkpd) {
            // Cari urusan level 5 dari UrusanSeeder: "UNSUR PENUNJANG URUSAN PEMERINTAHAN"
            $urusanPenunjang = \DB::table('kode_nomenklatur')
                ->where('nomor_kode', '5')
                ->where('jenis_nomenklatur', 0) // urusan
                ->first();

            // Cari bidang urusan Perencanaan (5.01) dari BidangUrusanSeeder: 'PERENCANAAN'
            $bidangUrusanPerencanaan = \DB::table('kode_nomenklatur')
                ->where('nomor_kode', '5.01')
                ->where('jenis_nomenklatur', 1) // bidang urusan
                ->first();

            if ($urusanPenunjang && $bidangUrusanPerencanaan) {
                // Data program dan kegiatan khusus untuk BAPPEDA
                $bappedaTugas = [
                    'urusan_induk_kode' => '5',
                    'urusan_induk_nama' => 'UNSUR PENUNJANG URUSAN PEMERINTAHAN', // Sesuai UrusanSeeder
                    'bidang_urusan_kode' => '5.01',
                    'bidang_urusan_nama' => 'PERENCANAAN', // Sesuai BidangUrusanSeeder
                    'skpd_id' => $bappedaSkpd->id,
                    'program' => [
                        [
                            'kode_program' => '5.01.01',
                            'nama_program' => 'Program Perencanaan, Pengendalian dan Evaluasi Pembangunan Daerah',
                            'kegiatan' => [
                                [
                                    'kode_kegiatan' => '5.01.01.1.01',
                                    'nama_kegiatan' => 'Penyusunan Dokumen Perencanaan Perangkat Daerah',
                                    'subkegiatan' => [
                                        [
                                            'kode_subkegiatan' => '5.01.01.1.01.0001',
                                            'nama_subkegiatan' => 'Penyusunan Rencana Strategis Perangkat Daerah',
                                            'deskripsi' => 'Penyusunan Renstra SKPD sebagai dokumen perencanaan jangka menengah'
                                        ],
                                        [
                                            'kode_subkegiatan' => '5.01.01.1.01.0006',
                                            'nama_subkegiatan' => 'Penyusunan Rencana Kerja Perangkat Daerah',
                                            'deskripsi' => 'Penyusunan Renja SKPD sebagai dokumen perencanaan tahunan'
                                        ]
                                    ]
                                ],
                                [
                                    'kode_kegiatan' => '5.01.01.1.02',
                                    'nama_kegiatan' => 'Koordinasi dan Penyusunan Dokumen RKP Daerah',
                                    'subkegiatan' => [
                                        [
                                            'kode_subkegiatan' => '5.01.01.1.02.0001',
                                            'nama_subkegiatan' => 'Koordinasi dan Penyusunan Dokumen Rencana Kerja Pemerintah Daerah',
                                            'deskripsi' => 'Koordinasi dan penyusunan RKP Daerah sebagai acuan penyusunan RAPBD'
                                        ],
                                        [
                                            'kode_subkegiatan' => '5.01.01.1.02.0002',
                                            'nama_subkegiatan' => 'Koordinasi dan Penyusunan Dokumen Perencanaan Daerah Lainnya',
                                            'deskripsi' => 'Koordinasi penyusunan dokumen perencanaan sektoral dan spasial'
                                        ]
                                    ]
                                ],
                                [
                                    'kode_kegiatan' => '5.01.01.1.03',
                                    'nama_kegiatan' => 'Koordinasi dan Penyusunan Dokumen RPJMD',
                                    'subkegiatan' => [
                                        [
                                            'kode_subkegiatan' => '5.01.01.1.03.0001',
                                            'nama_subkegiatan' => 'Koordinasi dan Penyusunan Dokumen Rencana Pembangunan Jangka Menengah Daerah (RPJMD)',
                                            'deskripsi' => 'Koordinasi dan penyusunan RPJMD sebagai dokumen perencanaan 5 tahun'
                                        ]
                                    ]
                                ],
                                [
                                    'kode_kegiatan' => '5.01.01.1.05',
                                    'nama_kegiatan' => 'Penelitian dan Pengembangan Daerah',
                                    'subkegiatan' => [
                                        [
                                            'kode_subkegiatan' => '5.01.01.1.05.0001',
                                            'nama_subkegiatan' => 'Penelitian dan Pengembangan Daerah',
                                            'deskripsi' => 'Pelaksanaan litbang untuk mendukung perencanaan pembangunan daerah'
                                        ]
                                    ]
                                ],
                                [
                                    'kode_kegiatan' => '5.01.01.1.06',
                                    'nama_kegiatan' => 'Pengendalian dan Evaluasi Pelaksanaan Rencana Pembangunan Daerah',
                                    'subkegiatan' => [
                                        [
                                            'kode_subkegiatan' => '5.01.01.1.06.0002',
                                            'nama_subkegiatan' => 'Pengendalian Pelaksanaan Rencana Pembangunan Daerah',
                                            'deskripsi' => 'Monitoring dan pengendalian pelaksanaan pembangunan daerah'
                                        ],
                                        [
                                            'kode_subkegiatan' => '5.01.01.1.06.0003',
                                            'nama_subkegiatan' => 'Evaluasi Pelaksanaan Rencana Pembangunan Daerah',
                                            'deskripsi' => 'Evaluasi capaian kinerja pembangunan daerah'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];

                // Log informasi tugas BAPPEDA
                \Log::info("=== TUGAS DAN URUSAN BAPPEDA ===");
                \Log::info("SKPD: {$bappedaSkpd->nama_skpd}");
                \Log::info("Urusan Induk: {$bappedaTugas['urusan_induk_nama']} (Kode: {$bappedaTugas['urusan_induk_kode']})");
                \Log::info("Bidang Urusan: {$bappedaTugas['bidang_urusan_nama']} (Kode: {$bappedaTugas['bidang_urusan_kode']})");
                
                foreach ($bappedaTugas['program'] as $program) {
                    \Log::info("📋 Program: {$program['nama_program']} ({$program['kode_program']})");
                    
                    foreach ($program['kegiatan'] as $kegiatan) {
                        \Log::info("  🎯 Kegiatan: {$kegiatan['nama_kegiatan']} ({$kegiatan['kode_kegiatan']})");
                        
                        foreach ($kegiatan['subkegiatan'] as $subkegiatan) {
                            \Log::info("    📝 Sub Kegiatan: {$subkegiatan['nama_subkegiatan']} ({$subkegiatan['kode_subkegiatan']})");
                            \Log::info("       💡 {$subkegiatan['deskripsi']}");
                        }
                    }
                }

                // Simpan relasi SKPD dengan urusan menggunakan model SkpdTugas
                // Cari KodeNomenklatur untuk bidang urusan 5.01 (PERENCANAAN)
                $kodeNomenklaturPerencanaan = \App\Models\KodeNomenklatur::where('nomor_kode', $bappedaTugas['bidang_urusan_kode'])
                    ->where('jenis_nomenklatur', 1) // bidang urusan (level 1)
                    ->first();
                
                if ($kodeNomenklaturPerencanaan) {
                    SkpdTugas::updateOrCreate(
                        [
                            'skpd_id' => $bappedaSkpd->id,
                            'kode_nomenklatur_id' => $kodeNomenklaturPerencanaan->id
                        ],
                        [
                            'is_aktif' => true,
                        ]
                    );
                    
                    \Log::info("✅ SKPD Tugas created for BAPPEDA with kode_nomenklatur_id: {$kodeNomenklaturPerencanaan->id}");
                } else {
                    \Log::error("❌ KodeNomenklatur not found for bidang urusan: {$bappedaTugas['bidang_urusan_kode']}");
                }
            }
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