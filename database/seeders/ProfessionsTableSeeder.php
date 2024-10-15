<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profession;

class ProfessionsTableSeeder extends Seeder
{
    public function run()
    {
        $professions = [
            ['name' => 'Kỹ sư phần mềm'],
            ['name' => 'Thiết kế đồ họa'],
            ['name' => 'Quản lý dự án'],
            ['name' => 'Nhân viên marketing'],
            ['name' => 'Chuyên viên phân tích dữ liệu'],
            ['name' => 'Giáo viên'],
            ['name' => 'Chuyên viên IT'],
            ['name' => 'Kế toán'],
            ['name' => 'Nhân viên bán hàng'],
            ['name' => 'Lập trình viên web'],
            ['name' => 'Chuyên viên tư vấn'],
            ['name' => 'Quản trị mạng'],
            ['name' => 'Kỹ thuật viên sửa chữa'],
            ['name' => 'Nhân viên chăm sóc khách hàng'],
            ['name' => 'Chuyên viên SEO'],
            ['name' => 'Nhà báo'],
            ['name' => 'Biên dịch viên'],
            ['name' => 'Chuyên viên nhân sự'],
            ['name' => 'Kỹ sư điện'],
            ['name' => 'Chuyên viên phát triển kinh doanh'],
            ['name' => 'Nhân viên vận hành'],
            ['name' => 'Chuyên viên bất động sản'],
            ['name' => 'Thiết kế nội thất'],
            ['name' => 'Kỹ sư xây dựng'],
            ['name' => 'Chuyên viên đầu tư'],
            ['name' => 'Người mẫu'],
            ['name' => 'Chuyên viên tài chính'],
            ['name' => 'Quản lý bán hàng'],
            ['name' => 'Chuyên viên tư vấn pháp luật'],
            ['name' => 'Nhà sản xuất phim'],
            ['name' => 'Lập trình viên ứng dụng di động'],
            ['name' => 'Kỹ thuật viên y tế'],
            ['name' => 'Chuyên viên phân tích thị trường'],
            ['name' => 'Chuyên gia dinh dưỡng'],
            ['name' => 'Kỹ sư cơ khí'],
            ['name' => 'Quản lý nhà hàng'],
            ['name' => 'Nhân viên lễ tân'],
            ['name' => 'Nhân viên bảo vệ'],
            ['name' => 'Chuyên viên marketing số'],
            ['name' => 'Chuyên viên phát triển sản phẩm'],
            ['name' => 'Nhà thiết kế thời trang'],
            ['name' => 'Chuyên viên kinh doanh quốc tế'],
            ['name' => 'Kỹ thuật viên phần cứng'],
            ['name' => 'Kỹ sư công nghệ thông tin'],
            ['name' => 'Chuyên viên bán hàng trực tuyến'],
            ['name' => 'Kỹ thuật viên môi trường'],
            ['name' => 'Chuyên viên tư vấn tài chính cá nhân'],
            ['name' => 'Chuyên viên bảo mật thông tin'],
            ['name' => 'Nhà phát triển game'],
            ['name' => 'Nhà khoa học dữ liệu'],
            ['name' => 'Kỹ sư sản xuất'],
            ['name' => 'Quản lý sự kiện'],
            ['name' => 'Kỹ sư thiết kế'],
            ['name' => 'Chuyên viên nghiên cứu thị trường'],
            ['name' => 'Chuyên viên dịch vụ khách hàng'],
            ['name' => 'Kỹ sư tự động hóa'],
            ['name' => 'Giám đốc điều hành'],
            ['name' => 'Chuyên viên kế hoạch sản xuất'],
            ['name' => 'Kỹ sư lập trình'],
            ['name' => 'Nhân viên giao hàng'],
            ['name' => 'Chuyên viên phát triển phần mềm di động'],
            ['name' => 'Chuyên viên truyền thông'],
            ['name' => 'Chuyên gia tư vấn quản lý'],
            ['name' => 'Nhân viên hành chính'],
            ['name' => 'Kỹ sư thiết bị'],
            ['name' => 'Nhân viên tiếp thị trực tiếp'],
            ['name' => 'Chuyên viên quản lý rủi ro'],
            ['name' => 'Nhân viên hỗ trợ kỹ thuật'],
            ['name' => 'Giám đốc tài chính'],
            ['name' => 'Chuyên viên chăm sóc sức khỏe'],
            ['name' => 'Nhân viên tổ chức sự kiện'],
            ['name' => 'Chuyên viên phát triển hệ thống'],
            ['name' => 'Nhân viên xuất nhập khẩu'],
            ['name' => 'Chuyên gia truyền thông xã hội'],
            ['name' => 'Chuyên viên xây dựng thương hiệu'],
            ['name' => 'Chuyên viên phân tích chiến lược'],
            ['name' => 'Giám sát thi công'],
            ['name' => 'Chuyên viên điều hành kinh doanh'],
            ['name' => 'Nhân viên kiểm toán'],
            ['name' => 'Kỹ sư hóa học'],
            ['name' => 'Chuyên viên thiết kế trải nghiệm người dùng (UX)'],
            ['name' => 'Chuyên viên phát triển ứng dụng web'],
            ['name' => 'Kỹ sư kiểm định chất lượng'],
            ['name' => 'Nhân viên quản lý dự án bất động sản'],
            ['name' => 'Chuyên viên chăm sóc khách hàng trực tuyến'],
            ['name' => 'Kỹ sư điện tử'],
            ['name' => 'Chuyên viên lập trình viên nhúng'],
            ['name' => 'Nhân viên bán hàng kỹ thuật'],
            ['name' => 'Kỹ sư logistics'],
        ];

        foreach ($professions as $profession) {
            Profession::create($profession);
        }
    }
}