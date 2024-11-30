<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobApplicationReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $job;
    public $applicant;
    public $notificationData;
    public function __construct($job, $applicant, $notificationData)
    {
        $this->job = $job;
        $this->applicant = $applicant;
        $this->notificationData = $notificationData;

    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return ['user.' . $this->job->Company->User->id];


    }
    public function broadcastAs()
    {
        return 'JobApplicationReceived';
    }
    public function broadcastWith(): array
    {
        return [
            'id' => $this->notificationData->id,
            'message' => "Ứng viên {$this->applicant->name} đã ứng tuyển vị trí {$this->job->title}.",
            'read_at' => $this->notificationData->read_at,
            'created_at' => $this->notificationData->created_at->format('Y-m-d H:i:s'),
        ];
    }

}
