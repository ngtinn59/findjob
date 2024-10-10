<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $cities = [
            ['users_id' => 1, 'country_id' => 1, 'name' => 'Hà Nội'],
            ['users_id' => 1, 'country_id' => 1, 'name' => 'TPHCM'],
            ['users_id' => 1, 'country_id' => 1, 'name' => 'Cần Thơ'],
            ['users_id' => 1, 'country_id' => 1, 'name' => 'Others'],

        ];

        DB::table('cities')->insert($cities);
    }
}
