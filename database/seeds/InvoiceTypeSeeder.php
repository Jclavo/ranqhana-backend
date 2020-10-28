<?php

use Illuminate\Database\Seeder;
use App\Models\InvoiceType;

class InvoiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InvoiceType::updateOrCreate(['code' => 1],['name' => 'Sell']);
        InvoiceType::updateOrCreate(['code' => 2],['name' => 'Purcharse']); 
    }
}
