<?php

use App\Country;

use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('countries')->insert(
        //     [
        //         'id' => 1,
        //         'name' => 'Brazil',
        //     ]);
        factory(Country::class)->create(['country_code' => '55','name' => 'Brazil']);
        factory(Country::class)->create(['country_code' => '51','name' => 'Peru']);
    }
}
