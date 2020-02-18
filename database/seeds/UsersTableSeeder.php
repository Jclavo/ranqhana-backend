<?php

use Illuminate\Database\Seeder;

use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('users')->insert([
        //     'name' => 'Jose Clavo',
        //     'email' => 'jc@gmail.com',
        //     'password' => bcrypt('123'),
        //     'identification' => '10210210210',
        //     'country_code' => 55,
        // ]);

        factory(User::class)->create([
            'name' => 'Jose Clavo',
            'email' => 'jc@gmail.com',
            'password' => bcrypt('123'),
            'identification' => '10210210210',
            'country_code' => 55,
            'store_id' => 1,
        ]);
    }
}
