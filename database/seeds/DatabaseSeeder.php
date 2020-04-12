<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call([
            CountriesTableSeeder::class,
            StoresTableSeeder::class,
            
            UsersTableSeeder::class,
            ItemsTableSeeder::class,
            // UnitsTableSeeder::class,
        ]);
    }
}
