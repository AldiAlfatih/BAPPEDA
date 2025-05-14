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

        // Permissions khusus untuk perangkat daerah (select dropdown)
        // $mpd = Permission::firstOrCreate(['name' => 'mengelola_perangkat_daerah', 'guard_name' => 'web']);
        $selectNomenklatur = Permission::firstOrCreate(['name' => 'select nomenklatur','guard_name' => 'web']);
        $addNomenklatur = Permission::firstOrCreate(['name' => 'add nomenklatur', 'guard_name' => 'web']);
        $editNomenklatur = Permission::firstOrCreate(['name' => 'edit nomenklatur', 'guard_name' => 'web']);
        $deleteNomenklatur = Permission::firstOrCreate(['name' => 'destroy nomenklatur', 'guard_name' => 'web']);
        $viewNomenklatur = Permission::firstOrCreate(['name' => 'view nomenklatur', 'guard_name' => 'web']);

        $addBantuan = Permission::firstOrCreate(['name' => 'add bantuan', 'guard_name' => 'web']);
        $editBantuan = Permission::firstOrCreate(['name' => 'edit bantuan', 'guard_name' => 'web']);
        $deleteBantuan = Permission::firstOrCreate(['name' => 'delete bantuan', 'guard_name' => 'web']);
        $viewBantuan = Permission::firstOrCreate(['name' => 'view bantuan', 'guard_name' => 'web']);

        $addPemberitahuan = Permission::firstOrCreate(['name' => 'add pemberitahuan', 'guard_name' => 'web']);
        $editPemberitahuan = Permission::firstOrCreate(['name' => 'edit pemberitahuan', 'guard_name' => 'web']);
        $deletePemberitahuan = Permission::firstOrCreate(['name' => 'delete pemberitahuan', 'guard_name' => 'web']);
        $viewPemberitahuan = Permission::firstOrCreate(['name' => 'view pemberitahuan', 'guard_name' => 'web']);

        $viewAkunadminPDoperator = Permission::firstOrCreate(['name' => 'add akun admin, pd, & operator', 'guard_name' => 'web']);
        $addAkunPDoperator = Permission::firstOrCreate(['name' => 'add akun pd & operator', 'guard_name' => 'web']);
        $editAkunPDoperator = Permission::firstOrCreate(['name' => 'edit akun pd & operator', 'guard_name' => 'web']);
        $deleteAkunPDoperator = Permission::firstOrCreate(['name' => 'delete akun pd & operstor', 'guard_name' => 'web']);

        $addPemberitahuan = Permission::firstOrCreate(['name' => 'add pemberitahuan', 'guard_name' => 'web']);
        $editPemberitahuan = Permission::firstOrCreate(['name' => 'edit pemberitahuan', 'guard_name' => 'web']);
        $deletePemberitahuan = Permission::firstOrCreate(['name' => 'delete pemberitahuan', 'guard_name' => 'web']);
        $viewPemberitahuan = Permission::firstOrCreate(['name' => 'view pemberitahuan', 'guard_name' => 'web']);
        // Assign permission ke role super_admin
        $rsa->givePermissionTo([

        ]);

        // Assign permission ke role admin
        $ra->givePermissionTo([
            $addNomenklatur,
            $editNomenklatur,
            $deleteNomenklatur,
            $viewNomenklatur,
            $addBantuan,
            $editBantuan,
            $deleteBantuan,
            $viewBantuan,
            $viewPemberitahuan,
            $addPemberitahuan,
            $editPemberitahuan,
            $deletePemberitahuan,
            $viewAkunadminPDoperator,
            $addAkunPDoperator,
            $editAkunPDoperator,
            $deleteAkunPDoperator,
        ]);

        // Assign permission ke role operator
        $rop->givePermissionTo([
            $viewNomenklatur,
            $viewBantuan,
            $viewPemberitahuan,
            $viewAkunadminPDoperator,
        ]);

        // Assign permission ke role perangkat daerah (hanya bisa memilih via dropdown)
        $rpd->givePermissionTo([
            $selectNomenklatur,
            $viewNomenklatur,
            $viewBantuan,
            $viewPemberitahuan,
            $viewAkunadminPDoperator,
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

        $roles = $user->getRoleNames();
    }
}
