<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Helpers;
use App\ProfessionInstitutes;
use App\ManageExcelUpload;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ImportProfessionInstituteBasicInformation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $results;
    protected $responseManageExcelUpload;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($results, $responseManageExcelUpload)
    {
        $this->results = $results;
        $this->responseManageExcelUpload = $responseManageExcelUpload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->objProfessionInstitutes = new ProfessionInstitutes();
        $this->objManageExcelUpload = new ManageExcelUpload();
        $this->log = new Logger('admin-profession-institute');
        $this->log->pushHandler(new StreamHandler(storage_path().'/logs/monolog-'.date('m-d-Y').'.log'));
        try{
            $count = 0;
            foreach (array_chunk($this->results,1000) as $t) {
                $response = $this->objProfessionInstitutes->insert($t);
                $count += count($t);
                $this->log->info($count ." Rescords Imported Successfully");
            }
            return $response;
        } 
        catch(\Exception $e){
            $excelUploadFinish['status'] = "2"; //Failed
            $excelUploadFinish['description'] = "Server error occurred.";

            $excelUploadFinish['id'] = $this->responseManageExcelUpload->id;
            $this->objManageExcelUpload->insertUpdate($excelUploadFinish);
            $this->log->info($excelUploadFinish['description']);
            $this->log->info("Exception Log -->".$e->getMessage());
            $this->log->info("Excel upload completed on ".date("Y-m-d h:i:s A"));
            return true;
        }
    }
}
