<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictsTableSeeder extends Seeder
{
    public function run()
    {
        // Giả sử bạn đã có một user với id = 1
        $userId = 1; // Thay đổi theo ID người dùng của bạn

        // Fake dữ liệu cho quận ở Cần Thơ
        $districts = [
            ['name' => 'Ninh Kiều', 'city_id' => 3, 'users_id' => $userId],
            ['name' => 'Bình Thủy', 'city_id' => 3, 'users_id' => $userId],
            ['name' => 'Cái Răng', 'city_id' => 3, 'users_id' => $userId],
            ['name' => 'Ô Môn', 'city_id' => 3, 'users_id' => $userId],
            ['name' => 'Thốt Nốt', 'city_id' => 3, 'users_id' => $userId],
            ['name' => 'Vĩnh Thạnh', 'city_id' => 3, 'users_id' => $userId],
            ['name' => 'Cờ Đỏ', 'city_id' => 3, 'users_id' => $userId],
            ['name' => 'Phong Điền', 'city_id' => 3, 'users_id' => $userId],
            ['name' => 'Thới Lai', 'city_id' => 3, 'users_id' => $userId],

            // Thêm quận của Hà Nội
            ['name' => 'Hoàn Kiếm', 'city_id' => 1, 'users_id' => $userId],
            ['name' => 'Đống Đa', 'city_id' => 1, 'users_id' => $userId],
            ['name' => 'Ba Đình', 'city_id' => 1, 'users_id' => $userId],
            ['name' => 'Hai Bà Trưng', 'city_id' => 1, 'users_id' => $userId],
            ['name' => 'Thanh Xuân', 'city_id' => 1, 'users_id' => $userId],
            ['name' => 'Cầu Giấy', 'city_id' => 1, 'users_id' => $userId],
            ['name' => 'Nam Từ Liêm', 'city_id' => 1, 'users_id' => $userId],
            ['name' => 'Bắc Từ Liêm', 'city_id' => 1, 'users_id' => $userId],
            ['name' => 'Long Biên', 'city_id' => 1, 'users_id' => $userId],

            // Thêm quận của Hồ Chí Minh
            ['name' => 'Quận 1', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Quận 2', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Quận 3', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Quận 4', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Quận 5', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Quận 6', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Quận 7', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Quận 8', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Quận 9', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Quận 10', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Quận 11', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Quận 12', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Bình Thạnh', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Thủ Đức', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Tân Bình', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Tân Phú', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Gò Vấp', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Phú Nhuận', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Nhà Bè', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Hóc Môn', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Củ Chi', 'city_id' => 2, 'users_id' => $userId],
            ['name' => 'Bình Tân', 'city_id' => 2, 'users_id' => $userId],
        ];

        // Thêm dữ liệu vào bảng districts
        DB::table('districts')->insert($districts);
    }
}
