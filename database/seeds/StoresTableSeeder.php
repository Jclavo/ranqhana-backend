<?php

use Illuminate\Database\Seeder;

use App\Models\Store;

class StoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Store::class)->create(['name' => 'Store Chepen', 'country_id' => 1]);
        factory(Store::class)->create(['name' => 'Store Guadalupe', 'country_id' => 1]);
    }
}
