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
        factory(Unit::class)->create(['code' => 'KG','description' => 'Kilograms', 'fractioned' => true]);
        factory(Unit::class)->create(['code' => 'BX','description' => 'Boxes', 'fractioned' => false]);
        factory(Unit::class)->create(['code' => 'BT','description' => 'Bottles', 'fractioned' => false]);
        factory(Unit::class)->create(['code' => 'UN','description' => 'Unit', 'fractioned' => false]);
        factory(Unit::class)->create(['code' => 'BG','description' => 'Bag', 'fractioned' => false]);
        factory(Unit::class)->create(['code' => 'PK','description' => 'Package', 'fractioned' => false]);
    }
}
