<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Item::create([
            'name' => 'Bota de Bronce',
            'item_type_id' => 1,
            'attack' => 12,
            'defense' => 6
        ]);
        Item::create([
            'name' => 'Armadura de Bronce',
            'item_type_id' => 2,
            'attack' => 10,
            'defense' => 25
        ]);
        Item::create([
            'name' => 'Espada de Bronce',
            'item_type_id' => 3,
            'attack' => 20,
            'defense' => 15
        ]);
    }
}
