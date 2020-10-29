<?php

use Illuminate\Database\Seeder;
use App\Models\ItemType;
use App\Utils\TranslationUtils;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $types = [
            (object) array('code' => 1, 'name' => 'Product',
                            'translations' => [
                                    (object) array('value' => 'Producto', 'locale' => 'es'),
                                    (object) array('value' => 'Produto', 'locale' => 'pt')]
                            ),
            (object) array('code' => 2, 'name' => 'Service',
            'translations' => [
                    (object) array('value' => 'Servicio', 'locale' => 'es'),
                    (object) array('value' => 'Serviço', 'locale' => 'pt')]
            ),
        ];

        TranslationUtils::customUpdateOrCreate($types,ItemType::class);
    }
}
