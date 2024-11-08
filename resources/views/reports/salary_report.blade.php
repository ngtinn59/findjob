<!DOCTYPE html>
<html>
<head>
    <title>Báo Cáo Lương</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }
    </style>
</head>
<body>
<h1>Báo Cáo Lương Theo Ngành Nghề theo Tháng</h1>

<table>
    <thead>
    <tr>
        <th>Tháng</th>
        <th>Ngành Nghề</th>
        <th>Loại Hình Công Việc</th>
        <th>Cấp Bậc</th>
        <th>Thành Phố</th>
        <th>Lương Trung Bình</th>
        <th>Lương Thấp Nhất</th>
        <th>Lương Cao Nhất</th>
        <th>Số Lượng Công Việc</th>
    </tr>
    </thead>
    <tbody>
    @foreach($salary_report as $report)
        <tr>
            <td>{{ $report->month }}</td>
            <td>{{ $report->profession->name ?? 'N/A' }}</td>
            <td>{{ $report->employmentType->name ?? 'N/A' }}</td>
            <td>{{ $report->desiredLevel->name ?? 'N/A' }}</td>
            <td>{{ $report->city->name ?? 'N/A' }}</td>
            <td>{{ number_format($report->avg_salary, 0, ',', '.') }} VND</td>
            <td>{{ number_format($report->min_salary, 0, ',', '.') }} VND</td>
            <td>{{ number_format($report->max_salary, 0, ',', '.') }} VND</td>
            <td>{{ $report->job_count }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
