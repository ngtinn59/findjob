<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Utillities\Common;
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

        $file_name = null; // Variable to store the file name if a file is uploaded

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Use the Common::uploadFile method to handle the upload and get the file name
            $file_name = Common::uploadFile($file, public_path('uploads'));
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'file_path' => $file_name,
        ]);


        // Broadcast the event with socket ID
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => 'Message sent successfully.',
            'data' => $message,
        ]);
    }




    public function getMessages($userId)
    {
        // Eager load sender và receiver
        $messages = Message::with(['sender', 'receiver']) // Tải trước thông tin người gửi và người nhận
        ->where(function ($query) use ($userId) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', Auth::id());
        })->get();

        // Tùy chỉnh dữ liệu để bao gồm thông tin người gửi và người nhận
        $customData = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'message' => $message->message,
                'file_url' => $message->file_path,
                'created_at' => $message->created_at->format('Y-m-d H:i:s'),
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                    'email' => $message->sender->email,
                ],
                'receiver' => [
                    'id' => $message->receiver->id,
                    'name' => $message->receiver->name,
                    'email' => $message->receiver->email,
                ],
            ];
        });

        return response()->json([
            'data' => $customData,
        ]);
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        $userId = Auth::id();

        // Lấy danh sách các tin nhắn giữa người dùng hiện tại với tất cả người dùng khác
        $messages = Message::with(['sender', 'receiver'])
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get();

        // Nhóm các tin nhắn theo ID của đối tượng trò chuyện
        $conversations = $messages->groupBy(function ($message) use ($userId) {
            return $message->sender_id === $userId ? $message->receiver_id : $message->sender_id;
        })
            ->map(function ($group) use ($userId) {
                // Lấy tin nhắn mới nhất trong nhóm
                $lastMessage = $group->sortByDesc('created_at')->first();
                $otherUser = $lastMessage->sender_id === $userId ? $lastMessage->receiver : $lastMessage->sender;

                return [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'email' => $otherUser->email,
                    'file_url' => $otherUser->file_path,
                    'last_message' => $lastMessage->message,
                    'last_message_time' => $lastMessage->created_at->format('Y-m-d H:i:s'),
                ];
            })
            ->values(); // Reset chỉ số mảng

        return response()->json([
            'data' => $conversations,
        ]);
    }


}
