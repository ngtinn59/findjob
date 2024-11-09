<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bạn sẽ sớm được liên hệ</title>
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
        .highlight-box {
            background-color: #e8f4fd;
            border-left: 4px solid #3498db;
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
    <p>Cảm ơn bạn đã nộp đơn ứng tuyển cho vị trí <strong>{{ $jobTitle }}</strong> tại <strong>{{ $companyName }}</strong>.</p>

    <div class="highlight-box">
        <p>Chúng tôi muốn thông báo rằng hồ sơ của bạn đang được xem xét và bạn sẽ sớm nhận được thông tin từ chúng tôi về các bước tiếp theo trong quá trình ứng tuyển.</p>
    </div>

    <p>Vui lòng kiểm tra hộp thư của bạn thường xuyên để không bỏ lỡ thông tin quan trọng từ chúng tôi.</p>

    <p>Nếu bạn có bất kỳ câu hỏi nào, đừng ngần ngại liên hệ với chúng tôi qua email này.</p>

    <p>Cảm ơn bạn đã quan tâm đến cơ hội làm việc với chúng tôi!</p>

    <a href="#" class="cta-button">Xem Trạng Thái Ứng Tuyển</a>

    <p class="signature">Trân trọng,<br>{{ $companyName }}</p>
</div>
<div class="footer">
    <p>Đây là email tự động, vui lòng không trả lời.</p>
</div>
</body>
</html>
