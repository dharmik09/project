<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CareerMapping extends Model {

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
    //protected $fillable = ['id', 'tcm_profession', 'tcm_scientific_reasoning', 'tcm_verbal_reasoning', 'tcm_numerical_ability', 'tcm_logical_reasoning', 'tcm_social_ability', 'tcm_artistic_ability', 'tcm_spatial_ability', 'tcm_creativity', 'tcm_clerical_ability', 'tcm_doers_realistic', 'tcm_thinkers_investigative', 'tcm_creators_artistic', 'tcm_helpers_social', 'tcm_persuaders_enterprising', 'tcm_organizers_conventional', 'tcm_linguistic', 'tcm_logical', 'tcm_musical', 'tcm_spatial', 'tcm_bodily_kinesthetic', 'tcm_naturalist', 'tcm_interpersonal', 'tcm_intrapersonal', 'tcm_existential', 'deleted'];
    protected $guarded = [];
    
}
