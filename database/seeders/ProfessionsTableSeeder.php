<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profession;

class ProfessionsTableSeeder extends Seeder
{
    public function run()
    {
        $professions = [
            ['name' => 'Hành chính - Thư ký'],
            ['name' => 'Khách sạn - Nhà hàng - Du lịch'],
            ['name' => 'Bán sỉ - Bán lẻ - Quản lý cửa hàng'],
            ['name' => 'Marketing'],
            ['name' => 'Bán hàng kinh doanh'],
            ['name' => 'Kế toán'],
            ['name' => 'Tài chính - Đầu tư - Chứng khoán'],
            ['name' => 'Kiểm toán'],
            ['name' => 'Khoa học kỹ thực'],
            ['name' => 'An ninh - bảo vệ'],
            ['name' => 'Thiết kế - Sáng tạo nghệ thuật'],
            ['name' => 'Kiến trúc - Thiết kế ngoaại thất'],
            ['name' => 'IT Phần cứng - Mạng'],
            ['name' => 'IT Phần mềm'],
            ['name' => 'Sản xuất - Lắp ráp - Chế biến'],
            ['name' => 'Vận hành - Bảo trì - Bảo dưỡng'],
            ['name' => 'Nông - Lâm - Ngư Nghiệp'],
            ['name' => 'Thu mua - Kho vận - Ngư Nghiệp'],
            ['name' => 'Khai thác năng lượng - Khoáng sản - Địa chất'],
            ['name' => 'Y tế - Chăm sóc sức khỏe'],
            ['name' => 'Nhân sự'],
            ['name' => 'Bảo hiểm, Thông tin - Truyền thông - Quảng cáo'],
            ['name' => 'Luật - Pháp Lý - Tuân thủ'],
            ['name' => 'Quản lý dự án'],
            ['name' => 'Quản lý tiêu chuẩn và chất lượng'],
            ['name' => 'Bất động sản'],
            ['name' => 'Chăm sóc khách hàng, Xây dựng'],
            ['name' => 'Giáo dục - Đào tạo'],
            ['name' => 'Phân tích - Thống kê dữ liệu, An toàn lao động'],
            ['name' => 'Biên phiên dịch'],
            ['name' => 'Bưu chính viễn thông'],
            ['name' => 'Dầu khí'],
            ['name' => 'Dệt may - Da giày - Thời trang'],
            ['name' => 'Điện - Điện tử - Điện lạnh'],
            ['name' => 'Dược phẩm'],
            ['name' => 'Hóa học - Hóa sinh'],
            ['name' => 'Môi trường - Xử lý chất thải'],
            ['name' => 'Thực phẩm - Đồ uống'],
            ['name' => 'Chăn nuôi - Thú y'],
            ['name' => 'Cơ khí - Ô tô - Tự động hóa'],
            ['name' => 'Công nghệ thực phẩm - Dinh dưỡng'],
            ['name' => 'Lao động phổ thông'],
            ['name' => 'Phi chính phủ - Phi lợi nhuận'],
            ['name' => 'Xuất bản - In ấn'],
            ['name' => 'Truyền hình - Báo chí - Biên tập'],

        ];

        foreach ($professions as $profession) {
            Profession::create($profession);
        }
    }
}
