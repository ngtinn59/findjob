<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư từ nhà tuyển dụng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 5px;
            padding: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-top: 0;
        }
        .job-title {
            font-weight: bold;
            color: #3498db;
        }
        .message-content {
            background-color: #f9f9f9;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
        }
        .signature {
            margin-top: 30px;
            font-style: italic;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
<div class="email-container">
    <h1>Xin chào {{ $userName }},</h1>
    <p>Bạn nhận được thông báo từ nhà tuyển dụng về công việc: <span class="job-title">{{ $jobTitle }}</span>.</p>
    <p>Nội dung:</p>
    <div class="message-content">
        <p>{{ $messageContent }}</p>
    </div>
    <div class="signature">
        <p>Trân trọng,</p>
        <p>Nhà tuyển dụng.</p>
    </div>
</div>
</body>
</html><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư từ nhà tuyển dụng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 5px;
            padding: 30px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-top: 0;
        }
        .job-title {
            font-weight: bold;
            color: #3498db;
        }
        .message-content {
            background-color: #f9f9f9;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
        }
        .signature {
            margin-top: 30px;
            font-style: italic;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
<div class="email-container">
    <h1>Xin chào {{ $userName }},</h1>
    <p>Bạn nhận được thông báo từ nhà tuyển dụng về công việc: <span class="job-title">{{ $jobTitle }}</span>.</p>
    <p>Nội dung:</p>
    <div class="message-content">
        <p>{{ $messageContent }}</p>
    </div>
    <div class="signature">
        <p>Trân trọng,</p>
        <p>Nhà tuyển dụng.</p>
    </div>
</div>
</body>
</html>
