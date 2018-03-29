<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Helpers;
use App\ProfessionInstitutes;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ImportProfessionInstituteBasicInformation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $results;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($results)
    {
        $this->results = $results;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->objProfessionInstitutes = new ProfessionInstitutes();
        $this->log = new Logger('admin-profession-institute');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
        $count = 0;
        foreach (array_chunk($this->results,1000) as $t) {
            $response = $this->objProfessionInstitutes->insert($t);
            $count += count($t);
            $this->log->info($count ." Rescords Imported Successfully");
        }
        // foreach ($this->results as $key => $value) {
        //     $schoolData = $this->objProfessionInstitutes->getProfessionInstitutesByInstitutesId($value->id);
            
        //     if($schoolData){
        //         $data['id'] = $schoolData->id;
        //     }

        //     $data['school_id'] = $value->id;
        //     $data['institute_state'] = $value->state;
        //     $data['college_institution'] = $value->college_institution;
        //     $data['address_line1'] = $value->address_line1;
        //     $data['address_line2'] = $value->address_line2;
        //     $data['city'] = $value->city;
        //     $data['district'] = $value->district;
        //     $data['pin_code'] = $value->pin_code;
        //     $data['website'] = $value->website;
        //     $data['year_of_establishment'] = $value->year_of_establishment;
        //     $data['affiliat_university'] = $value->affiliat_university;
        //     $data['year_of_affiliation'] = $value->year_of_affiliation;
        //     $data['location'] = $value->location;
        //     $data['latitude'] = $value->latitude;
        //     $data['longitude'] = $value->longitude;
        //     $data['institute_type'] = $value->type;
        //     $data['autonomous'] = $value->autonomous;
        //     $data['management'] = $value->management;
        //     $data['speciality'] = $value->speciality;
        //     $data['girl_exclusive'] = $value->girl_exclusive;
        //     $data['hostel_count'] = $value->hostel_count;
        //     $data['is_institute_signup'] = $value->is_institute_signup;
        //     $data['minimum_fee'] = $value->minimum_fee;
        //     $data['maximum_fee'] = $value->maximum_fee;
        //     $response = $this->objProfessionInstitutes->insertUpdate($data);
        // }
        return $response;
    }
}
