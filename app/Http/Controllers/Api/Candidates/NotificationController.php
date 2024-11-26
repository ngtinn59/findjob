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

        // Tùy chỉnh dữ liệu thông báo
        $customNotifications = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->data['message'], // Dữ liệu thông báo
                'read' => $notification->read_at,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'), // Định dạng thời gian
            ];
        });

        return response()->json($customNotifications);
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

            // Tùy chỉnh dữ liệu thông báo giống như trong hàm index
            $customNotification = [
                'id' => $notification->id,
                'message' => $notification->data['message'], // Dữ liệu thông báo
                'read' => true, // Đánh dấu là đã đọc
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'), // Định dạng thời gian
            ];

            return response()->json($customNotification,200);
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

    public function deleteNotification($notificationId)
    {
        try {
            // Lấy người dùng hiện tại
            $user = Auth::user();

            // Tìm thông báo theo ID
            $notification = $user->notifications()->findOrFail($notificationId);

            // Xóa thông báo
            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Thông báo đã được xóa thành công.',
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
                'message' => 'Có lỗi xảy ra khi xóa thông báo.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ], 500);
        }
    }


}
