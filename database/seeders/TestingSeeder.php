<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ItemTypeSeeder::class);
        $this->call(UserTypeSeeder::class);
        $this->call(RolSeeder::class);
        $this->call(AttackTypeSeeder::class);
    }
}
