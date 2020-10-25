<?php

use Illuminate\Database\Seeder;
use App\Models\InvoiceType;

class InvoiceTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(InvoiceType::class)->create(['code' => 'S','description' => 'Sell Invoice']);
        factory(InvoiceType::class)->create(['code' => 'P','description' => 'Purcharse Invoice']);
    }
}
