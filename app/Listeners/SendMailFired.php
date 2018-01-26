<?php

namespace App\Listeners;

use App\Events\SendMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
use Config;

class SendMailFired
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendMail  $event
     * @return void
     */
    public function handle(SendMail $event)
    {
        $template = ( isset($event->template) && $event->template != "") ? $event->template : "emails.Template";
        $data = $event->data;
        Mail::send(['html' => $template], $data, function($message) use ($data) {
            $subject = (isset($data['subject']) && $data['subject'] != "") ? $data['subject'] : Config::get('constant.NAME', 'ProTeenLife');
            $toEmail = (isset($data['toEmail']) && $data['toEmail'] != "") ? $data['toEmail'] : Config::get('constant.APP_EMAIL', 'info@proteenlife.com');
            $toName = (isset($data['toName']) && $data['toName'] != "") ? $data['toName'] : "";
            //send email
            $message->subject($subject);
            $message->to($toEmail, $toName);
        });
    }
}