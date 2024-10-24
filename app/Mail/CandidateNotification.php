<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateNotification extends Mailable
{
    use Queueable, SerializesModels;


    public $subject;
    public $messageContent;

    public $company_name;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $messageContent,$company_name)
    {
        $this->subject = $subject;
        $this->messageContent = $messageContent;
        $this->company_name = $company_name;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        return $this->from('ngtin590@gmail.com', $this->company_name) // Correct reference to $this->company_name
        ->subject($this->subject) // Correct chaining of methods
        ->view('emails.candidate_notification')
            ->with([
                'messageContent' => $this->messageContent,
                'company_name' => $this->company_name
            ]);
    }

}
