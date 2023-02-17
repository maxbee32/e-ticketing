<?php

namespace Database\Seeders;

use App\Models\Manager;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        //
        Permission::create(['name' => 'create-system-users']);
        Permission::create(['name' => 'edit-system-users']);
        Permission::create(['name' => 'delete-system-users']);
        Permission::create(['name' => 'delete-system-users']);
        Permission::create(['name' => 'menu-access']);
        Permission::create(['name' => 'menu-create']);
        Permission::create(['name' => 'menu-show']);
        Permission::create(['name' => 'menu-edit']);
        Permission::create(['name' => 'menu-delete']);
        Permission::create(['name' => 'gate-ticket-generate']);
        Permission::create(['name' => 'restaurant-ticket-generate']);
        Permission::create(['name' => 'view-gate-ticket']);
        Permission::create(['name' => 'view-restaurant-ticket']);

        $entranceManagerRole = Role::create(['name'=> 'Enterance Admin']);
        $restaurantManagerRole = Role::create(['name' => 'Restaurant Admin']);
        $AdminRole = Role::create(['name'=>'Super Admin']);


        $entranceManagerRole->givePermissionTo([
            'view-gate-ticket',
            'gate-ticket-generate'
        ]);


        $restaurantManagerRole->givePermissionTo([
            'restaurant-ticket-generate',
            'view-restaurant-ticket',

        ]);

        $AdminRole->givePermissionTo([Permission::all()]);


       // $user->assignRole($entranceManagerRole);


    }
}
