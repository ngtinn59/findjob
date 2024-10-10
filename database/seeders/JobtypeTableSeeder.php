<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobtypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobtype = [
            ['name' => 'Remote'],
            ['name' => 'At office'],
            ['name' => 'Hybrid'],
        ];

        DB::table('job_types')->insert($jobtype);
    }
}
