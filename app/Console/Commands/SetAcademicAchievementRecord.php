<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Helpers;
use Config;
use DB;
use App\Jobs\SetProfessionMatchScale;
use Log;
use Exception;
use App\Teenagers;

class SetAcademicAchievementRecord extends Command
{
    /**
     * The name and signature of the console command.
     * --for= "all OR specific teenagerId" && --infoFor= "all OR academic OR achievement"
     * @var command :: php artisan calculateAcademicAchievementInfo {--for=} {--infoFor=}
     */
    protected $signature = 'calculateAcademicAchievementInfo {--for=} {--infoFor=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate Acadeimc & Achievement for Teenagers.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Info should be academic / Achievement / All
        $infoFor = trim($this->option('infoFor'));
        //Calculation for Individual teenager / All teenagers
        $calculationFor = trim($this->option('for'));
        if($calculationFor != "" && $infoFor != "") {
            if( strtolower($calculationFor) == "all") {
                $objTeenager = new Teenagers;
                $getAllActiveTeenagerIds = $objTeenager->join('pro_tmd_teenager_meta_data', 'pro_t_teenagers.id', '=', 'pro_tmd_teenager_meta_data.tmd_teenager')
                                    ->select('pro_t_teenagers.id')
                                    ->where('pro_t_teenagers.deleted', 1)
                                    ->groupBy('pro_t_teenagers.id')
                                    ->get();
                if($getAllActiveTeenagerIds && isset($getAllActiveTeenagerIds[0]->id)) {
                    switch(strtolower($infoFor)) {
                        case 'all' :
                            foreach($getAllActiveTeenagerIds as $teenagerId) {
                                $metaInfoOfTeenager = $teenagerId->getTeenagerMetaDataInfo;
                                $teenagerAcademicCalculation = ($metaInfoOfTeenager) ? $metaInfoOfTeenager->where('tmd_meta_id', 2)->toArray() : [];
                                $teenagerAchievementCalculation = ($metaInfoOfTeenager) ? $metaInfoOfTeenager->where('tmd_meta_id', 1)->toArray() : [];
                                
                                if($teenagerAcademicCalculation && count($teenagerAcademicCalculation) > 1) {
                                    $academicString = "";
                                    foreach($teenagerAcademicCalculation as $dataLoop) {
                                        $academicString .= $dataLoop['tmd_meta_value']. " ";
                                    }
                                    //Delete all current academic data
                                    $metaDataDelete = DB::table('pro_tmd_teenager_meta_data')->where(['tmd_teenager' => $teenagerId->id, 'tmd_meta_id' => 2])->delete();
                                    
                                    $data = [];
                                    $data['tmd_teenager'] = $teenagerId->id;
                                    $data['tmd_meta_id'] = 2;
                                    $data['tmd_meta_value'] = $academicString;
                                    $metaDataInsert = DB::table('pro_tmd_teenager_meta_data')->insert($data);
                                }
                                if($teenagerAchievementCalculation && count($teenagerAchievementCalculation) > 1) {
                                    $achievementString = "";
                                    foreach($teenagerAchievementCalculation as $dataLoop) {
                                        $achievementString .= $dataLoop['tmd_meta_value']. " ";
                                    }
                                    //Delete all current academic data
                                    $metaDataDelete = DB::table('pro_tmd_teenager_meta_data')->where(['tmd_teenager' => $teenagerId->id, 'tmd_meta_id' => 1])->delete();
                                    
                                    $data = [];
                                    $data['tmd_teenager'] = $teenagerId->id;
                                    $data['tmd_meta_id'] = 1;
                                    $data['tmd_meta_value'] = $achievementString;
                                    $metaDataInsert = DB::table('pro_tmd_teenager_meta_data')->insert($data);
                                }
                            }
                            $this->info("--> Info Calculating for teenagerId = ".$calculationFor. " And Info of " .$infoFor);
                            break;
                        case 'academic' :
                            foreach($getAllActiveTeenagerIds as $teenagerId) {
                                $metaInfoOfTeenager = $teenagerId->getTeenagerMetaDataInfo;
                                $teenagerAcademicCalculation = ($metaInfoOfTeenager) ? $metaInfoOfTeenager->where('tmd_meta_id', 2)->toArray() : [];
                                if($teenagerAcademicCalculation && count($teenagerAcademicCalculation) > 1) {
                                    $academicString = "";
                                    foreach($teenagerAcademicCalculation as $dataLoop) {
                                        $academicString .= $dataLoop['tmd_meta_value']. " ";
                                    }
                                    //Delete all current academic data
                                    $metaDataDelete = DB::table('pro_tmd_teenager_meta_data')->where(['tmd_teenager' => $teenagerId->id, 'tmd_meta_id' => 2])->delete();
                                    
                                    $data = [];
                                    $data['tmd_teenager'] = $teenagerId->id;
                                    $data['tmd_meta_id'] = 2;
                                    $data['tmd_meta_value'] = $academicString;
                                    $metaDataInsert = DB::table('pro_tmd_teenager_meta_data')->insert($data);
                                }
                            }
                            $this->info("--> Info Calculating for teenagerId = ".$calculationFor. " And Info of academic");
                            break;
                        case 'achievement' :
                            foreach($getAllActiveTeenagerIds as $teenagerId) {
                                $metaInfoOfTeenager = $teenagerId->getTeenagerMetaDataInfo;
                                $teenagerAchievementCalculation = ($metaInfoOfTeenager) ? $metaInfoOfTeenager->where('tmd_meta_id', 1)->toArray() : [];
                                if($teenagerAchievementCalculation && count($teenagerAchievementCalculation) > 1) {
                                    $achievementString = "";
                                    foreach($teenagerAchievementCalculation as $dataLoop) {
                                        $achievementString .= $dataLoop['tmd_meta_value']. " ";
                                    }
                                    //Delete all current academic data
                                    $metaDataDelete = DB::table('pro_tmd_teenager_meta_data')->where(['tmd_teenager' => $teenagerId->id, 'tmd_meta_id' => 1])->delete();
                                    
                                    $data = [];
                                    $data['tmd_teenager'] = $teenagerId->id;
                                    $data['tmd_meta_id'] = 1;
                                    $data['tmd_meta_value'] = $achievementString;
                                    $metaDataInsert = DB::table('pro_tmd_teenager_meta_data')->insert($data);
                                }
                            }
                            $this->info("--> Info Calculating for teenagerId = ".$calculationFor. " And Info of achievement");
                            break;
                        default:
                    }
                } else {
                    Log::info("No any teenager metadata combinations found!");
                    $this->info("No any teenager metadata combinations found!");
                }
            } else {
                $objTeenager = new Teenagers;
                $getTeenagerDetail = $objTeenager->join('pro_tmd_teenager_meta_data', 'pro_t_teenagers.id', '=', 'pro_tmd_teenager_meta_data.tmd_teenager')
                                    ->select('pro_t_teenagers.id')
                                    ->where('pro_t_teenagers.deleted', 1)
                                    ->where('pro_t_teenagers.id', $calculationFor)
                                    ->groupBy('pro_t_teenagers.id')
                                    ->get();
                if($getTeenagerDetail && isset($getTeenagerDetail[0]->id)) {
                    switch(strtolower($infoFor)) {
                        case 'all' :
                            $metaInfoOfTeenager = $getTeenagerDetail[0]->getTeenagerMetaDataInfo;
                            $teenagerAcademicCalculation = ($metaInfoOfTeenager) ? $metaInfoOfTeenager->where('tmd_meta_id', 2)->toArray() : [];
                            $teenagerAchievementCalculation = ($metaInfoOfTeenager) ? $metaInfoOfTeenager->where('tmd_meta_id', 1)->toArray() : [];
                            
                            if($teenagerAcademicCalculation && count($teenagerAcademicCalculation) > 1) {
                                $academicString = "";
                                foreach($teenagerAcademicCalculation as $dataLoop) {
                                    $academicString .= $dataLoop['tmd_meta_value']. " ";
                                }
                                //Delete all current academic data
                                $metaDataDelete = DB::table('pro_tmd_teenager_meta_data')->where(['tmd_teenager' => $getTeenagerDetail[0]->id, 'tmd_meta_id' => 2])->delete();
                                
                                $data = [];
                                $data['tmd_teenager'] = $getTeenagerDetail[0]->id;
                                $data['tmd_meta_id'] = 2;
                                $data['tmd_meta_value'] = $academicString;
                                $metaDataInsert = DB::table('pro_tmd_teenager_meta_data')->insert($data);
                            }
                            if($teenagerAchievementCalculation && count($teenagerAchievementCalculation) > 1) {
                                $achievementString = "";
                                foreach($teenagerAchievementCalculation as $dataLoop) {
                                    $achievementString .= $dataLoop['tmd_meta_value']. " ";
                                }
                                //Delete all current academic data
                                $metaDataDelete = DB::table('pro_tmd_teenager_meta_data')->where(['tmd_teenager' => $getTeenagerDetail[0]->id, 'tmd_meta_id' => 1])->delete();
                                
                                $data = [];
                                $data['tmd_teenager'] = $getTeenagerDetail[0]->id;
                                $data['tmd_meta_id'] = 1;
                                $data['tmd_meta_value'] = $achievementString;
                                $metaDataInsert = DB::table('pro_tmd_teenager_meta_data')->insert($data);
                            }
                            Log::info("Info Calculating for teenagerId = ".$calculationFor. " And Info of academic and achievement");
                            $this->info("--> Info Calculating for teenagerId = ".$calculationFor. " And Info of academic and achievement");
                            break;
                        case 'academic' : 
                            $metaInfoOfTeenager = $getTeenagerDetail[0]->getTeenagerMetaDataInfo;
                            $teenagerAcademicCalculation = ($metaInfoOfTeenager) ? $metaInfoOfTeenager->where('tmd_meta_id', 2)->toArray() : [];
                            
                            if($teenagerAcademicCalculation && count($teenagerAcademicCalculation) > 1) {
                                $academicString = "";
                                foreach($teenagerAcademicCalculation as $dataLoop) {
                                    $academicString .= $dataLoop['tmd_meta_value']. " ";
                                }
                                //Delete all current academic data
                                $metaDataDelete = DB::table('pro_tmd_teenager_meta_data')->where(['tmd_teenager' => $getTeenagerDetail[0]->id, 'tmd_meta_id' => 2])->delete();
                                
                                $data = [];
                                $data['tmd_teenager'] = $getTeenagerDetail[0]->id;
                                $data['tmd_meta_id'] = 2;
                                $data['tmd_meta_value'] = $academicString;
                                $metaDataInsert = DB::table('pro_tmd_teenager_meta_data')->insert($data);
                            }
                            Log::info("Info Calculating for teenagerId = ".$calculationFor. " And Info of academic");
                            $this->info("--> Info Calculating for teenagerId = ".$calculationFor. " And Info of academic");
                            
                            break;
                        case 'achievement' :
                            $metaInfoOfTeenager = $getTeenagerDetail[0]->getTeenagerMetaDataInfo;
                            $teenagerAchievementCalculation = ($metaInfoOfTeenager) ? $metaInfoOfTeenager->where('tmd_meta_id', 1)->toArray() : [];
                            
                            if($teenagerAchievementCalculation && count($teenagerAchievementCalculation) > 1) {
                                $achievementString = "";
                                foreach($teenagerAchievementCalculation as $dataLoop) {
                                    $achievementString .= $dataLoop['tmd_meta_value']. " ";
                                }
                                //Delete all current academic data
                                $metaDataDelete = DB::table('pro_tmd_teenager_meta_data')->where(['tmd_teenager' => $getTeenagerDetail[0]->id, 'tmd_meta_id' => 1])->delete();
                                
                                $data = [];
                                $data['tmd_teenager'] = $getTeenagerDetail[0]->id;
                                $data['tmd_meta_id'] = 1;
                                $data['tmd_meta_value'] = $achievementString;
                                $metaDataInsert = DB::table('pro_tmd_teenager_meta_data')->insert($data);
                            }
                            Log::info("Info Calculating for teenagerId = ".$calculationFor. " And Info of achievement");
                            $this->info("--> Info Calculating for teenagerId = ".$calculationFor. " And Info of achievement");
                            
                            break;
                        default;
                    }
                    
                } else {
                    Log::info("No any teenager metadata combinations found!");
                    $this->info("No any teenager metadata combinations found!");
                }
            }
        } else {
            Log::info("Please enter the valid value of 'for=' and 'infoFor='. It's all OR teenagerId for 'for=' and academic OR achievement for 'infoFor'.");
            $this->info("Please enter the valid value of 'for=' and 'infoFor='. It's all OR teenagerId for 'for=' and academic OR achievement for 'infoFor'.");
        }
    }
}
