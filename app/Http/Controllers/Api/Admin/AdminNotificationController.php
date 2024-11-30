<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Lấy danh sách thông báo của user đã đăng nhập.
     */
    public function index()
    {
        $user = auth()->user();

        // Lấy tất cả thông báo của người dùng
        $notifications = $user->notifications;

        // Kiểm tra nếu không có thông báo
        if ($notifications->isEmpty()) {
            return response()->json(['message' => 'Không có thông báo nào.'], 204);
        }

        // Format lại thông báo
        $customData = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'message' => $notification->data['message'], // Sử dụng trường 'message' từ dữ liệu notification
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
            ];
        });

        // Trả về dữ liệu đã custom
        return response()->json($customData);
    }


    /**
     * Đánh dấu thông báo là đã đọc.
     */
    public function markAsRead($id)
    {
        $user = auth()->user();
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Thông báo đã được đánh dấu là đã đọc.']);
        }

        return response()->json(['message' => 'Không tìm thấy thông báo.'], 404);
    }

    /**
     * Xóa thông báo.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->delete();
            return response()->json(['message' => 'Thông báo đã được xóa.']);
        }

        return response()->json(['message' => 'Không tìm thấy thông báo.'], 404);
    }
}
