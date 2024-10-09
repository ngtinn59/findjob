<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanysizeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companysizes = [
            ['name' => '1-10'],
            ['name' => '11-50'],
            ['name' => '51-100'],
            ['name' => '101-200'],
            ['name' => '201-500'],
            ['name' => '501-1000'],
            ['name' => '1000+'],
        ];

        DB::table('company_sizes')->insert($companysizes);
    }
}
