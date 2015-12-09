<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\User;
use App\UserDevice;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call(UserTableSeeder::class);

        $this->call(UserTableSeeder::class);
        $this->command->info( " User Table Seeded! " );

        Model::reguard();
    }
}

class UserTableSeeder extends Seeder {

    public function run() {
        User::create( [
            'name' => 'Super Admin',
            'type' => 'mylan_admin',
            'email' => 'admin@mylan.com',
            'account_status' => 'active',
            'password' => Hash::make( 'admin' )
        ] ); 
        
                
        
    }

}
