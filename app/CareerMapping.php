<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Config;


class CareerMapping extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract {

    use Authenticatable,
        Authorizable,
        CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pro_tcm_teenager_career_mapping';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function getRelatedCareers($careerMapColumn, $lastCareerId = '')
    {
        $mappingDetails = $this->leftjoin(Config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'pro_tcm_teenager_career_mapping.tcm_profession', '=', 'profession.id')
                    ->selectRaw('pro_tcm_teenager_career_mapping.' . $careerMapColumn . ', profession.*')
                    ->whereIn('pro_tcm_teenager_career_mapping.' . $careerMapColumn, array('M', 'H'))
                    ->where('profession.deleted', Config::get('constant.ACTIVE_FLAG'))
                    ->where(function($query) use ($lastCareerId)  {
                        if(isset($lastCareerId) && !empty($lastCareerId)) {
                            $query->where('profession.id', '<', $lastCareerId);
                        }
                     })
                    ->orderBy('profession.id', 'DESC')
                    ->limit(Config::get('constant.RECORD_PER_PAGE'))
                    ->get();
        return $mappingDetails;
    }

    public function getRelatedCareersCount($careerMapColumn, $lastCareerId = '')
    {
        $careersCount = $this->leftjoin(Config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession", 'pro_tcm_teenager_career_mapping.tcm_profession', '=', 'profession.id')
                    ->selectRaw('pro_tcm_teenager_career_mapping.' . $careerMapColumn . ', profession.*')
                    ->whereIn('pro_tcm_teenager_career_mapping.' . $careerMapColumn, array('M', 'H'))
                    ->where('profession.deleted', Config::get('constant.ACTIVE_FLAG'))
                    ->where(function($query) use ($lastCareerId)  {
                        if(isset($lastCareerId) && !empty($lastCareerId)) {
                            $query->where('profession.id', '<', $lastCareerId);
                        }
                     })
                    ->orderBy('profession.id', 'DESC')
                    ->count();
        return $careersCount;
    }

    
}
