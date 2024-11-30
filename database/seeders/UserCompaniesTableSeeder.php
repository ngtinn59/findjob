<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserCompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create('vi_VN'); // Sử dụng Faker cho tiếng Việt

        // Tạo 50 người dùng ngẫu nhiên
        foreach (range(2, 50) as $index) {
            DB::table('users')->insert([
                'name' => $faker->name, // Tên người dùng
                'email' => $faker->unique()->safeEmail, // Email duy nhất
                'password' => Hash::make('password'), // Mật khẩu
                'account_type' => $faker->numberBetween(2), // Loại tài khoản (1-3)
                'email_verified_at' => now(), // Thời gian xác minh email
                'status' => $faker->numberBetween(3), // Trạng thái (1-3)
                'created_at' => now(), // Thời gian tạo
                'updated_at' => now(), // Thời gian cập nhật
            ]);
        }
    }
}
