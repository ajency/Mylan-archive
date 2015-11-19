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
            'reference_code' => '12345678',
            'reference_number' => Hash::make( '123456' ),
            'password' => Crypt::encrypt('1234'),
            'account_status' => 'active',
            'hospital_id' => '',
            'project_id' => ''
        ] ); 
        
        UserDevice::create( [
            'user_id' => '1',
            'device_type' => 'mobile',
            'device_identifier' => '123456',
            'device_os' => 'ios',
            'access_type' => 'app'

        ]); 
        
        
    }

}
