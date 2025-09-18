<?php

namespace Database\Seeders\Permissions;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class InquiryPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'inquiries.list',
            'inquiries.create', 
            'inquiries.edit',
            'inquiries.delete',
            'inquiries.restore',
            'inquiries.show',
            'inquiries.confirm'
        ];
        
        $permissions_db = [];
        foreach ($permissions as $permission) {
            $permissions_db[] = Permission::updateOrCreate([
                'name' => $permission
            ])->id;
        }

        // Grant to Administrator role
        if ($adminRole = Role::whereName('Administrator')->first()) {
            $adminRole->givePermissionTo($permissions_db);
        }

        // Also grant to Admin role if present
        if ($adminAlt = Role::whereName('Admin')->first()) {
            $adminAlt->givePermissionTo($permissions_db);
        }
    }
}





