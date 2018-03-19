<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Helpers;
use Config;
use DB;
use App\ProfessionInstitutesSpeciality;
use File;
use Excel;

class ImportInstituteSpeciality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'InstituteSpeciality {--file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Institute Speciality';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->objProfessionInstitutesSpeciality = new ProfessionInstitutesSpeciality();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = $this->option('file');

        ini_set('memory_limit', '20000M');
        ini_set('max_execution_time', 0);
        date_default_timezone_set('Asia/Kolkata');
        echo "\nExcel loading started on ".date("Y-m-d h:i:s A")."\n";

        $results = Excel::load($path, function($reader) {})->get();

        echo "\nExcel loaded on ".date("Y-m-d h:i:s A")."\n";

        $data = [];
        foreach ($results as $key => $value) {
            $eduStream = explode("#", $value->edu_stream);
            foreach ($eduStream as $key => $value) {
                $data[] = trim($value);
            }
        }
        
        $dataResults = array_unique($data);

        if(count($dataResults)>0){
            $this->objProfessionInstitutesSpeciality->truncate();
            echo "\n\n";
            echo "All Data removed from Profession Institutes Speciality \n";
            $bar = $this->output->createProgressBar(count($dataResults));
            $bar->setBarCharacter('*');
            foreach ($dataResults as $key => $value) {
                $pisData = [];
                $pisData['pis_name'] = $value;
                $response = $this->objProfessionInstitutesSpeciality->insertUpdate($pisData);
                $bar->advance();
            }
        }

        $bar->finish();

        echo "\n\n";
        echo "Excel upload completed on ".date("Y-m-d h:i:s A")."\n\n";
        echo "Total Record Imported ".count($dataResults)."\n\n";
    }
}