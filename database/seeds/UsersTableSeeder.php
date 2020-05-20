<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert([
            'username' => 'admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('1234567890'),
            'role_id' => '1' //Admin
        ]);
        DB::table('users')->insert([
            'username' => 'user',
            'email' => 'user@mail.com',
            'password' => Hash::make('1234567890'),
            'role_id' => '2' //User
        ]);
    }
}
