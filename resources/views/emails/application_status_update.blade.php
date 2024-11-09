<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo cập nhật trạng thái</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            margin-top: 0;
        }
        strong {
            color: #2980b9;
        }
        .status-message {
            background-color: #e8f4f8;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
        }
        .signature {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-style: italic;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Cập nhật trạng thái ứng tuyển</h1>
    <p>Xin chào <strong>{{ $applicantName }}</strong>,</p>
    <p>Trạng thái ứng tuyển cho công việc <strong>{{ $jobTitle }}</strong> tại công ty <strong>{{ $companyName }}</strong> đã được cập nhật.</p>

    <div class="status-message">
        @if($status === 'pending')
        <p>Trạng thái hiện tại: <strong>Đang chờ xác nhận</strong>.</p>
        @elseif($status === 'contacted')
        <p>Bạn đã được <strong>liên hệ</strong>. Vui lòng kiểm tra email hoặc điện thoại để cập nhật thông tin chi tiết.</p>
        @elseif($status === 'test_round')
        <p>Bạn đã được mời tham gia <strong>vòng test</strong>. Chúng tôi sẽ sớm gửi chi tiết cho bạn.</p>
        @elseif($status === 'interview')
        <p>Bạn đã được mời tham gia <strong>vòng phỏng vấn</strong>. Vui lòng chờ thêm thông tin từ chúng tôi.</p>
        @elseif($status === 'hired')
        <p><strong>Chúc mừng</strong> bạn đã trúng tuyển! Chúng tôi sẽ liên hệ để cung cấp thêm chi tiết.</p>
        @elseif($status === 'not_selected')
        <p>Rất tiếc, bạn không được chọn cho vị trí này. Chúc bạn may mắn trong các cơ hội tiếp theo.</p>
        @endif
    </div>

    <div class="signature">
        <p>Trân trọng,</p>
        <p><strong>{{ $companyName }}</strong></p>
    </div>
</div>
</body>
</html>
