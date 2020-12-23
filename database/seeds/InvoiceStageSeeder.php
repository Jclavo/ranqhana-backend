<?php

use App\Models\InvoiceStage;

use Illuminate\Database\Seeder;
use App\Utils\TranslationUtils;

class InvoiceStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $array = [
        //     (object) array('code' => , 'name' => '',
        //                     'translations' => [
        //                             (object) array('value' => '', 'locale' => 'es'),
        //                             (object) array('value' => '', 'locale' => 'pt')]
        //                     ),
        // ];
        $stages = [
                (object) array('code' => 1, 'name' => 'Paid',
                                'translations' => [
                                        (object) array('value' => 'Pagado', 'locale' => 'es'),
                                        (object) array('value' => 'Pago', 'locale' => 'pt')]
                                ),
                                (object) array('code' => 2, 'name' => 'Annulled',
                                'translations' => [
                                        (object) array('value' => 'Anulado', 'locale' => 'es'),
                                        (object) array('value' => 'Cancelada', 'locale' => 'pt')]
                                ),
                                (object) array('code' => 3, 'name' => 'Draft',
                                'translations' => [
                                        (object) array('value' => 'Borrador', 'locale' => 'es'),
                                        (object) array('value' => 'Rascunho', 'locale' => 'pt')]
                                ),
                                (object) array('code' => 4, 'name' => 'By installment',
                                'translations' => [
                                        (object) array('value' => 'Por cuotas', 'locale' => 'es'),
                                        (object) array('value' => 'Parcelado', 'locale' => 'pt')]
                                ),
                                (object) array('code' => 5, 'name' => 'Waiting for payment',
                                'translations' => [
                                        (object) array('value' => 'Esperando pago', 'locale' => 'es'),
                                        (object) array('value' => 'Aguardando pagamento', 'locale' => 'pt')]
                                ),
            ];

            TranslationUtils::customUpdateOrCreate($stages,InvoiceStage::class);
            
    }
}
