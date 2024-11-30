<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company; // Đảm bảo bạn đã tạo model cho bảng companies
use App\Models\Job; // Đảm bảo bạn đã tạo model cho bảng jobs
use Faker\Factory as Faker;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN'); // Sử dụng Faker cho tiếng Việt

        // Giả sử bạn muốn tạo 50 công ty
        foreach (range(1, 50) as $index) {
            $company = Company::create([
                'users_id' => $faker->numberBetween(2, 10), // Thay số này với ID của user trong bảng users
                'country_id' => $faker->numberBetween(1, ), // Thay số này với ID của country trong bảng countries
                'city_id' => $faker->numberBetween(1, 3), // Thay số này với ID của city trong bảng cities
                'district_id' => $faker->numberBetween(1, 10), // Thay số này với ID của district trong bảng districts
                'company_size_id' => $faker->numberBetween(1, 5), // Thay số này với ID của company size trong bảng company_sizes
                'company_type_id' => $faker->numberBetween(1, 5), // Thay số này với ID của company type trong bảng company_types
                'company_name' => $faker->company, // Tên công ty
                'phone' => $faker->phoneNumber, // Số điện thoại
                'company_email' => $faker->companyEmail, // Email công ty
                'tax_code' => $faker->ean8, // Mã số thuế
                'date_of_establishment' => $faker->date(), // Ngày thành lập
                'working_days' => $faker->randomElement(['Thứ Hai - Thứ Sáu', 'Thứ Hai - Thứ Bảy']), // Ngày làm việc
                'overtime_policy' => $faker->sentence, // Chính sách làm thêm
                'website' => $faker->url, // Website
                'facebook' => $faker->url, // Facebook
                'youtube' => $faker->url, // YouTube
                'linked' => $faker->url, // LinkedIn
                'logo' => $faker->imageUrl(200, 200, 'business'), // Hình ảnh ngẫu nhiên cho logo
                'banner' => $faker->imageUrl(800, 200, 'business'), // Hình ảnh ngẫu nhiên cho banner
                'address' => $faker->address, // Địa chỉ
                'latitude' => $faker->latitude, // Vĩ độ
                'longitude' => $faker->longitude, // Kinh độ
                'description' => $faker->paragraph, // Mô tả
                'is_hot' => $faker->boolean, // Trạng thái nóng
                'approved' => $faker->boolean, // Trạng thái phê duyệt
            ]);

            // Giả sử bạn muốn tạo từ 1 đến 5 công việc cho mỗi công ty
            foreach (range(1, $faker->numberBetween(1, 5)) as $jobIndex) {
                Job::create([
                    'users_id' => $faker->numberBetween(1, 10), // ID người dùng
                    'company_id' => $company->id, // ID công ty
                    'profession_id' => $faker->numberBetween(1, 10), // ID nghề nghiệp
                    'desired_level_id' => $faker->numberBetween(1, 5), // ID cấp độ mong muốn
                    'employment_type_id' => $faker->numberBetween(1, 5), // ID loại hình việc làm
                    'experience_level_id' => $faker->numberBetween(1, 5), // ID cấp độ kinh nghiệm
                    'education_level_id' => $faker->numberBetween(1, 5), // ID cấp độ học vấn
                    'city_id' => $faker->numberBetween(1, 10), // ID thành phố
                    'district_id' => $faker->numberBetween(1, 10), // ID quận
                    'country_id' => $faker->numberBetween(1, 10), // ID quốc gia
                    'workplace_id' => $faker->numberBetween(1, 10), // ID nơi làm việc
                    'title' => $faker->jobTitle, // Tiêu đề công việc
                    'quantity' => $faker->numberBetween(1, 10), // Số lượng
                    'salary_from' => $faker->numberBetween(5000000, 30000000), // Mức lương từ
                    'salary_to' => $faker->numberBetween(30000000, 70000000), // Mức lương đến
                    'work_address' => $faker->address, // Địa chỉ làm việc
                    'last_date' => $faker->dateTimeBetween('+1 week', '+1 month'), // Ngày hết hạn
                    'description' => $faker->paragraph, // Mô tả công việc
                    'skill_experience' => $faker->sentence, // Kinh nghiệm kỹ năng
                    'benefits' => $faker->sentence, // Quyền lợi
                    'work_location' => $faker->randomElement(['Hà Nội', 'TP.HCM', 'Đà Nẵng']), // Địa điểm làm việc
                    'latitude' => $faker->latitude, // Vĩ độ
                    'longitude' => $faker->longitude, // Kinh độ
                    'contact_name' => $faker->name, // Tên người liên hệ
                    'phone' => $faker->phoneNumber, // Số điện thoại liên hệ
                    'email' => $faker->companyEmail, // Email liên hệ
                    'views' => $faker->numberBetween(0, 100), // Số lượt xem
                    'status' => $faker->randomElement([0, 1]), // Trạng thái công việc (0: không hoạt động, 1: hoạt động)
                    'featured' => $faker->boolean, // Trạng thái nổi bật
                ]);
            }
        }
    }
}
