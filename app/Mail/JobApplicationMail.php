<?php

namespace App\Mail;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $job;
    public $name;
    public $email;
    public $phone;

    public function __construct(Job $job, $name, $email, $phone)
    {
        $this->job = $job;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
    }

    public function build()
    {
        return $this->subject('Thông báo ứng tuyển mới')
            ->view('emails.job_application') // Tạo file blade ở resources/views/emails/job_application.blade.php
            ->with([
                'jobTitle' => $this->job->title,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);
    }

}
