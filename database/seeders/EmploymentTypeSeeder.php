<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmploymentType;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Chạy các seed dữ liệu.
     *
     * @return void
     */
    public function run()
    {
        $employmentTypes = [
            ['name' => 'Toàn thời gian'], // Làm việc toàn thời gian
            ['name' => 'Bán thời gian'], // Làm việc bán thời gian
            ['name' => 'Tự do'], // Làm việc tự do
            ['name' => 'Hợp đồng'], // Hợp đồng
            ['name' => 'Tạm thời'], // Tạm thời
            ['name' => 'Thực tập'], // Thực tập
        ];

        foreach ($employmentTypes as $type) {
            EmploymentType::create($type);
        }
    }
}
