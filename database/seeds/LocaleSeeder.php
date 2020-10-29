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
        Locale::updateOrCreate(['code' => 'en']); 
        Locale::updateOrCreate(['code' => 'pt']); 
        Locale::updateOrCreate(['code' => 'es']); 
    }
}
