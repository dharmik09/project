<?php

namespace App;

use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Country;
use Config;

class Teenagers extends Authenticatable {

    use Notifiable;

    protected $table = 'pro_t_teenagers';
    //protected $fillable = ['id', 't_uniqueid', 't_school', 't_school_status', 't_name', 't_nickname', 't_email', 'password', 't_gender', 't_social_provider', 't_social_identifier', 't_social_accesstoken', 't_phone', 't_birthdate', 't_country', 't_pincode', 't_location', 't_photo', 't_level', 't_credit', 't_boosterpoints', 't_isfirstlogin', 't_sponsor_choice', 't_isverified', 't_device_token', 't_device_type', 'remember_token', 'deleted', 'is_search_on', 'is_notify', 'is_sound_on','t_last_activity'];
    protected $guarded = [];
  
    public function getActiveTeenagers() {
        $result = $this->select('*')
                ->where('t_name', '!=','')
                ->whereIn('deleted', ['1', '2'])
                ->get();
        return $result;
    }
    
    public function getTeenagersData($teenagerId) {
        $result = $this->select('*')
                ->where('deleted', '1')
                ->where('t_isverified', '1')
                ->where('id',$teenagerId)
                ->first();
        return $result;
    }

    public function getteenagerEmail($id) {
        $result = $this->select('*')
                ->where('t_school', $id)
                ->Orwhere('t_isverified', '0')
                ->get();
        return $result;
    }

    public function getBirthdate($id) {
        $result = $this->select('t_birthdate')
                ->where('id', $id)
                ->get();
        foreach ($result as $re) {
            return $re->t_birthdate;
        }
    }

