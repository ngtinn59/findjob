<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Confirmation</title>
</head>
<body>
<p>Chào {{ $userName }},</p>
<p>Bạn đã ứng tuyển công việc <b>{{ $jobTitle }}</b> thành công!</p>
<p>Thông tin công việc:</p>
<ul>
    <li>Công ty: {{ $companyName }}</li>
    <li>Địa chỉ: {{ $address }}</li>
    <li>Mức lương: {{ $salary }}</li>
{{--    <li>Loại công việc: {{ $jobtype }}</li>--}}
</ul>
<p>Chúng tôi sẽ liên hệ với bạn sớm.</p>
</body>
</html>
