<?php

namespace App;
use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class ProfessionHeaders extends Model
{

    protected $table = 'pro_pfic_profession_intro_content';
    protected $guarded = [];

    public function getActiveProfessionHeader($id)
    {   
        $result = ProfessionHeaders::where('pfic_profession', $id)->get();
        return $result;
    }

}
