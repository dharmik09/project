<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Auth;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Helpers;
use Config;

class earnPointNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'earnPointNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $TeenagersRepository)
    {
        parent::__construct();
        $this->TeenagersRepository = $TeenagersRepository;
        $this->userCerfificatePath = Config::get('constant.CERTIFICATE_PATH');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /*$userid = '';
        if (Auth::teenager()->check()) {
            $userid = Auth::teenager()->id();
        }*/

        $getTeenagerBoosterPoints = $this->TeenagersRepository->getTeenagerBoosterPoints(641);

        $totalPoints = $getTeenagerBoosterPoints['total'];
        if ($totalPoints == 1000 || $totalPoints == 2000 || $totalPoints == 3000 || $totalPoints == 4000 || $totalPoints == 5000) {
            $objDeviceToken = new DeviceToken();
            $tokenResult = $objDeviceToken->getDeviceTokenDetail(641);
            if (!empty($tokenResult)) {
                $token = $tokenResult[0]->tdt_device_token;
                $data = [];
                $data['message'] = "Congratulations! You scored  ". $totalPoints ." points in ProTeen! Keep playing ProTeen!";
                $certificatePath = $this->userCerfificatePath;
                if ($tokenResult[0]->tdt_device_type == 1) {
                    $return = Helpers::pushNotificationForiPhone($token,$data,$certificatePath);
                } else if ($tokenResult[0]->tdt_device_type == 2) {
                    $return = Helpers::pushNotificationForAndroid($token,$data);
                }
            }
        }
        $this->info('The notification sent successfully!');
    }
}
