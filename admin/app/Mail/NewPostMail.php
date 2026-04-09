<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPostMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $slug;
    public $excerpt;
    public $coverImage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $slug, $excerpt, $coverImage = '')
    {
        $this->title = $title;
        $this->slug = $slug;
        $this->excerpt = $excerpt;
        $this->coverImage = $coverImage;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Post: ' . $this->title)
                    ->view('emails.new_post');
    }
}
