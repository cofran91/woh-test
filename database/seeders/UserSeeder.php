<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@woh.com',
            'password' => 123456,
            'rol_id' => 1
        ]);
        User::create([
            'name' => 'Client One',
            'email' => 'client_one@woh.com',
            'password' => 123456,
        ]);
        User::create([
            'name' => 'Client Two',
            'email' => 'client_two@woh.com',
            'password' => 123456,
        ]);
    }
}
