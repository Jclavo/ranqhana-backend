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
        ItemType::updateOrCreate(['code' => 1],['name' => 'Product']); 
        ItemType::updateOrCreate(['code' => 2],['name' => 'Service']); 
    }
}
