<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguagesTableSeeder extends Seeder
{
    public function run()
    {
        // Dữ liệu mẫu cho ngôn ngữ
        $languages = [
            ['name' => 'Tiếng Việt'],
            ['name' => 'Tiếng Anh'],
            ['name' => 'Tiếng Pháp'],
            ['name' => 'Tiếng Tây Ban Nha'],
            ['name' => 'Tiếng Đức'],
            ['name' => 'Tiếng Hàn'],
            ['name' => 'Tiếng Nhật'],
            ['name' => 'Tiếng Trung'],
            // Thêm các ngôn ngữ khác nếu cần
        ];

        // Chèn dữ liệu vào bảng languages
        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}
