<?php

namespace Database\Seeders;

use App\Models\AttackType;
use Illuminate\Database\Seeder;

class AttackTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AttackType::create([
            'name' => 'Cuerpo a Cuerpo',
            'damage' => 1
        ]);
        AttackType::create([
            'name' => 'A distancia',
            'damage' => 0.8
        ]);
        AttackType::create([
            'name' => 'Ulti',
            'damage' => 2
        ]);
    }
}
