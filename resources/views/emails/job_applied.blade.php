<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận ứng tuyển</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            box-sizing: border-box;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.5em;
            font-weight: 700;
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
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease-in-out;
        }

        li:hover {
            transform: translateY(-2px);
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #7f8c8d;
            font-size: 0.9em;
        }

        .button {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #2980b9;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Xác nhận ứng tuyển</h1>
    <p>Chào <span class="highlight">{{ $userName }}</span>,</p>
    <p>Chúc mừng! Bạn đã ứng tuyển thành công vào vị trí <span class="highlight">{{ $jobTitle }}</span>.</p>
    <p>Thông tin chi tiết về công việc:</p>
    <ul>
        <li><strong>Công ty:</strong> {{ $companyName }}</li>
        <li><strong>Địa chỉ:</strong> {{ $address }}</li>
        <li><strong>Mức lương:</strong> {{ $salary_from }} - {{ $salary_to }}</li>
    </ul>
    <p>Chúng tôi đánh giá cao sự quan tâm của bạn và sẽ liên hệ với bạn trong thời gian sớm nhất để thông báo về các bước tiếp theo của quá trình tuyển dụng.</p>
    <div style="text-align: center;">
        <a href="#" class="button">Xem chi tiết ứng tuyển</a>
    </div>
    <div class="footer">
        <p>Cảm ơn bạn đã quan tâm đến cơ hội nghề nghiệp tại {{ $companyName }}!</p>
    </div>
</div>
</body>
</html>
