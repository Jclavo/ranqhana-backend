<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentMethod::updateOrCreate(['code' => 1],['name' => 'Money']); 
        PaymentMethod::updateOrCreate(['code' => 2],['name' => 'Card']); 
    }
}
