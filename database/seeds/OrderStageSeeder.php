<?php

use Illuminate\Database\Seeder;
use App\Models\OrderStage;
use App\Utils\TranslationUtils;

class OrderStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stages = [
            (object) array('code' => 1, 'name' => 'New',
                            'translations' => [
                                    (object) array('value' => 'Nuevo', 'locale' => 'es'),
                                    (object) array('value' => 'Novo', 'locale' => 'pt')]
                            ),
            (object) array('code' => 2, 'name' => 'Requested',
                            'translations' => [
                                    (object) array('value' => 'Solicitado', 'locale' => 'es'),
                                    (object) array('value' => 'Solicitado', 'locale' => 'pt')]
                            ),
            (object) array('code' => 3, 'name' => 'Accepted',
                            'translations' => [
                                    (object) array('value' => 'Aceptado', 'locale' => 'es'),
                                    (object) array('value' => 'Aceitado', 'locale' => 'pt')]
                            ),
            (object) array('code' => 4, 'name' => 'Preparing',
                            'translations' => [
                                    (object) array('value' => 'Preparando', 'locale' => 'es'),
                                    (object) array('value' => 'Preparando', 'locale' => 'pt')]
                            ),
            (object) array('code' => 5, 'name' => 'Wrapped',
                            'translations' => [
                                    (object) array('value' => 'Empaquetando', 'locale' => 'es'),
                                    (object) array('value' => 'Empacotando', 'locale' => 'pt')]
                            ),
            (object) array('code' => 6, 'name' => 'Ready',
                            'translations' => [
                                    (object) array('value' => 'Listo', 'locale' => 'es'),
                                    (object) array('value' => 'Pronto', 'locale' => 'pt')]
            ),
            (object) array('code' => 7, 'name' => 'Shipped',
                            'translations' => [
                                    (object) array('value' => 'Enviado', 'locale' => 'es'),
                                    (object) array('value' => 'Enviado', 'locale' => 'pt')]
            ),
            (object) array('code' => 8, 'name' => 'Delivered',
                            'translations' => [
                                    (object) array('value' => 'Entregado', 'locale' => 'es'),
                                    (object) array('value' => 'Entregado', 'locale' => 'pt')]
            ),
            (object) array('code' => 9, 'name' => 'Canceled',
                            'translations' => [
                                    (object) array('value' => 'Cancelado', 'locale' => 'es'),
                                    (object) array('value' => 'Cancelado', 'locale' => 'pt')]
            ),
            (object) array('code' => 10, 'name' => 'Automatic',
                            'translations' => [
                                    (object) array('value' => 'Automático', 'locale' => 'es'),
                                    (object) array('value' => 'Automático', 'locale' => 'pt')]
            ),
        ];

        TranslationUtils::customUpdateOrCreate($stages,OrderStage::class);
    }
}
