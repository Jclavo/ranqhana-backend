<?php

use Illuminate\Database\Seeder;
use App\Models\ItemType;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(ItemType::class)->create(['name' => 'Product']);
        factory(ItemType::class)->create(['name' => 'Service']);
    }
}
