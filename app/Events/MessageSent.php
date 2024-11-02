<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return [
            new Channel('chat.' . $this->message->receiver_id),
        ];
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }

    public function broadcastWith(): array
    {
        // Tải người gửi nếu chưa được tải
        $sender = $this->message->sender; // Giả sử bạn đã định nghĩa quan hệ 'sender' trong mô hình Message

        return [
            'message' => $this->message->message,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $sender->name,
            'receiver_id' => $this->message->receiver_id,
            'file_url' => $this->message->file_path ? url('uploads/' . $this->message->file_path) : null, // Đường dẫn file
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }

}
