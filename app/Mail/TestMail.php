<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;
    public $sender;
    public $subject;
    public $body;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sender, $subject, $body, $song, $msg, $date) {
        $this->sender = $sender;
        $this->subject = $subject;
        $this->body = $body;
        $this->song = $song;
        $this->msg = $msg;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */

    public function build() {
        return $this
            ->from($this->sender)
            ->subject($this->subject)
            ->markdown('emails.tests.testmail')->with([
                'body' => $this->body,
                'song' => $this->song,
                'msg' => $this->msg,
                'date' => $this->date,
            ]);
    }
}
