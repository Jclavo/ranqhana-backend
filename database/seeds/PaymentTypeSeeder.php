<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentType;
use App\Utils\TranslationUtils;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            (object) array('code' => 1, 'name' => 'Debit',
                            'translations' => [
                                    (object) array('value' => 'Debito', 'locale' => 'es'),
                                    (object) array('value' => 'Debito', 'locale' => 'pt')]
                            ),
            (object) array('code' => 2, 'name' => 'Credit',
                            'translations' => [
                                    (object) array('value' => 'Credito', 'locale' => 'es'),
                                    (object) array('value' => 'Credito', 'locale' => 'pt')]
                            ),
        ];

        TranslationUtils::customUpdateOrCreate($types,PaymentType::class);
    }
}
