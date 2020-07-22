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
        factory(StockType::class)->create(['name' => 'Sale']);
        factory(StockType::class)->create(['name' => 'Purchase']);
        factory(StockType::class)->create(['name' => 'Production']);
    }
}
