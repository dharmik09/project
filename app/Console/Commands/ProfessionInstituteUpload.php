<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ProfessionInstitutes;
use App\ManageExcelUpload;
use File;
use Excel;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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
        $this->log = new Logger('admin-teenager');
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

        // $results = Excel::load($path, function($reader) {})->get();

        // $this->log->info("Excel loaded on ".date("Y-m-d h:i:s A"));
        
        $response = '';        
        if($uploadType == "1") // Upload Basic information
        {
            Excel::filter('chunk')->load($path)->chunk(1000, function ($results) use (&$response) {
                if( !isset($results[0]->id) || !isset($results[0]->state) || !isset($results[0]->college_institution) || !isset($results[0]->address_line1) || !isset($results[0]->address_line2) || !isset($results[0]->city) || !isset($results[0]->district) || !isset($results[0]->pin_code) || !isset($results[0]->website) || !isset($results[0]->year_of_establishment) || !isset($results[0]->affiliat_university) || !isset($results[0]->year_of_affiliation) || !isset($results[0]->location) || !isset($results[0]->latitude) || !isset($results[0]->longitude) || !isset($results[0]->type) || !isset($results[0]->management) || !isset($results[0]->speciality) || !isset($results[0]->girl_exclusive) || !isset($results[0]->hostel_count) || !isset($results[0]->is_institute_signup) || !isset($results[0]->minimum_fee) || !isset($results[0]->maximum_fee) ) {

                    $excelUploadFinish['id'] = $responseManageExcelUpload->id;
                    $excelUploadFinish['status'] = "2"; //Failed
                    $excelUploadFinish['description'] = trans('labels.professioninstitueslistcolumnnotfoundbasicinformation');
                    $this->objManageExcelUpload->insertUpdate($excelUploadFinish);
                    $this->log->info($excelUploadFinish['description']);
                    $this->log->info("Excel upload completed on ".date("Y-m-d h:i:s A"));
                    return true;

                }
                foreach ($results as $key => $value) {
                    // $schoolData = $this->objProfessionInstitutes->getProfessionInstitutesByInstitutesId($value->id);
                    
                    // if($schoolData){
                    //     $data['id'] = $schoolData->id;
                    // }

                    $data['school_id'] = $value->id;
                    $data['institute_state'] = $value->state;
                    $data['college_institution'] = $value->college_institution;
                    $data['address_line1'] = $value->address_line1;
                    $data['address_line2'] = $value->address_line2;
                    $data['city'] = $value->city;
                    $data['district'] = $value->district;
                    $data['pin_code'] = $value->pin_code;
                    $data['website'] = $value->website;
                    $data['year_of_establishment'] = $value->year_of_establishment;
                    $data['affiliat_university'] = $value->affiliat_university;
                    $data['year_of_affiliation'] = $value->year_of_affiliation;
                    $data['location'] = $value->location;
                    $data['latitude'] = $value->latitude;
                    $data['longitude'] = $value->longitude;
                    $data['institute_type'] = $value->type;
                    $data['autonomous'] = $value->autonomous;
                    $data['management'] = $value->management;
                    $data['speciality'] = $value->speciality;
                    $data['girl_exclusive'] = $value->girl_exclusive;
                    $data['hostel_count'] = $value->hostel_count;
                    $data['is_institute_signup'] = $value->is_institute_signup;
                    $data['minimum_fee'] = $value->minimum_fee;
                    $data['maximum_fee'] = $value->maximum_fee;
                    $response = $this->objProfessionInstitutes->insertUpdate($data);
                    $this->log->info("Pointer on -> ".$key);
                }
            }, $shouldQueue = false);
            
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
            $notFoundSchool = [];
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
                foreach ($results as $key => $value) {
                    $schoolData = $this->objProfessionInstitutes->getProfessionInstitutesByInstitutesId($value->id);
                    if($schoolData){
                        
                        $data['id'] = $schoolData->id;
                        $data['accreditation_score'] = $value->score;
                        $data['accreditation_body'] = $value->accreditation_body;

                        $response = $this->objProfessionInstitutes->insertUpdate($data);
                    }
                    else{
                        $notFoundSchool[] = $value->name;
                    }
                    $this->log->info("Pointer on -> ".$key);
                }
            }, $shouldQueue = false);

            if($response) {
                $excelUploadFinish['status'] = "1"; //Success
                if(count($notFoundSchool)>0){
                    $notFoundSchoolImplode = implode(', ', $notFoundSchool);
                    $excelUploadFinish['description'] = $notFoundSchoolImplode.' '.trans('labels.professioninstitueslistuploadsuccesswithnotfound');
                }
                else{
                    $excelUploadFinish['description'] = trans('labels.professioninstitueslistuploadsuccess');
                }
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
