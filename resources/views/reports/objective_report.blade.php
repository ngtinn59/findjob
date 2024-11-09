<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo thống kê mục tiêu người dùng</title>
    <style>
        body {
            font-family: DejaVu Sans;
            margin: 20px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h1>Báo cáo thống kê mục tiêu người dùng</h1>

    <!-- 1. Thống kê mục tiêu theo vị trí mong muốn -->
<h2>1. Thống kê mục tiêu theo vị trí mong muốn</h2>
<table>
    <tr>
        <th>Vị trí mong muốn</th>
        <th>Số lượng mục tiêu</th>
    </tr>
    @foreach($objectivesByPosition as $position)
        <tr>
            <td>{{ $position->desired_level }}</td>
            <td>{{ $position->count }}</td>
        </tr>
    @endforeach
</table>

<!-- 2. Thống kê mục tiêu theo mức lương -->
<h2>2. Thống kê mục tiêu theo mức lương</h2>
<table>
    <tr>
        <th>Mức lương (VND)</th>
        <th>Số lượng mục tiêu</th>
    </tr>
    @foreach($objectivesBySalary as $salary)
        <tr>
            <td>{{ $salary->salary_range }}</td>
            <td>{{ $salary->count }}</td>
        </tr>
    @endforeach
</table>

<!-- 3. Thống kê mục tiêu theo trình độ học vấn -->
<h2>3. Thống kê mục tiêu theo trình độ học vấn</h2>
<table>
    <tr>
        <th>Trình độ học vấn</th>
        <th>Số lượng mục tiêu</th>
    </tr>
    @foreach($objectivesByEducationLevel as $education)
        <tr>
            <td>{{ $education->education_level }}</td>
            <td>{{ $education->count }}</td>
        </tr>
    @endforeach
</table>

<!-- 4. Thống kê mục tiêu theo ngành nghề -->
<h2>4. Thống kê mục tiêu theo ngành nghề</h2>
<table>
    <tr>
        <th>Ngành nghề</th>
        <th>Số lượng mục tiêu</th>
    </tr>
    @foreach($objectivesByProfession as $profession)
        <tr>
            <td>{{ $profession->profession }}</td>
            <td>{{ $profession->count }}</td>
        </tr>
    @endforeach
</table>

<!-- 5. Thống kê mục tiêu theo loại hình công việc -->
<h2>5. Thống kê mục tiêu theo loại hình công việc</h2>
<table>
    <tr>
        <th>Loại hình công việc</th>
        <th>Số lượng mục tiêu</th>
    </tr>
    @foreach($objectivesByEmploymentType as $employmentType)
        <tr>
            <td>{{ $employmentType->employment_type }}</td>
            <td>{{ $employmentType->count }}</td>
        </tr>
    @endforeach
</table>

<!-- 6. Thống kê mục tiêu theo mức độ kinh nghiệm -->
<h2>6. Thống kê mục tiêu theo mức độ kinh nghiệm</h2>
<table>
    <tr>
        <th>Mức độ kinh nghiệm</th>
        <th>Số lượng mục tiêu</th>
    </tr>
    @foreach($objectivesByExperienceLevel as $experienceLevel)
        <tr>
            <td>{{ $experienceLevel->experience_level }}</td>
            <td>{{ $experienceLevel->count }}</td>
        </tr>
    @endforeach
</table>

<!-- 7. Thống kê mục tiêu theo địa lý -->
<h2>7. Thống kê mục tiêu theo địa lý</h2>
<h3>Theo quốc gia</h3>
<table>
    <tr>
        <th>Quốc gia</th>
        <th>Số lượng mục tiêu</th>
    </tr>
    @foreach($objectivesByCountry as $country)
        <tr>
            <td>{{ $country->country_name }}</td>
            <td>{{ $country->count }}</td>
        </tr>
    @endforeach
</table>

<h3>Theo thành phố</h3>
<table>
    <tr>
        <th>Thành phố</th>
        <th>Số lượng mục tiêu</th>
    </tr>
    @foreach($objectivesByCity as $city)
        <tr>
            <td>{{ $city->city_name }}</td>
            <td>{{ $city->count }}</td>
        </tr>
    @endforeach
</table>

<h3>Theo quận</h3>
<table>
    <tr>
        <th>Quận</th>
        <th>Số lượng mục tiêu</th>
    </tr>
    @foreach($objectivesByDistrict as $district)
        <tr>
            <td>{{ $district->district_name }}</td>
            <td>{{ $district->count }}</td>
        </tr>
    @endforeach
</table>

</body>
</html>
