<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExperienceLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Dữ liệu cho bảng experience_levels
        $experienceLevels = [
            ['name' => '1-2 năm'],
            ['name' => '3-5 năm'],
            ['name' => '6-8 năm'],
            ['name' => '9-11 năm'],
            ['name' => '12+ năm'],
        ];

        // Chèn dữ liệu vào bảng
        DB::table('experience_levels')->insert($experienceLevels);
    }
}
