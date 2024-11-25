<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $jobId;
    public $userId;
    public $status;
    public $name;
    public $notification;
    public $job;


    public function __construct($jobId, $userId, $status, $name, $notification, $job)
    {
        $this->jobId = $jobId;
        $this->userId = $userId;
        $this->status = $status;
        $this->name = $name;
        $this->notification = $notification;
        $this->job = $job;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return ['user.' . $this->userId]; // Kênh riêng cho ứng viên
    }
    public function broadcastAs()
    {
        return 'ApplicationStatusUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'message' => "Trạng thái ứng tuyển của công việc \"{$this->job->title}\" đã được cập nhật.",
            'read_at' => $this->notification->read_at,
            'created_at' => $this->notification->created_at->format('Y-m-d H:i:s'),
        ];
    }

}
