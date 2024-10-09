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
        $company = $job->company;
//        $jobtype = $job->jobtype->first()->name;
        return $this->from('ngtin590@gmail.com', $company->name)
            ->subject('Xác nhận tuyển dụng')
            ->view('emails.job_applied')
            ->with([
                'jobTitle' => $job->title,
                'companyName' => $company->name,
                'address' => $job->address,
                'salary' => $job->salary,
//                'jobtype' => $jobtype,
                'userName' => Auth::user()->name,
            ]);
    }

}
