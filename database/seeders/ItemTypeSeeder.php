<?php

namespace Database\Seeders;

use App\Models\ItemType;
use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ItemType::create([
            'name' => 'Bota'
        ]);
        ItemType::create([
            'name' => 'Armadura'
        ]);
        ItemType::create([
            'name' => 'Arma'
        ]);
    }
}
