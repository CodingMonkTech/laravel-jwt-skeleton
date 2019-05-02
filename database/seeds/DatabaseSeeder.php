<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $permissions=[
           [
               'name' => 'role-read',
               'display_name' => 'Display Role Listing',
               'description' => 'See only Listing Of Role'
           ],
           [
               'name' => 'role-create',
               'display_name' => 'Create Role',
               'description' => 'Create New Role'
           ],
           [
               'name' => 'role-edit',
               'display_name' => 'Edit Role',
               'description' => 'Edit Role'
           ],
           [
               'name' => 'role-delete',
               'display_name' => 'Delete Role',
               'description' => 'Delete Role'
           ]
       ];

       $roles=[
           
           [
               'name' => 'superadmin',
               'display_name' => 'Super Admin',
               'description' => 'Can Do everything which is configurable.'
           ],
           [
               'name' => 'admin',
               'display_name' => 'admin',
               'description' => 'Can handle administrative tasks'
           ]
       ];


        $user=[
           
               'name' => 'Admin',
               'email' => 'admin@admin.com',
               'password' => bcrypt('123456')     
       	];
       	
       	foreach ($permissions as $key=>$value){
        	Permission::create($value);
      	}

       	foreach ($roles as $key=>$value){
        	Role::create($value);
      	}

       	$user=User::create($user);
       	$user->roles()->sync(1);
    }
}
