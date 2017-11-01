<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Config;

class InterpretationRange extends Model
{
    protected $table = 'pro_ir_interpretation_range';
    protected $fillable = ['id', 'ir_text', 'ir_min_score','ir_max_score', 'created_at', 'updated_at'];

    public function getActiveInterpretationRange() {
        $result = InterpretationRange::select('*')
                    ->get()->toArray();
        return $result;
    }

    public function saveInterpretationRangeDetail($interpretationRangeDetail) {
        $irLength = count($interpretationRangeDetail['ir_text']);
        $irArray = [];
        for ($i = 0; $i < $irLength; $i++) {
            $irArray['ir_text'] = $interpretationRangeDetail['ir_text'][$i];
            $irArray['ir_min_score'] = $interpretationRangeDetail['ir_min_score'][$i];
            $irArray['ir_max_score'] = $interpretationRangeDetail['ir_max_score'][$i];
            $result = InterpretationRange::select('id')
                    ->where('ir_text', $interpretationRangeDetail['ir_text'][$i])
                    ->get();
            if (count($result) > 0) {
               $this->where('ir_text', $interpretationRangeDetail['ir_text'][$i])->update($irArray);
            } else {
              $this->create($irArray);
            }
        }
        return '1';
    }
}
