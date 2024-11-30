<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo hệ thống tìm việc</title>
    <style>
        body { font-family: DejaVu Sans; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h1>Báo cáo hệ thống tìm việc</h1>

<h2>1. Tổng quan hệ thống</h2>
<p>Tổng số người dùng: {{ $totalUsers }}</p>
<p>Tổng số công ty: {{ $totalCompanies }}</p>
<p>Số lượng công việc đang hoạt động: {{ $activeJobs }}</p>

<h2>2. Thống kê người dùng</h2>
<p>Số người dùng mới trong tháng: {{ $newUsersThisMonth }}</p>
<p>Nhà tuyển dụng: {{ $usersEmployer }}</p>
<p>Ứng viên: {{ $usersDeveloper }}</p>

<h2>3. Thống kê công việc</h2>
<p>Công việc mới trong tháng: {{ $newJobsThisMonth }}</p>
<p>Công việc không còn hoạt động: {{ $inactiveJobs }}</p>

<h2>4. Thống kê theo khu vực địa lý</h2>
<table>
    <tr>
        <th>Khu vực</th>
        <th>Số lượng công việc</th>
    </tr>
    @foreach($jobsByLocation as $location)
        <tr>
            <td>{{ $location->city_name }}</td>
            <td>{{ $location->count }}</td>
        </tr>
    @endforeach
</table>

<h2>5. Thống kê theo ngành nghề và mức lương</h2>
<table>
    <tr>
        <th>Ngành nghề</th>
        <th>Số công việc</th>
        <th>Lương trung bình</th>
        <th>Lương thấp nhất</th>
        <th>Lương cao nhất</th>
    </tr>
    @foreach($salaryStats as $stat)
        <tr>
            <td>{{ $stat->profession->name ?? 'N/A' }}</td>
            <td>{{ $stat->job_count }}</td>
            <td>{{ number_format($stat->avg_salary, 2) }} VND</td>
            <td>{{ number_format($stat->min_salary) }} VND</td>
            <td>{{ number_format($stat->max_salary) }} VND</td>
        </tr>
    @endforeach
</table>

<h2>6. Tỷ lệ tương tác của người dùng</h2>
<p>Tổng số lượt ứng tuyển: {{ $applicationsCount }}</p>
<p>Tỷ lệ ứng tuyển trung bình mỗi người dùng: {{ number_format($avgApplicationsPerUser, 2) }}</p>

<h2>7. Thống kê công việc theo các tiêu chí khác</h2>

<h3>Công việc theo cấp bậc</h3>
<table>
    <tr>
        <th>Cấp bậc công việc</th>
        <th>Số lượng công việc</th>
    </tr>
    @foreach($jobsByDesiredLevel as $level)
        <tr>
            <td>{{ $level->desired_level }}</td>
            <td>{{ $level->count }}</td>
        </tr>
    @endforeach
</table>

<h3>Công việc theo loại hình tuyển dụng</h3>
<table>
    <tr>
        <th>Loại hình tuyển dụng</th>
        <th>Số lượng công việc</th>
    </tr>
    @foreach($jobsByEmploymentType as $employment)
        <tr>
            <td>{{ $employment->employment_type }}</td>
            <td>{{ $employment->count }}</td>
        </tr>
    @endforeach
</table>

<h3>Công việc theo mức độ kinh nghiệm</h3>
<table>
    <tr>
        <th>Mức độ kinh nghiệm</th>
        <th>Số lượng công việc</th>
    </tr>
    @foreach($jobsByExperienceLevel as $experience)
        <tr>
            <td>{{ $experience->experience_level }}</td>
            <td>{{ $experience->count }}</td>
        </tr>
    @endforeach
</table>

<h3>Công việc theo trình độ học vấn</h3>
<table>
    <tr>
        <th>Trình độ học vấn</th>
        <th>Số lượng công việc</th>
    </tr>
    @foreach($jobsByEducationLevel as $education)
        <tr>
            <td>{{ $education->education_level }}</td>
            <td>{{ $education->count }}</td>
        </tr>
    @endforeach
</table>

<h3>Công việc theo quốc gia</h3>
<table>
    <tr>
        <th>Quốc gia</th>
        <th>Số lượng công việc</th>
    </tr>
    @foreach($jobsByCountry as $country)
        <tr>
            <td>{{ $country->country }}</td>
            <td>{{ $country->count }}</td>
        </tr>
    @endforeach
</table>

<h3>Công việc theo nơi làm việc</h3>
<table>
    <tr>
        <th>Nơi làm việc</th>
        <th>Số lượng công việc</th>
    </tr>
    @foreach($jobsByWorkplace as $workplace)
        <tr>
            <td>{{ $workplace->workplace }}</td>
            <td>{{ $workplace->count }}</td>
        </tr>
    @endforeach
</table>

</body>
</html>
