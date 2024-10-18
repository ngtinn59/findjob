<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationContacted extends Mailable
{
    use Queueable, SerializesModels;

    public $job;
    public $name;

    public function __construct($job, $name)
    {
        $this->job = $job;
        $this->name = $name;
    }

    /**
     * Get the message envelope.
     */


    public function build()
    {
        // Lấy tên công ty từ công việc liên quan
        $companyName = $this->job->company->company_name ?? 'Company';  // Nếu không có, sử dụng giá trị mặc định 'Company'

        return $this->from('ngtin590@gmail.com', $companyName)
            ->subject('Cập Nhật Trạng Thái Đơn Xin Việc')
            ->view('emails.application_contacted')
            ->with([
                'jobTitle' => $this->job->title,
                'applicantName' => $this->name,   // Lấy tên từ bảng pivot
                'applicantEmail' => $this->email, // Lấy email từ bảng pivot
            ]);
    }
}

