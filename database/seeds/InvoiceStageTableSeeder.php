<?php

use App\Models\InvoiceStage;

use Illuminate\Database\Seeder;

class InvoiceStageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(InvoiceStage::class)->create(['code' => 'P', 'description' => 'Paid']);
        factory(InvoiceStage::class)->create(['code' => 'A', 'description' => 'Anulled']);
        factory(InvoiceStage::class)->create(['code' => 'D', 'description' => 'Draft']);
        factory(InvoiceStage::class)->create(['code' => 'I', 'description' => 'By installment']);
        factory(InvoiceStage::class)->create(['code' => 'U', 'description' => 'Stock Updated']);
    }
}
