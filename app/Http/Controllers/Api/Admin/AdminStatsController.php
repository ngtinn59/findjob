<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use App\Utillities\Constant;
use Illuminate\Http\Request;

class AdminStatsController extends Controller
{
    public function index()
    {
        // Đếm tổng số người dùng
        $totalUsers = User::count();

        // Đếm số tin đăng công việc đang hoạt động
        $activeJobs = Job::where('status', '3')->count();

        // Đếm tổng số công ty
        $totalCompanies = Company::count();

        // Thống kê số lượng công việc theo ngày
        $jobsByDate = Job::select(\DB::raw('DATE(created_at) as date'), \DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc') // Sắp xếp theo ngày mới nhất
            ->where('status', '3')
            ->get();

        $usersEmployer = User::where('account_type', Constant::user_level_employer)->count();
        $usersDeveloper = User::where('account_type', Constant::user_level_developer)->count();
        $jobsByLocation = Job::select('cities.name as city_name', \DB::raw('COUNT(*) as count'))
            ->join('cities', 'jobs.city_id', '=', 'cities.id') // Thực hiện join với bảng cities
            ->groupBy('jobs.city_id', 'cities.name') // Nhóm theo city_id và tên thành phố
            ->get();


        // Chuẩn bị dữ liệu phản hồi
        $data = [
            'total_users' => $totalUsers,
            'active_jobs' => $activeJobs,
            'total_companies' => $totalCompanies,
            'jobs_by_date' => $jobsByDate,
            'total_employer' => $usersEmployer,
            'usersEmployer' => $usersDeveloper,
            'jobsByLocation' => $jobsByLocation,

        ];

        // Trả về JSON response
        return response()->json([
            'message' => 'Lấy danh sách thống kê thành công',
            'data' => $data
        ], 200); // Status code 200 OK
    }

}
