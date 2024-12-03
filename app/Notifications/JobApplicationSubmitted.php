<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class JobApplicationSubmitted extends Notification
{
    use Queueable;
    public $job;
    public $user;
    public $name;
    public $phone;
    public $email;
    /**
     * Create a new notification instance.
     */
    public function __construct(Job $job, $user, $name, $phone, $email)
    {
        $this->job = $job;
        $this->user = $user;
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Ứng viên {$this->name} đã ứng tuyển vị trí {$this->job->title}.",
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Thông báo ứng tuyển mới')
            ->line('Ứng viên ' . $this->name . ' (' . $this->email . ', ' . $this->phone . ') đã ứng tuyển vào công việc ' . $this->job->title)
            ->action('Xem chi tiết', url('/jobs/' . $this->job->id))
            ->line('Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!');
    }

    /**
     * Get the mail representation of the notification.
     */


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */

}
