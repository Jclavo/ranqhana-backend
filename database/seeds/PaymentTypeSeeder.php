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
        factory(PaymentType::class)->create(['name' => 'Debit']);
        factory(PaymentType::class)->create(['name' => 'Credit']);
    }
}
