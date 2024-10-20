<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Báo Từ Chối Đơn Xin Việc</title>
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
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 10px;
        }
        p {
            color: #34495e;
            margin-bottom: 15px;
        }
        strong {
            color: #c0392b;
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
        .message-box {
            background-color: #fdf2f0;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin-bottom: 20px;
        }
        .cta-button {
            display: inline-block;
            background-color: #3498db;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 10px;
        }
        .cta-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Chào {{ $applicantName }},</h1>
    <div class="message-box">
        <p>Chúng tôi rất tiếc phải thông báo rằng đơn xin việc của bạn cho vị trí <strong>{{ $jobTitle }}</strong> tại <strong>{{ $companyName }}</strong> đã không được chấp nhận.</p>
    </div>

    <p>Chúng tôi rất trân trọng thời gian và công sức bạn đã dành cho việc ứng tuyển này. Mặc dù bạn không được chọn cho vị trí này, chúng tôi khuyến khích bạn tiếp tục theo dõi các cơ hội việc làm khác tại công ty của chúng tôi trong tương lai.</p>

    <p>Cảm ơn bạn đã quan tâm đến cơ hội làm việc với chúng tôi!</p>

    <a href="#" class="cta-button">Xem Cơ Hội Khác</a>

    <p class="signature">Trân trọng,<br>{{ $companyName }}</p>
</div>
<div class="footer">
    <p>Đây là email tự động, vui lòng không trả lời.</p>
</div>
</body>
</html>
