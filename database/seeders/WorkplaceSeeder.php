<?php

namespace Database\Seeders;

use App\Models\Workplace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workplaces = [
            ['name' => 'Làm từ xa'],
            ['name' => 'Tại văn phòng'],
            ['name' => 'Kết hợp'],
        ];

        foreach ($workplaces as $workplace) {
            Workplace::create($workplace);
        }
    }
}
