<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Helpers;
use App\ProfessionInstitutes;

class ImportProfessionInstituteAccreditation implements ShouldQueue
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
        $notFoundSchool = [];
        $this->objProfessionInstitutes = new ProfessionInstitutes();
        foreach ($this->results as $key => $value) {
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
        }

        $returnData['notFoundSchool'] = $notFoundSchool;
        $returnData['response'] = $response;
        return $returnData;
    }
}
