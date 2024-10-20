<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Các Bước Tiếp Theo Trong Đơn Xin Việc</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #555;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Chào {{ $applicantName }},</h1>
    <p>Chúng tôi xin thông báo rằng bạn đã vượt qua vòng đầu tiên trong quá trình ứng tuyển cho vị trí <strong>{{ $jobTitle }}</strong>.</p>
    <p>Các bước tiếp theo sẽ như sau:</p>
    <ol>
        <li>Chúng tôi sẽ liên hệ với bạn để sắp xếp thời làm bài test.</li>
        <li>Trong khi chờ đợi, bạn có thể chuẩn bị cho các câu hỏi cho bài test.</li>
        <li>Hãy kiểm tra hộp thư đến của bạn thường xuyên để không bỏ lỡ thông tin từ chúng tôi.</li>
    </ol>
    <p>Cảm ơn bạn đã quan tâm đến cơ hội làm việc với chúng tôi!</p>
    <p>Trân trọng,<br>{{ $companyName }}</p>
</div>
<div class="footer">
    <p>Đây là email tự động, vui lòng không trả lời.</p>
</div>
</body>
</html>
