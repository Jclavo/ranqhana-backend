<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Utils\TranslationUtils;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $methods = [
            (object) array('code' => 1, 'name' => 'Money',
                            'translations' => [
                                    (object) array('value' => 'Dinero', 'locale' => 'es'),
                                    (object) array('value' => 'Dinheiro', 'locale' => 'pt')]
                            ),
            (object) array('code' => 2, 'name' => 'Card',
                            'translations' => [
                                    (object) array('value' => 'Tarjeta', 'locale' => 'es'),
                                    (object) array('value' => 'Cartão', 'locale' => 'pt')]
                            ),
        ];

        TranslationUtils::customUpdateOrCreate($methods,PaymentMethod::class);
    }
}
