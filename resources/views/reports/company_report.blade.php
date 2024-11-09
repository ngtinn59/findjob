<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo thống kê công ty</title>
    <style>
        body { font-family: DejaVu Sans; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<h1>Báo cáo thống kê công ty</h1>

<h2>1. Thống kê tổng quan</h2>
<p>Tổng số công ty: {{ $totalCompanies }}</p>
<p>Công ty đã được phê duyệt: {{ $approvedCompanies }}</p>
<p>Công ty nổi bật: {{ $hotCompanies }}</p>
<p>Công ty vừa nổi bật vừa được phê duyệt: {{ $hotAndApprovedCompanies }}</p>

<h2>2. Thống kê công ty theo vị trí địa lý</h2>
<h3>Theo quốc gia</h3>
<table>
    <tr>
        <th>Quốc gia</th>
        <th>Số lượng công ty</th>
    </tr>
    @foreach($companiesByCountry as $country)
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
        <th>Số lượng công ty</th>
    </tr>
    @foreach($companiesByCity as $city)
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
        <th>Số lượng công ty</th>
    </tr>
    @foreach($companiesByDistrict as $district)
        <tr>
            <td>{{ $district->district_name }}</td>
            <td>{{ $district->count }}</td>
        </tr>
    @endforeach
</table>

<h2>3. Thống kê công ty theo quy mô</h2>
<table>
    <tr>
        <th>Quy mô công ty</th>
        <th>Số lượng công ty</th>
    </tr>
    @foreach($companiesBySize as $size)
        <tr>
            <td>{{ $size->size_name }}</td>
            <td>{{ $size->count }}</td>
        </tr>
    @endforeach
</table>

<h2>4. Thống kê công ty theo loại hình</h2>
<table>
    <tr>
        <th>Loại hình công ty</th>
        <th>Số lượng công ty</th>
    </tr>
    @foreach($companiesByType as $type)
        <tr>
            <td>{{ $type->type_name }}</td>
            <td>{{ $type->count }}</td>
        </tr>
    @endforeach
</table>

</body>
</html>
