<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesiredLevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('desired_levels')->insert([
            ['name' => 'Mới ra trường'],
            ['name' => 'Nhân viên'],
            ['name' => 'Chuyên viên'],
            ['name' => 'Trưởng nhóm'],
            ['name' => 'Quản lý'],
        ]);
    }
}
