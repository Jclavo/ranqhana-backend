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
        // $this->call([
        //     RanqhanaUsersSeeder::class,
        //     ItemsTableSeeder::class,
        //     LocaleSeeder::class,
        // ]);

        $this->call([
            ItemTypeSeeder::class,
            StockTypeSeeder::class,
            OrderStageSeeder::class,
            InvoiceTypeSeeder::class,
            InvoiceStageSeeder::class,
            PaymentStageSeeder::class,
            PaymentTypeSeeder::class,
            PaymentMethodSeeder::class,
            UnitTableSeeder::class,
        ]);
    }
}
