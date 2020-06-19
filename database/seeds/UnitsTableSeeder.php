<?php

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Unit::class)->create(['code' => 'KG','description' => 'Kilograms']);
        factory(Unit::class)->create(['code' => 'BX','description' => 'Boxes']);
        factory(Unit::class)->create(['code' => 'BT','description' => 'Bottles']);
        factory(Unit::class)->create(['code' => 'UN','description' => 'Unit']);
    }
}
