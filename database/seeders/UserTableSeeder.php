<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Reset the admins table
         */
        if (\App::environment('local')) {
            \DB::table('users')->truncate();
        }

        \DB::table('users')->insert([
            'first_name'      => 'Jon',
            'last_name'       => 'Doe',
            'phone'           => '123456789',
            'otp'           => '1234',
            'device_id'           => '0',
            'email'           => 'jon@yopmail.com',
            'password'        => bcrypt('@dmin123'),
            'is_active'       => 1,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s')
        ]);
    }
}
