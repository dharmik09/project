<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class ManageExcelUpload extends Model 
{

    protected $table = 'pro_meu_manage_excel_upload';

    protected $fillable = ['file_type','status','description','deleted'];

    /**
     * Insert and Update Manage Excel Upload
     */
    public function insertUpdate($data)
    {
        if (isset($data['id']) && $data['id'] != '' && $data['id'] > 0) {
            return ManageExcelUpload::where('id', $data['id'])->update($data);
        } else {
            return ManageExcelUpload::create($data);
        }
    }

    /**
     * get all Manage Excel Upload
     */
    public function getAllManageExcelUpload() {  
        $tags = ManageExcelUpload::where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->get();
        return $tags;
    }
    
    /**
     * get all Manage Excel Upload
     */
    public function getLatestRecordByExcelType($fileType) {  
        $tags = ManageExcelUpload::orderBy('created_at','DESC')->where('file_type', $fileType)->where('deleted', '<>', Config::get('constant.DELETED_FLAG'))->first();
        return $tags;
    }
}