<?php

use App\Models\InvoiceStage;

use Illuminate\Database\Seeder;

class InvoiceStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InvoiceStage::updateOrCreate(['code' => 1],['name' => 'Paid']);
        InvoiceStage::updateOrCreate(['code' => 2],['name' => 'Annulled']); 
        InvoiceStage::updateOrCreate(['code' => 3],['name' => 'Draft']); 
        InvoiceStage::updateOrCreate(['code' => 4],['name' => 'By installment']); 
        InvoiceStage::updateOrCreate(['code' => 5],['name' => 'Stock Updated']); 
    }
}
