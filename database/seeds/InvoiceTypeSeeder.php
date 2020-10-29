<?php

use Illuminate\Database\Seeder;
use App\Models\InvoiceType;
use App\Utils\TranslationUtils;

class InvoiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            (object) array('code' => 1, 'name' => 'Sell Invoice',
                            'translations' => [
                                    (object) array('value' => 'Factura de venta', 'locale' => 'es'),
                                    (object) array('value' => 'Fatura de venda', 'locale' => 'pt')]
                            ),
            (object) array('code' => 2, 'name' => 'Purcharse Invoice',
                                    'translations' => [
                                    (object) array('value' => 'Factura de compra', 'locale' => 'es'),
                                    (object) array('value' => 'Fatura de compra', 'locale' => 'pt')]
                            ),
        ];

        TranslationUtils::customUpdateOrCreate($types,InvoiceType::class);
                       
    }


}
