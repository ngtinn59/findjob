<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use App\Utillities\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminJobController extends Controller
{
    public function confirmJob($jobId)
    {
        $job = Job::find($jobId);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Công việc không tồn tại.',
            ], 404);
        }

        // Chỉ cho phép admin xác nhận


        $job->status = Constant::user_status_active; // Bật công việc
        $job->save();

        return response()->json([
            'success' => true,
            'message' => 'Công việc đã được xác nhận và kích hoạt.',
            'data' => $job,
            'status_code' => 200
        ]);
    }

    public function unconfirmJob($jobId)
    {
        $job = Job::find($jobId);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Công việc không tồn tại.',
            ], 404);
        }

        // Chỉ cho phép admin hủy xác nhận


        $job->status = Constant::user_status_inactive; // Tắt công việc
        $job->save();

        return response()->json([
            'success' => true,
            'message' => 'Công việc đã được hủy xác nhận và vô hiệu hóa.',
            'data' => $job,
            'status_code' => 200
        ]);
    }


    public function index()
    {
        // Lấy danh sách tất cả công việc từ tất cả các công ty
        $jobs = \App\Models\Job::paginate(10); // Phân trang, mỗi trang 10 công việc

        // Map dữ liệu công việc
        $jobsData = $jobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'company' => $job->company ? $job->company->company_name : null,
                'job_type' => $job->jobtype ? $job->jobtype->pluck('name')->toArray() : null,
                'job_city' => $job->jobcity ? $job->jobcity->pluck('name')->toArray() : null,
                'salary_from' => $job->salary_from,
                'salary_to' => $job->salary_to,
                'status' => $job->status === 3 ? 'Được xác minh' : 'Chưa được xác minh',
                'featured' => $job->featured == 1 ? 'Công việc nổi bật' : 'Công việc không nổi bật',
                'work_address' => $job->work_address,
                'description' => $job->description,
                'skills' => $job->skill->pluck('name')->toArray(),
                'skill_experience' => $job->skill_experience,
                'benefits' => $job->benefits,
                'last_date' => $job->last_date,
                'created_at' => $job->created_at->diffForHumans(),
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
        $statusJob = Constant::user_status_inactive;

        // Lấy công ty của người dùng
        $company = $user->companies->first() ?? null; // Use first() to get the first company

        if (!$company) {
            // Nếu không có công ty, tạo công ty mới
            $companyData = $request->only(['company_name', 'address', 'company_email', 'country_id', 'city_id']);
            $companyValidator = Validator::make($companyData, [
                'company_name' => 'required|string',
                'address' => 'required|string',
                'company_email' => 'required|email',
            ]);

            if ($companyValidator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thông tin công ty không hợp lệ.',
                    'errors' => $companyValidator->errors(),
                    'status_code' => 400
                ], 400);
            }

            // Tạo công ty mới
            $company = Company::create(array_merge($companyData, [
                'users_id' => $user->id,
            ]));
        }

        // Validation rules cho công việc
        $validator = Validator::make($request->all(), [
            'jobtype_id' => 'required|exists:job_types,id',
            'city_id' => 'required|exists:cities,id',
            'title' => 'required|string',
            'profession' => 'required|string',
            'position' => 'nullable|string',
            'experience_years' => 'nullable|integer',
            'work_address' => 'nullable|string',
            'employment_type' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'salary_from' => 'nullable|numeric',
            'salary_to' => 'nullable|numeric',
            'education_level' => 'nullable|string',
            'last_date' => 'required|date',
            'description' => 'required|string',
            'skill_experience' => 'nullable|string',
            'benefits' => 'nullable|string',
            'city' => 'nullable|string',
            'district' => 'nullable|string',
            'work_location' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'featured' => 'required|integer',
            'job_skills' => 'required|array',
            'job_skills.*.name' => 'required|string',
        ]);

        $messages = [
            'jobtype_id.required' => 'Trường loại công việc là bắt buộc.',
            'city_id.required' => 'Trường thành phố là bắt buộc.',
            'title.required' => 'Tiêu đề là bắt buộc.',
            'profession.required' => 'Nghề nghiệp là bắt buộc.',
            'last_date.required' => 'Ngày hết hạn là bắt buộc.',
            'description.required' => 'Mô tả là bắt buộc.',
            'job_skills.required' => 'Kỹ năng công việc là bắt buộc.',
            'job_skills.array' => 'Kỹ năng công việc phải là một mảng.',
            'job_skills.*.name.required' => 'Tên kỹ năng là bắt buộc.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
        ];

        $validator->setCustomMessages($messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực',
                'errors' => $validator->errors(),
                'status_code' => 400
            ], 400);
        }

        $validatedData = $validator->validated();
        $validatedData['users_id'] = $user->id;
        $validatedData['status'] = $statusJob;
        $jobSkillsData = $validatedData['job_skills'];
        unset($validatedData['job_skills']);

        try {
            \DB::beginTransaction();

            // Tạo công việc
            $job = Job::create($validatedData);

            // Tạo các kỹ năng cho công việc
            foreach ($jobSkillsData as $skillData) {
                $job->jobSkills()->create($skillData);
            }

            \DB::commit();

            $jobData = [
                'id' => $job->id,
                'title' => $job->title,
                'profession' => $job->profession,
                'position' => $job->position,
                'experience_years' => $job->experience_years,
                'work_address' => $job->work_address,
                'employment_type' => $job->employment_type,
                'quantity' => $job->quantity,
                'salary_from' => $job->salary_from,
                'salary_to' => $job->salary_to,
                'education_level' => $job->education_level,
                'status' => 'inactive',
                'featured' => $job->featured ? 'active' : 'inactive',
                'last_date' => $job->last_date,
                'description' => $job->description,
                'skill_experience' => $job->skill_experience,
                'benefits' => $job->benefits,
                'city' => $job->city,
                'district' => $job->district,
                'work_location' => $job->work_location,
                'latitude' => $job->latitude,
                'longitude' => $job->longitude,
                'contact_name' => $job->contact_name,
                'phone' => $job->phone,
                'email' => $job->email,
                'job_skills' => $job->jobSkills->pluck('name')->toArray(),
                'company' => [
                    'id' => $company->id,
                    'name' => $company->company_name // Add company name here
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Công việc đã được khởi tạo kèm theo công ty nếu có, vui lòng chờ người kiểm duyệt xác minh.',
                'data' => $jobData,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Tạo công việc không thành công.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
//    public function show(Job $job)
//    {
//        // Lấy các công việc đề xuất
//        $jobRecommendation = $this->jobRecommend($job);
//
//        // Tạo một mảng dữ liệu chứa thông tin về công việc và đề xuất công việc
//        $responseData = [
//            'job' => $job,
//            'job_recommendation' => $jobRecommendation,
//        ];
//
//        // Trả về dữ liệu dưới dạng JSON
//        return response()->json([
//            'success' => true,
//            'message' => 'success',
//            'data' => $responseData,
//        ]);
//    }

    public function show(Job $job)
    {
        // Load relationships in advance
        $job->load('jobtype', 'skill', 'jobcity');
        // Prepare data for the job
        $jobData = [
            'id' => $job->id,
            'title' => $job->title,
            'profession' => $job->profession,
            'position' => $job->position,
            'experience_years' => $job->experience_years,
            'work_address' => $job->work_address,
            'employment_type' => $job->employment_type,
            'quantity' => $job->quantity,
            'salary_from' => $job->salary_from,
            'salary_to' => $job->salary_to,
            'education_level' => $job->education_level,
            'status' => 'inactive',
            'featured' => $job->featured ? 'active' : 'inactive',
            'last_date' => $job->last_date,
            'description' => $job->description,
            'skill_experience' => $job->skill_experience,
            'benefits' => $job->benefits,
            'city' => $job->city,
            'district' => $job->district,
            'work_location' => $job->work_location,
            'latitude' => $job->latitude,
            'longitude' => $job->longitude,
            'contact_name' => $job->contact_name,
            'phone' => $job->phone,
            'email' => $job->email,
            'job_skills' => $job->jobSkills->pluck('name')->toArray(),
        ];

        // Return data as JSON response
        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $jobData,
            'status_code' => 200
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Job $job)
    {


        // Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'jobtype_id' => 'nullable|exists:job_types,id',
            'city_id' => 'nullable|exists:cities,id',
            'title' => 'nullable|string',
            'profession' => 'nullable|string',
            'position' => 'nullable|string',
            'experience_years' => 'nullable|integer',
            'work_address' => 'nullable|string',
            'employment_type' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'salary_from' => 'nullable|numeric',
            'salary_to' => 'nullable|numeric',
            'education_level' => 'nullable|string',
            'last_date' => 'nullable|date',
            'description' => 'nullable|string',
            'skill_experience' => 'nullable|string',
            'benefits' => 'nullable|string',
            'city' => 'nullable|string',
            'district' => 'nullable|string',
            'work_location' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'featured' => 'nullable|integer',
            'job_skills' => 'nullable|array',
            'job_skills.*.name' => 'nullable|string',
        ], [
            'jobtype_id.exists' => 'Loại công việc không hợp lệ.',
            'city_id.exists' => 'Thành phố không hợp lệ.',
            'title.string' => 'Tiêu đề phải là một chuỗi.',
            'profession.string' => 'Nghề nghiệp phải là một chuỗi.',
            'experience_years.integer' => 'Số năm kinh nghiệm phải là một số nguyên.',
            'salary_from.numeric' => 'Mức lương bắt đầu phải là một số.',
            'salary_to.numeric' => 'Mức lương kết thúc phải là một số.',
            'last_date.date' => 'Ngày kết thúc phải là một ngày hợp lệ.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'job_skills.array' => 'Kỹ năng công việc phải là một mảng.',
            'job_skills.*.name.string' => 'Tên kỹ năng phải là một chuỗi.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu đầu vào không hợp lệ.',
                'errors' => $validator->errors(),
                'status_code' => 422
            ], 422); // 422 là mã lỗi "Unprocessable Entity"
        }

        try {
            // Bắt đầu giao dịch
            \DB::beginTransaction();

            // Cập nhật thông tin công việc
            $job->update($request->only([
                'jobtype_id', 'city_id', 'title', 'salary', 'featured',
                'description', 'last_date', 'address', 'skill_experience', 'benefits'
            ]));

            // Cập nhật kỹ năng công việc nếu có
            if ($request->has('job_skills')) {
                $jobSkillsData = $request->input('job_skills');
                $existingSkills = $job->jobSkills()->pluck('name')->toArray();

                // Xóa các kỹ năng không còn trong danh sách cập nhật
                foreach ($existingSkills as $existingSkill) {
                    if (!in_array($existingSkill, array_column($jobSkillsData, 'name'))) {
                        $job->jobSkills()->where('name', $existingSkill)->delete();
                    }
                }

                // Cập nhật hoặc tạo mới các kỹ năng công việc
                foreach ($jobSkillsData as $skillData) {
                    $job->jobSkills()->updateOrCreate(['name' => $skillData['name']], $skillData);
                }
            }

            // Cam kết giao dịch
            \DB::commit();

            $jobData = [
                'id' => $job->id,
                'title' => $job->title,
                'profession' => $job->profession,
                'position' => $job->position,
                'experience_years' => $job->experience_years,
                'work_address' => $job->work_address,
                'employment_type' => $job->employment_type,
                'quantity' => $job->quantity,
                'salary_from' => $job->salary_from,
                'salary_to' => $job->salary_to,
                'education_level' => $job->education_level,
                'status' => 'inactive',
                'featured' => $job->featured ? 'active' : 'inactive',
                'last_date' => $job->last_date,
                'description' => $job->description,
                'skill_experience' => $job->skill_experience,
                'benefits' => $job->benefits,
                'city' => $job->city,
                'district' => $job->district,
                'work_location' => $job->work_location,
                'latitude' => $job->latitude,
                'longitude' => $job->longitude,
                'contact_name' => $job->contact_name,
                'phone' => $job->phone,
                'email' => $job->email,
                'job_skills' => $job->jobSkills->pluck('name')->toArray(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Job and job skills updated successfully.',
                'data' => $jobData,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            // Lùi lại giao dịch nếu có lỗi xảy ra
            \DB::rollBack();

            \Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cập nhật job thất bại.',
                'status_code' => 500
            ], 500); // 500 là mã lỗi "Internal Server Error"
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job deleted successfully',
            'status_code' => 200
        ]);
    }



}
