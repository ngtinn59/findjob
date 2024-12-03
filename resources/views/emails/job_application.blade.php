<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo ứng tuyển mới</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; background-color: #f6f9fc; margin: 0; padding: 0;">
<div style="max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 0; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow: hidden;">
    <!-- Header -->
    <div style="background-color: #4f46e5; padding: 30px; text-align: center;">
        <h1 style="color: #ffffff; margin-top: 15px; font-size: 24px;">Thông báo ứng tuyển mới</h1>
    </div>

    <!-- Content -->
    <div style="padding: 30px;">
        <p style="font-size: 16px; color: #333333;">Chào bạn,</p>

        <p style="font-size: 16px; color: #333333; background-color: #f0f4f8; padding: 15px; border-radius: 6px; margin: 20px 0;">
            Ứng viên <strong style="color: #4f46e5;">{{ $name }}</strong> đã ứng tuyển vào công việc <strong style="color: #4f46e5;">{{ $jobTitle }}</strong>.
        </p>

        <h3 style="color: #4f46e5; font-size: 18px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-top: 30px;">Thông tin ứng viên:</h3>
        <table style="width: 100%; border-collapse: separate; border-spacing: 0 10px;">
            <tr>
                <td style="padding: 10px; background-color: #f0f4f8; border-radius: 6px;">
                    <strong style="color: #4f46e5;">Tên:</strong>
                </td>
                <td style="padding: 10px; background-color: #f0f4f8; border-radius: 6px;">
                    {{ $name }}
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; background-color: #f0f4f8; border-radius: 6px;">
                    <strong style="color: #4f46e5;">Email:</strong>
                </td>
                <td style="padding: 10px; background-color: #f0f4f8; border-radius: 6px;">
                    {{ $email }}
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; background-color: #f0f4f8; border-radius: 6px;">
                    <strong style="color: #4f46e5;">Số điện thoại:</strong>
                </td>
                <td style="padding: 10px; background-color: #f0f4f8; border-radius: 6px;">
                    {{ $phone }}
                </td>
            </tr>
        </table>


        <p style="font-size: 16px; color: #333333; margin-top: 30px;">Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!</p>

        <p style="font-size: 16px; color: #333333;">Trân trọng,<br>Đội ngũ tuyển dụng</p>
    </div>

    <!-- Footer -->
    <div style="background-color: #f0f4f8; padding: 20px; text-align: center;">
        <p style="font-size: 14px; color: #666666; margin-bottom: 10px;">Kết nối với chúng tôi:</p>
        <a href="#" style="display: inline-block; margin: 0 10px;"><img src="https://via.placeholder.com/30x30" alt="Facebook" style="width: 30px; height: 30px;"></a>
        <a href="#" style="display: inline-block; margin: 0 10px;"><img src="https://via.placeholder.com/30x30" alt="LinkedIn" style="width: 30px; height: 30px;"></a>
        <a href="#" style="display: inline-block; margin: 0 10px;"><img src="https://via.placeholder.com/30x30" alt="Twitter" style="width: 30px; height: 30px;"></a>
        <p style="font-size: 12px; color: #999999; margin-top: 15px;">© 2024 Công ty của bạn. Tất cả các quyền được bảo lưu.</p>
    </div>
</div>
</body>
</html>

