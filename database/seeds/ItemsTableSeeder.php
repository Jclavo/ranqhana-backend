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
        factory(Item::class, 100)->create();
        factory(Item::class)->create(
            ['name' => 'pastillas', 'price' => 5, 'stock' => 200, 'stocked' => true, 'store_id' => 1 ]
        );
        factory(Item::class)->create(
            ['name' => 'gaseosa', 'price' => 8, 'stock' => 500, 'stocked' => true, 'store_id' => 1 ]
        );
        factory(Item::class)->create(
            ['name' => 'marmita', 'price' => 12, 'stock' => 0, 'stocked' => false, 'store_id' => 1 ]
        );
    }
}
