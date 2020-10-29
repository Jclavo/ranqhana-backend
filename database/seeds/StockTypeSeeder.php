<?php

use Illuminate\Database\Seeder;
use App\Models\StockType;
use App\Utils\TranslationUtils;

class StockTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            (object) array('code' => 1, 'name' => 'Sale',
                            'translations' => [
                                    (object) array('value' => 'Venta', 'locale' => 'es'),
                                    (object) array('value' => 'Venda', 'locale' => 'pt')]
                            ),
            (object) array('code' => 2, 'name' => 'Purchase',
                            'translations' => [
                                    (object) array('value' => 'Compra', 'locale' => 'es'),
                                    (object) array('value' => 'Compra', 'locale' => 'pt')]
                            ),
            (object) array('code' => 3, 'name' => 'Production',
                            'translations' => [
                                    (object) array('value' => 'Producción', 'locale' => 'es'),
                                    (object) array('value' => 'Produção', 'locale' => 'pt')]
                            ),
        ];

        TranslationUtils::customUpdateOrCreate($types,StockType::class);
    }
}
