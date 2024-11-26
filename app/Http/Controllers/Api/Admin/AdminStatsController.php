<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanySize;
use App\Models\DesiredLevel;
use App\Models\Job;
use App\Models\Objective;
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
            'Job_Seekers' => $usersDeveloper,
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
        // 1. Tổng quan hệ thống
        $totalUsers = User::count();
        $totalCompanies = Company::count();
        $activeJobs = Job::where('status', '1')->count();

        // 2. Thống kê người dùng trong tháng này
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();
        $usersEmployer = User::where('account_type', Constant::user_level_employer)->count();
        $usersDeveloper = User::where('account_type', Constant::user_level_developer)->count();

        // 3. Thống kê công việc trong tháng này
        $newJobsThisMonth = Job::whereMonth('created_at', now()->month)->count();
        $inactiveJobs = Job::where('status', '0')->count();

        // 4. Thống kê công việc theo vị trí địa lý
        $jobsByLocation = Job::select('cities.name as city_name', \DB::raw('COUNT(*) as count'))
            ->join('cities', 'jobs.city_id', '=', 'cities.id')
            ->groupBy('jobs.city_id', 'cities.name')
            ->get();

        // 5. Thống kê mức lương theo ngành nghề
        $salaryStats = Job::select('profession_id')
            ->selectRaw('AVG((salary_from + salary_to) / 2) as avg_salary')
            ->selectRaw('MIN(salary_from) as min_salary')
            ->selectRaw('MAX(salary_to) as max_salary')
            ->selectRaw('COUNT(*) as job_count')
            ->groupBy('profession_id')
            ->with(['profession']) // Load bảng ngành nghề
            ->get();

        // 6. Tỷ lệ tương tác của người dùng
        $applicationsCount = \DB::table('job_user')->count();
        $avgApplicationsPerUser = $applicationsCount / max($totalUsers, 1);

        // Các thống kê khác
        $jobsByDesiredLevel = Job::select('desired_levels.name as desired_level', \DB::raw('COUNT(*) as count'))
            ->join('desired_levels', 'jobs.desired_level_id', '=', 'desired_levels.id')
            ->groupBy('jobs.desired_level_id', 'desired_levels.name')
            ->get();

        $jobsByEmploymentType = Job::select('employment_types.name as employment_type', \DB::raw('COUNT(*) as count'))
            ->join('employment_types', 'jobs.employment_type_id', '=', 'employment_types.id')
            ->groupBy('jobs.employment_type_id', 'employment_types.name')
            ->get();

        $jobsByExperienceLevel = Job::select('experience_levels.name as experience_level', \DB::raw('COUNT(*) as count'))
            ->join('experience_levels', 'jobs.experience_level_id', '=', 'experience_levels.id')
            ->groupBy('jobs.experience_level_id', 'experience_levels.name')
            ->get();

        $jobsByEducationLevel = Job::select('education_levels.name as education_level', \DB::raw('COUNT(*) as count'))
            ->join('education_levels', 'jobs.education_level_id', '=', 'education_levels.id')
            ->groupBy('jobs.education_level_id', 'education_levels.name')
            ->get();

        $jobsByCountry = Job::select('countries.name as country', \DB::raw('COUNT(*) as count'))
            ->join('countries', 'jobs.country_id', '=', 'countries.id')
            ->groupBy('jobs.country_id', 'countries.name')
            ->get();

        $jobsByWorkplace = Job::select('workplaces.name as workplace', \DB::raw('COUNT(*) as count'))
            ->join('workplaces', 'jobs.workplace_id', '=', 'workplaces.id')
            ->groupBy('jobs.workplace_id', 'workplaces.name')
            ->get();

        // Chuẩn bị dữ liệu phản hồi
        $data = [
            'totalUsers' => $totalUsers,
            'totalCompanies' => $totalCompanies,
            'activeJobs' => $activeJobs,
            'newUsersThisMonth' => $newUsersThisMonth,
            'usersEmployer' => $usersEmployer,
            'usersDeveloper' => $usersDeveloper,
            'newJobsThisMonth' => $newJobsThisMonth,
            'inactiveJobs' => $inactiveJobs,
            'jobsByLocation' => $jobsByLocation,
            'salaryStats' => $salaryStats,
            'applicationsCount' => $applicationsCount,
            'avgApplicationsPerUser' => $avgApplicationsPerUser,
            'jobsByDesiredLevel' => $jobsByDesiredLevel,
            'jobsByEmploymentType' => $jobsByEmploymentType,
            'jobsByExperienceLevel' => $jobsByExperienceLevel,
            'jobsByEducationLevel' => $jobsByEducationLevel,
            'jobsByCountry' => $jobsByCountry,
            'jobsByWorkplace' => $jobsByWorkplace,
        ];

        $pdf = PDF::loadView('reports.salary_report', $data);
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', true);
        $pdf->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->set_option("defaultFont", "DejaVu Sans");

        return $pdf->download('salary_report.pdf');
    }

    public function generateCompanyReport()
    {
        // 1. Thống kê tổng quan
        $totalCompanies = Company::count();
        $approvedCompanies = Company::where('approved', true)->count();
        $hotCompanies = Company::where('is_hot', true)->count();

        // 2. Thống kê công ty theo vị trí địa lý (quốc gia, thành phố, quận)
        $companiesByCountry = Company::select('countries.name as country_name', \DB::raw('COUNT(*) as count'))
            ->join('countries', 'companies.country_id', '=', 'countries.id')
            ->groupBy('companies.country_id', 'countries.name')
            ->get();

        $companiesByCity = Company::select('cities.name as city_name', \DB::raw('COUNT(*) as count'))
            ->join('cities', 'companies.city_id', '=', 'cities.id')
            ->groupBy('companies.city_id', 'cities.name')
            ->get();

        $companiesByDistrict = Company::select('districts.name as district_name', \DB::raw('COUNT(*) as count'))
            ->join('districts', 'companies.district_id', '=', 'districts.id')
            ->groupBy('companies.district_id', 'districts.name')
            ->get();

        // 3. Thống kê công ty theo quy mô
        $companiesBySize = Company::select('company_sizes.name as size_name', \DB::raw('COUNT(*) as count'))
            ->join('company_sizes', 'companies.company_size_id', '=', 'company_sizes.id')
            ->groupBy('companies.company_size_id', 'company_sizes.name')
            ->get();

        // 4. Thống kê công ty theo loại hình
        $companiesByType = Company::select('company_types.name as type_name', \DB::raw('COUNT(*) as count'))
            ->join('company_types', 'companies.company_type_id', '=', 'company_types.id')
            ->groupBy('companies.company_type_id', 'company_types.name')
            ->get();

        // 5. Thống kê công ty theo trạng thái "hot" và "approved"
        $hotAndApprovedCompanies = Company::where('is_hot', true)
            ->where('approved', true)
            ->count();

        // Chuẩn bị dữ liệu để đưa vào view
        $data = [
            'totalCompanies' => $totalCompanies,
            'approvedCompanies' => $approvedCompanies,
            'hotCompanies' => $hotCompanies,
            'companiesByCountry' => $companiesByCountry,
            'companiesByCity' => $companiesByCity,
            'companiesByDistrict' => $companiesByDistrict,
            'companiesBySize' => $companiesBySize,
            'companiesByType' => $companiesByType,
            'hotAndApprovedCompanies' => $hotAndApprovedCompanies,
        ];

        // Tạo file PDF từ view `reports.company_report`
        $pdf = PDF::loadView('reports.company_report', $data);
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', true);
        $pdf->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->set_option("defaultFont", "DejaVu Sans");

        return $pdf->download('company_report.pdf');
    }

    public function generateObjectiveStats()
    {
        // Lấy dữ liệu thống kê từ database
        $objectivesByPosition = Objective::select('desired_levels.name as desired_level', \DB::raw('COUNT(*) as count'))
            ->join('desired_levels', 'objectives.desired_level_id', '=', 'desired_levels.id')
            ->groupBy('objectives.desired_level_id', 'desired_levels.name')
            ->get();

        $objectivesBySalary = Objective::select(\DB::raw('CONCAT(salary_from, " - ", salary_to) as salary_range'), \DB::raw('COUNT(*) as count'))
            ->groupBy('salary_from', 'salary_to')
            ->get();

        $objectivesByEducationLevel = Objective::select('education_levels.name as education_level', \DB::raw('COUNT(*) as count'))
            ->join('education_levels', 'objectives.education_level_id', '=', 'education_levels.id')
            ->groupBy('objectives.education_level_id', 'education_levels.name')
            ->get();

        $objectivesByProfession = Objective::select('professions.name as profession', \DB::raw('COUNT(*) as count'))
            ->join('professions', 'objectives.profession_id', '=', 'professions.id')
            ->groupBy('objectives.profession_id', 'professions.name')
            ->get();

        $objectivesByEmploymentType = Objective::select('employment_types.name as employment_type', \DB::raw('COUNT(*) as count'))
            ->join('employment_types', 'objectives.employment_type_id', '=', 'employment_types.id')
            ->groupBy('objectives.employment_type_id', 'employment_types.name')
            ->get();

        $objectivesByExperienceLevel = Objective::select('experience_levels.name as experience_level', \DB::raw('COUNT(*) as count'))
            ->join('experience_levels', 'objectives.experience_level_id', '=', 'experience_levels.id')
            ->groupBy('objectives.experience_level_id', 'experience_levels.name')
            ->get();

        $objectivesByCountry = Objective::select('countries.name as country_name', \DB::raw('COUNT(*) as count'))
            ->join('countries', 'objectives.country_id', '=', 'countries.id')
            ->groupBy('objectives.country_id', 'countries.name')
            ->get();

        $objectivesByCity = Objective::select('cities.name as city_name', \DB::raw('COUNT(*) as count'))
            ->join('cities', 'objectives.city_id', '=', 'cities.id')
            ->groupBy('objectives.city_id', 'cities.name')
            ->get();

        $objectivesByDistrict = Objective::select('districts.name as district_name', \DB::raw('COUNT(*) as count'))
            ->join('districts', 'objectives.district_id', '=', 'districts.id')
            ->groupBy('objectives.district_id', 'districts.name')
            ->get();

        // Chuẩn bị dữ liệu trả về
        $data = [
            'objectivesByPosition' => $objectivesByPosition,
            'objectivesBySalary' => $objectivesBySalary,
            'objectivesByEducationLevel' => $objectivesByEducationLevel,
            'objectivesByProfession' => $objectivesByProfession,
            'objectivesByEmploymentType' => $objectivesByEmploymentType,
            'objectivesByExperienceLevel' => $objectivesByExperienceLevel,
            'objectivesByCountry' => $objectivesByCountry,
            'objectivesByCity' => $objectivesByCity,
            'objectivesByDistrict' => $objectivesByDistrict        ];


        $pdf = PDF::loadView('reports.objective_report', $data);
        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);
        $pdf->getDomPDF()->getOptions()->set('isRemoteEnabled', true);
        $pdf->setPaper('A4', 'portrait');
        $pdf->getDomPDF()->set_option("defaultFont", "DejaVu Sans");

        return $pdf->download('objective_report.pdf');
    }


}
