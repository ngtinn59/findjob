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
            'notifications' => [
                'notifiable_id' => $this->notification->id,
                'data' => [
                    'job_id' => $this->jobId,
                    'job_title' => $this->job->title,
                    'status' => $this->status,
                    'message' => 'Trạng thái đơn ứng tuyển của bạn đã được cập nhật.'
                ],
                'read' => $this->notification->read_at ? true : false, // Kiểm tra xem thông báo đã đọc hay chưa
                'created_at' => $this->notification->created_at->format('Y-m-d H:i:s'), // Định dạng thời gian
            ]

        ];
    }

}
