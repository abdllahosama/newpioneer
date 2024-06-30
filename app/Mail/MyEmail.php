<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    protected $title;
    protected $content;
    protected $type;
    protected $type_id;




    public function __construct($title = null, $content = null, $type = null, $type_id = null)
    {
        $this->title   = $title;
        $this->content = $content;
        $this->type    = $type;
        $this->type_id = $type_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $title   = $this->title;
        $content = $this->content;
        $type    = $this->type;
        $type_id = $this->type_id;
        if ($type == 'offer') {
            $type = 'orderOffers';
        }
        $url = "https://monaqladashboard.thetechtitans.net/";

        return $this->subject('New Notification')
            ->view('email', compact('title', 'content', 'type', 'type_id', 'url'));
    }
}
