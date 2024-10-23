<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập Nhật Trạng Thái Đơn Xin Việc</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 20px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        p {
            color: #34495e;
            margin-bottom: 15px;
        }
        strong {
            color: #2980b9;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
            font-size: 12px;
            color: #7f8c8d;
            text-align: center;
        }
        .signature {
            margin-top: 25px;
            font-style: italic;
        }
        .interview-details {
            background-color: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Cập Nhật Trạng Thái Đơn Xin Việc</h1>
    <p>Chào {{ $applicantName }},</p>

    <p>Chúng tôi xin thông báo rằng hồ sơ của bạn đã được xem xét và bạn đã được mời tham gia phỏng vấn cho vị trí <strong>{{ $jobTitle }}</strong> tại <strong>{{ $companyName }}</strong>.</p>

    <div class="interview-details">
        <p>Chúng tôi sẽ sớm liên hệ với bạn để cung cấp thông tin chi tiết về thời gian, địa điểm và hình thức phỏng vấn.</p>
    </div>

    <p>Chúng tôi rất mong chờ được gặp bạn và chúc bạn thành công trong buổi phỏng vấn!</p>
    <p class="signature">Trân trọng,<br>{{ $companyName }}</p>
</div>
<div class="footer">
    <p>Đây là email tự động, vui lòng không trả lời.</p>
</div>
</body>
</html>
