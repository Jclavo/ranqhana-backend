<?php

use Illuminate\Database\Seeder;
use App\Unit;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Unit::class)->create(['code' => 'KG','description' => 'Kilograms','store_id' => 0]);
        factory(Unit::class)->create(['code' => 'BX','description' => 'Boxes','store_id' => 0]);
        factory(Unit::class)->create(['code' => 'BT','description' => 'Bottles','store_id' => 0]);
    }
}
