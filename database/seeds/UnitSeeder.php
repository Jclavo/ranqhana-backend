<?php

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Utils\TranslationUtils;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $units = [
            (object) array('code' => 1, 'abbreviation' => 'KG', 'name' => 'Kilograms', 'fractioned' => true,
                            'translations' => [
                                    (object) array('abbreviation' => 'KG', 'name' => 'Kilogramos', 'locale' => 'es'),
                                    (object) array('abbreviation' => 'KG', 'name' => 'Kilogramos',  'locale' => 'pt')]
                            ),
            (object) array('code' => 2, 'abbreviation' => 'BX', 'name' => 'Boxes', 'fractioned' => false,
                            'translations' => [
                                (object) array('abbreviation' => 'CJ', 'name' => 'Cajas', 'locale' => 'es'),
                                (object) array('abbreviation' => 'CX', 'name' => 'Caixas',  'locale' => 'pt')]
                            ),
            (object) array('code' => 3, 'abbreviation' => 'BT', 'name' => 'Bottles', 'fractioned' => false,
                            'translations' => [
                                (object) array('abbreviation' => 'BT', 'name' => 'Botella', 'locale' => 'es'),
                                (object) array('abbreviation' => 'GF', 'name' => 'Garrafas',  'locale' => 'pt')]
                            ),
            (object) array('code' => 4, 'abbreviation' => 'UN', 'name' => 'Unit', 'fractioned' => false,
                            'translations' => [
                                (object) array('abbreviation' => 'UN', 'name' => 'Unidades', 'locale' => 'es'),
                                (object) array('abbreviation' => 'UN', 'name' => 'Unidades',  'locale' => 'pt')]
                            ),
            (object) array('code' => 5, 'abbreviation' => 'BG', 'name' => 'Bag', 'fractioned' => false,
                            'translations' => [
                                (object) array('abbreviation' => 'BL', 'name' => 'Bolsas', 'locale' => 'es'),
                                (object) array('abbreviation' => 'SC', 'name' => 'Sacolas',  'locale' => 'pt')]
                            ),
            (object) array('code' => 6, 'abbreviation' => 'PK', 'name' => 'Package', 'fractioned' => false,
                            'translations' => [
                                (object) array('abbreviation' => 'PQ', 'name' => 'Paquetes', 'locale' => 'es'),
                                (object) array('abbreviation' => 'PC', 'name' => 'Pacotes',  'locale' => 'pt')]
                            ),
        ];

        foreach ($units as $unit) {

            $newUnit = Unit::updateOrCreate(['code' => $unit->code, 'abbreviation' => $unit->abbreviation],
                                            ['name' => $unit->name, 'fractioned' => $unit->fractioned]);

            foreach ($unit->translations as $translation) {
                
                TranslationUtils::updateOrCreateTranslation($newUnit->code, $translation->abbreviation, $translation->locale,
                                                            Unit::class, 'abbreviation');

                TranslationUtils::updateOrCreateTranslation($newUnit->code, $translation->name, $translation->locale,
                Unit::class, 'name');
            }
        }

    }
}
