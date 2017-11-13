<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Helpers;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use Config;

class InactiveUserNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InactiveUserNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Notifications to inactive user in Proteen';

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
        $teenData = $this->TeenagersRepository->getInactiveTeenDetailForNotification();
        $Current_time = strtotime(date('Y-m-d H:i:s'));
        foreach ($teenData AS $key => $value) {
            $Send_time = $value->t_last_activity;
            $final_date = round(abs($Current_time - $Send_time) / 86400, 2);
            $certificatePath = public_path($this->userCerfificatePath);
            $day = round($final_date);
            if ($day > 7) {
                if ($value->is_notify == 1) {
                    $token = $value->tdt_device_token;
                    $data = [];
                    $data['message'] = trans('labels.inactivenoti_message');
                    if ($value->tdt_device_type == 1) {
                        $return = Helpers::pushNotificationForiPhone($token,$data,$certificatePath);
                    } else if ($value->tdt_device_type == 2) {
                        $return = Helpers::pushNotificationForAndroid($token,$data);
                    }
                }
            }
        }
        $this->info('Push Notifications sent successfully!');
    }
}