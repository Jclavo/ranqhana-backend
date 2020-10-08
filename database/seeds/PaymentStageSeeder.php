<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentStage;

class PaymentStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // factory(PaymentStage::class)->create(['name' => 'Initial']);
        factory(PaymentStage::class)->create(['name' => 'Waiting']);
        factory(PaymentStage::class)->create(['name' => 'Delayed']);
        factory(PaymentStage::class)->create(['name' => 'Paid']);
        factory(PaymentStage::class)->create(['name' => 'Anulled']);
    }
}
