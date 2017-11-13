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

class expiredProCoinsForTeenager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expiredProCoinsDateForTeenager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ProCoins become zero after Package Expired for Teenager';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TeenagersRepository $TeenagersRepository,CoinRepository $CoinRepository, ParentsRepository $ParentsRepository, SponsorsRepository $SponsorsRepository)
    {
        parent::__construct();
        $this->TeenagersRepository = $TeenagersRepository;
        $this->CoinRepository =  $CoinRepository;
        $this->ParentsRepository = $ParentsRepository;
        $this->SponsorsRepository = $SponsorsRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userDetail = $this->TeenagersRepository->getAllUserDeatilForTeenager();
        $userArray = [];
        $objGiftUser = new TeenagerCoinsGift();

        foreach ($userDetail AS $key => $value) {
            if(!in_array($value->tn_userid, $userArray)) {
                $userArray[] = $value->tn_userid;
                $Current_time = strtotime(date('Y-m-d'));

                $coinsDetail = $this->CoinRepository->getAllCoinsDetailByid($value->tn_package_id);
                $end_time = strtotime($value->tn_trans_date . "+".$coinsDetail[0]->c_valid_for." days");

                $final_date = round(abs($end_time - $Current_time) / 86400, 2);
                $day = round($final_date);
                $coins = 0;
                $teenagerData = $userDetail = $objGiftUser->getAllTeenagerCoinsGiftDetailByAdmin($value->tn_userid,0,1);
                if ($end_time < $Current_time) {
                    if(!empty($teenagerData) && count($teenagerData) > 0)
                    {
                        $c_time = strtotime(date('Y-m-d'));
                        $validDays = Helpers::getConfigValueByKey('ADMIN_GIFTED_COINS_VALIDITY');
                        $coinsDetail = $this->CoinRepository->getAllCoinsDetailByid($value->tn_package_id);
                        $e_time = strtotime($teenagerData->tcg_gift_date . "+".$validDays." days");
                        if ($e_time < $c_time) {
                            $return = $this->TeenagersRepository->updateTeenagerCoinsDetail($value->tn_userid, $coins);
                        }
                    }
                    else
                    {
                        $return = $this->TeenagersRepository->updateTeenagerCoinsDetail($value->tn_userid, $coins);
                    }

                    //Update `tcg_coins_expired` field in teenager Gifted coins table
                    $objGiftUser->updateExpiredCoinsField($value->tn_userid,1);
                    //Update tn_coins_expired field in transaction table
                    $this->TeenagersRepository->updateExpiredCoinsField($value->tn_userid,1);
                }
            }
        }

        $parentDetail = $this->TeenagersRepository->getAllUserDeatilForParent();
        $userArray = [];
        foreach ($parentDetail AS $key => $value) {
            if(!in_array($value->tn_userid, $userArray)) {
                $userArray[] = $value->tn_userid;
                $Current_time = strtotime(date('Y-m-d'));

                $coinsDetail = $this->CoinRepository->getAllCoinsDetailByid($value->tn_package_id);
                $Send_time = strtotime($value->tn_trans_date . "+".$coinsDetail[0]->c_valid_for." days");

                $final_date = round(abs($Send_time - $Current_time) / 86400, 2);
                $day = round($final_date);
                $coins = 0;
                $parentData = $userDetail = $objGiftUser->getAllTeenagerCoinsGiftDetailByAdmin($value->tn_userid,0,2);
                if ($Send_time < $Current_time) {
                    if(!empty($parentData) && count($parentData) > 0)
                    {
                        $c_time = strtotime(date('Y-m-d'));
                        $validDays = Helpers::getConfigValueByKey('ADMIN_GIFTED_COINS_VALIDITY');
                        $coinsDetail = $this->CoinRepository->getAllCoinsDetailByid($value->tn_package_id);
                        $e_time = strtotime($parentData->tcg_gift_date . "+".$validDays." days");
                        if ($e_time < $c_time) {
                            $responseArray = $this->ParentsRepository->updateParentCoinsDetail($value->tn_userid, $coins);
                        }
                    }
                    else
                    {
                        $responseArray = $this->ParentsRepository->updateParentCoinsDetail($value->tn_userid, $coins);
                    }

                    //Update `tcg_coins_expired` field in teenager Gifted coins table
                    $objGiftUser->updateExpiredCoinsField($value->tn_userid,2);
                    //Update tn_coins_expired field in transaction table
                    $this->TeenagersRepository->updateExpiredCoinsField($value->tn_userid,2);
                }
            }
        }

        $sponsorDetail = $this->TeenagersRepository->getAllUserDeatilForSponsor();
        $userArray = [];
        foreach ($sponsorDetail AS $key => $value) {
            if(!in_array($value->tn_userid, $userArray)) {
                $userArray[] = $value->tn_userid;
                $Current_time = strtotime(date('Y-m-d'));

                $coinsDetail = $this->CoinRepository->getAllCoinsDetailByid($value->tn_package_id);
                $Send_time = strtotime($value->tn_trans_date . "+".$coinsDetail[0]->c_valid_for." days");

                $final_date = round(abs($Send_time - $Current_time) / 86400, 2);
                $day = round($final_date);
                $coins = 0;
                $sponsorData = $userDetail = $objGiftUser->getAllTeenagerCoinsGiftDetailByAdmin($value->tn_userid,0,4);
                if ($Send_time < $Current_time) {
                    if(!empty($sponsorData) && count($sponsorData) > 0)
                    {
                        $c_time = strtotime(date('Y-m-d'));
                        $validDays = Helpers::getConfigValueByKey('ADMIN_GIFTED_COINS_VALIDITY');
                        $coinsDetail = $this->CoinRepository->getAllCoinsDetailByid($value->tn_package_id);
                        $e_time = strtotime($sponsorData->tcg_gift_date . "+".$validDays." days");
                        if ($e_time < $c_time) {
                            $result = $this->SponsorsRepository->updateSponsorCoinsDetail($value->tn_userid, $coins);
                        }
                    }
                    else
                    {
                        $result = $this->SponsorsRepository->updateSponsorCoinsDetail($value->tn_userid, $coins);
                    }

                    //Update `tcg_coins_expired` field in teenager Gifted coins table
                    $objGiftUser->updateExpiredCoinsField($value->tn_userid,4);
                    //Update tn_coins_expired field in transaction table
                    $this->TeenagersRepository->updateExpiredCoinsField($value->tn_userid,4);
                }
            }
        }
        $this->info('ProCoins become zero after Package Expired for Teenager');
    }
}