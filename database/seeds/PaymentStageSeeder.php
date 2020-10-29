<?php

use Illuminate\Database\Seeder;
use App\Models\PaymentStage;
use App\Utils\TranslationUtils;

class PaymentStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stages = [
            (object) array('code' => 1, 'name' => 'Waiting',
                            'translations' => [
                                    (object) array('value' => 'En espera', 'locale' => 'es'),
                                    (object) array('value' => 'Na espera', 'locale' => 'pt')]
                            ),
            (object) array('code' => 2, 'name' => 'Delayed',
                            'translations' => [
                                    (object) array('value' => 'Retrasado', 'locale' => 'es'),
                                    (object) array('value' => 'Retrasado', 'locale' => 'pt')]
                            ),
            (object) array('code' => 3, 'name' => 'Paid',
                            'translations' => [
                                    (object) array('value' => 'Pagado', 'locale' => 'es'),
                                    (object) array('value' => 'Pagado', 'locale' => 'pt')]
                            ),
            (object) array('code' => 4, 'name' => 'Annulled',
                            'translations' => [
                                    (object) array('value' => 'Cancelado', 'locale' => 'es'),
                                    (object) array('value' => 'Cancelado', 'locale' => 'pt')]
                            ),
        ];

        TranslationUtils::customUpdateOrCreate($stages,PaymentStage::class);

    }
}
