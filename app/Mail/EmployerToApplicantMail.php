<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Job;
use App\Models\User;

class EmployerToApplicantMail extends Mailable
{
    use Queueable, SerializesModels;

    public $job;
    public $user;
    public $subject;
    public $messageContent;

    /**
     * Create a new message instance.
     */
    public function __construct(Job $job, User $user, $subject, $messageContent)
    {
        $this->job = $job;
        $this->user = $user;
        $this->subject = $subject;
        $this->messageContent = $messageContent;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from('ngtin590@gmail.com', $this->job->company->company_name) // Correct reference to $this->job
        ->view('emails.employer_to_applicant') // View này sẽ chứa nội dung của email
        ->with([
            'jobTitle' => $this->job->title,
            'userName' => $this->user->name,
            'messageContent' => $this->messageContent,
        ]);
    }

}


