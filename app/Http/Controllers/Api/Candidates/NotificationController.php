<?php

namespace App\Http\Controllers\Api\Candidates;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        // Lấy người dùng hiện tại
        $user = Auth::user();

        // Lấy tất cả thông báo cho người dùng
        $notifications = $user->notifications;

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'status_code' => 200,
        ], 200);
    }

    public function markAsRead($notificationId)
    {
        try {
            // Lấy người dùng hiện tại
            $user = Auth::user();

            // Tìm thông báo theo ID
            $notification = $user->notifications()->findOrFail($notificationId);

            // Đánh dấu thông báo là đã đọc
            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Thông báo đã được đánh dấu là đã đọc.',
                'notification' => $notification, // Trả về dữ liệu thông báo
                'status_code' => 200,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo.',
                'status_code' => 404,
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đánh dấu thông báo.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ], 500);
        }
    }
}
