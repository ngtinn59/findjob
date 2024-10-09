<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationApproved extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        // Bạn có thể truyền dữ liệu vào constructor nếu cần
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Application Approved')
            ->view('emails.application_approved');
        // Nếu bạn có dữ liệu để truyền vào view, sử dụng ->with(['key' => $value])
    }
}
