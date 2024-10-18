<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật thông tin trúng tuyển</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 15px;
            margin-top: 0;
            font-size: 28px;
        }
        p {
            margin-bottom: 20px;
            font-size: 16px;
        }
        strong {
            color: #e74c3c;
            font-weight: 600;
        }
        .signature {
            margin-top: 30px;
            font-style: italic;
            color: #7f8c8d;
            text-align: right;
        }
        .highlight {
            background-color: #f1c40f;
            color: #34495e;
            padding: 3px 6px;
            border-radius: 4px;
            font-weight: 600;
        }
        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }
            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Chúc mừng, <span class="highlight">{{ $applicantName }}</span>!</h1>
    <p>Chúng tôi xin thông báo rằng đơn ứng tuyển của bạn cho vị trí <strong>{{ $jobTitle }}</strong> đã được phê duyệt!</p>
    <p>Bạn sẽ nhận được thông tin chi tiết và các bước tiếp theo từ nhà tuyển dụng trong thời gian sớm nhất.</p>
    <p>Chúng tôi rất mong chờ việc bạn sẽ gia nhập đội ngũ của chúng tôi!</p>
    <p>Trân trọng,</p>
    <p class="signature">Đội ngũ quản lý công việc</p>
</div>
</body>
</html>
