<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ProfessionInstitutes;
use App\ManageExcelUpload;
use File;
use Excel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Jobs\ImportProfessionInstituteBasicInformation;
use App\Jobs\ImportProfessionInstituteAccreditation;

class ProfessionInstituteUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProfessionInstituteUpload {--file=} {--uploadType=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Excel file for Elector Details in Karanataka Election';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->objProfessionInstitutes = new ProfessionInstitutes();
        $this->objManageExcelUpload = new ManageExcelUpload();
        $this->namespace = 'App';
        $this->log = new Logger('admin-profession-institute');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = $this->option('file');
        $uploadType = $this->option('uploadType');

        $excelUpload['file_type'] = $uploadType;
        $excelUpload['status'] = "0"; //Pendind
        $excelUpload['description'] = "Upload in progress"; 
        
        $responseManageExcelUpload = $this->objManageExcelUpload->insertUpdate($excelUpload);

        ini_set('memory_limit', '20000M');
        ini_set('max_execution_time', 0);

        $this->log->info("Excel loading started on ".date("Y-m-d h:i:s A"));

        $results = Excel::load($path, function($reader) {})->get();

        $this->log->info("Excel loaded on ".date("Y-m-d h:i:s A"));
        
        $response = '';        
        if($uploadType == "1") // Upload Basic information
        {
            $data = [];
            
            foreach ($results as $value) {
                $data[] = $value->toArray();
            }
            
            $this->log->info("==  data variable loaded on ".date("Y-m-d h:i:s A")." ==");

            $response = dispatch( new ImportProfessionInstituteBasicInformation($data) )->onQueue('processing');

            if($response) {
                $excelUploadFinish['status'] = "1"; //Success
                $excelUploadFinish['description'] = trans('labels.professioninstitueslistuploadsuccess');
            } else {
                $excelUploadFinish['status'] = "2"; //Failed
                $excelUploadFinish['description'] = trans('labels.commonerrormessage');
            }

            $excelUploadFinish['id'] = $responseManageExcelUpload->id;
            $this->objManageExcelUpload->insertUpdate($excelUploadFinish);
            $this->log->info($excelUploadFinish['description']);
            $this->log->info("Excel upload completed on ".date("Y-m-d h:i:s A"));
            return true;
        }
        elseif($uploadType == "2") // Upload Accreditation
        {
            Excel::filter('chunk')->load($path)->chunk(500, function ($results) use (&$response) {
                if(!isset($results[0]->id) || !isset($results[0]->name) || !isset($results[0]->survey_year) || !isset($results[0]->is_accredited) || !isset($results[0]->has_score) || !isset($results[0]->accreditation_body) || !isset($results[0]->max_score) || !isset($results[0]->score)){
                    
                    $excelUploadFinish['id'] = $responseManageExcelUpload->id;
                    $excelUploadFinish['status'] = "2"; //Failed
                    $excelUploadFinish['description'] = trans('labels.professioninstitueslistcolumnnotfoundaccreditation');
                    $this->objManageExcelUpload->insertUpdate($excelUploadFinish);
                    $this->log->info($excelUploadFinish['description']);
                    $this->log->info("Excel upload completed on ".date("Y-m-d h:i:s A"));
                    return true;
                }
                $response = dispatch( new ImportProfessionInstituteAccreditation($results) );
            }, $shouldQueue = false);

            if($response) {
                $excelUploadFinish['status'] = "1"; //Success
                $excelUploadFinish['description'] = trans('labels.professioninstitueslistuploadsuccess');
                    // $excelUploadFinish['status'] = "1"; //Success
                    // if(count($response['notFoundSchool'])>0){
                    //     $notFoundSchoolImplode = implode(', ', $response['notFoundSchool']);
                    //     $excelUploadFinish['description'] = $notFoundSchoolImplode.' '.trans('labels.professioninstitueslistuploadsuccesswithnotfound');
                    // }
                    // else{
                    //     $excelUploadFinish['description'] = trans('labels.professioninstitueslistuploadsuccess');
                    // }
            } else {
                $excelUploadFinish['status'] = "2"; //Failed
                $excelUploadFinish['description'] = trans('labels.commonerrormessage');
            }

            $excelUploadFinish['id'] = $responseManageExcelUpload->id;
            $this->objManageExcelUpload->insertUpdate($excelUploadFinish);
            $this->log->info($excelUploadFinish['description']);
            $this->log->info("Excel upload completed on ".date("Y-m-d h:i:s A"));
            return true;

        }

    }
}
