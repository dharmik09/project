<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Helpers;
use Config;
use DB;
use App\Jobs\CalculateProfessionCompletePercentage;
use Log;
use Exception;

class CalculateProfessionCompletePercentageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var php artisan CalculateProfessionComplete --teenager=all --profession=all
     * --teenager= all/teenagerId => Here, if you want to calculate only for specific teenager's score then use --teenager=teenagerId otherwise use --teenager=all. 
     * all will calculate all teenager's profession complete percentage
     * --profession= all/professionId => Here, if you want to calculate only for specific profession's score then use --profession=professionId otherwise use --profession=all. 
     * Total 3 cases possible
     * php artisan CalculateProfessionComplete --teenager=all --profession=all   ::: This command will calculate all teenager's percentage for all professions
     * php artisan CalculateProfessionComplete --teenager=108 --profession=all   ::: This command will calculate 108 teenagerId's percentage for all professions
     * php artisan CalculateProfessionComplete --teenager=all --profession=111   ::: This command will calculate all teenager's percentage for 111 professionId
     * All teenager means, ==>> We are only getting profession played teenager as all teenager. Otherwise all teenager percentage inserted and which is not required.
     */

    protected $signature = 'CalculateProfessionComplete {--teenager=} {--profession=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate Teenagers Profession Complete Percentage';

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
        $teenagerFor = trim($this->option('teenager'));  // all / specific teenager Id
        $professionFor = trim($this->option('profession'));  // all / specific profession Id
        if($teenagerFor != "" && $professionFor != "") {
            //Only get profession played teenager. Otherwise all teenager inserted which is not required.
            $getAllActiveTeenager = DB::table('pro_t_teenagers')->select('pro_t_teenagers.id')
                                ->where('pro_t_teenagers.deleted', 1)
                                ->where( function($query) {
                                    $query->whereIn('id', function($query2) {
                                        $query2->select("level4_basic_activity_answer.teenager_id")
                                            ->from('level4_basic_activity_answer');
                                    });
                                    $query->orWhereIn('id', function($query3) { 
                                        $query3->select("level4_inetermediate_activity_user_answer.l4iaua_teenager")
                                            ->from('level4_inetermediate_activity_user_answer'); 
                                    });
                                })->get();
            $getAllActiveProfession = DB::table('pro_pf_profession')->select('pro_pf_profession.id')->where('pro_pf_profession.deleted', 1)->get();
            
            //$getAllActiveProfession = DB::table('pro_pf_profession')->select('pro_pf_profession.id')->where('pro_pf_profession.deleted', 1)->get();
            //$getAllActiveTeenager = DB::table('pro_t_teenagers')->select('pro_t_teenagers.id')->where('pro_t_teenagers.deleted', 1)->get();
            
            if(strtolower($teenagerFor) == "all" && strtolower($professionFor) == "all") {
                if(isset($getAllActiveProfession[0]->id) && isset($getAllActiveTeenager[0]->id)) {
                    foreach($getAllActiveTeenager as $teenId) {
                        foreach($getAllActiveProfession as $profId) { 
                            Log::info("Profession Percentage Calculating for teenagerId = ".$teenId->id ." And professionId = ".$profId->id);
                            $this->info("Profession Percentage Calculating for teenagerId = ".$teenId->id ." And professionId = ".$profId->id);
                            dispatch( new CalculateProfessionCompletePercentage($teenId->id, $profId->id) );
                            $this->info("Profession Percentage Calculated for teenagerId = ".$teenId->id ." And professionId = ".$profId->id);
                        }
                    }
                } else {
                    Log::info("Something went wrong with your inputs. teenagerId =".$teenagerFor." and professionId = ".$professionFor);
                    $this->info("Something went wrong with your inputs. teenagerId =".$teenagerFor." and professionId = ".$professionFor);
                }
            } else if (strtolower($teenagerFor) != "all" && strtolower($professionFor) == "all") {
                $isTeenagerActive = DB::table('pro_t_teenagers')->where(['id' => $teenagerFor, 'deleted' => 1])->first();
                if($isTeenagerActive && isset($getAllActiveProfession[0]->id)) {
                    foreach($getAllActiveProfession as $profId) {
                        Log::info("Profession Percentage Calculating for teenagerId = ".$teenagerFor ." And professionId = ".$profId->id);
                        $this->info("Profession Percentage Calculating for teenagerId = ".$teenagerFor ." And professionId = ".$profId->id);
                        dispatch( new CalculateProfessionCompletePercentage($teenagerFor, $profId->id) );
                        $this->info("Profession Percentage Calculated for teenagerId = ".$teenagerFor ." And professionId = ".$profId->id);
                    }
                } else {
                    if(!$isTeenagerActive) {
                        Log::info("Something wrong with this teenager Id. teenagerId = ".$teenagerFor);
                        $this->info("Something wrong with this teenager Id. teenagerId = ".$teenagerFor);
                    } else if(!isset($getAllActiveProfession[0]->id)) {
                        Log::info("No any active professions found!");
                        $this->info("No any active professions found!");
                    } else {
                        Log::info("Something went wrong with your inputs. teenagerId =".$teenagerFor." and professionId = ".$professionFor);
                        $this->info("Something went wrong with your inputs. teenagerId =".$teenagerFor." and professionId = ".$professionFor);
                    }
                }
            } else if (strtolower($professionFor) != "all" && strtolower($teenagerFor) == "all") {
                $isProfessionActive = DB::table('pro_pf_profession')->where(['id' => $professionFor, 'deleted' => 1])->first();
                if($isProfessionActive && isset($getAllActiveTeenager[0]->id)) {
                    foreach($getAllActiveTeenager as $teenId) {
                        Log::info("Profession Percentage Calculating for teenagerId = ".$teenId->id ." And professionId = ".$professionFor);
                        $this->info("Profession Percentage Calculating for teenagerId = ".$teenId->id ." And professionId = ".$professionFor);
                        dispatch( new CalculateProfessionCompletePercentage($teenId->id, $professionFor) );
                        $this->info("Profession Percentage Calculated for teenagerId = ".$teenId->id ." And professionId = ".$professionFor);
                    }
                } else {
                    if(!$isProfessionActive) {
                        Log::info("Something wrong with this profession Id. professionId = ".$professionFor);
                        $this->info("Something wrong with this profession Id. professionId = ".$professionFor);
                    } else if(!isset($getAllActiveTeenager[0]->id)) {
                        Log::info("No any active teenager found!");
                        $this->info("No any active teenager found!");
                    } else {
                        Log::info("Something went wrong with your inputs. teenagerId =".$teenagerFor." and professionId = ".$professionFor);
                        $this->info("Something went wrong with your inputs. teenagerId =".$teenagerFor." and professionId = ".$professionFor);
                    }
                }
            } else {
                $isTeenagerActive = DB::table('pro_t_teenagers')->where(['id' => $teenagerFor, 'deleted' => 1])->first();
                $isProfessionActive = DB::table('pro_pf_profession')->where(['id' => $professionFor, 'deleted' => 1])->first();
                if ($isTeenagerActive && $isProfessionActive) {
                    Log::info("Profession Percentage Calculating for teenagerId = ".$teenagerFor ." And professionId = ".$professionFor);
                    $this->info("Profession Percentage Calculating for teenagerId = ".$teenagerFor ." And professionId = ".$professionFor);
                    dispatch( new CalculateProfessionCompletePercentage($teenagerFor, $professionFor) );
                    $this->info("Profession Percentage Calculated for teenagerId = ".$teenagerFor ." And professionId = ".$professionFor);
                } else {
                    Log::info("Something went wrong with your inputs. teenagerId =".$teenagerFor." and professionId = ".$professionFor);
                    $this->info("Something went wrong with your inputs. teenagerId =".$teenagerFor." and professionId = ".$professionFor);
                }
            }
        } else {
            Log::info("Please enter the valid value of 'teenager=' and 'profession='. It can be all / teenagerId / professionId.");
            $this->info("Please enter the valid value of 'teenager=' and 'profession='. It can be all / teenagerId / professionId.");
        }
    }
}