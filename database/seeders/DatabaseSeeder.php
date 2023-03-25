<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ItemTypeSeeder::class);
        $this->call(UserTypeSeeder::class);
        $this->call(RolSeeder::class);
        $this->call(AttackTypeSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ItemSeeder::class);
        $this->call(UserItemSeeder::class);
    }
}
