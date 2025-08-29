<?php

namespace App\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class sendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public function __construct($email_data)
    {
        $this->data = $email_data;
    }
    public function build()
    {
        $function_name = 'build';
        try {
            return $this->from(config('constants.mail_from_address'), config('constants.mail_from_name'))->subject($this->data['subject'])
                ->view($this->data['view']);
        } catch (Exception $e) {
            logCatchException($e, 'sendEmail.php', $function_name);
            return redirect()->back()->with('error', 'Something went wrong. Please try after sometime.');
        }
    }
}
