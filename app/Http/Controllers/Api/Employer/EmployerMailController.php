<?php

namespace App\Http\Controllers\Api\Employer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\job_user;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\EmployerToApplicantMail;
use Illuminate\Support\Facades\Mail;

class EmployerMailController extends Controller
{
    public function sendEmailToApplicant(Request $request, $jobId, $userId)
    {
        // Lấy thông tin công việc và ứng viên
        $job = Job::find($jobId);

        // Lấy thông tin ứng viên từ pivot
        $applicant = job_user::where('user_id', $userId)
            ->where('job_id', $jobId)
            ->first();

        // Kiểm tra xem có bản ghi applicant hay không
        if (!$job || !$applicant) {
            return response()->json(['message' => 'Công việc hoặc ứng viên không tồn tại.'], 404);
        }

        // Lấy email từ pivot table
        $email = $applicant->email;

        // Check if the email is valid
        if (empty($email)) {
            return response()->json(['message' => 'Email của ứng viên không tồn tại.'], 400);
        }

        // Lấy thông tin user từ bảng User
        $user = User::find($userId);

        // Lấy nội dung email từ request
        $subject = $request->input('subject');
        $messageContent = $request->input('message');

        if (!$subject || !$messageContent) {
            return response()->json(['message' => 'Vui lòng cung cấp tiêu đề và nội dung email.'], 400);
        }

        // Gửi email
        Mail::to($email)->send(new EmployerToApplicantMail($job, $user, $subject, $messageContent));

        return response()->json([
            'success' => true,
            'message' => 'Email đã được gửi đến ứng viên thành công.',
        ], 200);
    }}
