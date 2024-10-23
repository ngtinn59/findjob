<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationRejectedNotification extends Notification
{
    use Queueable;

    protected $job;

    /**
     * Create a new notification instance.
     */
    public function __construct($job)
    {
        $this->job = $job;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database', 'mail']; // Hoặc chỉ cần 'database' nếu bạn không muốn gửi email
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'message' => 'Ứng tuyển của bạn cho công việc "' . $this->job->title . '" đã bị xóa.',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Thông báo về ứng tuyển')
            ->line('Ứng tuyển của bạn cho công việc "' . $this->job->title . '" đã bị nhà tuyển dụng từ chối và xóa đi.')
            ->action('Xem công việc', url('/jobs/' . $this->job->id))
            ->line('Cảm ơn bạn đã quan tâm đến công việc của chúng tôi!');
    }
}
