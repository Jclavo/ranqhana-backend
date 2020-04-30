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
        factory(Country::class)->create(['country_code' => '55','name' => 'Brazil', 'timezone' => 'America/Sao_Paulo']);
        factory(Country::class)->create(['country_code' => '51','name' => 'Peru', 'timezone' => 'America/Lima']);
    }
}
