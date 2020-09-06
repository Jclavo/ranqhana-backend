<?php

use Illuminate\Database\Seeder;
use App\Models\Locale;

class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Locale::class)->create(['code' => 'en']);
        factory(Locale::class)->create(['code' => 'pt']);
        factory(Locale::class)->create(['code' => 'es']);
    }
}
