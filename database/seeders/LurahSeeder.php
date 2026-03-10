<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class LurahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Role
        $roleLurah = Role::firstOrCreate(['name' => 'lurah']);

        // Permissions equivalent to admin_kelurahan
        $permissions = [
            'create_permohonan',
            'view_permohonan',
            'approve_permohonan_kelurahan',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign Permissions to Role
        $roleLurah->givePermissionTo($permissions);

        // Create User Lurah
        $lurahUser = User::firstOrCreate(
            ['email' => 'lurah.guntungmanggis@example.com'],
            [
                'name' => 'Lurah Guntung Manggis',
                'password' => Hash::make('password'),
                'kelurahan_id' => 6372011004,
                'kecamatan_id' => 6372011,
                'kabupaten_id' => 6372,
            ]
        );
        $lurahUser->assignRole($roleLurah);

        $this->command->info('Role Lurah and User seeded successfully!');
    }
}
