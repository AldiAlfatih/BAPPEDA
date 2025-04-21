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
        
        // Membuat Permissions
        // $rsa = Permission::create(['name']);
        $mpd = Permission::firstOrCreate(['name' => 'mengelola_perangkat_daerah']);
        $createProgramPermission = Permission::firstOrCreate(['name' => 'create program']);
        $editProgramPermission = Permission::firstOrCreate(['name' => 'edit program']);
        $deleteProgramPermission = Permission::firstOrCreate(['name' => 'delete program']);
        $viewProgramPermission = Permission::firstOrCreate(['name' => 'view program']);
        $createKegiatanPermission = Permission::firstOrCreate(['name' => 'create kegiatan']);
        $editKegiatanPermission = Permission::firstOrCreate(['name' => 'edit kegiatan']);
        $deleteKegiatanPermission = Permission::firstOrCreate(['name' => 'delete kegiatan']);
        $viewKegiatanPermission = Permission::firstOrCreate(['name' => 'view kegiatan']);
        $createSubKegiatanPermission = Permission::firstOrCreate(['name' => 'create sub kegiatan']);
        $editSubKegiatanPermission = Permission::firstOrCreate(['name' => 'edit sub kegiatan']);
        $deleteSubKegiatanPermission = Permission::firstOrCreate(['name' => 'delete sub kegiatan']);
        $viewSubKegiatanPermission = Permission::firstOrCreate(['name' => 'view sub kegiatan']);
        $rsa->givePermissionTo($mpd);

        // Memberikan Permissions ke Roles
        $rsa->givePermissionTo([
            // $createProgramPermission,
            // $editProgramPermission,
            // $deleteProgramPermission,
            $viewProgramPermission,
            // $createKegiatanPermission,
            // $editKegiatanPermission,
            // $deleteKegiatanPermission,
            $viewKegiatanPermission,
            // $createSubKegiatanPermission,
            // $editSubKegiatanPermission,
            // $deleteSubKegiatanPermission,
            $viewSubKegiatanPermission
        ]);

        $ra->givePermissionTo([
            $createProgramPermission,
            $editProgramPermission,
            $deleteProgramPermission,
            $viewProgramPermission,
            $createKegiatanPermission,
            $editKegiatanPermission,
            $deleteKegiatanPermission,
            $viewKegiatanPermission,
            $createSubKegiatanPermission,
            $editSubKegiatanPermission,
            $deleteSubKegiatanPermission,
            $viewSubKegiatanPermission
        ]);

        $rop->givePermissionTo([
            $viewProgramPermission,
            $viewKegiatanPermission,
            $viewSubKegiatanPermission
        ]);

        $rpd->givePermissionTo([
            $viewProgramPermission,
            $viewKegiatanPermission,
            $viewSubKegiatanPermission
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
            'name' => 'Bappeda',
            'email' => 'Bappeda@gmail.com',
        ]);
        $user3->assignRole($rop);

        $user4 = User::factory()->create([
            'email' => 'dinas@gmail.com',
            'name' => 'Dinas',
        ]);
        $user4->assignRole($rpd);
    }
}
