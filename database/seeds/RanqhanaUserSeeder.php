<?php

use Illuminate\Database\Seeder;
use App\Models\RanqhanaUser;

class RanqhanaUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(RanqhanaUser::class)->create(['external_user_id' => 2, 'company_project_id' => 2]);
    }
}
