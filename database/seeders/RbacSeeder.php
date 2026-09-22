<?php

namespace Database\Seeders;

use App\Models\Permission;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'create-post',
            'update-own-post',
            'delete-own-post',
            'update-any-post',
            'delete-any-post',

            'create-category',
            'update-category',
            'delete-category',

            'create-role',
            'update-role',
            'delete-role',
            'assign-role',

            'manage-users',
        ];

        foreach($permissions as $permission){
            Permission::firstOrCreate(['name' => $permission]);
        }

    }
}
