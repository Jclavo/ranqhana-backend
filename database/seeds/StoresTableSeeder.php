<?php

use Illuminate\Database\Seeder;

use App\Store;

class StoresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Store::class)->create(['id' => '1', 'name' => 'Store Chepen']);
        factory(Store::class)->create(['name' => 'Store Guadalupe']);
    }
}
