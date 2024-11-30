<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class JobCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $job;
    public $notification;
    public function __construct($job, $notification)
    {
        $this->job = $job;
        $this->notification = $notification;

    }


    public function broadcastOn()
    {
        $admin = '1';
        return ['user.' . $admin];
    }
    public function broadcastAs()
    {
        return 'JobCreated';
    }
    public function broadcastWith(): array
    {
        // Get the notification data as an array
        $data = $this->notification->toArray($this->notification);

        return $data;
    }


}
