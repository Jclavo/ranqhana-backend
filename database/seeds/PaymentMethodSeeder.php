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
        factory(PaymentMethod::class)->create(['name' => 'Money']);
        factory(PaymentMethod::class)->create(['name' => 'Card']);
    }
}
