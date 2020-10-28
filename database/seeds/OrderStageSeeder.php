<?php

use Illuminate\Database\Seeder;
use App\Models\OrderStage;

class OrderStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrderStage::updateOrCreate(['code' => 1],['name' => 'New']); 
        OrderStage::updateOrCreate(['code' => 2],['name' => 'Requested']); 
        OrderStage::updateOrCreate(['code' => 3],['name' => 'Accepted']); 
        OrderStage::updateOrCreate(['code' => 4],['name' => 'Preparing']); 
        OrderStage::updateOrCreate(['code' => 5],['name' => 'Wrapped']); 
        OrderStage::updateOrCreate(['code' => 6],['name' => 'Ready']); 
        OrderStage::updateOrCreate(['code' => 7],['name' => 'Shipped']); 
        OrderStage::updateOrCreate(['code' => 8],['name' => 'Delivered']); 
        OrderStage::updateOrCreate(['code' => 9],['name' => 'Canceled']); 
        OrderStage::updateOrCreate(['code' => 10],['name' => 'Automatic']); 
    }
}
