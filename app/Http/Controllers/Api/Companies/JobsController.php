<?php

namespace App\Http\Controllers\Api\Companies;

use App\Http\Controllers\Controller;
use App\Mail\JobApplied;
use App\Models\Company;
use App\Models\Job;
use App\Utillities\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class JobsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lấy người dùng hiện tại
        $user = auth()->user();

        // Kiểm tra xem người dùng có công ty hay không
        $company = $user->companies; // Lấy công ty đầu tiên, nếu có

        // Nếu người dùng không có công ty, trả về thông báo lỗi
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không có công ty.',
                'status_code' => 404
            ], 404); // 404 cho trường hợp không có công ty
        }

        // Lấy danh sách công việc của công ty
        $jobs = Job::where('company_id', $company->id)->paginate(10); // Thay đổi số lượng trang theo nhu cầu

        // Kiểm tra nếu có công việc
        if ($jobs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Không có công việc nào cho công ty này.',
                'status_code' => 404
            ], 404); // 404 nếu không có công việc
        }

        // Dữ liệu công việc
        $jobsData = $jobs->map(function ($job) {
            $applicationsCount = $job->applicants()->count(); // Số lượng người ứng tuyển
            $viewsCount = $job->views; // Số lượng người xem (giả định có trường views trong bảng jobs)

            return [
                'job_id' => $job->id,
                'title' => $job->title,
                'featured' => ($job->featured == 1) ? 'Tuyển gấp' : 'Không có',
                'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                'last_date' => $job->last_date,
                'status' => ($job->status == 3) ? 'Đã duyệt' : 'Chưa duyệt',
                'applications_count' => $applicationsCount, // Số lượng người ứng tuyển
                'views_count' => $viewsCount, // Số lượng người xem
            ];
        });

        // Trả về dữ liệu dưới dạng JSON
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $jobsData,
            'links' => [
                'first' => $jobs->url(1),
                'last' => $jobs->url($jobs->lastPage()),
                'prev' => $jobs->previousPageUrl(),
                'next' => $jobs->nextPageUrl(),
            ],
            'status_code' => 200
        ]);
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $company = $user->companies->id ?? null; // Check if the user has a company
        $statusJob = Constant::user_status_inactive; // Initial status for a job post (inactive)

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy công ty cho người dùng. Vui lòng kiểm tra lại thông tin công ty.',
                'status_code' => 404
            ], 404);
        }

        // Validation rules
        $validator = Validator::make($request->all(), [
            'profession_id' => 'required|exists:professions,id',
            'desired_level_id' => 'required|exists:desired_levels,id',
            'employment_type_id' => 'required|exists:employment_types,id',
            'experience_level_id' => 'required|exists:experience_levels,id',
            'education_level_id' => 'required|exists:education_levels,id',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'title' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'salary_from' => 'nullable|integer|min:0',
            'salary_to' => 'nullable|integer|min:0|gte:salary_from',
            'work_address' => 'required|string|max:255',
            'last_date' => 'required|date|after:today',
            'description' => 'nullable|string',
            'skill_experience' => 'nullable|string',
            'benefits' => 'nullable|string',
            'workplace_id' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'featured' => 'nullable|boolean',
        ], [
            'profession_id.required' => 'Nghề nghiệp là bắt buộc.',
            'profession_id.exists' => 'Nghề nghiệp không tồn tại.',
            'desired_level_id.required' => 'Cấp độ mong muốn là bắt buộc.',
            'employment_type_id.required' => 'Hình thức làm việc là bắt buộc.',
            'experience_level_id.required' => 'Cấp độ kinh nghiệm là bắt buộc.',
            'education_level_id.required' => 'Cấp độ học vấn là bắt buộc.',
            'country_id.required' => 'Quốc gia là bắt buộc.',
            'city_id.required' => 'Thành phố là bắt buộc.',
            'city_id.exists' => 'Thành phố không tồn tại.',
            'district_id.required' => 'Quận/huyện là bắt buộc.',
            'district_id.exists' => 'Quận/huyện không tồn tại.',
            'title.required' => 'Tiêu đề là bắt buộc.',
            'quantity.required' => 'Số lượng là bắt buộc.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'salary_from.integer' => 'Mức lương từ phải là số nguyên.',
            'salary_to.integer' => 'Mức lương đến phải là số nguyên.',
            'salary_to.gte' => 'Mức lương đến phải lớn hơn hoặc bằng mức lương từ.',
            'work_address.required' => 'Địa chỉ làm việc là bắt buộc.',
            'last_date.required' => 'Ngày hết hạn là bắt buộc.',
            'last_date.after' => 'Ngày hết hạn phải sau hôm nay.',
            'contact_name.required' => 'Tên liên hệ là bắt buộc.',
            'phone.required' => 'Số điện thoại là bắt buộc.',
            'phone.max' => 'Số điện thoại không được quá 15 ký tự.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'featured.boolean' => 'Trường này phải là true hoặc false.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực dữ liệu, vui lòng kiểm tra lại.',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        // Save the job in the database
        $validatedData = $validator->validated();
        $validatedData['users_id'] = $user->id;
        $validatedData['company_id'] = $company;
        $validatedData['status'] = $statusJob; // Job is initially inactive

        $job = Job::create($validatedData); // Assuming you have a Job model

        // Custom response after successful job creation
        return response()->json([
            'success' => true,
            'message' => 'Công việc đã được khởi tạo thành công. Vui lòng đợi người kiểm duyệt xác nhận trước khi hiển thị công khai.',
            'data' => [
                'job_id' => $job->id,
                'title' => $job->title,
                'featured' => ($job->featured == 1) ? 'Tuyển gấp' : 'Không có',
                'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                'last_date' => $job->last_date,
                'status' => $statusText = ($job->status == 3) ? 'Đã duyệt' : (($job->status == 4) ? 'Chưa duyệt' : 'Trạng thái không xác định'),
            ],
            'status_code' => 201
        ], 201);
    }


    /**
     * Display the specified resource.
     */



    public function show(Job $job)
    {
        // Lấy người dùng hiện tại
        $user = auth()->user();

        // Kiểm tra xem người dùng có công ty hay không
        $company = $user->companies; // Lấy công ty đầu tiên, nếu có
        // Kiểm tra xem công việc này có thuộc về công ty của người dùng hay không
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không có công ty.',
                'status_code' => 404
            ], 404); // 404 cho trường hợp không có công ty
        }

        if ($job->company_id != $company->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem công việc này.',
                'status_code' => 403
            ], 403); // 403 là mã lỗi "Forbidden"
        }

        // Trả về dữ liệu chi tiết của công việc
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => [
                'job_id' => $job->id,
                'title' => $job->title,
                'profession' => $job->profession->name,
                'desired_level' => $job->desiredLevel->name,
                'workplace' => $job->workPlace->name,
                'employment_type' => $job->employmentType->name,
                'quantity' => $job->quantity,
                'salary_from' => $job->salary_from,
                'salary_to' => $job->salary_to,
                'education_level' => $job->educationLevel->name,
                'last_date' => $job->last_date,
                'description' => $job->description,
                'skill_experience' => $job->skill_experience,
                'benefits' => $job->benefits,
                'country' => $job->country->name,
                'city' => $job->city->name,
                'district' => $job->district->name,
                'work_address' => $job->work_address,
                'latitude' => $job->latitude,
                'longitude' => $job->longitude,
                'contact_name' => $job->contact_name,
                'phone' => $job->phone,
                'email' => $job->email,
                'featured' => ($job->featured == 1) ? 'Tuyển gấp' : 'Không có',
            ],
            'status_code' => 200
        ]);
    }


    public function update(Request $request, Job $job)
    {
        // Lấy người dùng hiện tại
        $user = auth()->user();

        // Kiểm tra xem người dùng có công ty hay không
        $company = $user->companies; // Lấy công ty đầu tiên, nếu có
        // Kiểm tra xem công việc này có thuộc về công ty của người dùng hay không
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không có công ty.',
                'status_code' => 404
            ], 404); // 404 cho trường hợp không có công ty
        }

        if ($job->company_id != $company->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem công việc này.',
                'status_code' => 403
            ], 403); // 403 là mã lỗi "Forbidden"
        }

        // Validation rules
        $validator = Validator::make($request->all(), [
            'profession_id' => 'nullable|exists:professions,id',
            'desired_level_id' => 'nullable|exists:desired_levels,id',
            'employment_type_id' => 'nullable|exists:employment_types,id',
            'experience_level_id' => 'nullable|exists:experience_levels,id',
            'education_level_id' => 'nullable|exists:education_levels,id',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'title' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'salary_from' => 'nullable|integer|min:0',
            'salary_to' => 'nullable|integer|min:0|gte:salary_from',
            'work_address' => 'nullable|string|max:255',
            'last_date' => 'nullable|date|after:today',
            'description' => 'nullable|string',
            'skill_experience' => 'nullable|string',
            'benefits' => 'nullable|string',
            'workplace_id' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:255',
            'featured' => 'nullable|boolean',
        ], [
            'profession_id.required' => 'Nghề nghiệp là bắt buộc.',
            'profession_id.exists' => 'Nghề nghiệp không tồn tại.',
            'desired_level_id.required' => 'Cấp độ mong muốn là bắt buộc.',
            'employment_type_id.required' => 'Hình thức làm việc là bắt buộc.',
            'experience_level_id.required' => 'Cấp độ kinh nghiệm là bắt buộc.',
            'education_level_id.required' => 'Cấp độ học vấn là bắt buộc.',
            'country_id.required' => 'Quốc gia là bắt buộc.',
            'city_id.required' => 'Thành phố là bắt buộc.',
            'city_id.exists' => 'Thành phố không tồn tại.',
            'district_id.required' => 'Quận/huyện là bắt buộc.',
            'district_id.exists' => 'Quận/huyện không tồn tại.',
            'title.required' => 'Tiêu đề là bắt buộc.',
            'quantity.required' => 'Số lượng là bắt buộc.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'salary_from.integer' => 'Mức lương từ phải là số nguyên.',
            'salary_to.integer' => 'Mức lương đến phải là số nguyên.',
            'salary_to.gte' => 'Mức lương đến phải lớn hơn hoặc bằng mức lương từ.',
            'work_address.required' => 'Địa chỉ làm việc là bắt buộc.',
            'last_date.required' => 'Ngày hết hạn là bắt buộc.',
            'last_date.after' => 'Ngày hết hạn phải sau hôm nay.',
            'contact_name.required' => 'Tên liên hệ là bắt buộc.',
            'phone.required' => 'Số điện thoại là bắt buộc.',
            'phone.max' => 'Số điện thoại không được quá 15 ký tự.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'featured.boolean' => 'Trường này phải là true hoặc false.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực dữ liệu, vui lòng kiểm tra lại.',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        // Cập nhật công việc với dữ liệu đã xác thực
        $validatedData = $validator->validated();

        $job->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Công việc đã được cập nhật thành công.',
            'data' => [
                'job_id' => $job->id,
                'title' => $job->title,
                'featured' => ($job->featured == 1) ? 'Tuyển gấp' : 'Không có',
                'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                'last_date' => $job->last_date,
                'status' => ($job->status == 3) ? 'Đã duyệt' : 'Chưa duyệt',
            ],
            'status_code' => 200
        ], 200);
    }


    public function destroy(Request $request, Job $job)
    {
        // Lấy người dùng hiện tại
        $user = auth()->user();

        // Kiểm tra xem người dùng có công ty hay không
        $company = $user->companies; // Lấy công ty đầu tiên, nếu có
        // Kiểm tra xem công việc này có thuộc về công ty của người dùng hay không
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không có công ty.',
                'status_code' => 404
            ], 404); // 404 cho trường hợp không có công ty
        }

        if ($job->company_id != $company->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xem công việc này.',
                'status_code' => 403
            ], 403); // 403 là mã lỗi "Forbidden"
        }

        // Xóa công việc
        $job->delete();

        // Trả về thông báo thành công
        return response()->json([
            'success' => true,
            'message' => 'Job đã được xóa thành công.',
            'status_code' => 200
        ], 200);
    }


    public function  indexShow()
    {

    }

    public function showJob(Job $job)
    {
        // Tăng lượt xem mỗi khi công việc được xem
        $job->increment('views');

        // Tải thêm thông tin liên quan (nếu cần)
        $jobDetails = $job->load(['company', 'profession', 'employmentType', 'experienceLevel', 'educationLevel', 'city', 'district', 'country', 'desiredLevel', 'workplace']);

        // Lấy danh sách công việc liên quan
        // Lấy danh sách công việc liên quan với hệ thống chấm điểm
        $relatedJobs = $this->getRelatedJobs($job);

        $relatedJobsData = $relatedJobs->map(function($relatedJob) {
            return [
                'id' => $relatedJob['job']->id,
                'title' => $relatedJob['job']->title,
                'city' => $relatedJob['job']->city->name,
                'company' => $relatedJob['job']->company->company_name,
                'last_date' =>$relatedJob['job']->last_date,
                'score' => $relatedJob['score'],
            ];
        });

        $dataRespone = [
            'id' => $job->id,
            'company' => [
                'logo' => $job->Company->logo,
                'name' => $job->Company->company_name,
                'size' => $job->Company->companysize->name,
            ],
            'job' => [
                'title' => $job->title,
                'last_date' => $job->last_date,
                'views' => $job->views,
                'created_at' => \Carbon\Carbon::parse($job->created_at)->format('Y-m-d'),
                'experience_level' => $job->experienceLevel->name,
                'salary' => [
                    'salary_from' => $job->salary_from,
                    'salary_to' => $job->salary_to
                ],
                'desired_level' => $job->desiredLevel->name,
                'employment_type' => $job->employmentType->name,
                'profession' => $job->profession->name,
                'workplace' => $job->workPlace->name,
                'education_level' => $job->educationLevel->name,
                'quantity' => $job->quantity,
                'city' => $job->city->name,
                'description' => $job->description,
                'skill_experience' => $job->skill_experience,
                'benefits' => $job->benefits,
                'latitude' => $job->latitude,
                'longitude' => $job->longitude,
                'contact_name' => $job->contact_name,
                'email' => $job->email,
                'phone' => $job->phone,
                'work_address' => $job->work_address,
            ],
            'related_jobs' => $relatedJobsData,
        ];

        // Trả về thông tin công việc dưới dạng JSON
        return response()->json([
            'success' => true,
            'data' => $dataRespone,
        ]);
    }

    public function getRelatedJobs(Job $job)
    {
        // Tìm các công việc khác để so sánh
        $jobs = Job::where('id', '!=', $job->id)->get();

        // Tạo danh sách công việc với điểm số
        $relatedJobs = $jobs->map(function ($relatedJob) use ($job) {
            $score = 0;

            // Chấm điểm dựa trên ngành nghề
            if ($relatedJob->profession_id == $job->profession_id) {
                $score += 40;  // Ví dụ: ngành nghề giống nhau được 40 điểm
            }

            // Chấm điểm dựa trên loại hình công việc
            if ($relatedJob->employment_type_id == $job->employment_type_id) {
                $score += 30;  // Loại hình công việc giống nhau được 30 điểm
            }

            // Chấm điểm dựa trên kinh nghiệm yêu cầu
            if ($relatedJob->experience_level_id == $job->experience_level_id) {
                $score += 20;  // Kinh nghiệm tương đương được 20 điểm
            }

            // Chấm điểm dựa trên thành phố
            if ($relatedJob->city_id == $job->city_id) {
                $score += 10;  // Cùng thành phố được 10 điểm
            }

            // Trả về job cùng với số điểm
            return [
                'job' => $relatedJob,
                'score' => $score,
            ];
        });

        // Sắp xếp danh sách theo điểm từ cao đến thấp
        $sortedJobs = $relatedJobs->sortByDesc('score')->take(5); // Lấy top 5 công việc liên quan nhất

        return $sortedJobs;
    }


    public function search(Request $request)
    {
        // Nhận các tham số từ request
        $keyword = $request->input('keyword');
        $professionId = $request->input('profession_id');
        $cityId = $request->input('city_id');

        // Bắt đầu truy vấn
        $jobs = Job::query();

        // Tìm kiếm theo từ khóa
        if ($keyword) {
            $jobs->where(function ($query) use ($keyword) {
                $query->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%');
            });
        }

        // Lọc theo ngành nghề
        if ($professionId) {
            $jobs->where('profession_id', $professionId);
        }

        // Lọc theo thành phố
        if ($cityId) {
            $jobs->where('city_id', $cityId);
        }

        // Thực hiện truy vấn và phân trang kết quả
        $results = $jobs->with(['company', 'city', 'profession'])
            ->paginate(10); // Phân trang 10 công việc mỗi trang

        // Lấy công việc đề xuất
        $suggestedJobs = $this->getSuggestedJobs();

        // Trả về kết quả dưới dạng JSON
        return response()->json([
            'success' => true,
            'data' => [
                'searched_jobs' => $results->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'featured' => ($job->featured == 1) ? 'Tuyển gấp' : 'Không có',
                        'is_hot' => ($job->views > 100) ? 'HOT' : 'Không hot', // Kiểm tra lượt xem
                        'company' => $job->company->company_name,
                        'salary' => [
                            'salary_from' => $job->salary_from,
                            'salary_to' => $job->salary_to
                        ],
                        'city' => $job->city->name,
                        'last_date' => \Carbon\Carbon::parse($job->last_date)->format('d-m-Y'),
                    ];
                }),
                'suggested_jobs' => $suggestedJobs,
            ],
            'current_page' => $results->currentPage(),
            'last_page' => $results->lastPage(),
            'total' => $results->total(),
        ]);
    }

    private function getSuggestedJobs()
    {
        // Lấy người dùng hiện tại
        $user = auth()->user();

        // Lấy profile của người dùng
        $profile = $user->profile;


        if (!$profile) {
            return []; // Không có đề xuất nếu không có profile
        }

        // Lấy objectives liên quan đến profile
        $objectives = $profile->objectives;

        // Kiểm tra xem có objectives không
        if ($objectives->isEmpty()) {
            return []; // Không có đề xuất nếu không có objectives
        }

        // Tìm kiếm các công việc dựa trên các tiêu chí trong objectives
        $jobs = Job::query();

        foreach ($objectives as $objective) {
            // Lọc theo vị trí mong muốn
            if ($objective->desired_position) {
                $jobs->orWhere('title', 'LIKE', '%' . $objective->desired_position . '%');
            }

            // Lọc theo ngành nghề
            if ($objective->profession_id) {
                $jobs->orWhere('profession_id', $objective->profession_id);
            }

            // Lọc theo cấp độ giáo dục
            if ($objective->education_level_id) {
                $jobs->orWhere('education_level_id', $objective->education_level_id);
            }

            // Lọc theo loại hình làm việc
            if ($objective->employment_type_id) {
                $jobs->orWhere('employment_type_id', $objective->employment_type_id);
            }

            // Lọc theo khu vực
            if ($objective->city_id) {
                $jobs->orWhere('city_id', $objective->city_id);
            }
        }

        // Lấy danh sách công việc đề xuất
        return $jobs->with(['company', 'city', 'profession'])->take(5)->get()->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'featured' => ($job->featured == 1) ? 'Tuyển gấp' : 'Không có',
                'is_hot' => ($job->views > 100) ? 'HOT' : 'Không hot', // Kiểm tra lượt xem

                'company' => $job->company->company_name,
                'logo' => $job->company->logo,
                'salary' => [
                    'salary_from' => $job->salary_from,
                    'salary_to' => $job->salary_to
                ],
                'city' => $job->city->name,
                'last_date' => \Carbon\Carbon::parse($job->last_date)->format('d-m-Y'),
            ];
        });
    }

    public function getNotifications()
    {
        $user = auth()->user();
        $companyId =  $user->companies->id;
        // Tìm công ty dựa trên ID
        $company = Company::find($companyId);
        if (!$company) {
            return response()->json(['message' => 'Công ty không tồn tại.'], 404);
        }

        // Lấy tất cả thông báo cho công ty
        $notifications = $company->notifications; // Hoặc sử dụng phương thức `notifications` nếu có

        // Nếu không có thông báo nào
        if ($notifications->isEmpty()) {
            return response()->json(['message' => 'Không có thông báo nào.'], 204);
        }

        $customData = $notifications->map(function ($notification) {
            return [
                'job_id' => $notification->data['job_id'],
                'job_title' => $notification->data['job_title'],
                'user_id' => $notification->data['user_id'],
                'user_name' => $notification->data['user_name'],
                'applicant_name' => $notification->data['applicant_name'],
                'applicant_phone' => $notification->data['applicant_phone'],
                'applicant_email' => $notification->data['applicant_email'],

                'notification_id' => $notification->id,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Lấy thông báo thành công',
            'data' => $customData,
            'status_code' => 200
        ]);
    }


    public function markAsRead(Request $request)
    {
        $user = auth()->user();
        $companyId =  $user->companies->id;

        // Tìm công ty dựa trên ID
        $company = Company::find($companyId);
        if (!$company) {
            return response()->json(['message' => 'Công ty không tồn tại.'], 404);
        }

        // Lấy thông báo cụ thể nếu có ID được gửi lên
        $notificationId = $request->input('notification_id');

        if ($notificationId) {
            // Tìm thông báo dựa trên ID
            $notification = $company->notifications()->where('id', $notificationId)->first();

            if (!$notification) {
                return response()->json(['message' => 'Thông báo không tồn tại.'], 404);
            }

            // Đánh dấu thông báo này là đã đọc
            $notification->markAsRead();
        } else {
            // Đánh dấu tất cả thông báo là đã đọc nếu không có ID nào được gửi lên
            $company->notifications()->whereNull('read_at')->get()->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đánh dấu thông báo đã đọc thành công',
            'status_code' => 200
        ]);
    }


    public function applicant()
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $appliedJobs = $user->jobs()->withPivot('status')->get();

        $formattedJobs = $appliedJobs->map(function ($job) {
            $company = $job->company()->first();
            $city = $job->jobcity()->first();
            $lastDate = Carbon::createFromFormat('Y-m-d', $job->last_date);
            $daysRemaining = $lastDate->diffInDays(Carbon::now());

            // Sử dụng status từ $statusMap
            $statusMap = [
                'pending'      => 'Chờ xác nhận',
                'contacted'    => 'Đã liên hệ',
                'test_round'   => 'Vòng test',
                'interview'    => 'Vòng phỏng vấn',
                'hired'        => 'Trúng tuyển',
                'not_selected' => 'Không trúng tuyển'
            ];

            $status = $job->pivot->status;
            $formattedStatus = isset($statusMap[$status]) ? $statusMap[$status] : 'Trạng thái không xác định'; // Nếu trạng thái không có trong map thì hiển thị thông báo

            return [
                'id' => $job->id,
                'company' => $company ? $company->company_name : null,
                'logo' => $company ? $company->logo : null, // Kiểm tra xem công ty có tồn tại không trước khi truy cập trường name
                'title' => $job->title,
                'city' => $city ? $city->name : null, // Kiểm tra xem thành phố có tồn tại không trước khi truy cập trường name
                'salary_to' => $job->salary_to,
                'salary_from' => $job->salary_from,
                'status' => $formattedStatus, // Sử dụng trạng thái đã được định dạng
                'last_date' => $job->last_date,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $formattedJobs,
            'status_code' => 200
        ], 200);
    }

    public function indexUrgent(Request $request)
    {


        $jobs = Job::where('featured', 1);


        // Thực hiện truy vấn và phân trang kết quả
        $results = $jobs->with(['company', 'city', 'profession'])
            ->paginate(10); // Phân trang 10 công việc mỗi trang

        // Trả về kết quả dưới dạng JSON
        return response()->json([
            'success' => true,
            'data' => [
                'urgent_jobs' => $results->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'featured' => 'Tuyển gấp', // Luôn là tuyển gấp do 'featured' = 1
                        'is_hot' => ($job->views > 100) ? 'HOT' : 'Không hot', // Kiểm tra lượt xem
                        'company' => $job->company->company_name,
                        'salary' => [
                            'salary_from' => $job->salary_from,
                            'salary_to' => $job->salary_to
                        ],
                        'city' => $job->city->name,
                        'last_date' => \Carbon\Carbon::parse($job->last_date)->format('d-m-Y'),
                    ];
                }),
            ],
            'current_page' => $results->currentPage(),
            'last_page' => $results->lastPage(),
            'total' => $results->total(),
        ]);
    }




}
