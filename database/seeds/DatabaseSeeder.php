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
        $this->call([
            // InvoiceStagesTableSeeder::class,
            UnitsTableSeeder::class,
            RanqhanaUsersSeeder::class,
            ItemsTableSeeder::class,
            InvoiceTypesTableSeeder::class,
            InvoiceStagesTableSeeder::class,
        ]);
    }
}
