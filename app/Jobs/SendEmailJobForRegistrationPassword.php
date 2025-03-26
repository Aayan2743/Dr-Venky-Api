<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use App\Mail\SendPassword;

class SendEmailJobForRegistrationPassword implements ShouldQueue
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

     //   Mail::to($this->mailid)->send(new SendPassword($this->data));
    }
}
