<?php

use App\Models\InvoiceStages;

use Illuminate\Database\Seeder;

class InvoiceStagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(InvoiceStages::class)->create(['code' => 'P', 'description' => 'Paid']);
        factory(InvoiceStages::class)->create(['code' => 'A', 'description' => 'Anulled']);
        factory(InvoiceStages::class)->create(['code' => 'I', 'description' => 'Initial']);
    }
}
