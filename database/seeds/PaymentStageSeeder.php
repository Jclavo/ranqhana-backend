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
        PaymentStage::updateOrCreate(['code' => 1],['name' => 'Waiting']); 
        PaymentStage::updateOrCreate(['code' => 2],['name' => 'Delayed']); 
        PaymentStage::updateOrCreate(['code' => 3],['name' => 'Paid']); 
        PaymentStage::updateOrCreate(['code' => 4],['name' => 'Annulled']); 
    }
}
