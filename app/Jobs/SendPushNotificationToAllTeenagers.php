<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Helpers;
use Config;
use App\DeviceToken;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SendPushNotificationToAllTeenagers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $pushNotificationData;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pushNotificationData)
    {
        $this->pushNotificationData = $pushNotificationData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->objDeviceToken = new DeviceToken();

        $this->log = new Logger('push-notification');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));

        $androidToken = [];
        // $pushNotificationData = [];
        // $pushNotificationData['isAdmin'] = $this->pushNotificationData['notificationMessage'];
        // $pushNotificationData['message'] = $this->pushNotificationData['notificationMessage'];
        $certificatePath = public_path(Config::get('constant.CERTIFICATE_PATH'));

        $userDeviceToken = $this->objDeviceToken->getAllDeviceTokenDetail();

        $this->log->info("Notification sent started");

        if(count($userDeviceToken)>0){

            $this->log->info("IOS notification sent started");

            foreach ($userDeviceToken as $key => $value) {
                if($value->tdt_device_type == 2){
                    $androidToken[] = $value->tdt_device_token;
                }
                if($value->tdt_device_type == 1){
                    Helpers::pushNotificationForiPhone($value->tdt_device_token,$this->pushNotificationData,$certificatePath);
                    // $this->log->info("IOS notification found on Pointer => ". $key);
                }
            }

            $this->log->info("All IOS notification Sent Successfully");

            if(isset($androidToken) && count($androidToken) > 0)
            {
                $tokenArrChunk = array_chunk($androidToken, 995); 
                if(isset($tokenArrChunk) && count($tokenArrChunk) > 0)
                {
                    $this->log->info("Android notification sent started");
                    foreach($tokenArrChunk as $k1=>$tokenBunch)
                    {                       
                        $return = Helpers::pushNotificationForAndroid($tokenBunch,$this->pushNotificationData); 
                        // $this->log->info("Andorid notification found on Pointer => ". $k1);
                    }
                   $this->log->info("All Andorid notification Sent Successfully");
                }
            }
        
        }

        $this->log->info("All Notification sent Successfully");

        return $this->pushNotificationData;
    }
}
