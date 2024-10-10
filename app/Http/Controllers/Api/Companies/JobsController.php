<?php

namespace App\Http\Controllers\Api\Companies;

use App\Http\Controllers\Controller;
use App\Mail\JobApplied;
use App\Models\Job;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        // Kiểm tra xem người dùng có một công ty không
        if ($user->companies) {
            // Lấy danh sách công việc của công ty của người dùng hiện tại
            $jobs = $user->companies->jobs()->paginate(5);

            $jobsData = $jobs->map(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'company' => $job->company ? $job->company->name : null,
                    'job_type' => $job->jobtype ? $job->jobtype->pluck('name')->toArray() : null,
                    'job_city' => $job->jobcity ? $job->jobcity->pluck('name')->toArray() : null,
                    'salary' => $job->salary,
                    'status' => $job->status,
                    'featured' => $job->featured,
                    'address' => $job->address,
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

        // Nếu người dùng không có công ty, trả về thông báo lỗi hoặc thông tin tùy thuộc vào yêu cầu của bạn
        return response()->json([
            'success' => false,
            'message' => 'User does not have a company.',
            'status_code' => 404
        ], 404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $company = $user->companies->id;

        // Validation rules
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
            'status' => 'required|integer',
            'featured' => 'required|integer',
            'job_skills' => 'required|array',
            'job_skills.*.name' => 'required|string',
        ]);

        // Custom error messages
        $messages = [
            'jobtype_id.required' => 'Trường loại công việc là bắt buộc.',
            'jobtype_id.exists' => 'Loại công việc không tồn tại.',
            'city_id.required' => 'Trường thành phố là bắt buộc.',
            'city_id.exists' => 'Thành phố không tồn tại.',
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

        // Add users_id and company_id to the validated data array.
        $validatedData['users_id'] = $user->id;
        $validatedData['company_id'] = $company;

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy công ty cho người dùng.',
            ], 404);
        }

        // Extract job skills data from the request and remove it from the validated data
        $jobSkillsData = $validatedData['job_skills'];
        unset($validatedData['job_skills']);

        try {
            \DB::beginTransaction();
            $job = Job::create($validatedData);

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
                'status' => $job->status ? 'inactive' : 'active',
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
                'message' => 'Công việc và kỹ năng công việc đã được tạo thành công.',
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
            'salary' => $job->salary,
            'status' => $job->status,
            'featured' => $job->featured,
            'description' => $job->description,
            'benefits' => $job->benefits,
            'last_date' => $job->last_date,
            'job_type' => $job->jobtype ? $job->jobtype->pluck('name')->toArray() : null,
            'job_city' => $job->jobcity ? $job->jobcity->pluck('name')->toArray() : null,
            'skills' => $job->skill->pluck('name')->toArray(),
            'address' => $job->address,
            'created_at' => $job->created_at->diffForHumans(),
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
        if ($request->user()->id !== $job->company->users_id ) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền chỉnh sửa job này.',
                'status_code' => 403
            ], 403); // 403 là mã lỗi "Forbidden" khi người dùng không có quyền truy cập
        }
        try {
            // Start a transaction
            \DB::beginTransaction();

            // Update the job with the request data
            $job->update([
                'jobtype_id' => $request->input('jobtype_id'),
                'city_id' => $request->input('city_id'),
                'title' => $request->input('title'),
                'salary' => $request->input('salary'),
                'status' => $request->input('status'),
                'featured' => $request->input('featured'),
                'description' => $request->input('description'),
                'last_date' => $request->input('last_date'),
                'address' => $request->input('address'),
                'skill_experience' => $request->input('skill_experience'),
                'benefits' => $request->input('benefits'),
            ]);

            // If 'job_skills' are provided in the request, update job skills accordingly
            // If 'job_skills' are provided in the request, update job skills accordingly
            if ($request->has('job_skills')) {
                $jobSkillsData = $request->input('job_skills');
                $existingSkills = $job->jobSkills()->pluck('name')->toArray();

                // Delete job skills that are not in the updated list
                foreach ($existingSkills as $existingSkill) {
                    if (! in_array($existingSkill, array_column($jobSkillsData, 'name'))) {
                        $job->jobSkills()->where('name', $existingSkill)->delete();
                    }
                }

                // Attach the updated job skills to the job
                foreach ($jobSkillsData as $skillData) {
                    $job->jobSkills()->updateOrCreate(['name' => $skillData['name']], $skillData);
                }
            }

            // Commit the transaction
            \DB::commit();

            $jobData = [
                'id' => $job->id,
                'title' => $job->title,
                'job_type' => $job->jobtype ? $job->jobtype->pluck('name')->toArray() : null,
                'salary' => $job->salary,
                'status' => $job->status ? 'active' : 'inactive',
                'featured' => $job->featured ?  'active' : 'inactive',
                'address' => $job->address,
                'description' => $job->description,
                'skill_experience' => $job->skill_experience,
                'last_date' => $job->last_date,
                'benefits' => $job->benefits,
                'job_skills' => $job->jobSkills->pluck('name')->toArray(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Job and job skills updated successfully.',
                'data' => $jobData,
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            // Rollback the transaction
            \DB::rollBack();

            \Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Job update failed.',
            ], 500);
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

    public function jobRecommend(Job $job)
    {
        $currentJobSkills = $this->getJobSkills($job);
        $otherJobs = $this->getOtherJobs($job->id);

        // Filter jobs to include only those with at least one matching skill
        $recommendedJobs = $otherJobs->filter(function ($otherJob) use ($currentJobSkills) {
            $otherJobSkills = $this->getJobSkills($otherJob);
            return $this->hasMatchingSkills($currentJobSkills, $otherJobSkills);
        });

        return $recommendedJobs;
    }

    private function getJobSkills(Job $job)
    {
        return $job->skill->pluck('name')->toArray();
    }

    private function getOtherJobs($jobId)
    {
        return Job::with('skill')->where('id', '!=', $jobId)->get();
    }

    private function hasMatchingSkills(array $currentJobSkills, array $otherJobSkills)
    {
        return count(array_intersect($currentJobSkills, $otherJobSkills)) > 0;
    }

    public function apply(Request $request, $id)
    {
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Công việc không tồn tại.'], 404);
        }

        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($job->users()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'message' => 'Bạn đã ứng tuyển công việc này rồi.',
                'status_code' => 409
            ], 409);
        }

        if ($request->hasFile('cv')) {
            $cv = $request->file('cv');
            $cvFileName = time() . '_' . $cv->getClientOriginalName();
            $cv->storeAs('public/cv', $cvFileName); // Lưu file CV vào public/cv
            $cvUrl = Storage::url('public/cv/' . $cvFileName); // Lấy URL của file CV từ thư mục storage/cv
        }
        else {
            // No new CV file was uploaded, attempt to use the default CV
            $defaultCv = $user->cvs()->where('is_default', true)->first();
            $cvFileName = $defaultCv ? $defaultCv->file_path : null;
            $cvUrl = $cvFileName ? Storage::url('public/cv/' . $cvFileName) : null; // Get the URL of the default CV from the public directory
        }

        // Continue with the application process...
        Mail::to($user->email)->send(new JobApplied($job, $user, $cvUrl));
        $job->users()->attach($user->id, ['status' => 'pending', 'cv' => $cvFileName]);

        return response()->json([
            'success' => true,
            'message' => 'Ứng tuyển công việc thành công.',
            'status_code' => 200,
        ], 200);
    }




    public function applicant()
    {
        $user = Auth::guard('sanctum')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $appliedJobs = $user->jobs()->withPivot('status')->get();

        $formattedJobs = $appliedJobs->map(function ($job) {
            $company = $job->company()->first();
            $jobType = $job->jobType()->first();
            $city = $job->jobcity()->first();
            $lastDate = Carbon::createFromFormat('Y-m-d', $job->last_date);
            $daysRemaining = $lastDate->diffInDays(Carbon::now());
            return [
                'id' => $job->id,
                'company' => $company ? $company->name : null, // Kiểm tra xem công ty có tồn tại không trước khi truy cập trường name
                'job_type' => $jobType ? $jobType->name : null, // Kiểm tra xem loại công việc có tồn tại không trước khi truy cập trường name
                'city' => $city ? $city->name : null, // Kiểm tra xem thành phố có tồn tại không trước khi truy cập trường name
                'title' => $job->title,
                'salary' => $job->salary,
                'status' => $job->pivot->status,
//                'featured' => $job->featured,
                'address' => $job->address,
                'description' => $job->description,
                'skill_experience' => $job->skill_experience,
                'benefits' => $job->benefits,
                'last_date' => $job->last_date,
                'last_date' => $daysRemaining,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $formattedJobs,
            'status_code' => 200
        ], 200);
    }


    public function search(Request $request)
    {
        $searchQuery = $request->input('query', '');

        if (empty($searchQuery)) {
            return $this->index();
        }

        $searchColumns = ['title', 'description', 'address', 'benefits', 'last_date', 'salary', 'skill_experience'];

        $jobs = Job::with('jobtype', 'skill', 'company')
            ->where(function ($query) use ($searchQuery, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $query->orWhere($column, 'like', "%{$searchQuery}%");
                }
            })
            ->orWhereHas('jobcity', function ($query) use ($searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%");
            })
            ->orWhereHas('skill', function ($query) use ($searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%");
            })
            ->orWhereHas('jobtype', function ($query) use ($searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%");
            })
            ->orWhereHas('company', function ($query) use ($searchQuery) {
                $query->where('name', 'like', "%{$searchQuery}%");
            })
            ->paginate(5);

        $jobsData = $this->mapJobs($jobs);

        return response()->json([
            'success' => true,
            'message' => 'Search results',
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

    private function mapJobs($jobs)
    {
        return $jobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'company' => $job->company ? $job->company->name : null,
                'salary' => $job->salary,
                'job_type' => $job->jobtype ? $job->jobtype->pluck('name')->toArray() : null,
                'skills' => $job->skill->pluck('name')->toArray(),
                'address' => $job->address,
                'last_date' => $job->last_date,
                'created_at' => $job->created_at->diffForHumans(),
            ];
        });
    }

    /**
     * Get list of saved jobs
     */
    public function savedJobs(Request $request)
    {
        $user = Auth::user();
        $savedJobs = $user->favorites;

        $savedJobsData = $savedJobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'company' => $job->company ? $job->company->name : null,
                'salary' => $job->salary,
                'job_type' => $job->jobtype ? $job->jobtype->pluck('name')->toArray() : null,
                'skills' => $job->skill->pluck('name')->toArray(),
                'address' => $job->address,
                'last_date' => $job->last_date,
                'created_at' => $job->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Saved jobs',
            'data' => $savedJobsData,
            'status_code' => 200
        ], 200);
    }

    /**
     * Get list of applied jobs
     */
    public function appliedJobs(Request $request)
    {
        $user = $request->user();
        $appliedJobs = $user->jobs;
    }

    /**
     * Save Job in favorite table
     */
    public function saveJob(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $user = $request->user();

        // Kiểm tra xem công việc đã được thêm vào danh sách yêu thích của người dùng chưa
        if ($user->favorites()->where('job_id', $job->id)->exists()) {
            return response()->json(['message' => 'Job is already saved to favorites'], 200);
        } else {
            // Nếu công việc chưa được thêm vào danh sách yêu thích, thực hiện thêm mới
            $user->favorites()->syncWithoutDetaching([$job->id]);
            return response()->json([
                 'success' => 'true',
                 'message' => 'Job saved to favorites',
                 'status_code' => 200
            ], 200);
        }
    }


    /**
     * Unsave Job from favorite table
     */
    public function unsaveJob(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $user = $request->user();
        $user->favorites()->detach($job->id);

        return response()->json([
            'success' => 'true',
            'message' => 'Job removed from favorites',
            'status_code' => 200,

        ], 200);
    }

    public function indexShow()
    {
        $jobs = Job::where('status', 1)
            ->orderBy('featured', 'desc') // Sắp xếp các công việc có featured = 1 trước
            ->orderBy('created_at', 'desc') // Sắp xếp theo thời gian tạo mới nhất
            ->with('jobtype', 'skill', 'company', 'jobcity')
            ->paginate(5);

        $jobsData = $jobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'company' => $job->company ? $job->company->name : null,
                'logo' => $job->company ? $job->company->logo : null, // Lấy logo của công ty
                'salary' => $job->salary,
                'job_type' => $job->jobtype ? $job->jobtype->pluck('name')->toArray() : null,
                'job_city' => $job->jobcity ? $job->jobcity->pluck('name')->toArray() : null,
                'skills' => $job->skill->pluck('name')->toArray(),
                'address' => $job->company ? $job->company->address : null,
                'featured' => $job->featured,
                'applicant_count' => $job->applicants()->count(), // Số lượng người ứng tuyển
                'last_date' => $job->last_date,
                'created_at' => $job->created_at->diffForHumans(),
            ];
        });

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



    public function suggestJobs(Request $request)
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        $userSkills = $user->getProfileSkills();
        $suggestedJobs = $this->getSuggestedJobs($userSkills);
        $formattedJobs = $this->formatJobSuggestions($suggestedJobs);

        return $this->successfulResponse($formattedJobs);
    }

    private function getAuthenticatedUser()
    {
        return Auth::guard('sanctum')->user();
    }

    private function unauthorizedResponse()
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
            'status_code' => 401
        ], 401);
    }

    private function getSuggestedJobs(array $userSkills)
    {
        return Job::whereHas('skill', function ($query) use ($userSkills) {
            $query->whereIn('name', $userSkills);
        })->inRandomOrder()->take(5)->get();
    }

    private function formatJobSuggestions($suggestedJobs)
    {
        return $suggestedJobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'company' => optional($job->company)->name,
                'salary' => $job->salary,
                'job_type' => optional($job->jobtype)->pluck('name')->toArray(),
                'job_city' => optional($job->jobcity)->pluck('name')->toArray(),
                'skills' => $job->skill->pluck('name')->toArray(),
                'address' => optional($job->company)->address,
                'last_date' => $job->last_date,
                'created_at' => $job->created_at->diffForHumans(),
            ];
        });
    }

    private function successfulResponse($formattedJobs)
    {
        return response()->json([
            'success' => true,
            'message' => 'Suggested jobs retrieved successfully',
            'data' => $formattedJobs,
            'status_code' => 200
        ]);
    }

    public function showJob(Job $job)
    {
        // If the job does not exist, return a 404 response
        if (!$job) {
            return $this->errorResponse('Job not found', 404);
        }

        try {
            $job->load('jobtype', 'skill', 'company', 'jobcity');

            $jobData = $this->prepareJobData($job);
            $jobRecommendations = $this->prepareJobRecommendations($job);

            $response = $this->successResponse(
                'Job details retrieved successfully',
                [
                    'job' => $jobData,
                    'jobRecommendations' => $jobRecommendations,
                ],
                200
            );

            return response()->json($response);

        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while retrieving job details', 500, $e->getMessage());
        }
    }

    private function prepareJobData(Job $job)
    {
        return [
            'id' => $job->id,
            'title' => $job->title,
            'company' => $job->company ? $job->company->name : null,
            'salary' => $job->salary,
            'job_type' => $job->jobtype ? $job->jobtype->pluck('name')->toArray() : null,
            'jobcity' => $job->jobcity ? $job->jobcity->pluck('name')->toArray() : null,
            'skills' => $job->skill->pluck('name')->toArray(),
            'address' => $job->company->address,
            'description' => $job->description,
            'skill_experience' => $job->skill_experience,
            'benefits' => $job->benefits,
            'last_date' => $job->last_date,
            'created_at' => $job->created_at->diffForHumans(),
        ];
    }

    private function prepareJobRecommendations(Job $job)
    {
        return $this->jobRecommend($job)->take(5)->map(function ($recommendedJob) {
            return [
                'id' => $recommendedJob->id,
                'title' => $recommendedJob->title,
                'company' => $recommendedJob->company ? $recommendedJob->company->name : null,
                'salary' => $recommendedJob->salary,
                'job_type' => $recommendedJob->jobtype ? $recommendedJob->jobtype->pluck('name')->toArray() : null,
                'job_city' => $recommendedJob->jobcity ? $recommendedJob->jobcity->pluck('name')->toArray() : null,
                'skills' => $recommendedJob->skill->pluck('name')->toArray(),
                'address' => $recommendedJob->company->address,
                'last_date' => $recommendedJob->last_date,
                'created_at' => $recommendedJob->created_at->diffForHumans(),
            ];
        })->toArray();
    }

    private function errorResponse($message, $statusCode, $error = null)
    {
        return [
            'success' => false,
            'message' => $message,
            'error' => $error,
            'status_code' => $statusCode
        ];
    }

    private function successResponse($message, $data, $statusCode)
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status_code' => $statusCode
        ];
    }

}
