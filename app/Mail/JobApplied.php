<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;

class JobApplied extends Mailable
{
    use Queueable, SerializesModels;

    public $job;

    /**
     * Create a new message instance.
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $job = $this->job;
        $company = $job->Company;
        return $this->from('ngtin590@gmail.com', $company->company_name)
            ->subject('Xác nhận tuyển dụng')
            ->view('emails.job_applied')
            ->with([
                'jobTitle' => $job->title,
                'companyName' => $job->Company->company_name,
                'address' => $job->work_address,
                'salary_from' => $job->salary_from,
                'salary_to' => $job->salary_to,
                'userName' => Auth::user()->name,
            ]);
    }

}
