<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AssignPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::findByName('admin');
        $admin->givePermissionTo('create_barang', 'read_barang', 'edit_barang', 'update_barang', 'delete_barang', 'read_log');
        $admin->givePermissionTo('create_user', 'read_user', 'edit_user', 'update_user', 'delete_user');

        $staff = Role::findByName('staff');
        $staff->givePermissionTo('create_barang', 'read_barang', 'edit_barang', 'update_barang');


    }
}
