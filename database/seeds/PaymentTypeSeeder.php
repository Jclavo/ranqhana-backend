<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentType::updateOrCreate(['code' => 1],['name' => 'Debit']); 
        PaymentType::updateOrCreate(['code' => 2],['name' => 'Credit']); 
    }
}
