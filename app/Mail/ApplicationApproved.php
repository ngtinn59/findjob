<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $job;
    public $name;
    public $email;


    /**
     * Create a new message instance.
     *
     * @param  mixed  $job
     * @param  mixed  $name
     * @param mixed $email
 */
    public function __construct($job, $name,$email)
    {
        // Truyền dữ liệu công việc và ứng viên vào mail class
        $this->job = $job;
        $this->name = $name;
        $this->email = $email;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Lấy tên công ty từ công việc liên quan
        $companyName = $this->job->company->company_name ?? 'Company';  // Nếu không có, sử dụng giá trị mặc định 'Company'

        return $this->from('ngtin590@gmail.com', $companyName)
            ->subject('Cập nhật trạng thái công việc  - ' . $this->job->title)
            ->view('emails.application_approved')
            ->with([
                'jobTitle' => $this->job->title,
                'applicantName' => $this->name,   // Lấy tên từ bảng pivot
                'applicantEmail' => $this->email, // Lấy email từ bảng pivot
            ]);
    }
}
