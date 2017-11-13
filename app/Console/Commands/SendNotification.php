<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications;
use Helpers;
use Config;
use App\Services\Teenagers\Contracts\TeenagersRepository;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends a Notification to users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $TeenagersRepository)
    {
        parent::__construct();
        $this->userCerfificatePath = Config::get('constant.CERTIFICATE_PATH');
        $this->TeenagersRepository = $TeenagersRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $objNotification = new Notifications();
        $teenData = $objNotification->getTeenDetailForNotification();
        if(isset($teenData) && count($teenData) > 0)
        {
            foreach($teenData AS $key => $value) 
            {
                if ($value->is_notify == 1) 
                {
                    $data = [];
                    $data['message'] = $value->n_notification_text;                
                    if ($value->tdt_device_type == 1) 
                    {
                        $singletoken = $value->tdt_device_token;
                        $certificatePath = public_path($this->userCerfificatePath); 
                        $return = Helpers::pushNotificationForiPhone($singletoken,$data,$certificatePath);
                    } 
                    elseif($value->tdt_device_type == 2) 
                    {
                        $tokenArr[] = $value->tdt_device_token;    
                    }
                }                
            }
            if(isset($tokenArr) && count($tokenArr) > 0)
            {
               $tokenArrChunk = array_chunk($tokenArr, 995); 
               if(isset($tokenArrChunk) && count($tokenArrChunk) > 0)
               {
                   foreach($tokenArrChunk as $k1=>$tokenBunch)
                   {                       
                        $return = Helpers::pushNotificationForAndroid($tokenBunch,$data); 
                   }
               }              
            } 
            
            //All notification sent successfully so now update the staqtus 
            foreach($teenData AS $key => $value) 
            {
                $objNotification->updateNotificationStatusById($value->id);
            }
            $this->info('Push Notifications sent successfully!');
        }
    }
}