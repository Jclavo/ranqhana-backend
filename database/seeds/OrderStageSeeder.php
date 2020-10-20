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
        factory(OrderStage::class)->create(['name' => 'New']);
        factory(OrderStage::class)->create(['name' => 'Requested']);
        factory(OrderStage::class)->create(['name' => 'Accepted']);
        factory(OrderStage::class)->create(['name' => 'Preparing']);
        factory(OrderStage::class)->create(['name' => 'Wrapped']);
        factory(OrderStage::class)->create(['name' => 'Ready']);
        factory(OrderStage::class)->create(['name' => 'Shipped']);
        factory(OrderStage::class)->create(['name' => 'Delivered']);
        factory(OrderStage::class)->create(['name' => 'Canceled']);
        factory(OrderStage::class)->create(['name' => 'Automatic']);
    }
}
