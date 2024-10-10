<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Confirmation</title>
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
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }
        .highlight {
            color: #3498db;
            font-weight: bold;
        }
        ul {
            list-style-type: none;
            padding-left: 0;
        }
        li {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #ecf0f1;
            border-radius: 4px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #7f8c8d;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Xác nhận ứng tuyển</h1>
    <p>Chào <span class="highlight">{{ $userName }}</span>,</p>
    <p>Bạn đã ứng tuyển công việc <span class="highlight">{{ $jobTitle }}</span> thành công!</p>
    <p>Thông tin công việc:</p>
    <ul>
        <li><strong>Công ty:</strong> {{ $companyName }}</li>
        <li><strong>Địa chỉ:</strong> {{ $address }}</li>
        <li><strong>Mức lương:</strong> {{ $salary }}</li>
    </ul>
    <p>Chúng tôi sẽ liên hệ với bạn sớm.</p>
    <div class="footer">
        <p>Cảm ơn bạn đã quan tâm đến công việc của chúng tôi!</p>
    </div>
</div>
</body>
</html>
