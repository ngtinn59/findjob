<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận email của bạn</title>
</head>
<body style="font-family: 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%); margin: 0; padding: 0;">
<table cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 40px auto; background-color: rgba(255, 255, 255, 0.9); border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
    <tr>
        <td style="padding: 60px 40px;">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="padding: 0 0 20px 0;">
                        <h1 style="color: #333333; font-size: 28px; font-weight: 700; margin: 0; text-align: center;">Xin chào, {{ $user->name }}!</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 0 30px 0;">
                        <p style="color: #666666; font-size: 18px; margin: 0; text-align: center; line-height: 1.8;">Cảm ơn bạn đã đăng ký. Để bắt đầu trải nghiệm tuyệt vời, vui lòng xác nhận địa chỉ email của bạn.</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 0 30px 0;">
                        <table cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td align="center">
                                    <a href="{{ $verificationUrl }}" style="display: inline-block; background: linear-gradient(to right, #4CAF50, #45a049); color: #ffffff; text-decoration: none; font-size: 18px; font-weight: bold; padding: 15px 40px; border-radius: 50px; transition: all 0.3s; box-shadow: 0 4px 10px rgba(76, 175, 80, 0.3);">Xác nhận Email</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0;">
                        <p style="color: #999999; font-size: 16px; margin: 0; text-align: center; font-style: italic;">Nếu bạn không tạo tài khoản, vui lòng bỏ qua email này.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding: 20px 40px; background-color: rgba(0, 0, 0, 0.05); border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center">
                        <p style="color: #666666; font-size: 14px; margin: 0;">© {{ date('Y') }} Công ty ITViet. Bảo lưu mọi quyền.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
