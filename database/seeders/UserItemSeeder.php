<?php

namespace Database\Seeders;

use App\Models\UserItem;
use Illuminate\Database\Seeder;

class UserItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserItem::create([
            'user_id' => 2,
            'item_id' => 1
        ]);
        UserItem::create([
            'user_id' => 2,
            'item_id' => 2
        ]);
        UserItem::create([
            'user_id' => 2,
            'item_id' => 3
        ]);
    }
}
