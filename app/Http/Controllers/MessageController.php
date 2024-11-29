<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Job;
use App\Models\Message;
use App\Utillities\Common;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function sendMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'nullable|string',
        ]);

        $file_name = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $file_name = Common::uploadFile($file, public_path('uploads'));
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'file_path' => $file_name,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => 'Message sent successfully.',
            'data' => [
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name,
                'receiver_id' => $message->receiver_id,
                'file_url' => $message->file_path ? url('uploads/' . $message->file_path) : null,
                'created_at' => $message->created_at->toDateTimeString(),
            ]
        ]);
    }





    public function getMessages($userId)
    {
        $messages = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', Auth::id())
                    ->where('receiver_id', $userId);
            })->orWhere(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->where('receiver_id', Auth::id());
            })->get();

        $customData = $messages->map(function ($message) {
            return [
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender->name,
                'receiver_id' => $message->receiver_id,
                'file_url' => $message->file_path ? url('uploads/' . $message->file_path) : null,
                'created_at' => $message->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'data' => $customData,
        ]);
    }


    public function index(): \Illuminate\Http\JsonResponse
    {
        $userId = Auth::id();

        // Load thông tin tin nhắn và mối quan hệ
        $messages = Message::with(['sender.companies', 'receiver.companies'])
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get();

        // Nhóm hội thoại theo người dùng khác
        $conversations = $messages->groupBy(function ($message) use ($userId) {
            return $message->sender_id === $userId ? $message->receiver_id : $message->sender_id;
        })->map(function ($group) use ($userId) {
            $lastMessage = $group->sortByDesc('created_at')->first();
            $otherUser = $lastMessage->sender_id === $userId ? $lastMessage->receiver : $lastMessage->sender;

            return [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'email' => $otherUser->email,
                'logo' => $otherUser->companies?->logo ? url('uploads/images/' . $otherUser->companies->logo) : null,
                'last_message' => $lastMessage->message,
                'last_message_time' => $lastMessage->created_at->toDateTimeString(),
            ];
        })->values();

        return response()->json([
            'data' => $conversations,
        ]);
    }

    public function indexEmployer(): \Illuminate\Http\JsonResponse
    {
        $userId = Auth::id();

        // Load thông tin tin nhắn và mối quan hệ với profile của người gửi/nhận
        $messages = Message::with(['sender.profile', 'receiver.profile'])
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get();

        // Nhóm hội thoại theo người dùng khác
        $conversations = $messages->groupBy(function ($message) use ($userId) {
            return $message->sender_id === $userId ? $message->receiver_id : $message->sender_id;
        })->map(function ($group) use ($userId) {
            $lastMessage = $group->sortByDesc('created_at')->first();
            $otherUser = $lastMessage->sender_id === $userId ? $lastMessage->receiver : $lastMessage->sender;

            return [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'email' => $otherUser->email,
                'logo' => $otherUser->profile?->image ? url('uploads/images/' . $otherUser->profile->image) : null, // Lấy logo nếu tồn tại
                'last_message' => $lastMessage->message,
                'last_message_time' => $lastMessage->created_at->toDateTimeString(),
            ];
        })->values();

        return response()->json([
            'data' => $conversations,
        ]);
    }

    public function indexAppliant()
    {
        $user = Auth::user(); // Lấy người dùng hiện tại

        // Kiểm tra nếu người dùng không có công ty liên kết
        if (!$user->companies) {
            return response()->json([
                'success' => false,
                'message' => 'Không có thông tin công ty.'
            ], 403);
        }

        // Lấy công ty đầu tiên của người dùng (nếu có nhiều công ty, điều này cần được điều chỉnh)
        $companyId = $user->companies->id;

        // Lấy tất cả các công việc thuộc về công ty của người dùng hiện tại với phân trang
        $jobs = Job::with([
            'applicants' => function ($query) {
                $query->withPivot('status', 'cv', 'name', 'phone', 'email', 'created_at')
                    ->with('profile'); // Tải thêm thông tin profile
            }
        ])->where('company_id', $companyId)->paginate(10);


        // Tùy chỉnh dữ liệu ứng viên
        $formattedApplicants = $jobs->flatMap(function ($job) {
            return $job->applicants->map(function ($applicant) {
                return [
                    'id' => $applicant->id,
                    'name' => $applicant->pivot->name,
                    'logo' => $applicant->profile?->image ? url('uploads/images/' . $applicant->profile->image) : null, // Lấy logo nếu tồn tại
                ];
            });
        });

        // Loại bỏ các bản ghi trùng lặp dựa trên 'id'
        $uniqueApplicants = $formattedApplicants->unique('id')->values();

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $uniqueApplicants,
            'pagination' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'total' => $jobs->total(),
                'per_page' => $jobs->perPage(),
                'next_page_url' => $jobs->nextPageUrl(),
                'previous_page_url' => $jobs->previousPageUrl(),
            ],
            'status_code' => 200
        ], 200);
    }


    public function indexapplicantuser()
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Lấy các công việc mà người dùng đã ứng tuyển
        $appliedJobs = $user->jobs()->withPivot('status')->get();

        // Map dữ liệu công việc sang thông tin công ty và user
        $formattedJobs = $appliedJobs->map(function ($job) {
            $company = $job->company()->first();
            return [
                'id' => $company->user->id,
                'logo' => $company->logo ? asset('uploads/images/' . $company->logo) : null,
                'name' => $company->user->name
            ];
        });

        // Loại bỏ các bản ghi trùng lặp dựa trên 'id'
        $uniqueUsers = $formattedJobs->unique('id')->values();

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => $uniqueUsers,
            'status_code' => 200
        ], 200);
    }
}
