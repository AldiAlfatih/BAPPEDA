<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Nomenklatur;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\RoleHasPermissions;
use Spatie\Permission\Models\PermissionRole;

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
        // $addNomenklatur = Permission::firstOrCreate(['name' => 'create nomenklatur']);
        // $editNomenklatur = Permission::firstOrCreate(['name' => 'edit nomenklatur']);
        // $deleteNomenklatur = Permission::firstOrCreate(['name' => 'delete nomenklatur']);
        // $viewNomenklatur = Permission::firstOrCreate(['name' => 'view nomenklatur']);
        // $createAkun = Permission::firstOrCreate(['name' => 'create akun']);

        // Permissions khusus untuk perangkat daerah (select dropdown)
        $mpd = Permission::firstOrCreate(['name' => 'mengelola_perangkat_daerah', 'guard_name' => 'web']);
        
        $selectNamakode = Permission::firstOrCreate(['name' => 'select nomor_kode','guard_name' => 'web']);
        $selectNomenklatur = Permission::firstOrCreate(['name' => 'select nomenklatur','guard_name' => 'web']);
        $selectUrusan = Permission::firstOrCreate(['name' => 'select urusan','guard_name' => 'web']);
        $selectBidang_Urusan = Permission::firstOrCreate(['name' => 'select bidang_urusan','guard_name' => 'web']);
        $selectProgram = Permission::firstOrCreate(['name' => 'select program','guard_name' => 'web']);
        $selectKegiatan = Permission::firstOrCreate(['name' => 'select kegiatan','guard_name' => 'web']);
        $selectSubkegiatan = Permission::firstOrCreate(['name' => 'select subkegiatan','guard_name' => 'web']);

        $addNomenklatur = Permission::firstOrCreate(['name' => 'add nomenklatur', 'guard_name' => 'web']);
        $editNomenklatur = Permission::firstOrCreate(['name' => 'edit nomenklatur', 'guard_name' => 'web']);
        $deleteNomenklatur = Permission::firstOrCreate(['name' => 'destroy nomenklatur', 'guard_name' => 'web']);
        $viewNomenklatur = Permission::firstOrCreate(['name' => 'view nomenklatur', 'guard_name' => 'web']);

        $addBantuan = Permission::firstOrCreate(['name' => 'add bantuan', 'guard_name' => 'web']);
        $editBantuan = Permission::firstOrCreate(['name' => 'edit bantuan', 'guard_name' => 'web']);
        $deleteBantuan = Permission::firstOrCreate(['name' => 'delete bantuan', 'guard_name' => 'web']);
        $viewBantuan = Permission::firstOrCreate(['name' => 'view bantuan', 'guard_name' => 'web']);

        $addAkunPengguna = Permission::firstOrCreate(['name' => 'create akun', 'guard_name' => 'web']);
        $editAkunPengguna = Permission::firstOrCreate(['name' => 'edit akun', 'guard_name' => 'web']);
        $deleteAkunPengguna = Permission::firstOrCreate(['name' => 'delete akun', 'guard_name' => 'web']);

        // Assign permission ke role super_admin
        $rsa->givePermissionTo([

        ]);

        // Assign permission ke role admin
        $ra->givePermissionTo([
            $addNomenklatur,
            $editNomenklatur,
            $deleteNomenklatur,
            $viewNomenklatur,
            $viewBantuan,
            $addBantuan,
            $editBantuan,
            $deleteBantuan,
            $addAkunPengguna,
            $editAkunPengguna,
            $deleteAkunPengguna,
        ]);

        // Assign permission ke role operator
        $rop->givePermissionTo([
            $viewNomenklatur,
            $viewBantuan,
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
            $viewNomenklatur,
            $viewBantuan
        ]);

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
