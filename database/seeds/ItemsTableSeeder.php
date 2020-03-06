<?php

use Illuminate\Database\Seeder;
use App\Item;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Item::class, 300)->create(['store_id' => '1']);
        factory(Item::class, 200)->create(['store_id' => '2']);
    }
}
