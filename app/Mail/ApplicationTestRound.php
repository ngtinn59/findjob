<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationTestRound extends Mailable
{
    use Queueable, SerializesModels;
    public $job;
    public $name;
    public $email;
    /**
     * Create a new message instance.
     */
    public function __construct($job, $name , $email)
    {
        $this->job = $job;
        $this->name = $name;
        $this->email = $email;

    }

    /**
     * Get the message envelope.
     */

    public function build()
    {
        // Lấy tên công ty từ công việc liên quan
        $companyName = $this->job->Company->company_name ?? 'Company';  // Nếu không có, sử dụng giá trị mặc định 'Company'
        return $this->from('ngtin590@gmail.com', $companyName)
            ->subject('Cập Nhật Trạng Thái Đơn Xin Việc')
            ->view('emails.application_test_round')
            ->with([
                'jobTitle' => $this->job->title,
                'applicantName' => $this->name,   // Lấy tên từ bảng pivot
                'applicantEmail' => $this->email,
                'companyName' => $companyName
            ]);
    }


}
