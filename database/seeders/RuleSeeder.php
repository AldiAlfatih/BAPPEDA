<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat Roles
        $rsa = Role::firstOrCreate(['name' => 'super_admin']);
        $ra = Role::firstOrCreate(['name' => 'admin']);
        $rop = Role::firstOrCreate(['name'=> 'operator']);
        $rpd = Role::firstOrCreate(['name' => 'perangkat_daerah']);
        

        // // Membuat Permissions umum
        // $mpd = Permission::firstOrCreate(['name' => 'mengelola_perangkat_daerah']);
        // $createNomenklatur = Permission::firstOrCreate(['name' => 'create nomenklatur']);
        // $editNomenklatur = Permission::firstOrCreate(['name' => 'edit nomenklatur']);
        // $deleteNomenklatur = Permission::firstOrCreate(['name' => 'delete nomenklatur']);
        // $viewNomenklatur = Permission::firstOrCreate(['name' => 'view nomenklatur']);
        // $createAkun = Permission::firstOrCreate(['name' => 'create akun']);

        // Permissions khusus untuk perangkat daerah (select dropdown)
        $mpd = Permission::firstOrCreate(['name' => 'mengelola_perangkat_daerah', 'guard_name' => 'web']);
        $selectNamakode = Permission::firstOrCreate(['name' => 'select nama_kode','guard_name' => 'web']);
        $selectNomenklatur = Permission::firstOrCreate(['name' => 'select nomenklatur','guard_name' => 'web']);
        $selectUrusan = Permission::firstOrCreate(['name' => 'select urusan','guard_name' => 'web']);
        $selectBidang_Urusan = Permission::firstOrCreate(['name' => 'select bidang_urusan','guard_name' => 'web']);
        $selectProgram = Permission::firstOrCreate(['name' => 'select program','guard_name' => 'web']);
        $selectKegiatan = Permission::firstOrCreate(['name' => 'select kegiatan','guard_name' => 'web']);
        $selectSubkegiatan = Permission::firstOrCreate(['name' => 'select subkegiatan','guard_name' => 'web']);

        $createNomenklatur = Permission::firstOrCreate(['name' => 'create nomenklatur', 'guard_name' => 'web']);
        $editNomenklatur = Permission::firstOrCreate(['name' => 'edit nomenklatur', 'guard_name' => 'web']);
        $deleteNomenklatur = Permission::firstOrCreate(['name' => 'delete nomenklatur', 'guard_name' => 'web']);
        $viewNomenklatur = Permission::firstOrCreate(['name' => 'view nomenklatur', 'guard_name' => 'web']);

        // Assign permission ke role super_admin
        $rsa->givePermissionTo([
            $mpd,
            $createNomenklatur,
            $editNomenklatur,
            $deleteNomenklatur,
            $viewNomenklatur,

        ]);

        // Assign permission ke role admin
        $ra->givePermissionTo([
            $createNomenklatur,
            $editNomenklatur,
            $deleteNomenklatur,
            $viewNomenklatur,

        ]);

        // Assign permission ke role operator
        $rop->givePermissionTo([
            $viewNomenklatur,
        ]);

        // Assign permission ke role perangkat daerah (hanya bisa memilih via dropdown)
        $rpd->givePermissionTo([
            $selectNamakode,
            $selectNomenklatur,
            $selectUrusan,
            $selectBidang_Urusan,
            $selectProgram,
            $selectKegiatan,
            $selectSubkegiatan,
        ]);


        // Membuat Permissions
        // $rsa = Permission::create(['name']);
        // $mpd = Permission::firstOrCreate(['name' => 'mengelola_perangkat_daerah']);
        // $createProgramPermission = Permission::firstOrCreate(['name' => 'create program']);
        // $editProgramPermission = Permission::firstOrCreate(['name' => 'edit program']);
        // $deleteProgramPermission = Permission::firstOrCreate(['name' => 'delete program']);
        // $viewProgramPermission = Permission::firstOrCreate(['name' => 'view program']);
        // $createKegiatanPermission = Permission::firstOrCreate(['name' => 'create kegiatan']);
        // $editKegiatanPermission = Permission::firstOrCreate(['name' => 'edit kegiatan']);
        // $deleteKegiatanPermission = Permission::firstOrCreate(['name' => 'delete kegiatan']);
        // $viewKegiatanPermission = Permission::firstOrCreate(['name' => 'view kegiatan']);
        // $createSubKegiatanPermission = Permission::firstOrCreate(['name' => 'create sub kegiatan']);
        // $editSubKegiatanPermission = Permission::firstOrCreate(['name' => 'edit sub kegiatan']);
        // $deleteSubKegiatanPermission = Permission::firstOrCreate(['name' => 'delete sub kegiatan']);
        // $viewSubKegiatanPermission = Permission::firstOrCreate(['name' => 'view sub kegiatan']);
        // $createAkunPermission = Permission::firstOrCreate(['name' => 'create akun']);
        // $rsa->givePermissionTo($mpd);

        // // // Memberikan Permissions ke Roles
        // // $rsa->givePermissionTo([
        //     // $createProgramPermission,
        //     // $editProgramPermission,
        //     // $deleteProgramPermission,
        //     $viewProgramPermission,
        //     // $createKegiatanPermission,
        //     // $editKegiatanPermission,
        //     // $deleteKegiatanPermission,
        //     $viewKegiatanPermission,
        //     // $createSubKegiatanPermission,
        //     // $editSubKegiatanPermission,
        //     // $deleteSubKegiatanPermission,
        //     $viewSubKegiatanPermission
        // ]);

        // $ra->givePermissionTo([
        //     $createProgramPermission,
        //     $editProgramPermission,
        //     $deleteProgramPermission,
        //     $viewProgramPermission,
        //     $createKegiatanPermission,
        //     $editKegiatanPermission,
        //     $deleteKegiatanPermission,
        //     $viewKegiatanPermission,
        //     $createSubKegiatanPermission,
        //     $editSubKegiatanPermission,
        //     $deleteSubKegiatanPermission,
        //     $viewSubKegiatanPermission,
        //     $createAkunPermission
        // ]);

        // $rop->givePermissionTo([
        //     $viewProgramPermission,
        //     $viewKegiatanPermission,
        //     $viewSubKegiatanPermission
        // ]);

        // $rpd->givePermissionTo([
        //     $viewProgramPermission,
        //     $viewKegiatanPermission,
        //     $viewSubKegiatanPermission
        // ]);

        // Membuat User
        $user = User::factory()->create([
            'email' => 'super_admin@gmail.com',
            'name' => 'Super Admin',
        ]);
        $user->assignRole($rsa);

        $user2 = User::factory()->create([
            'email' => 'admin@gmail.com',
            'name' => 'Admin',
        ]);
        $user2->assignRole($ra);

        $user3 = User::factory()->create([
            'name' => 'Operator',
            'email' => 'Operator@gmail.com',
        ]);
        $user3->assignRole($rop);

        $user4 = User::factory()->create([
            'name' => 'Perangkat_daerah',
            'email' => 'PD@gmail.com',
            
        ]);
        $user4->assignRole($rpd);
    }
}
