<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function sendMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);


        // Broadcast event with socket ID
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

}
