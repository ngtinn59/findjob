<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanytypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $company_types = [
            ['name' => 'IT Outsourcing'],
            ['name' => 'IT Product'],
            ['name' => 'Headhunt'],
           ['name' => 'IT Service and IT Consulting'],
           ['name' => 'Non-IT'],
       ];

        DB::table('company_types')->insert($company_types);
    }
}