    public function getTeenagerBoosterPoints($teenagerId) {
        $finalArray = array();
        $boosterArray = array();
        $zeroBoosterLevel = array();
        //$boosterPoints = DB::select(config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS'))->where("tlb_teenager", $teenagerId)->get();
        $boosterPoints = DB::select(DB::raw("select SUM(tlb_points) as points,tlb_level from " . config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS') . " where tlb_teenager=" . $teenagerId . " GROUP BY tlb_level"), array());

        foreach ($boosterPoints as $points) {
            $boosterArray["Level" . $points->tlb_level] = $points->points;
        }

        $systemLevels = DB::table(config::get('databaseconstants.TBL_SYSTEM_LEVELS'))->get();
        foreach ($systemLevels as $key => $level) {
            if (!array_key_exists($level->sl_name, $boosterArray)) {
                $zeroBoosterLevel[$level->sl_name] = 0;
            }
        }
        $toalAndAttemptedQuestionCount = DB::select(DB::raw("select (SELECT count(*) FROM " . config::get('databaseconstants.TBL_LEVEL1_ACTIVITY') . " where deleted=1) as 'NoOfTotalQuestions', (SELECT count(*) FROM " . config::get('databaseconstants.TBL_LEVEL2_ACTIVITY') . " where deleted=1) as 'NoOfTotalQuestionsLevel2', (select count(*) from " . config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " where l1ans_teenager=" . $teenagerId . ") as 'NoOfAttemptedQuestions',(select count(*) from " . config::get('databaseconstants.TBL_LEVEL2_ANSWERS') . " where l2ans_teenager=" . $teenagerId . ") as 'NoOfAttemptedQuestionsLevel2',(select count(*) from " . config::get('databaseconstants.TBL_PROFESSIONS') . " where deleted=1) as 'NoOftotalProfession',(select count(*) from pro_tpa_teenager_profession_attempted where tpa_teenager=" . $teenagerId . ") as 'NoOfAttemptedProfessions' "), array());

        if (!empty($toalAndAttemptedQuestionCount)) {
            $zeroBoosterLevel['Level1Progress'] = round((100 * $toalAndAttemptedQuestionCount[0]->NoOfAttemptedQuestions) / $toalAndAttemptedQuestionCount[0]->NoOfTotalQuestions);
            $zeroBoosterLevel['Level2Progress'] = round((100 * $toalAndAttemptedQuestionCount[0]->NoOfAttemptedQuestionsLevel2) / $toalAndAttemptedQuestionCount[0]->NoOfTotalQuestionsLevel2);
            $zeroBoosterLevel['Level3Progress'] = round((100 * $toalAndAttemptedQuestionCount[0]->NoOfAttemptedProfessions) / $toalAndAttemptedQuestionCount[0]->NoOftotalProfession);
        }
        $total['total'] = array_sum($boosterArray);
        $boosterArray = array_merge($boosterArray, $zeroBoosterLevel);
        $finalArray = array_merge($boosterArray, $total);
        return $finalArray;
    }

    public function getActiveTeenagersForDashboard($teenId = '') {
        $result = $this->select('*')
                ->where('deleted', '=', 1)
                ->where('t_name', '!=', ' ')
                ->where('t_isverified', '=', 1)
                ->where('id', '!=', $teenId)
                ->where('is_search_on', '=', 1)
                ->paginate(Config::get('constant.RECORD_PER_PAGE'));
        return $result;
    }

    public function getActiveTeenagersForGiftCoins($teenId = '', $searchData) {
        $result = $this->select('*')
                ->where('deleted', '=', 1)
                ->where('t_isverified', '=', 1)
                ->where('id', '!=', $teenId)
                ->where('is_search_on', '=', 1)
                ->where(function ($query) use ($searchData) {
                    $query->where('t_name', 'rlike', $searchData)
                          ->orwhere('t_email', 'rlike', $searchData);
                })
                ->paginate(Config::get('constant.RECORD_PER_PAGE'));
        return $result;
    }

    public function getActiveTeenagersForGift($teenId = '',$slot) {
        if ($slot > 0) {
            $slot = $slot * config::get('constant.RECORD_PER_PAGE');
        }
        $result = $this->select('*')
                ->where('deleted', '=', 1)
                ->where('t_name', '!=', ' ')
                ->where('t_isverified', '=', 1)
                ->where('id', '!=', $teenId)
                ->where('is_search_on', '=', 1)
                ->skip($slot)
                ->take(config::get('constant.RECORD_PER_PAGE'))
                ->get();
        return $result;
    }

    public function getSearchActiveTeenagersForGift($teenId = '',$searchData,$slot) {
        if ($slot > 0) {
            $slot = $slot * config::get('constant.RECORD_PER_PAGE');
        }
        $whereArray = [];
        foreach ($searchData AS $key => $value) {
            $whereArray[] = " t_name LIKE '%" . $value . "%'";
            $whereArray[] = " t_email LIKE '%" . $value . "%'";
        }
         if (!empty($whereArray)) {
            $whereStr = implode(" OR ", $whereArray);
        }
        $result = $this->select('*')
                ->where('deleted', '=', 1)
                ->where('t_isverified', '=', 1)
                ->where('id', '!=', $teenId)
                ->where('is_search_on', '=', 1)
                ->where(function ($query) use ($whereStr) {
                    $query->whereRaw($whereStr);
                })
                ->skip($slot)
                ->take(config::get('constant.RECORD_PER_PAGE'))
                ->get();
        return $result;
    }

    public function getMultipleActiveTeenagersForGiftCoins($teenId = '', $searchData) {
        $whereStr = '';
        $whereArray = [];
        foreach ($searchData AS $key => $value) {
            $whereArray[] = " t_name LIKE '%" . $value . "%'";
            $whereArray[] = " t_email LIKE '%" . $value . "%'";
        }
         if (!empty($whereArray)) {
            $whereStr = implode(" OR ", $whereArray);
        }
        $result = $this->select('*')
                ->where('deleted', '=', 1)
                ->where('t_isverified', '=', 1)
                ->where('id', '!=', $teenId)
                ->where('is_search_on', '=', 1)
                ->where(function ($query) use ($whereStr) {
                    $query->whereRaw($whereStr);
                })
                ->paginate(Config::get('constant.RECORD_PER_PAGE'));
        return $result;
    }

    public function getActiveTeenagersForGiftCoupon($teenId = '',$searchData) {
        $whereStr = '';
        $whereArray = [];
        foreach ($searchData AS $key => $value) {
            $whereArray[] = " t_name LIKE '%" . $value . "%'";
            $whereArray[] = " t_email LIKE '%" . $value . "%'";
        }
         if (!empty($whereArray)) {
            $whereStr = implode(" OR ", $whereArray);
        }

        $result = $this->select('*')
                ->where('deleted', '=', 1)
                ->where('t_isverified', '=', 1)
                ->where('id', '!=', $teenId)
                ->where('is_search_on', '=', 1)
                ->where('t_name', '!=', ' ')
                ->where(function ($query) use ($whereStr) {
                    $query->whereRaw($whereStr);
                })
                ->paginate(Config::get('constant.RECORD_PER_PAGE'));
        return $result;
    }

    public function getActiveTeenagersForCoupon($teenId = '',$slot) {
        if ($slot > 0) {
            $slot = $slot * config::get('constant.RECORD_PER_PAGE');
        }
        $result = $this->select('*')
                ->where('deleted', '=', 1)
                ->where('t_isverified', '=', 1)
                ->where('id', '!=', $teenId)
                ->where('is_search_on', '=', 1)
                ->where('t_name', '!=', ' ')
                ->skip($slot)
                ->take(config::get('constant.RECORD_PER_PAGE'))
                ->get();
        return $result;
    }

    public function getActiveTeenagersForCouponSearch($teenId = '',$slot,$searchData) {
      if ($slot > 0) {
          $slot = $slot * config::get('constant.RECORD_PER_PAGE');
      }
      $whereArray = [];
      foreach ($searchData AS $key => $value) {
          $whereArray[] = " t_name LIKE '%" . $value . "%'";
          $whereArray[] = " t_email LIKE '%" . $value . "%'";
      }
       if (!empty($whereArray)) {
          $whereStr = implode(" OR ", $whereArray);
      }
      $result = $this->select('*')
                ->where('deleted', '=', 1)
                ->where('t_isverified', '=', 1)
                ->where('id', '!=', $teenId)
                ->where('is_search_on', '=', 1)
                ->where('t_name', '!=', ' ')
                ->where(function ($query) use ($whereStr) {
                    $query->whereRaw($whereStr);
                })
                ->skip($slot)
                ->take(config::get('constant.RECORD_PER_PAGE'))
                ->get();
      return $result;
    }

    public function getCountry()
    {
        return $this->belongsTo(Country::class, 't_country')->withDefault();
    }
}