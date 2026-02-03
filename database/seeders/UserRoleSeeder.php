<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Roles
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $roleAdminKabupaten = Role::firstOrCreate(['name' => 'admin_kabupaten']);
        $roleAdminKecamatan = Role::firstOrCreate(['name' => 'admin_kecamatan']);
        $roleAdminKelurahan = Role::firstOrCreate(['name' => 'admin_kelurahan']);

        // 2. Create Permissions
        $permissions = [
            'manage_users',
            'manage_master_data',
            'manage_approval_flows',
            'create_permohonan',
            'view_permohonan',
            'approve_permohonan_kelurahan',
            'approve_permohonan_kecamatan',
            'approve_permohonan_kabupaten',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Assign Permissions to Roles
        $roleSuperAdmin->givePermissionTo(Permission::all());

        $roleAdminKabupaten->givePermissionTo([
            'view_permohonan',
            'approve_permohonan_kabupaten',
        ]);

        $roleAdminKecamatan->givePermissionTo([
            'view_permohonan',
            'approve_permohonan_kecamatan',
        ]);

        $roleAdminKelurahan->givePermissionTo([
            'create_permohonan',
            'view_permohonan',
            'approve_permohonan_kelurahan',
        ]);

        // 4. Create Users

        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
        ]);
        $superAdmin->assignRole($roleSuperAdmin);

        // Admin Kecamatan (Landasan Ulin - 2709)
        $adminKecamatan = User::create([
            'name' => 'Admin Kecamatan Landasan Ulin',
            'email' => 'admin.landasanulin@example.com',
            'password' => Hash::make('password'),
            'kecamatan_id' => 6372011,
            // Perlu juga set kabupaten_id agar konsisten? 
            // Dari data JSON: Landasan Ulin (2709) -> City 176 (Banjar Baru). 
            // Mari kita set kabupaten_id juga jika memungkinkan, tapi tabel users nullable.
            // Untuk saat ini cukup kecamatan_id sesuai request user.
            'kabupaten_id' => 6372,
        ]);
        $adminKecamatan->assignRole($roleAdminKecamatan);

        // Admin Kelurahan (Guntung Manggis - 31518)
        $adminKelurahan = User::create([
            'name' => 'Admin Kelurahan Guntung Manggis',
            'email' => 'admin.guntungmanggis@example.com',
            'password' => Hash::make('password'),
            'kelurahan_id' => 6372011004,
            'kecamatan_id' => 6372011,
            'kabupaten_id' => 6372,
        ]);
        $adminKelurahan->assignRole($roleAdminKelurahan);

        $this->command->info('Roles, Permissions, and Admin Users seeded successfully!');
    }
}
