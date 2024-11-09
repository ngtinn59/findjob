<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EducationLevel; // Đảm bảo rằng bạn đã có model EducationLevel

class EducationLevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Dữ liệu mẫu cho bảng education_levels
        $educationLevels = [
            ['name' => 'Cấp 1'],
            ['name' => 'Cấp 2'],
            ['name' => 'Cấp 3'],
            ['name' => 'Trung cấp'],
            ['name' => 'Cao đẳng'],
            ['name' => 'Đại học'],
            ['name' => 'Sau đại học'],
        ];

        // Thêm dữ liệu vào bảng education_levels
        foreach ($educationLevels as $level) {
            EducationLevel::create($level);
        }
    }
}
