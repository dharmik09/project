<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications;
use Helpers;
use Config;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Coin\Contracts\CoinRepository;
use App\Services\Sponsors\Contracts\SponsorsRepository;
use App\Services\Parents\Contracts\ParentsRepository;
use App\TeenagerCoinsGift;
use App\Services\Schools\Contracts\SchoolsRepository;

class resetAdminGiftedProCoins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resetAdminGiftedProCoins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $TeenagersRepository,CoinRepository $CoinRepository,ParentsRepository $ParentsRepository, SponsorsRepository $SponsorsRepository,SchoolsRepository $SchoolsRepository)
    {
        parent::__construct();
        $this->TeenagersRepository = $TeenagersRepository;
        $this->CoinRepository =  $CoinRepository;
        $this->ParentsRepository = $ParentsRepository;
        $this->SponsorsRepository = $SponsorsRepository;
        $this->SchoolsRepository  = $SchoolsRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $objGiftUser = new TeenagerCoinsGift();
        $validDays = Helpers::getConfigValueByKey('ADMIN_GIFTED_COINS_VALIDITY');

        $teenDetail = $objGiftUser->getAllTeenagerCoinsGiftDetailByAdminGifted(0,1);
        $userArray = [];

        if(!empty($teenDetail) && count($teenDetail) >0)
        {
            foreach($teenDetail as $key => $value)
            {
                if(!in_array($value->tcg_reciver_id, $userArray))
                {
                    $userArray[] = $value->tcg_reciver_id;
                    $Current_time = strtotime(date('Y-m-d'));
                    $end_time = strtotime($value->tcg_gift_date . "+".$validDays." days");
                    $coins = 0;
                    $teenagerData = $userDetail = $this->TeenagersRepository->getAllUserDeatilForTeenagerByUserId($value->tcg_reciver_id,1);
                    if ($end_time < $Current_time) {
                        if(!empty($teenagerData) && count($teenagerData) > 0)
                        {
                            $c_time = strtotime(date('Y-m-d'));

                            $coinsDetail = $this->CoinRepository->getAllCoinsDetailByid($teenagerData->tn_package_id);
                            $e_time = strtotime($teenagerData->tn_trans_date . "+".$coinsDetail[0]->c_valid_for." days");
                            if ($e_time < $c_time) {
                                $return = $this->TeenagersRepository->updateTeenagerCoinsDetail($value->tcg_reciver_id, $coins);
                            }
                        }
                        else
                        {
                            $return = $this->TeenagersRepository->updateTeenagerCoinsDetail($value->tcg_reciver_id, $coins);
                        }
                        //Update `tcg_coins_expired` field in teenager Gifted coins table
                        $objGiftUser->updateExpiredCoinsField($value->tcg_reciver_id,1);
                        //Update tn_coins_expired field in transaction table
                        $this->TeenagersRepository->updateExpiredCoinsField($value->tcg_reciver_id,1);
                    }
                }
            }
        }

        $parentDetail = $objGiftUser->getAllTeenagerCoinsGiftDetailByAdminGifted(0,2);
        $userArray = [];

        if(!empty($parentDetail) && count($parentDetail) >0)
        {
            foreach($parentDetail as $key => $value)
            {
                if(!in_array($value->tcg_reciver_id, $userArray))
                {
                    $userArray[] = $value->tcg_reciver_id;
                    $Current_time = strtotime(date('Y-m-d'));
                    $end_time = strtotime($value->tcg_gift_date . "+".$validDays." days");
                    $coins = 0;
                    $parentData = $userDetail = $this->TeenagersRepository->getAllUserDeatilForTeenagerByUserId($value->tcg_reciver_id,2);
                    if ($end_time < $Current_time) {
                        if(!empty($parentData) && count($parentData) > 0)
                        {
                            $c_time = strtotime(date('Y-m-d'));

                            $coinsDetail = $this->CoinRepository->getAllCoinsDetailByid($parentData->tn_package_id);
                            $e_time = strtotime($parentData->tn_trans_date . "+".$coinsDetail[0]->c_valid_for." days");
                            if ($e_time < $c_time) {
                                $responseArray = $this->ParentsRepository->updateParentCoinsDetail($value->tcg_reciver_id, $coins);
                            }
                        }
                        else
                        {
                            $responseArray = $this->ParentsRepository->updateParentCoinsDetail($value->tcg_reciver_id, $coins);
                        }
                        //Update `tcg_coins_expired` field in teenager Gifted coins table
                        $objGiftUser->updateExpiredCoinsField($value->tcg_reciver_id,2);
                        //Update tn_coins_expired field in transaction table
                        $this->TeenagersRepository->updateExpiredCoinsField($value->tcg_reciver_id,2);
                    }
                }
            }
        }


        $sponsorDetail = $objGiftUser->getAllTeenagerCoinsGiftDetailByAdminGifted(0,4);
        $userArray = [];

        if(!empty($sponsorDetail) && count($sponsorDetail) >0)
        {
            foreach($sponsorDetail as $key => $value)
            {
                if(!in_array($value->tcg_reciver_id, $userArray))
                {
                    $userArray[] = $value->tcg_reciver_id;
                    $Current_time = strtotime(date('Y-m-d'));
                    $end_time = strtotime($value->tcg_gift_date . "+".$validDays." days");
                    $coins = 0;
                    $sponsorData = $userDetail = $this->TeenagersRepository->getAllUserDeatilForTeenagerByUserId($value->tcg_reciver_id,4);
                    if ($end_time < $Current_time) {
                        if(!empty($sponsorData) && count($sponsorData) > 0)
                        {
                            $c_time = strtotime(date('Y-m-d'));

                            $coinsDetail = $this->CoinRepository->getAllCoinsDetailByid($sponsorData->tn_package_id);
                            $e_time = strtotime($sponsorData->tn_trans_date . "+".$coinsDetail[0]->c_valid_for." days");
                            if ($e_time < $c_time) {
                                $result = $this->SponsorsRepository->updateSponsorCoinsDetail($value->tcg_reciver_id, $coins);
                            }
                        }
                        else
                        {
                            $result = $this->SponsorsRepository->updateSponsorCoinsDetail($value->tcg_reciver_id, $coins);
                        }
                        //Update `tcg_coins_expired` field in teenager Gifted coins table
                        $objGiftUser->updateExpiredCoinsField($value->tcg_reciver_id,4);
                        //Update tn_coins_expired field in transaction table
                        $this->TeenagersRepository->updateExpiredCoinsField($value->tcg_reciver_id,4);
                    }
                }
            }
        }

        $schoolDetail = $objGiftUser->getAllTeenagerCoinsGiftDetailByAdminGifted(0,3);
        $userArray = [];

        if(!empty($schoolDetail) && count($schoolDetail) >0)
        {
            foreach($schoolDetail as $key => $value)
            {
                if(!in_array($value->tcg_reciver_id, $userArray))
                {
                    $userArray[] = $value->tcg_reciver_id;
                    $Current_time = strtotime(date('Y-m-d'));
                    $end_time = strtotime($value->tcg_gift_date . "+".$validDays." days");
                    $coins = 0;
                    if ($end_time < $Current_time) {
                        $result = $this->$SchoolsRepository->updateSchoolCoinsDetail($value->tcg_reciver_id, $coins);
                        //Update `tcg_coins_expired` field in teenager Gifted coins table
                        $objGiftUser->updateExpiredCoinsField($value->tcg_reciver_id,3);
                    }
                }
            }
        }

        $this->info('ProCoins become zero after Package Expired for Teenager');
       /* echo $validDays;

        print_r($teenDetail);
        exit;*/
    }
}
