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
                'featured' => $job->featured,
                'is_hot' => ($job->views > 100) ? 1 : 0,
                'company' => [
                    'id' => $job->company->id,
                    'name' => $job->company->company_name,
                    'logo' => $job->company->logo ? asset('uploads/images/' . $job->company->logo) : null,
                ],
                'salary' => [
                    'salary_from' => $job->salary_from,
                    'salary_to' => $job->salary_to
                ],
                'city' => [
                    'id' => $job->city->id,
                    'name' => $job->city->name,
                ],
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
        // Kiểm tra công việc có tồn tại không
        $job = Job::find($id);
        if (!$job) {
            return response()->json(['message' => 'Công việc không tồn tại.'], 404);
        }

        // Kiểm tra người dùng đã đăng nhập chưa
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Kiểm tra xem người dùng đã ứng tuyển công việc này chưa
        if ($job->users()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'message' => 'Bạn đã ứng tuyển công việc này rồi.',
                'status_code' => 409
            ], 409);
        }

        if ($request->has('selected_cv_id')) {
            // Người dùng chọn CV có sẵn
            $selectedCv = Objective::find($request->selected_cv_id);
            if (!$selectedCv) {
                return response()->json(['message' => 'CV đã chọn không tồn tại.'], 404);
            }

            // Lấy file từ CV đã chọn và tạo tên file mới
            $cvFileName = time() . '_' . basename($selectedCv->file);
            $sourcePath = public_path('cvs/' . $selectedCv->file); // Đường dẫn đầy đủ của file trong public/cvs
            $cvFileName = time() . '_' . basename($selectedCv->file); // Tên file mới để lưu vào storage

            if (file_exists($sourcePath)) {
                try {
                    // Đọc nội dung file từ public/cvs
                    $fileContents = file_get_contents($sourcePath);

                    // Lưu nội dung file vào storage/app/public/cv với tên mới
                    Storage::disk('public')->put('cv/' . $cvFileName, $fileContents);
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Lỗi khi sao chép file CV: ' . $e->getMessage()], 500);
                }
            } else {
                return response()->json(['message' => 'File CV không tồn tại.'], 404);
            }


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

        // Kiểm tra nếu thiếu thông tin
        if (!$name || !$phone || !$email) {
            return response()->json(['message' => 'Vui lòng cung cấp đủ thông tin cá nhân.'], 400);
        }

        // Tiếp tục quá trình ứng tuyển
        Mail::to($user->email)->send(new JobApplied($job, $user, $cvFileName));
        $job->company->notify(new JobApplicationSubmitted($job, $user, $name, $phone, $email));

        // Thêm thông tin vào bảng job_user
        $job->users()->attach($user->id, [
            'status' => 'pending',
            'cv' => $cvFileName,
            'name' => $name,
            'phone' => $phone,
            'email' => $email
        ]);

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
                'attached_file' => $cv->file ? 'Hồ sơ đính kèm' : 'Hồ sơ trực tuyến',
                'file' => asset('cvs/' . $cv->file),
            ];
        });


        return response()->json([
            'success' => true,
            'data' => $customData,
            'message' => 'Danh sách CV của người dùng đã được lấy thành công.'
        ]);
    }



}
