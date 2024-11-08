<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use App\Utillities\Constant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AdminStatsController extends Controller
{
    public function index()
    {
        // Đếm tổng số người dùng
        $totalUsers = User::count();

        // Đếm số tin đăng công việc đang hoạt động
        $activeJobs = Job::where('status', '1')->count();

        // Đếm tổng số công ty
        $totalCompanies = Company::count();

        // Thống kê số lượng công việc theo ngày
        $jobsByDate = Job::select(\DB::raw('DATE(created_at) as date'), \DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc') // Sắp xếp theo ngày mới nhất
            ->where('status', '1')
            ->get();

        $usersEmployer = User::where('account_type', Constant::user_level_employer)->count();
        $usersDeveloper = User::where('account_type', Constant::user_level_developer)->count();
        $jobsByLocation = Job::select('cities.name as city_name', \DB::raw('COUNT(*) as count'))
            ->join('cities', 'jobs.city_id', '=', 'cities.id') // Thực hiện join với bảng cities
            ->groupBy('jobs.city_id', 'cities.name')
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
            'data' => $data,
            'status_code' => 200,
        ], 200); // Status code 200 OK
    }

    public function generateSalaryReport()
    {
        // Lấy dữ liệu báo cáo lương, bổ sung thông tin về tháng
        $salaryReport = Job::select('profession_id', 'employment_type_id', 'desired_level_id', 'city_id')
            ->selectRaw('MONTH(created_at) as month')
            ->selectRaw('AVG((salary_from + salary_to) / 2) as avg_salary')
            ->selectRaw('MIN(salary_from) as min_salary')
            ->selectRaw('MAX(salary_to) as max_salary')
            ->selectRaw('COUNT(*) as job_count')
            ->groupBy('profession_id', 'employment_type_id', 'desired_level_id', 'city_id')
            ->groupByRaw('MONTH(created_at)')
            ->with(['profession', 'employmentType', 'desiredLevel', 'city']) // Liên kết các bảng liên quan
            ->orderBy('profession_id')
            ->orderByRaw('MONTH(created_at)')
            ->get();

        // Chuẩn bị dữ liệu để hiển thị trong PDF
        $data = [
            'salary_report' => $salaryReport,
        ];

        $pdf = PDF::loadView('reports.salary_report', $data);
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', true);
        $pdf->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->set_option("defaultFont", "DejaVu Sans");

        return $pdf->download('salary_report.pdf');
    }




}
