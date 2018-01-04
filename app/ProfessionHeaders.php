<?php

namespace App;
use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class ProfessionHeaders extends Model
{

    protected $table = 'pro_pfic_profession_intro_content';
    protected $guarded = [];

    public function getActiveProfessionHeader($id, $countryId)
    {   
        $result = ProfessionHeaders::where('pfic_profession', $id)->where('country_id', $countryId)->get();
        return $result;
    }

}
