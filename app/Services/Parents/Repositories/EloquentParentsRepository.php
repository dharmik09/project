<?php

namespace App\Services\Parents\Repositories;

use DB;
use Config;
use App\Services\Parents\Contracts\ParentsRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;

class EloquentParentsRepository extends EloquentBaseRepository implements ParentsRepository {

    /**
     * @return array of all the active Parents
      Parameters
      @$searchParamArray : Array of Searching and Sorting parameters
     */
    public function getAllParents($searchParamArray = array(), $type) {
        $parents = DB::table(config::get('databaseconstants.TBL_PARENTS') . " AS parent")
                ->join(config::get('databaseconstants.TBL_PARENT_TEEN_PAIR') . " AS pair", 'pair.ptp_parent_id', '=', 'parent.id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'pair.ptp_teenager', '=', 'teenager.id')
                ->selectRaw('parent.*')
                ->where('p_user_type', $type)
                ->whereIn('parent.deleted', ['1', '2'])
                ->groupBy('parent.id')
                ->get();

        return $parents;
    }

    /**
     * @return Parent details object
      Parameters
      @$parentDetail : Array of parents detail from front
     */
    public function saveParentDetail($parentDetail) {
        if (isset($parentDetail['id']) && $parentDetail['id'] != '' && $parentDetail['id'] > 0) {
            $return = $this->model->where('id', $parentDetail['id'])->update($parentDetail);
        } else {
            $return = $this->model->create($parentDetail);
        }
        return $return;
    }

    /**
     * get entire detail related user
     */
    public function getParentById($id) {
        $ParentDetails = DB::select(DB::raw("SELECT parent.*, country.c_name, country.id as country_id, country.c_code, s_name, city.c_name as city
                                          FROM " . config::get('databaseconstants.TBL_PARENTS') . " AS parent
                                            left join " . config::get('databaseconstants.TBL_COUNTRIES') . " AS country on country.id = parent.p_country
                                            left join " . config::get('databaseconstants.TBL_STATES') . " AS state on state.id = parent.p_state
                                            left join " . config::get('databaseconstants.TBL_CITIES') . " AS city on city.id = parent.p_city
                                           where parent.deleted != 3 AND parent.id = " . $id));
        return $ParentDetails[0];
    }

    /**
     * @return Boolean True/False
      Parameters
      @$id : Parent ID
     */
    /*
      return : array of Teenager detail by email id
     */
    public function getParentDetailByEmailId($email) {
        $parentDetail = $this->model->where('deleted', '1')->where('p_email', $email)->first();
        return $parentDetail;
    }

    /*
     * get country id by country name....
     */

    public function getCountryIdByName($country) {
        $country = DB::select(DB::raw("select country.id from " . config::get('databaseconstants.TBL_COUNTRIES') . " AS country
                                                      where country.c_name ='" . $country . "' or country.c_code='" . $country . "'"));
        if (!empty($country)) {
            return $country[0];
        } else {
            return 0;
        }
    }

    /*
     * get state id by state name....
     */

    public function getStateIdByName($state) {
        $state = DB::select(DB::raw("select state.id from " . config::get('databaseconstants.TBL_STATES') . " AS state
                                                      where state.s_name ='" . $state . "' or state.s_code='" . $state . "'"));
        if (!empty($state)) {
            return $state[0];
        } else {
            return 0;
        }
    }

    /*
     * get state id by city name....
     */

    public function getCityIdByName($city) {
        $city = DB::select(DB::raw("select city.id from " . config::get('databaseconstants.TBL_CITES') . " AS country
                                                      where city.c_name ='" . $city . "' or city.c_code='" . $city . "'"));
        if (!empty($city)) {
            return $city[0];
        } else {
            return 0;
        }
    }

    public function saveParentPasswordResetRequest($resetRequest) {
        DB::table(config::get('databaseconstants.TBL_PARENT_RESET_PASSWORD'))->insert($resetRequest);
        return true;
    }

    /**
     * Parameter $parentId : Parent ID from provider
     * Parameter $OTP : One Time Password
     * return : Boolean TRUE / FALSE
     */
    public function verifyOTPAgainstParentId($parentId, $OTP) {
        $result = DB::table(config::get('databaseconstants.TBL_PARENT_RESET_PASSWORD'))->where("trp_parent", $parentId)->where("trp_otp", $OTP)->where("trp_status", 1)->first();

        if (isset($result) && !empty($result)) {
            $currentDatetime = time(); // or your date as well
            $requestDatetime = strtotime($result->created_at);
            $datediff = $currentDatetime - $requestDatetime;
            $daysDifference = floor($datediff / (60 * 60 * 24));
            if ($daysDifference > config::get('constant.PASSWORD_RESET_REQUEST_VALIDITY_DAYS')) {
                return false;
            } else {
                $row = DB::table(config::get('databaseconstants.TBL_PARENT_RESET_PASSWORD'))->find($result->id);
                DB::table(config::get('databaseconstants.TBL_PARENT_RESET_PASSWORD'))->where('id', $result->id)->update(['trp_status' => 0]);
                return true;
            }
        } else {
            return false;
        }
    }

    public function checkCurrentPasswordAgainstParent($parentId, $currentPassword) {
        $result = $this->model->select('p_email')->where('id', $parentId)->where('deleted', '1')->first();

        if (isset($result) && !empty($result)) {
            $result = $result->toArray();
            if ($user = Auth::parent()->attempt(['p_email' => $result['p_email'], 'password' => $currentPassword, 'deleted' => 1, 'is_verified' => '1'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Parameter $parentId: Parent ID and token
     * add parent token for varify parent
     */
    public function addParentEmailVarifyToken($parentTokenDetail) {
        DB::table(config::get('databaseconstants.TBL_PARENT_EMAIL_VERICATION'))->insert($parentTokenDetail);
    }

    /*
     * update parent varify status by uniqueid
     */

    public function updateParentVerifyStatusById($parentid) {
        $parentvarify = $this->model->where('id', $parentid)->update(['p_isverified' => '1']);
        if ($parentvarify) {
            //$result = $this->model->where('id', $teenagerid)->get();
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * @return Boolean True/False
      Parameters
      @$teenager id : Teenager's id
     */
    public function checkActiveParent($id) {
        $parent = $this->model->where('deleted', '1')->where('id', $id)->get();
        if ($parent->count() > 0) {
            $verifyParent = $this->model->where('deleted', '1')->where('p_isverified', '1')->where('id', $id)->get();
            if ($verifyParent->count() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function deleteParent($id, $type) {
        $flag = true;
        $parent = $this->model->find($id);
        $parent->deleted = config::get('constant.DELETED_FLAG');
        $response = $parent->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Boolean True/False
      Parameters
      @$email : Parent's email
     */
    public function checkActiveEmailExist($email, $id = '') {
        if ($id != '') {
            $user = $this->model->where('deleted', '1')->where('p_email', $email)->where('id', '!=', $id)->get();
        } else {
            $user = $this->model->where('deleted', '1')->where('p_email', $email)->get();
        }
        
        if ($user->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * update Parent varify status by Token
     */

    public function updateParentTokenStatusByToken($token) {
        $parentverify = DB::table('pro_tev_parent_email_verification')
                ->where('tev_token', $token)
                ->update(['tev_status' => 1]);
        if ($parentverify) {
            $parentDetail = DB::select(DB::raw("select tev_parent from pro_tev_parent_email_verification where tev_token ='" . $token . "'"));
            return $parentDetail;
        } else {
            return 0;
        }
    }

    /*
     * Get all active teengers
     */

    public function getAllVerifiedTeenagers($parentId) {
        $result = DB::table('pro_ptp_parent_teen_pair')->where('ptp_parent_id', $parentId)->where('ptp_is_verified', 1)->get();        
        return $result;
    }

    /* Update parent-teen status */

    public function updateParentTeenStatusByToken($token) {
        $parentverify = DB::table('pro_ptp_parent_teen_pair')
                ->where('ptp_token', $token)
                ->update(['ptp_is_verified' => 1]);
        if ($parentverify) {
            $parentDetail = DB::select(DB::raw("select ptp_parent_id from pro_ptp_parent_teen_pair where ptp_token ='" . $token . "'"));
            return $parentDetail;
        } else {
            return 0;
        }
    }

    public function checkPairAvailability($teenagerId, $parentId) {
        $checkPairAvailability = DB::table(config::get('databaseconstants.TBL_PARENT_TEEN_PAIR'))->where('ptp_teenager',$teenagerId)->where('ptp_parent_id', $parentId)->first();
        return $checkPairAvailability;
    }

    public function getParentDataForCoinsDetail($id) {
        $parentData = $this->model->where('id', '=', $id)->where('deleted', 1)->get()->toArray();
        $data = [];
        if (isset($parentData)) {
            $data['p_coins'] = $parentData[0]['p_coins'];
        }
        return $data;
    }

    public function updateParentCoinsDetail($id, $Coins) {
        $parentDetail = $this->model->where('id', $id)->update(['p_coins' => $Coins]);
        return $parentDetail;
    }

    public function getParentDetailByParentId($id) {
        $parentData = $this->model->where('id', '=', $id)->where('deleted', 1)->first();
        return $parentData;
    }


    public function getParentAllTypeBadges($parentId, $professionId) {
        $array = [];
        $totalBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY'))
                ->select(DB::raw('SUM(points) as total_points'))
                ->where('profession_id', $professionId)
                ->where('deleted', 1)
                ->get();
        $basicTotalPoints = (isset($totalBasicPoints[0]->total_points) && $totalBasicPoints[0]->total_points != '') ? $totalBasicPoints[0]->total_points : 0;
        $finalbasicAttemptedPoints = 0;
        //get total points for attempted questions
        $basicAttemptedTotalPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_act")
                ->join(config::get('databaseconstants.TBL_LEVEL4_ANSWERS_PARENT') . " AS l4_ans", 'l4_act.id', '=', 'l4_ans.lbac_activity_id')
                ->select(DB::raw('l4_act.points as attemptedpoint'))
                ->where('l4_act.profession_id', $professionId)
                ->where('l4_ans.lbac_parent_id', $parentId)
                ->groupBy('l4_ans.lbac_activity_id')
                ->get();

        if(isset($basicAttemptedTotalPoints) && !empty($basicAttemptedTotalPoints))
        {
            foreach ($basicAttemptedTotalPoints as $b1Key => $b1Value) {
                $finalbasicAttemptedPoints += (isset($b1Value->attemptedpoint) && $b1Value->attemptedpoint > 0) ? $b1Value->attemptedpoint : 0;
            }
        }

        $totalEarnBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_act")
                ->join(config::get('databaseconstants.TBL_LEVEL4_ANSWERS_PARENT') . " AS l4_ans", 'l4_act.id', '=', 'l4_ans.lbac_activity_id')
                ->select(DB::raw('l4_ans.lbac_earned_points AS earned_points'),'l4_act.points as attemptedpoint')
                ->where('l4_act.profession_id', $professionId)
                ->where('l4_ans.lbac_parent_id', $parentId)
                ->where('l4_ans.lbac_answer_id','!=',0)
                ->groupBy('l4_ans.lbac_activity_id')
                ->orderBy('earned_points', 'desc')
                ->get();
        $basicEarnedPoints = 0;
        if (isset($totalEarnBasicPoints) && isset($totalBasicPoints) && !empty($totalEarnBasicPoints)) {
            foreach ($totalEarnBasicPoints as $bKey => $bValue) {
                $basicEarnedPoints += (isset($bValue->earned_points) && $bValue->earned_points > 0) ? $bValue->earned_points : 0;
            }
        }
        $basicImagePoint = '';
        $basicBadgeRating = 0;
        if ($basicTotalPoints > 0 && $basicEarnedPoints > 0) {
            $badges = floor((($basicEarnedPoints) * 100) / ($basicTotalPoints));
            if ($badges > 0 && $badges <= 20) {
                $basicBadgeRating = 1;
            } elseif ($badges > 20 && $badges <= 40) {
                $basicBadgeRating = 2;
            } elseif ($badges > 40 && $badges <= 60) {
                $basicBadgeRating = 3;
            } elseif ($badges > 60 && $badges <= 80) {
                $basicBadgeRating = 4;
            } elseif ($badges > 80 && $badges <= 100) {
                $basicBadgeRating = 5;
            } else {
                $basicBadgeRating = 0;
            }
            if ($basicBadgeRating > 0) {
                $basicImagePoint = asset(Config::get('constant.BADGES_ORIGINAL_IMAGE_UPLOAD_PATH') . $basicBadgeRating . '.png');
            }
        }
         $basicArray = [];
        $templateWiseEarnedPoint = $templateWiseTotalPoint = $templateWiseTotalAttemptedPoint = array();
        $basicAttemptedQuestion = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_aa.id)) FROM " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_aa join " . config::get('databaseconstants.TBL_LEVEL4_OPTIONS') . " AS l4_an on l4_aa.id = l4_an.activity_id where l4_aa.deleted=1 and l4_aa.profession_id = $professionId ) as 'NoOfTotalQuestions', (select count(DISTINCT(L4_ANS.lbac_activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS L4_AC join " . config::get('databaseconstants.TBL_LEVEL4_ANSWERS_PARENT') . " AS L4_ANS on L4_AC.id = L4_ANS.lbac_activity_id  where L4_ANS.lbac_parent_id=" . $parentId . " AND L4_AC.profession_id = " . $professionId . " ) as 'NoOfAttemptedQuestions' "), array());
        $basicArray['noOfTotalQuestion'] = (isset($basicAttemptedQuestion[0]->NoOfTotalQuestions) && $basicAttemptedQuestion[0]->NoOfTotalQuestions != '' ) ? $basicAttemptedQuestion[0]->NoOfTotalQuestions : '';
        $basicArray['noOfAttemptedQuestion'] = (isset($basicAttemptedQuestion[0]->NoOfAttemptedQuestions) && $basicAttemptedQuestion[0]->NoOfAttemptedQuestions != '' ) ? $basicAttemptedQuestion[0]->NoOfAttemptedQuestions : '';
        $basicArray['totalPoints'] = $basicTotalPoints;
        $basicArray['earnedPoints'] = $basicEarnedPoints;
        $basicArray['badges'] = $basicImagePoint;
        $basicArray['badgesStarCount'] = $basicBadgeRating;
        $basicArray['basicAttemptedTotalPoints'] = $finalbasicAttemptedPoints;
        $array['level4Basic'] = $basicArray;
        $level4IntermediatePoint = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))
                ->distinct()
                ->where(['deleted' => 1, 'l4ia_profession_id' => $professionId])
                ->selectRaw('l4ia_question_template')
                ->get();
        if (isset($level4IntermediatePoint) && !empty($level4IntermediatePoint)) {
            $intermediateAttemptedQuestion = $intermediateTotalQuestion = $earned_points_intermediate = $total_points_intermediate = 0;

            foreach ($level4IntermediatePoint as $key => $templateId) {
                $templateTotalPoints[$templateId->l4ia_question_template] = DB::select(DB::raw("select (SELECT SUM(l4ia_question_point) FROM " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " where deleted=1 and l4ia_profession_id = $professionId and l4ia_question_template = $templateId->l4ia_question_template) as 'total_points'"));
                $totalEarnIntermediatePoints[$templateId->l4ia_question_template] = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_act")
                        ->join(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT') . " AS l4_ans", 'l4_act.id', '=', 'l4_ans.l4iapa_activity_id')
                        ->select(DB::raw('l4_ans.l4iapa_earned_point AS earned_points'), 'l4_ans.l4iapa_parent_id', 'l4_act.l4ia_question_point')
                        ->where('l4_act.l4ia_profession_id', $professionId)
                        ->where('l4_ans.l4iapa_parent_id', $parentId)
                        ->where('l4_ans.l4iapa_template_id', $templateId->l4ia_question_template)
                        ->groupBy('l4_ans.l4iapa_activity_id')
                        ->orderBy('earned_points', 'desc')
                        ->get();
                $getIntermediateQuestionI = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_ic.id)) FROM " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_ic join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS') . " AS l4_ao on l4_ic.id = l4_ao.l4iao_question_id where l4_ic.deleted=1 AND l4_ic.l4ia_profession_id = " . $professionId . " AND l4_ic.l4ia_question_template = " . $templateId->l4ia_question_template . ") as 'NoOfTotalQuestions', (select count(DISTINCT(L4_I_ANS.l4iapa_activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS L4_I_AC join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT') . " AS L4_I_ANS on L4_I_AC.id = L4_I_ANS.l4iapa_activity_id  where L4_I_ANS.l4iapa_parent_id=" . $parentId . " AND L4_I_AC.l4ia_profession_id = " . $professionId . " AND L4_I_AC.l4ia_question_template=" . $templateId->l4ia_question_template . ") as 'NoOfAttemptedQuestions' "), array());
                $intermediateTotalQuestion += (isset($getIntermediateQuestionI[0]->NoOfTotalQuestions) && $getIntermediateQuestionI[0]->NoOfTotalQuestions > 0 ) ? $getIntermediateQuestionI[0]->NoOfTotalQuestions : 0;
                $intermediateAttemptedQuestion += (isset($getIntermediateQuestionI[0]->NoOfAttemptedQuestions) && $getIntermediateQuestionI[0]->NoOfAttemptedQuestions > 0 ) ? $getIntermediateQuestionI[0]->NoOfAttemptedQuestions : 0;

                if (isset($totalEarnIntermediatePoints) && !empty($totalEarnIntermediatePoints)) {
                    foreach ($totalEarnIntermediatePoints as $key2 => $value2) {
                        $earned_points = $ptotal_points = $total_attempted_points = 0;
                        if (isset($value2) && !empty($value2)) {
                            foreach ($value2 as $key3 => $value3) {
                                $earned_points += $value3->earned_points;
                                $total_attempted_points += $value3->l4ia_question_point;
                            }
                            $earned_points = $earned_points;
                        } else {
                            $earned_points = 0;
                        }
                        $ptotal_points = (isset($templateTotalPoints[$key2][0]->total_points)) ? $templateTotalPoints[$key2][0]->total_points : 0;
                        $templateWiseEarnedPoint[$templateId->l4ia_question_template] = $earned_points;
                        $templateWiseTotalPoint[$templateId->l4ia_question_template] = $ptotal_points;
                        $templateWiseTotalAttemptedPoint[$templateId->l4ia_question_template] = $total_attempted_points;
                    }
                } else {
                    foreach ($templateTotalPoints as $key2 => $value2) {
                        $earned_points = 0;
                        $ptotal_points = (isset($templateTotalPoints[$key2][0]->total_points)) ? $templateTotalPoints[$key2][0]->total_points : 0;
                    }
                }
                $earned_points_intermediate += $earned_points;
                $total_points_intermediate += $ptotal_points;
            }
        } else {
            $earned_points_intermediate = $intermediateTotalQuestion = $total_points_intermediate = $intermediateAttemptedQuestion = 0;
        }
        $intermediateImagePoint = '';
        $intermediateBadgeRating = 0;
        if ($earned_points_intermediate > 0 && $total_points_intermediate > 0) {
            $badgesI = floor((($earned_points_intermediate) * 100) / ($total_points_intermediate));
            if ($badgesI > 0 && $badgesI <= 20) {
                $intermediateBadgeRating = 1;
            } elseif ($badgesI > 20 && $badgesI <= 40) {
                $intermediateBadgeRating = 2;
            } elseif ($badgesI > 40 && $badgesI <= 60) {
                $intermediateBadgeRating = 3;
            } elseif ($badgesI > 60 && $badgesI <= 80) {
                $intermediateBadgeRating = 4;
            } elseif ($badgesI > 80 && $badgesI <= 100) {
                $intermediateBadgeRating = 5;
            } else {
                $intermediateBadgeRating = 0;
            }
            if ($intermediateBadgeRating > 0) {
                $intermediateImagePoint = asset(Config::get('constant.BADGES_ORIGINAL_IMAGE_UPLOAD_PATH') . "a" . $intermediateBadgeRating . '.png');
            }
        }
        $intermediateArray = [];
        $intermediateArray['noOfTotalQuestion'] = $intermediateTotalQuestion;
        $intermediateArray['noOfAttemptedQuestion'] = $intermediateAttemptedQuestion;
        $intermediateArray['totalPoints'] = $total_points_intermediate;
        $intermediateArray['earnedPoints'] = $earned_points_intermediate;
        $intermediateArray['badges'] = $intermediateImagePoint;
        $intermediateArray['badgesCount'] = $intermediateBadgeRating;
        $intermediateArray['templateWiseEarnedPoint'] = $templateWiseEarnedPoint;
        $intermediateArray['templateWiseTotalPoint'] = $templateWiseTotalPoint;
        $intermediateArray['templateWiseTotalAttemptedPoint'] = $templateWiseTotalAttemptedPoint;
        $array['level4Intermediate'] = $intermediateArray;
        $level4AdvancePoint = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_PARENT_DATA'))
                ->distinct()
                ->where(['deleted' => 1, 'l4aapa_parent_id' => $parentId, 'l4aapa_profession_id' => $professionId, 'l4aapa_is_verified' => 2])
                ->selectRaw('l4aapa_media_type, id, l4aapa_earned_points')
                ->get();
        $data = [];
        $advanceEarnedPoint = 0;
        $advanceTotalPoints = 0;

        if (isset($level4AdvancePoint) && !empty($level4AdvancePoint)) {
            foreach ($level4AdvancePoint as $key => $value) {
                if ($value->l4aapa_media_type != '') {
                    $data[] = $value->l4aapa_media_type;
                    $advanceEarnedPoint += (isset($value->l4aapa_earned_points) && $value->l4aapa_earned_points != '') ? $value->l4aapa_earned_points : 0;
                    if ($value->l4aapa_media_type == 1) {
                        $advanceTotalPoints += Config::get('constant.USER_L4_VIDEO_POINTS');
                    } else if ($value->l4aapa_media_type == 2) {
                        $advanceTotalPoints += Config::get('constant.USER_L4_DOCUMENT_POINTS');
                    } else if ($value->l4aapa_media_type == 3) {
                        $advanceTotalPoints += Config::get('constant.USER_L4_ONE_PHOTO_POINTS');
                    }
                }
            }
        }

        $data = array_count_values($data);
        $rimage = (isset($data[3]) ? $data[3] : 0);
        $rvideo = (isset($data[1]) ? $data[1] : 0);
        $rdocument = (isset($data[2]) ? $data[2] : 0);
        $advanceBadgeRating = 0;
        $advanceImagePoint = '';
        if ($rimage > 0) {
            $advanceBadgeRating += 1;
        }
        if ($rvideo > 0) {
            $advanceBadgeRating += 2;
        }
        if ($rdocument > 0) {
            $advanceBadgeRating += 2;
        }
        if ($advanceBadgeRating > 0) {
            $advanceImagePoint = asset(Config::get('constant.BADGES_ORIGINAL_IMAGE_UPLOAD_PATH') . "w" . $advanceBadgeRating . '.png');
        }
        $advanceArray = [];
        $advanceArray['totalPoints'] = $advanceEarnedPoint;
        $advanceArray['earnedPoints'] = $advanceEarnedPoint;
        $advanceArray['badges'] = $advanceImagePoint;
        $advanceArray['snap'] = $rimage;
        $advanceArray['shoot'] = $rvideo;
        $advanceArray['report'] = $rdocument;
        $advanceArray['advanceBadgeStar'] = $advanceBadgeRating;
        $advanceArray['advanceTotalPoints'] = $advanceTotalPoints;
        $array['level4Advance'] = $advanceArray;

        return $array;
    }

}
