<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'delete_permohonan']);

        $roles = ['super_admin', 'admin_kecamatan', 'admin_kelurahan'];
        foreach ($roles as $roleName) {
            $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($permission);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = \Spatie\Permission\Models\Permission::where('name', 'delete_permohonan')->first();
        if ($permission) {
            $roles = ['super_admin', 'admin_kecamatan', 'admin_kelurahan'];
            foreach ($roles as $roleName) {
                $role = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
                if ($role) {
                    $role->revokePermissionTo($permission);
                }
            }
            $permission->delete();
        }
    }
};
