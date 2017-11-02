<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class Audit extends Model {

    protected $table = 'pro_au_audits';
    protected $recordPerPage;
    protected $guarded = [];

    /*
        $logData : array of Audit data to be inserted
    */
    public function saveAudit($auditData)
    {
        $row = $this->create($auditData);
        return $row->id;
    }
}