<?php

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unit::updateOrCreate(['code' => 'KG'],['description' => 'Kilograms', 'fractioned' => true]);
        Unit::updateOrCreate(['code' => 'BX'],['description' => 'Boxes', 'fractioned' => false]);
        Unit::updateOrCreate(['code' => 'BT'],['description' => 'Bottles', 'fractioned' => false]);
        Unit::updateOrCreate(['code' => 'UN'],['description' => 'Unit', 'fractioned' => false]);
        Unit::updateOrCreate(['code' => 'BG'],['description' => 'Bag', 'fractioned' => false]);
        Unit::updateOrCreate(['code' => 'PK'],['description' => 'Package', 'fractioned' => false]);
    }
}
