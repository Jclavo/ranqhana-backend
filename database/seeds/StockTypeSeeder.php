<?php

use Illuminate\Database\Seeder;
use App\Models\StockType;

class StockTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StockType::updateOrCreate(['code' => 1],['name' => 'Sale']); 
        StockType::updateOrCreate(['code' => 2],['name' => 'Purchase']); 
        StockType::updateOrCreate(['code' => 3],['name' => 'Production']); 
    }
}
