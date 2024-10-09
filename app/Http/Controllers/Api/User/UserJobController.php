<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Mail\JobApplied;
use App\Models\Job;
use Illuminate\Http\Request;

class UserJobController extends Controller
{
    public function jobRecommend(Job $job)
    {
        $currentJobSkills = $job->skill->pluck('name')->toArray();

        // Lấy tất cả các công việc khác từ cơ sở dữ liệu
        $otherJobs = Job::with('skill')->where('id', '!=', $job->id)->get();

        // Lọc các công việc khác để chỉ chọn những công việc có ít nhất một kỹ năng giống với công việc hiện tại
        $recommendedJobs = $otherJobs->filter(function ($otherJob) use ($currentJobSkills) {
            // Lấy kỹ năng của công việc khác

            $otherJobSkills = $otherJob->skill->pluck('name')->toArray();

            // Kiểm tra xem công việc khác có chứa ít nhất một kỹ năng của công việc hiện tại không
            $hasSkill = count(array_intersect($currentJobSkills, $otherJobSkills)) > 0;

            return $hasSkill;
        });

        return $recommendedJobs;
    }

    public function apply(Request $request, $id)
    {
        $job = Job::find($id);
        if (! $job) {
            return response()->json(['message' => 'Công việc không tồn tại.'], 404);
        }

        $user = Auth::guard('sanctum')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($job->users()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'Bạn đã ứng tuyển công việc này rồi.'], 409);
        }

        // Process the CV file
        if ($request->hasFile('cv')) {
            $cv = $request->file('cv');
            $cvFileName = time().'_'.$cv->getClientOriginalName();
            $cv->storeAs('cv', $cvFileName); // Store the CV file in storage/cv directory
        } else {
            $cvFileName = null;
        }

        // Gửi email thông báo về việc ứng tuyển công việc
        Mail::to($user->email)->send(new JobApplied($job, $user, $cvFileName));

        $job->users()->attach($user->id, ['status' => 'pending', 'cv' => $cvFileName]);

        return response()->json(['message' => 'Ứng tuyển công việc thành công.'], 200);
    }



    public function applicant(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $appliedJobs = $user->jobs()->withPivot('status')->get();

        $formattedJobs = $appliedJobs->map(function ($job) {
            $company = $job->company()->first(); // Lấy thông tin của công ty
            $jobType = $job->jobType()->first(); // Lấy thông tin của loại công việc
            $city = $job->city()->first(); // Lấy thông tin của thành phố

            return [
                'id' => $job->id,
                'company' => $company ? $company->name : null, // Kiểm tra xem công ty có tồn tại không trước khi truy cập trường name
                'job_type' => $jobType ? $jobType->name : null, // Kiểm tra xem loại công việc có tồn tại không trước khi truy cập trường name
                'city' => $city ? $city->name : null, // Kiểm tra xem thành phố có tồn tại không trước khi truy cập trường name
                'title' => $job->title,
                'salary' => $job->salary,
                'status' => $job->pivot->status,
                'featured' => $job->featured,
                'address' => $job->address,
                'description' => $job->description,
                'skill_experience' => $job->skill_experience,
                'benefits' => $job->benefits,
                'last_date' => $job->last_date,
                'created_at' => $job->created_at,
                'updated_at' => $job->updated_at,
            ];
        });

        return response()->json($formattedJobs, 200);
    }




    public function search(Request $request)
    {
        echo  1;
        // Get the search query from the request
        $searchQuery = $request->input('query', '');

        // If the search query is empty, return all jobs
        if (empty($searchQuery)) {
            return $this->index();
        }

        // Search for jobs where the title or description contains the search query
        // You can adjust the fields you wish to search in
        $jobs = Job::with('jobtype', 'skill', 'Company')
            ->where('title', 'like', "%{$searchQuery}%")
            ->orWhere('description', 'like', "%{$searchQuery}%")
            ->paginate(5);

        // Transform the jobs as done in the index method or any other preferred format
        $jobsData = $jobs->map(function ($job) {
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

        // Return the search results
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
        ]);
    }

    /**
     * Get list of saved jobs
     */
    public function savedJobs(Request $request)
    {
        $user = $request->user();
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

        return response()->json(['saved_jobs' => $savedJobsData], 200);
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
            return response()->json(['message' => 'Job saved to favorites'], 200);
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

        return response()->json(['message' => 'Job removed from favorites'], 200);
    }

    public function indexShow()
    {
        $jobs = Job::with('jobtype', 'skill', 'Company')->paginate(5);

        $jobsData = $jobs->map(function ($job) {
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
            'message' => 'success',
            'data' => $jobsData,
            'links' => [
                'first' => $jobs->url(1),
                'last' => $jobs->url($jobs->lastPage()),
                'prev' => $jobs->previousPageUrl(),
                'next' => $jobs->nextPageUrl(),
            ],
        ]);
    }

    public function showJob(Job $job)
    {
        $job->load('jobtype', 'skill', 'company');

        $jobData = [
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

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $jobData,
        ]);
    }
}
