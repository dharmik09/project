<?php

namespace App;

use DB;
use Config;
use Illuminate\Database\Eloquent\Model;

class Level1Traits extends Model
{
    protected $table = 'pro_tqq_traits_quality_activity';
    protected $guarded = [];

    public function getActiveLevel1Traits($id)
    {
        $level1Traits = DB::select( DB::raw("SELECT
                                traits.* , GROUP_CONCAT(options.tqo_option) AS tqo_option
                                FROM " . config::get('databaseconstants.TBL_TRAITS_QUALITY_ACTIVITY') . " AS traits join " . config::get('databaseconstants.TBL_TRAITS_QUALITY_OPTIONS') ." AS options on traits.id = options.tqq_id
                                where traits.id = ".$id." group by  traits.id "
                            ));
        return $level1Traits;
    }

    public function options() {
        return $this->hasMany(Level1TraitsOptions::class, 'tqq_id');
    }

    public function questionOptions($questionId) {
        return $this->where('id', $questionId)->with('options')->get();
    }
}
