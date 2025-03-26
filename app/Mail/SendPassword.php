<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $password_details;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($password_details)
    {
        //

        $this->password_details=$password_details;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Send Password',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        //    $details=['name'=>$req->name , 'mail'=>$req->email ,'password'=>$password_generate];    
        return new Content(
            view: 'Mails.sendpassword',
            with:[
                'name'=>$this->password_details['name'],
                'mail'=>$this->password_details['mail'],
                'password'=>$this->password_details['password'],
            ]
        );
        
        
        
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
