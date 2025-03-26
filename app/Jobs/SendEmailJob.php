<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendOTP;
use Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     public $data;
     public $mailid;
    public function __construct($data,$mailid)
    {
        //

        $this->data=$data;
        $this->mailid=$mailid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        try {
        Mail::to($this->email)->send(new SendOTP($this->data));
    } catch (\Exception $e) {
        Log::error('Email failed to send: ' . $e->getMessage());
    }

      //  Mail::to($this->mailid)->send(new SendOTP($this->data));


    }
}
