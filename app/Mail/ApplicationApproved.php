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
    public $status;


    /**
     * Create a new message instance.
     *
     * @param  mixed  $job
     * @param  mixed  $name
     * @param mixed $email
 */
    public function __construct($job, $name, $status)
    {
        $this->job = $job;
        $this->name = $name;
        $this->status = $status;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('ngtin590@gmail.com', $this->job->Company->company_name ?? 'Company')
            ->subject('Cập nhật trạng thái ứng tuyển: ' . $this->job->title)
            ->view('emails.application_status_update')
            ->with([
                'jobTitle' => $this->job->title,
                'applicantName' => $this->name,
                'status' => $this->status,
                'companyName' => $this->job->Company->company_name
            ]);
    }
}
