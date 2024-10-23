<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusNotification extends Notification
{
    use Queueable;
    protected $job;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($job, $status)
    {
        $this->job = $job;
        $this->status = $status;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database']; // Gửi qua cả email và lưu vào database
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Cập nhật trạng thái đơn ứng tuyển')
            ->line('Trạng thái đơn ứng tuyển của bạn cho công việc "' . $this->job->title . '" đã được cập nhật.')
            ->line('Trạng thái hiện tại: ' . $this->status)
            ->action('Xem chi tiết', url('/jobs/' . $this->job->id))
            ->line('Cảm ơn bạn đã quan tâm đến công việc của chúng tôi!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'status' => $this->status,
            'message' => 'Trạng thái đơn ứng tuyển của bạn đã được cập nhật.',
        ];
    }
}
