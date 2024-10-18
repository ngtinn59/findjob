<?php

namespace App\Http\Controllers\Api\Candidates;

use App\Http\Controllers\Controller;
use App\Mail\JobApplied;
use App\Models\Job;
use App\Models\Objective;
use App\Notifications\JobApplicationSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class JobSeekersController extends Controller
{
    public function saveJob(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $user = $request->user();

        // Kiểm tra xem công việc đã được thêm vào danh sách yêu thích của người dùng chưa
        if ($user->favorites()->where('job_id', $job->id)->exists()) {
            return response()->json(['message' => 'Công việc đã lưu trước đó'], 200);
        } else {
            // Nếu công việc chưa được thêm vào danh sách yêu thích, thực hiện thêm mới
            $user->favorites()->syncWithoutDetaching([$job->id]);
            return response()->json([
                'success' => 'true',
                'message' => 'Đã lưu công việc vào danh sách yêu thích',
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
            'message' => 'Xóa công việc đã lưu thành công',
            'status_code' => 200,
        ], 200);
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

        return response()->json([
            'success' => true,
            'message' => 'Saved jobs',
            'data' => $savedJobsData,
            'status_code' => 200
        ], 200);
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

        // Kiểm tra xem người dùng đã chọn CV có sẵn hay upload CV mới
        if ($request->has('selected_cv_id')) {
            // Người dùng chọn một CV có sẵn
            $selectedCv = Objective::find($request->selected_cv_id);

            $cvFileName = $selectedCv->file;
        } elseif ($request->hasFile('cv')) {
            // Người dùng tải lên CV mới
            $cv = $request->file('cv');
            $cvFileName = time() . '_' . $cv->getClientOriginalName();
            $cv->storeAs('public/cv', $cvFileName);
        } else {
            // Không có CV nào được chọn hoặc tải lên
            return response()->json(['message' => 'Vui lòng chọn hoặc tải lên CV.'], 400);
        }

        // Lấy thông tin name, phone, email từ request
        $name = $request->input('name');
        $phone = $request->input('phone');
        $email = $request->input('email');

        // Tiếp tục quá trình ứng tuyển
        Mail::to($user->email)->send(new JobApplied($job, $user, $cvFileName));
        $job->company->notify(new JobApplicationSubmitted($job, $user, $name, $phone, $email));

        // Thêm thông tin vào bảng job_user
        $job->users()->attach($user->id, [
            'status' => 'pending',
            'cv' => $cvFileName,
            'name' => $name,     // Thêm trường name
            'phone' => $phone,   // Thêm trường phone
            'email' => $email    // Thêm trường email
        ]);

        // Gửi thông báo cho nhà tuyển dụng
        // $job->company->notify(new JobApplicationSubmitted($job, $user));

        return response()->json([
            'success' => true,
            'message' => 'Ứng tuyển công việc thành công.',
            'status_code' => 200,
        ], 200);
    }

    public function getUserCvs(Request $request)
    {
        $user = $request->user();

        // Lấy danh sách CV từ profile của người dùng
        $cvs = $user->profile->objectives;

        // Tạo một mảng để chứa dữ liệu CV
        $customData = $cvs->map(function ($cv) {
            return [
                'id' => $cv->id,
                'desired_position' => $cv->desired_position,
                'attached_file' => $cv->file ? 'Hồ sơ đính kèm' : 'Hồ sơ trực tuyến', // Lấy URL tệp đính kèm
            ];
        });


        return response()->json([
            'success' => true,
            'data' => $customData,
            'message' => 'Danh sách CV của người dùng đã được lấy thành công.'
        ]);
    }



}
