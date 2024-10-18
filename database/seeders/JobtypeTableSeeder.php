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
            ['name' => 'Làm từ xa'],
            ['name' => 'Tại văn phòng'],
            ['name' => 'Kết hợp'],
        ];

        DB::table('job_types')->insert($jobtype);
    }

}
