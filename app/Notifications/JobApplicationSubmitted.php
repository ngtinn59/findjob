<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        return ['database', 'mail']; // Có thể thêm 'mail' nếu bạn muốn gửi qua email
    }

    public function toArray($notifiable)
    {
        return [
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'applicant_name' => $this->name,  // Thông tin name
            'applicant_phone' => $this->phone,  // Thông tin phone
            'applicant_email' => $this->email  // Thông tin email
        ];
    }

    public function toMail($notifiable)
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
