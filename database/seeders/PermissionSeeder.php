<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(
            ['name' => 'create_barang'],
        );
        Permission::create(
            ['name' => 'read_barang'],
        );
        Permission::create(
            ['name' => 'edit_barang'],
        );
        Permission::create(
            ['name' => 'update_barang'],
        );
        Permission::create(
            ['name' => 'delete_barang'],
        );

        Permission::create(
            ['name' => 'create_user'],
        );
        Permission::create(
            ['name' => 'read_user'],
        );
        Permission::create(
            ['name' => 'edit_user'],
        );
        Permission::create(
            ['name' => 'update_user'],
        );
        Permission::create(
            ['name' => 'delete_user'],
        );

        Permission::create(
            ['name' => 'read_log'],
        );

    }
}
