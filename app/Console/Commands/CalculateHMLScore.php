<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Helpers;
use Config;
use DB;
use App\Jobs\SetProfessionMatchScale;
use Log;
use Exception;

class CalculateHMLScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var php artisan calculateHMLScore --for=all
     * --for= all/teenagerId => Here, if you want to calculate only for specific teenager's score then use --for=teenagerId otherwise use --for=all. All will calculate all teenager's HML
     */
    protected $signature = 'calculateHMLScore {--for=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate Teenagers HML Data';

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
        $calculationFor = trim($this->option('for'));
        if($calculationFor != "") {
            if( strtolower($calculationFor) == "all") {
                //DB::enableQueryLog();
                //Get all teenager whose entry not in both table pro_teenager_promise_score, pro_upms_user_profession_match_scale.
                //So, in this case we are only calculating those teenagers
                $data = DB::table('pro_t_teenagers')->select('pro_t_teenagers.*')
                        ->where('pro_t_teenagers.deleted', 1)
                        ->where( function($query) {
                            $query->whereNotIn('id', function($query2) {
                                $query2->select("pro_teenager_promise_score.teenager_id")
                                    ->from('pro_teenager_promise_score');
                            });
                            $query->orWhereNotIn('id', function($query3) { 
                                $query3->select("pro_upms_user_profession_match_scale.teenager_id")
                                    ->from('pro_upms_user_profession_match_scale'); 
                            });
                        })->get();
                
                //DB::getQueryLog();
                if($data && isset($data[0]) && isset($data[0]->id)) {
                    foreach($data as $user) {
                        if(isset($user->id)) {
                            Log::info("HML Calculating for teenagerId = ".$user->id);
                            $this->info("HML Calculating for teenagerId = ".$user->id);
                            dispatch( new SetProfessionMatchScale($user->id) );
                            $this->info("HML Calculated successfully for teenagerId = ".$user->id."");
                        }
                    }
                } else {
                    Log::info("No any teenager left for HML");
                    $this->info("No any teenager left for HML Calculation");        
                }
            } else {
                Log::info("HML Calculating for teenagerId = ".$calculationFor);
                $this->info("HML Calculating for teenagerId = ".$calculationFor);
                dispatch( new SetProfessionMatchScale($calculationFor) );
                $this->info("HML Calculated successfully for teenagerId = ".$calculationFor."");
            }
        } else {
            Log::info("Please enter the valid value of 'for='. It's all OR teenagerId.");
            $this->info("Please enter the valid value of 'for='. It's all OR teenagerId.");
        }
    }
}
