<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Helpers;
use Config;
use File;
use Storage;
use Excel;
use Image;
use DB;

class checkFilesInFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Default Path is set for command is public/uploads/
     *
     * php artisan checkFilesInFolder --table="table_name" --imgField="image field name in db" --imgSource="image folder path"
     *
     * Example Command : php artisan checkFilesInFolder --table="pro_gt_gamification_template" --imgField="gt_template_image" --imgSource="concept\original"
     *
     * @var string
     */
    protected $signature = 'checkFilesInFolder {--table=} {--imgField=} {--imgSource=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check images are available or not in folder if not then give sheet for not available image records';

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
        ini_set('memory_limit', '20000M');
        ini_set('max_execution_time', 0);

        $tableName = $this->option('table');
        $imgField = $this->option('imgField');
        $imgSource = 'uploads/'.$this->option('imgSource').'/';

        $tableData = DB::table($tableName)
                        ->where('deleted', Config::get('constant.ACTIVE_FLAG'))
                        ->get();
        
        $tableData = DB::table("pro_l4iam_level4_intermediate_activity_media AS media")
                        ->join("pro_l4ia_level4_intermediate_activity AS l4act", 'l4act.id', '=', 'media.l4iam_question_id')
                        ->join(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'profession.id', '=', 'l4act.l4ia_profession_id')
                        ->join("pro_gt_gamification_template AS concept", 'concept.id', '=', 'l4act.l4ia_question_template')
                        ->select('profession.pf_name','concept.gt_template_title','l4act.l4ia_question_text','media.*')
                        ->where('media.deleted', Config::get('constant.ACTIVE_FLAG'))
                        ->get();
        
        $notFoundData = [];

        $bar = $this->output->createProgressBar(count($tableData));        
        
        $bar->setBarCharacter('*');

        foreach ($tableData as $key => $value) {
            if(isset($value->{$imgField}) && ($value->{$imgField} != "" || $value->{$imgField} != NULL) ){
                if (!file_exists(public_path($imgSource.$value->{$imgField}))) 
                {
                    $notFoundData[] = $value;
                }
            }
            $bar->advance();
        }

        $bar->finish();
        echo "\n\n";
        echo "Image Checking completed on ".date("Y-m-d h:i:s A")."\n\n";

        if(count($notFoundData)>0){       

            $filename = $tableName.'.csv';
            $fp = fopen($filename, 'w');

            $header = array_keys((array)$notFoundData[0]);
            fputcsv($fp, $header);

            foreach ($notFoundData as $element) {
                fputcsv($fp, (array)$element);
            }

            echo "Total Images not found ".count($notFoundData)."\n\n";
            echo "Please find ".$filename." sheet in Main folder for details\n\n";
        }
        else{
            echo "All Images are available in folder according to record";
        }
    }
}