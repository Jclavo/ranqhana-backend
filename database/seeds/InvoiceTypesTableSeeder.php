<?php

use Illuminate\Database\Seeder;
use App\InvoiceTypes;

class InvoiceTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(InvoiceTypes::class)->create(['code' => 'S','description' => 'Sell Invoice']);
        factory(InvoiceTypes::class)->create(['code' => 'P','description' => 'Purcharse Invoice']);
    }
}
