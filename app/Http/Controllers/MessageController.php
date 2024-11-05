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

        $messages = Message::with(['sender', 'receiver'])
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get();

        $conversations = $messages->groupBy(function ($message) use ($userId) {
            return $message->sender_id === $userId ? $message->receiver_id : $message->sender_id;
        })->map(function ($group) use ($userId) {
            $lastMessage = $group->sortByDesc('created_at')->first();
            $otherUser = $lastMessage->sender_id === $userId ? $lastMessage->receiver : $lastMessage->sender;

            return [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'email' => $otherUser->email,
                'file_url' => $lastMessage->file_path ? url('uploads/' . $lastMessage->file_path) : null,
                'last_message' => $lastMessage->message,
                'last_message_time' => $lastMessage->created_at->toDateTimeString(),
            ];
        })->values();

        return response()->json([
            'data' => $conversations,
        ]);
    }



}
