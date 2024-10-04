<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lại Mật Khẩu</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #ffffff;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
    <tr>
        <td style="padding: 40px 30px; text-align: center; border-bottom: 2px solid #f0f0f0;">
            <h1 style="color: #333333; font-size: 28px; margin: 0;">Đặt Lại Mật Khẩu</h1>
        </td>
    </tr>
    <tr>
        <td style="padding: 40px 30px;">
            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">Xin chào,</p>
            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 20px;">Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này.</p>
            <p style="color: #333333; font-size: 16px; line-height: 1.5; margin-bottom: 30px;">Để đặt lại mật khẩu, vui lòng nhấp vào nút bên dưới:</p>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center">
                        <a href="{{ url('/reset-password?token=' . $token) }}" style="display: inline-block; padding: 14px 30px; background-color: #ffffff; color: #333333; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 4px; border: 2px solid #333333; transition: background-color 0.3s ease, color 0.3s ease;">Đặt Lại Mật Khẩu</a>
                    </td>
                </tr>
            </table>
            <p style="color: #666666; font-size: 14px; line-height: 1.5; margin-top: 30px;">Nếu bạn gặp vấn đề với nút trên, vui lòng sao chép và dán liên kết sau vào trình duyệt của bạn:</p>
            <p style="color: #333333; font-size: 14px; line-height: 1.5; word-break: break-all;">{{ url('/reset-password?token=' . $token) }}</p>
            <p style="color: #666666; font-size: 14px; line-height: 1.5; margin-top: 30px;">Trân trọng,<br>Đội ngũ hỗ trợ của chúng tôi</p>
        </td>
    </tr>
    <tr>
        <td style="padding: 20px 30px; text-align: center; border-top: 2px solid #f0f0f0;">
            <p style="color: #888888; font-size: 12px; margin: 0;">Email này được gửi tự động. Vui lòng không trả lời.</p>
        </td>
    </tr>
</table>
</body>
</html>
