<?php

namespace App\Services\Teenagers\Repositories;

use DB;
use Auth;
use Config;
use App\Services\Teenagers\Contracts\TeenagersRepository;
use App\Services\Repositories\Eloquent\EloquentBaseRepository;
use Helpers;
use Illuminate\Pagination\Paginator;

class EloquentTeenagersRepository extends EloquentBaseRepository implements TeenagersRepository {

    public function getAllActiveTeenagers(){
        $teenagers = DB::table(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager ")
                    ->where('teenager.deleted', 1)->where('teenager.t_name', '!=', '')->get();
        return $teenagers;
    }
    /**
     * @return array of all the active teenagers
      Parameters
      @$searchParamArray : Array of Searching and Sorting parameters
     */

    public function getAllTeenagersData() {
        $teenagers = DB::table("pro_t_teenagers AS teenager")
                ->leftjoin(config::get('databaseconstants.TBL_SCHOOLS') . " AS school", 'teenager.t_school', '=', 'school.id')
                ->selectRaw('teenager.*, school.sc_name')
                ->whereIn('teenager.deleted', ['1','2'])
                ->where('teenager.t_name', '!=', '')
                ->get();

        return $teenagers;
    }

    public function getAllTeenagers($searchParamArray = array(),$currentPage = 0) {
        $whereStr = '';
        $orderStr = '';

        $whereArray = [];
        $whereArray[] = 'teenager.deleted IN (1,2)';
        $whereArray[] = 'teenager.t_name != "" ';

        if (isset($searchParamArray) && !empty($searchParamArray)) {
            if (isset($searchParamArray['searchBy']) && isset($searchParamArray['searchText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['searchText'] != '') {
                $whereArray[] = $searchParamArray['searchBy'] . " LIKE '%" . $searchParamArray['searchText'] . "%'";
            }

            if (isset($searchParamArray['searchBy']) && isset($searchParamArray['fromText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['fromText'] != '' && $searchParamArray['toText'] != '') {
                $whereArray[] = $searchParamArray['searchBy'] . " BETWEEN  '" . $searchParamArray['fromText'] . "'" . " AND  '" . $searchParamArray['toText'] ."'"  ;
            }

            if (isset($searchParamArray['orderBy']) && isset($searchParamArray['sortOrder']) && $searchParamArray['orderBy'] != '' && $searchParamArray['sortOrder'] != '') {
                $orderStr = " ORDER BY " . $searchParamArray['orderBy'] . " " . $searchParamArray['sortOrder'];
            }
        }

        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }
        
        if(isset($currentPage) && $currentPage > 0){
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });
        }

        $teenagers = DB::table(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager ")
                ->leftjoin(config::get('databaseconstants.TBL_SCHOOLS') . " AS school ", 'teenager.t_school', '=', 'school.id')
                ->selectRaw('teenager.*,school.sc_name')
                ->whereRaw($whereStr . $orderStr)
                ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));

        return $teenagers;
    }

    public function getAllTeenagersExport($searchParamArray = array()) {
        $whereStr = '';
        $orderStr = '';

        $whereArray = [];
        $whereArray[] = 'teenager.deleted IN (1,2)';
        $whereArray[] = 'teenager.t_name != "" ';
        if (isset($searchParamArray) && !empty($searchParamArray)) {
            if (isset($searchParamArray['searchBy']) && isset($searchParamArray['searchText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['searchText'] != '') {
                $whereArray[] = $searchParamArray['searchBy'] . " LIKE '%" . $searchParamArray['searchText'] . "%'";
            }

            if (isset($searchParamArray['orderBy']) && isset($searchParamArray['sortOrder']) && $searchParamArray['orderBy'] != '' && $searchParamArray['sortOrder'] != '') {
                $orderStr = " ORDER BY " . $searchParamArray['orderBy'] . " " . $searchParamArray['sortOrder'];
            }
        }

        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }

        $teenagers = DB::table(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager ")
                ->leftjoin(config::get('databaseconstants.TBL_SCHOOLS') . " AS school ", 'teenager.t_school', '=', 'school.id')
                ->leftjoin(config::get('databaseconstants.TBL_COUNTRIES') . " AS country ", 'teenager.t_country', '=', 'country.id')
                ->selectRaw('teenager.*,school.sc_name,country.c_name')
                ->whereRaw($whereStr . $orderStr)
                ->get();
        return $teenagers;
    }

    /**
     * @return array
      Parameters
      @$id : Searchtext
     */
    public function getsearchByText($serachtext) {
        $teenagers = DB::select(DB::raw("select id,t_name,t_nickname,t_email,is_search_on,t_uniqueid,t_photo from " . config::get('databaseconstants.TBL_TEENAGERS') . "
                                         where deleted = 1 AND t_isverified = 1 AND is_search_on = 1 AND ( t_name like '%" . $serachtext . "%' or t_nickname like '%" . $serachtext . "%' or t_email like '%" . $serachtext . "%' ) "));

        return $teenagers;
    }

    /**
     * @return Teenager details object
      Parameters
      @$teenagerDetail : Array of teenagers detail from front
     */
    public function saveTeenagerDetail($teenagerDetail) {
        if (isset($teenagerDetail['id']) && $teenagerDetail['id'] != '' && $teenagerDetail['id'] > 0) {
            $returnUpdate = $this->model->where('id', $teenagerDetail['id'])->update($teenagerDetail);
            $return = $this->model->where('id', $teenagerDetail['id'])->first();
        } else {
            $return = $this->model->create($teenagerDetail);
        }
        return $return;
    }

    /**
     * @return Boolean True/False
      Parameters
      @$id : Teenager ID
     */
    public function deleteTeenager($id) {
        $flag = true;
        $teenager = $this->model->find($id);
        $teenager->deleted = config::get('constant.DELETED_FLAG');
        $response = $teenager->save();
        if ($response) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Boolean True/False
      Parameters
      @$email : Teenager's email
     */
    public function checkActiveEmailExist($email, $id = '') {
        if ($id != '') {
            $user = $this->model->where('deleted', '1')->where('t_email', $email)->where('id', '!=', $id)->get();
        } else {
            $user = $this->model->where('deleted', '1')->where('t_email', $email)->get();
        }
        if ($user->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Boolean True/False
      Parameters
      @$phone : Teenager's phone
     */
    public function checkActivePhoneExist($phone, $id = '') {
        if ($id != '') {
            $user = $this->model->where('deleted', '1')->where('t_phone', $phone)->where('id', '!=', $id)->get();
        } else {
            $user = $this->model->where('deleted', '1')->where('t_phone', $phone)->get();
        }

        if ($user->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * update device token
     */
    public function updateTeenagerDeviceToken($userid, $deviceDetail) {
        $deviceDetail = $this->model->where('id', $userid)->update($deviceDetail);
        return $deviceDetail;
    }

    /**
     * get user detail by mobile
     */
    public function getTeenagerByMobile($mobile) {
        $teenager = $this->model->where('t_phone', $mobile)->where('deleted', '1')->first();
        if ($teenager) {
            $teenager = $teenager->toArray();
            return $teenager;
        } else {
            return false;
        }
    }

    public function getTeenagerByEmail($email) {
        $teenager = $this->model->where('t_email', $email)->where('deleted', '1')->first();
        if ($teenager) {
            $teenager = $teenager->toArray();
            return $teenager;
        } else {
            return false;
        }
    }

    public function getTeenagerByTeenagerId($id) {
        $teenager = $this->model->where('id', $id)->where('deleted', '1')->first();
        if ($teenager) {
            $teenager = $teenager->toArray();
            return $teenager;
        } else {
            return false;
        }
    }

    /**
     * get entire detail related user
     */
    public function getSelfSponserListData($id) {
        $TeenagerSponsorDetails = DB::select(DB::raw("select sponsor.id AS sponsor_id,sponsor.sp_email,sponsor.sp_admin_name,sponsor.sp_company_name,sponsor.sp_logo from " . config::get('databaseconstants.TBL_TEENAGERS_SPONSERS') . " AS teenager_sponsor
                                                      left join " . config::get('databaseconstants.TBL_SPONSORS') . " AS sponsor on teenager_sponsor.ts_sponsor=sponsor.id
                                                      where teenager_sponsor.ts_teenager =" . $id));
        return $TeenagerSponsorDetails;
    }

    public function getTeenagerById($id) {
        $TeenagerSponsorDetails = DB::select(DB::raw("select sponsor.id AS sponsor_id,sponsor.sp_email,sponsor.sp_admin_name,sponsor.sp_company_name,sponsor.sp_logo from " . config::get('databaseconstants.TBL_TEENAGERS_SPONSERS') . " AS teenager_sponsor
                                                      left join " . config::get('databaseconstants.TBL_SPONSORS') . " AS sponsor on teenager_sponsor.ts_sponsor=sponsor.id
                                                      where teenager_sponsor.ts_teenager =" . $id));

        $TeenagerDetails = DB::select(DB::raw("SELECT teenager.*,country.c_name,country.id as country_id,country.c_code
                                          FROM " . config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager
                                            left join " . config::get('databaseconstants.TBL_COUNTRIES') . " AS country on country.id = teenager.t_country
                                           where teenager.id = " . $id . " and teenager.deleted = 1"));
        if (isset($TeenagerDetails) && isset($TeenagerDetails[0])) {
            if (count($TeenagerSponsorDetails) > 0) {
                $TeenagerDetails[0]->t_sponsors = $TeenagerSponsorDetails;
            }
        } else {
            $TeenagerDetails[0] = array();
        }
        return $TeenagerDetails[0];
    }

    public function getTeenagerAllTypeBadges($teenagerId, $professionId) {
        $array = [];
        $totalBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY'))
                ->select(DB::raw('SUM(points) as total_points'))
                ->where('profession_id', $professionId)
                ->where('deleted', 1)
                ->get();
        $basicTotalPoints = (isset($totalBasicPoints[0]->total_points) && $totalBasicPoints[0]->total_points != '') ? $totalBasicPoints[0]->total_points : 0;
        $finalbasicAttemptedPoints = 0;
        //get total points for attempted questions
        $basicAttemptedTotalPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_act ")
                ->join(config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS l4_ans ", 'l4_act.id', '=', 'l4_ans.activity_id')
                ->select(DB::raw('l4_act.points as attemptedpoint'))
                ->where('l4_act.profession_id', $professionId)
                ->where('l4_ans.teenager_id', $teenagerId)
                ->groupBy('l4_ans.activity_id')
                ->get();

        if(isset($basicAttemptedTotalPoints) && !empty($basicAttemptedTotalPoints))
        {
            foreach ($basicAttemptedTotalPoints as $b1Key => $b1Value) {
                $finalbasicAttemptedPoints += (isset($b1Value->attemptedpoint) && $b1Value->attemptedpoint > 0) ? $b1Value->attemptedpoint : 0;
            }
        }

        $totalEarnBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_act ")
                ->join(config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS l4_ans ", 'l4_act.id', '=', 'l4_ans.activity_id')
                ->select(DB::raw('l4_ans.earned_points AS earned_points'),'l4_act.points as attemptedpoint')
                ->where('l4_act.profession_id', $professionId)
                ->where('l4_ans.teenager_id', $teenagerId)
                ->where('l4_ans.answer_id','!=',0)
                ->groupBy('l4_ans.activity_id')
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
        $basicAttemptedQuestion = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_aa.id)) FROM " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_aa join " . config::get('databaseconstants.TBL_LEVEL4_OPTIONS') . " AS l4_an on l4_aa.id = l4_an.activity_id where l4_aa.deleted=1 and l4_aa.profession_id = $professionId ) as 'NoOfTotalQuestions', (select count(DISTINCT(L4_ANS.activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS L4_AC join " . config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS L4_ANS on L4_AC.id = L4_ANS.activity_id  where L4_ANS.teenager_id=" . $teenagerId . " AND L4_AC.profession_id = " . $professionId . " ) as 'NoOfAttemptedQuestions' "), array());

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
                $totalEarnIntermediatePoints[$templateId->l4ia_question_template] = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_act ")
                        ->join(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER') . " AS l4_ans ", 'l4_act.id', '=', 'l4_ans.l4iaua_activity_id')
                        ->select(DB::raw('l4_ans.l4iaua_earned_point AS earned_points'), 'l4_ans.l4iaua_teenager', 'l4_act.l4ia_question_point')
                        ->where('l4_act.l4ia_profession_id', $professionId)
                        ->where('l4_ans.l4iaua_teenager', $teenagerId)
                        ->where('l4_ans.l4iaua_template_id', $templateId->l4ia_question_template)
                        ->groupBy('l4_ans.l4iaua_activity_id')
                        ->orderBy('earned_points', 'desc')
                        ->get();
                $getIntermediateQuestionI = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_ic.id)) FROM " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_ic join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS') . " AS l4_ao on l4_ic.id = l4_ao.l4iao_question_id where l4_ic.deleted=1 AND l4_ic.l4ia_profession_id = " . $professionId . " AND l4_ic.l4ia_question_template = " . $templateId->l4ia_question_template . ") as 'NoOfTotalQuestions', (select count(DISTINCT(L4_I_ANS.l4iaua_activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS L4_I_AC join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER') . " AS L4_I_ANS on L4_I_AC.id = L4_I_ANS.l4iaua_activity_id  where L4_I_ANS.l4iaua_teenager=" . $teenagerId . " AND L4_I_AC.l4ia_profession_id = " . $professionId . " AND L4_I_AC.l4ia_question_template=" . $templateId->l4ia_question_template . ") as 'NoOfAttemptedQuestions' "), array());
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
        $level4AdvancePoint = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA'))
                ->distinct()
                ->where(['deleted' => 1, 'l4aaua_teenager' => $teenagerId, 'l4aaua_profession_id' => $professionId, 'l4aaua_is_verified' => 2])
                ->selectRaw('l4aaua_media_type, id, l4aaua_earned_points')
                ->get();
        $data = [];
        $advanceEarnedPoint = 0;
        $advanceTotalPoints = 0;

        if (isset($level4AdvancePoint) && !empty($level4AdvancePoint)) {
            foreach ($level4AdvancePoint as $key => $value) {
                if ($value->l4aaua_media_type != '') {
                    $data[] = $value->l4aaua_media_type;
                    $advanceEarnedPoint += (isset($value->l4aaua_earned_points) && $value->l4aaua_earned_points != '') ? $value->l4aaua_earned_points : 0;
                    if ($value->l4aaua_media_type == 1) {
                        $advanceTotalPoints += Config::get('constant.USER_L4_VIDEO_POINTS');
                    } else if ($value->l4aaua_media_type == 2) {
                        $advanceTotalPoints += Config::get('constant.USER_L4_DOCUMENT_POINTS');
                    } else if ($value->l4aaua_media_type == 3) {
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

    /**
     * Parameter $teenagerId: Teenager ID
     * Parameter $sponsorId: comma seperated sponsor IDs
     * save teenager sponsor detail
     */
    public function saveTeenagerSponserId($teenagerId, $sponsorId) {
        $this->deleteTeenagerSponsors($teenagerId);
        $explodeSponser = explode(",", $sponsorId);

        foreach ($explodeSponser as $sponserId) {
            $sponserArray = array();
            $sponserArray['ts_sponsor'] = $sponserId;
            $sponserArray['ts_teenager'] = $teenagerId;
            $sponserArray['deleted'] = '1';
            $sponsorDetail = DB::table(config::get('databaseconstants.TBL_TEENAGERS_SPONSERS'))->insert($sponserArray);
        }

        return $sponsorDetail;
    }

    /**
     * Parameter $teenagerId: Teenager ID
     * delete teenager sponsor detail
     */
    public function deleteTeenagerSponsors($teenagerId) {
        DB::table(config::get('databaseconstants.TBL_TEENAGERS_SPONSERS'))->where('ts_teenager', $teenagerId)->delete();

        return true;
    }

    public function saveTeenagerBulkDetail($teenagerDetail) {
        $result = DB::select(DB::raw("SELECT
                                            *
                                            FROM " . config::get('databaseconstants.TBL_TEENAGERS')));
        $flag = true;
        foreach ($result as $value) {
            foreach ($teenagerDetail as $data) {
                if ($data == $value->t_email) {
                    $flag = false;
                }
            }
        }
        if ($flag) {
            $return = $this->model->create($teenagerDetail);
            return $return;
        } else {
            return false;
        }
    }

    /*
      return : array of Teenager detail by email id
     */

    public function getTeenagerDetailByEmailId($email) {
        $teenagerDetail = $this->model->where('deleted', '1')->where('t_email', $email)->first();
        return $teenagerDetail;
    }

    /*
     * Parameter $resetRequest : array of Details of password reset request
     * return : Boolean TRUE
     */

    public function saveTeenagerPasswordResetRequest($resetRequest) {
        DB::table(config::get('databaseconstants.TBL_TEENAGER_RESET_PASSWORD'))->insert($resetRequest);

        return true;
    }

    /**
     * Parameter : Social ID from provider
     * return : array of Teenager detail by socialID
     */
    public function getTeenagerBySocialId($socialId, $socialProvider) {
        if ($socialProvider == 'Google') {
            $teenagerDetail = $this->model->where('t_social_identifier', $socialId)->where('deleted', '1')->first();
        } else if ($socialProvider == 'Facebook') {
            $teenagerDetail = $this->model->where('t_fb_social_identifier', $socialId)->where('deleted', '1')->first();
        } else {
            $teenagerDetail = [];
        }
        return $teenagerDetail;
    }

    /**
     * Parameter $id : Teenager ID from provider
     * return : array of Teenager detail by socialID
     */
    public function getTeenagerPhotoById($id) {
        $teenagerPhoto = $this->model->select('t_photo')->where('id', $id)->where('deleted', '1')->first()->toArray();
        return $teenagerPhoto;
    }

    /**
     * Parameter $teenagerId : Teenager ID from provider
     * Parameter $OTP : One Time Password
     * return : Boolean TRUE / FALSE
     */
    public function verifyOTPAgainstTeenagerId($teenagerId, $OTP) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_RESET_PASSWORD'))->where("trp_teenager", $teenagerId)->where("trp_otp", $OTP)->where("trp_status", 1)->first();

        if (isset($result) && !empty($result)) {
            $currentDatetime = time(); // or your date as well
            $requestDatetime = strtotime($result->created_at);
            $datediff = $currentDatetime - $requestDatetime;
            $daysDifference = floor($datediff / (60 * 60 * 24));
            if ($daysDifference > config::get('constant.PASSWORD_RESET_REQUEST_VALIDITY_DAYS')) {
                return false;
            } else {
                $row = DB::table(config::get('databaseconstants.TBL_TEENAGER_RESET_PASSWORD'))->find($result->id);
                DB::table(config::get('databaseconstants.TBL_TEENAGER_RESET_PASSWORD'))->where('id', $result->id)->update(['trp_status' => 0]);
                return true;
            }
        } else {
            return false;
        }
    }

    public function checkCurrentPasswordAgainstTeenager($teenagerId, $currentPassword) {
        $result = $this->model->select('t_email')->where('id', $teenagerId)->where('deleted', '1')->first();

        if (isset($result) && !empty($result)) {
            $result = $result->toArray();
            if ($user = Auth::teenager()->attempt(['t_email' => $result['t_email'], 'password' => $currentPassword, 'deleted' => 1, 't_isverified' => '1'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
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
     * get school id by school name....
     */

    public function getSchoolIdByName($school) {
        $school = DB::select(DB::raw("select school.id from " . config::get('databaseconstants.TBL_SCHOOLS') . " AS school
                                       where school.sc_name ='" . $school . "' and school.deleted != 3"));
        if (!empty($school)) {
            return $school[0];
        } else {
            return 0;
        }
    }

    /*
     * update teenager varify status by Token
     */

    public function updateTeenagerTokenStatusByToken($token) {
        $teenagervarify = DB::table(config::get('databaseconstants.TBL_TEENAGER_EMAIL_VERICATION'))
                ->where('tev_token', $token)
                ->update(['tev_status' => 1]);
        if ($teenagervarify) {
            $teenagerDetail = DB::select(DB::raw("select tev_teenager from " . config::get('databaseconstants.TBL_TEENAGER_EMAIL_VERICATION') . " where tev_token ='" . $token . "'"));
            return $teenagerDetail;
        } else {
            return 0;
        }
    }

    /**
     * Parameter $teenagerId: Teenager ID and token
     * add teenager token for varify teenager
     */
    public function addTeenagerEmailVarifyToken($teenagerTokenDetail) {
        DB::table(config::get('databaseconstants.TBL_TEENAGER_EMAIL_VERICATION'))->insert($teenagerTokenDetail);
    }

    /*
     * update teenager varify status by uniqueid
     */

    public function updateTeenagerVerifyStatusById($teenagerid) {
        $teenagervarify = $this->model->where('id', $teenagerid)->update(['t_isverified' => '1']);
        if ($teenagervarify) {
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
    public function checkActiveTeenager($id) {
        $teenager = $this->model->where('deleted', '1')->where('id', $id)->get();

        if ($teenager->count() > 0) {
            if ($teenager[0]->t_social_provider == 'Normal') {
                $verifyTeenager = $this->model->where('deleted', '1')->where('t_isverified', '1')->where('id', $id)->get();
                if ($verifyTeenager->count() > 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @ Add data pf teenager career maping
      Parameters
      @$detail : detail
     */
    public function addTeenCareerMapping($detail) {
        DB::table(config::get('databaseconstants.TBL_TEENAGER_CAREER_MAPPING'))->insert($detail);
    }

    /**
     * $update detail of teenager career mapping
      Parameters
      @ $detail : array of detail
      @ professionid : profession id
     */
    public function UpdateTeenCareerMapping($mainArray, $professionid) {
        DB::table(config::get('databaseconstants.TBL_TEENAGER_CAREER_MAPPING'))->where('tcm_profession', $professionid)->update($mainArray);
    }

    /**
     * @check profession id for teenager career mapping
      Parameters
      @professionID : profession id
     */
    public function checkTeenCareerMappingProfessionId($professionid) {
        $professionid = DB::select(DB::raw("SELECT
                                            tcm_profession
                                            FROM " . config::get('databaseconstants.TBL_TEENAGER_CAREER_MAPPING') . " where tcm_profession=" . $professionid));
        return $professionid;
    }

    /**
     * @ get all detail of teenager career mapping
     */
    public function getAllTeenagerCareerMap($searchParamArray = array()) {
        $whereStr = '';
        $orderStr = '';

        $whereArray = [];
        $whereArray[] = 'profession.deleted IN (1,2)';
        if (isset($searchParamArray) && !empty($searchParamArray)) {
            if (isset($searchParamArray['searchBy']) && isset($searchParamArray['searchText']) && $searchParamArray['searchBy'] != '' && $searchParamArray['searchText'] != '') {
                $whereArray[] = $searchParamArray['searchBy'] . " LIKE '%" . $searchParamArray['searchText'] . "%'";
            }

            if (isset($searchParamArray['orderBy']) && isset($searchParamArray['sortOrder']) && $searchParamArray['orderBy'] != '' && $searchParamArray['sortOrder'] != '') {
                $orderStr = " ORDER BY " . $searchParamArray['orderBy'] . " " . $searchParamArray['sortOrder'];
            }
        }
        
        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }
        
        
        $detail = DB::table(config::get('databaseconstants.TBL_TEENAGER_CAREER_MAPPING') . " AS mapping ")
                ->leftjoin(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession ", 'mapping.tcm_profession', '=', 'profession.id')
                ->selectRaw('mapping.*,profession.pf_name')
                ->whereRaw($whereStr . $orderStr)
                ->paginate(Config::get('constant.ADMIN_RECORD_PER_PAGE'));

        return $detail;
    }

    /**
     * add data of make payment
     * Parameter: array of detail
     */
    public function addMakePayment($detail) {
        $paymentDetail = DB::table(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION'))->insert($detail);
        return $paymentDetail;
    }

    public function getLevel4BasicPoint($teenagerId, $professionId) {
        $totalBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY'))
                ->select(DB::raw('SUM(points) as total_points'))
                ->where('profession_id', $professionId)
                ->where('deleted', 1)
                ->get();

        $totalEarnBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_act ")
                ->join(config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS l4_ans ", 'l4_act.id', '=', 'l4_ans.activity_id')
                ->select(DB::raw('l4_ans.earned_points AS earned_points'), 'l4_ans.teenager_id')
                ->where('l4_act.profession_id', $professionId)
                ->where('l4_ans.teenager_id', $teenagerId)
                ->groupBy('l4_ans.activity_id')
                ->orderBy('earned_points', 'desc')
                ->get();
        $totalBasicPoints4 = [];
        if (isset($totalEarnBasicPoints) && !empty($totalEarnBasicPoints)) {
            foreach ($totalEarnBasicPoints as $keyPoint => $valuePoint) {
                $totalBasicPoints2[$valuePoint->teenager_id][] = $valuePoint->earned_points;
            }
            foreach ($totalBasicPoints2 as $k => $p) {
                $totalBasicPoints3['earned_points'] = array_sum($p);
                $totalBasicPoints3['teenager_id'] = $k;
                $totalBasicPoints3['total_points'] = $totalBasicPoints[0]->total_points;
                $totalBasicPoints4[] = $totalBasicPoints3;
            }
        } else {
            $totalBasicPoints3['earned_points'] = 0;
            $totalBasicPoints3['teenager_id'] = 0;
            $totalBasicPoints3['total_points'] = (isset($totalBasicPoints[0]->total_points) && $totalBasicPoints[0]->total_points > 0) ? $totalBasicPoints[0]->total_points : 0;
            $totalBasicPoints4[] = $totalBasicPoints3;
        }
        $basicAttemptedQuestion = DB::select(DB::raw("select (SELECT count(*) FROM " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_aa join " . config::get('databaseconstants.TBL_LEVEL4_OPTIONS') . " AS l4_an on l4_aa.id = l4_an.activity_id where l4_aa.deleted=1 and l4_aa.profession_id = $professionId ) as 'NoOfTotalQuestions', (select count(DISTINCT(L4_ANS.activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS L4_AC join " . config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS L4_ANS on L4_AC.id = L4_ANS.activity_id  where L4_ANS.teenager_id=" . $teenagerId . " AND L4_AC.profession_id = " . $professionId . " ) as 'NoOfAttemptedQuestions' "), array());
        $totalBasicPoints4[0]['noOfTotalBasicQuestion'] = (isset($basicAttemptedQuestion[0]->NoOfTotalQuestions) && $basicAttemptedQuestion[0]->NoOfTotalQuestions != '' ) ? $basicAttemptedQuestion[0]->NoOfTotalQuestions : '';
        $totalBasicPoints4[0]['noOfBasicAttemptedQuestion'] = (isset($basicAttemptedQuestion[0]->NoOfAttemptedQuestions) && $basicAttemptedQuestion[0]->NoOfAttemptedQuestions != '' ) ? $basicAttemptedQuestion[0]->NoOfAttemptedQuestions : '';
        return $totalBasicPoints4;
    }

    public function getLevel4IntermediatePoint($teenagerId, $professionId) {
        $level4IntermediatePoint = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))
                ->distinct()
                ->where(['deleted' => 1, 'l4ia_profession_id' => $professionId])
                ->selectRaw('l4ia_question_template')
                ->get();
        if (isset($level4IntermediatePoint) && !empty($level4IntermediatePoint)) {
            foreach ($level4IntermediatePoint as $key => $templateId) {
                $templateTotalPoints[$templateId->l4ia_question_template] = DB::select(DB::raw("select (SELECT SUM(l4ia_question_point) FROM " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " where deleted=1 and l4ia_profession_id = $professionId and l4ia_question_template = $templateId->l4ia_question_template) as 'total_points'"));
                $totalEarnIntermediatePoints[$templateId->l4ia_question_template] = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_act ")
                        ->join(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER') . " AS l4_ans ", 'l4_act.id', '=', 'l4_ans.l4iaua_activity_id')
                        ->select(DB::raw('l4_ans.l4iaua_earned_point AS earned_points'), 'l4_ans.l4iaua_teenager')
                        ->where('l4_act.l4ia_profession_id', $professionId)
                        ->where('l4_ans.l4iaua_teenager', $teenagerId)
                        ->where('l4_ans.l4iaua_template_id', $templateId->l4ia_question_template)
                        ->groupBy('l4_ans.l4iaua_activity_id')
                        ->orderBy('earned_points', 'desc')
                        ->get();
                if (isset($totalEarnIntermediatePoints) && !empty($totalEarnIntermediatePoints)) {
                    foreach ($totalEarnIntermediatePoints as $key2 => $value2) {
                        $earned_points = 0;
                        if (isset($value2) && !empty($value2)) {
                            foreach ($value2 as $key3 => $value3) {
                                $earned_points += $value3->earned_points;
                            }
                            $earned_points = $earned_points;
                        } else {
                            $earned_points = 0;
                        }
                        $p['earned_points'] = $earned_points;
                        $p['template_id'] = $key2;
                        $p['total_points'] = (isset($templateTotalPoints[$key2][0]->total_points)) ? $templateTotalPoints[$key2][0]->total_points : 0;
                        $result[$key2] = $p;
                    }
                } else {
                    foreach ($templateTotalPoints as $key2 => $value2) {
                        $p['earned_points'] = 0;
                        $p['template_id'] = $key2;
                        $p['total_points'] = (isset($templateTotalPoints[$key2][0]->total_points)) ? $templateTotalPoints[$key2][0]->total_points : 0;
                        $result[$key2] = $p;
                    }
                }
            }
        } else {
            $result = [];
        }
        return $result;
    }

    public function getLevel4AdvancePoint($teenagerId, $professionId) {
        $level4AdvancePoint = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA'))
                ->distinct()
                ->where(['deleted' => 1, 'l4aaua_teenager' => $teenagerId, 'l4aaua_profession_id' => $professionId, 'l4aaua_is_verified' => 2])
                ->selectRaw('l4aaua_media_type, id')
                ->get();

        $data = [];
        if (isset($level4AdvancePoint) && !empty($level4AdvancePoint)) {
            foreach ($level4AdvancePoint as $key => $value) {
                if ($value->l4aaua_media_type != '') {
                    $data[] = $value->l4aaua_media_type;
                }
            }
        }
        $data = array_count_values($data);
        $result['image'] = (isset($data[3]) ? $data[3] : 0);
        $result['video'] = (isset($data[1]) ? $data[1] : 0);
        $result['document'] = (isset($data[2]) ? $data[2] : 0);
        return $result;
    }

    public function getLevel4AllLevelProgress($teenagerId, $professionId) {
        $array = $completed = $result = $attempted = $data = [];

        $basic['completed'] = $basic['attempted'] = "no";
        $basicAttemptedQuestion = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_aa.id)) FROM " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_aa join " . config::get('databaseconstants.TBL_LEVEL4_OPTIONS') . " AS l4_an on l4_aa.id = l4_an.activity_id where l4_aa.deleted=1 and l4_aa.profession_id = $professionId ) as 'NoOfTotalQuestions', (select count(DISTINCT(L4_ANS.activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS L4_AC join " . config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS L4_ANS on L4_AC.id = L4_ANS.activity_id  where L4_ANS.teenager_id=" . $teenagerId . " AND L4_AC.profession_id = " . $professionId . " ) as 'NoOfAttemptedQuestions' "), array());
        if (isset($basicAttemptedQuestion) && !empty($basicAttemptedQuestion)) {
            if ($basicAttemptedQuestion[0]->NoOfTotalQuestions > 0 && ($basicAttemptedQuestion[0]->NoOfTotalQuestions == $basicAttemptedQuestion[0]->NoOfAttemptedQuestions) || ($basicAttemptedQuestion[0]->NoOfTotalQuestions < $basicAttemptedQuestion[0]->NoOfAttemptedQuestions)) {
                $basic['completed'] = "yes";
            }
            if ($basicAttemptedQuestion[0]->NoOfAttemptedQuestions > 0) {
                $basic['attempted'] = "yes";
            }
        }
        $totalIntermediateTemplate = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))
                ->distinct()
                ->where(['deleted' => 1, 'l4ia_profession_id' => $professionId])
                ->selectRaw('l4ia_question_template')
                ->get();
        if (isset($totalIntermediateTemplate) && !empty($totalIntermediateTemplate)) {
            foreach ($totalIntermediateTemplate as $templateId) {
                $result[$templateId->l4ia_question_template] = DB::select(DB::raw("select (SELECT count(*) FROM " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " where deleted=1 and l4ia_profession_id = $professionId and l4ia_question_template = $templateId->l4ia_question_template) as 'NoOfTotalQuestions', (select count(DISTINCT(L4_I_ANS.l4iaua_activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS L4_I_AC join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER') . " AS L4_I_ANS on L4_I_AC.id = L4_I_ANS.l4iaua_activity_id  where L4_I_ANS.l4iaua_teenager=" . $teenagerId . " AND L4_I_AC.l4ia_profession_id = " . $professionId . " AND L4_I_AC.l4ia_question_template=" . $templateId->l4ia_question_template . ") as 'NoOfAttemptedQuestions' "), array());
                if (isset($result[$templateId->l4ia_question_template]) && $result[$templateId->l4ia_question_template][0]->NoOfTotalQuestions > 0 && ($result[$templateId->l4ia_question_template][0]->NoOfTotalQuestions == $result[$templateId->l4ia_question_template][0]->NoOfAttemptedQuestions)) {
                    $completed[$templateId->l4ia_question_template] = $templateId->l4ia_question_template;
                }
                if (isset($result[$templateId->l4ia_question_template]) && $result[$templateId->l4ia_question_template][0]->NoOfAttemptedQuestions > 0) {
                    $attempted[$templateId->l4ia_question_template] = $templateId->l4ia_question_template;
                }
            }
        }
        if (count($completed) > 0 && count($totalIntermediateTemplate) > 0 && (count($totalIntermediateTemplate) == count($completed))) {
            $array['intermediateCompleted'] = "yes";
        } else {
            $array['intermediateCompleted'] = "no";
        }
        $array['basicCompleted'] = $basic['completed'];
        $array['basicAttempted'] = $basic['attempted'];
        $array['totalIntermediateTemplate'] = count($totalIntermediateTemplate);
        $array['totalCompletedTemplate'] = count($completed);
        $array['totalAttemptedTemplate'] = count($attempted);
        //$array['intermediate'] = $result;
        return $array;
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


        //Calculate Level 1 progress based on new logic discussed on 4 May 2017
        $totalL1QsPoints = DB::select(DB::raw("select SUM(l1ac_points) as totalL1QsPoints FROM " . config::get('databaseconstants.TBL_LEVEL1_ACTIVITY') . " where deleted=1"));
        //get teenagers attmpted question point for L1

        $userL1TotalAttemptedPoints = DB::table(config::get('databaseconstants.TBL_LEVEL1_ACTIVITY') . " AS l1 ")
                ->join(config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " AS l1_ans ", 'l1.id', '=', 'l1_ans.l1ans_activity')
                ->select(DB::raw('SUM(l1.l1ac_points) as totalattemptedpoints'))
                ->where('l1_ans.l1ans_teenager', $teenagerId)
                ->where('l1.deleted', 1)
                ->get();

        //get icon point
        $iconSelectionPoint = Helpers::getConfigValueByKey('LEVEL1_ICON_SELECTION_POINTS');
        $totalIconSelectionPoints = $iconSelectionPoint*config::get('constant.LEVEL1_ICON_BASE_COUNT_PROGRESS');
        $noOfIconUserSelected = 0;
        $teenagerSelectedIcon = $this->getTeenagerSelectedIcon($teenagerId);
        if(isset($teenagerSelectedIcon) && !empty($teenagerSelectedIcon)){
            if(count($teenagerSelectedIcon) > config::get('constant.LEVEL1_ICON_BASE_COUNT_PROGRESS'))
            {
                $noOfIconUserSelected = config::get('constant.LEVEL1_ICON_BASE_COUNT_PROGRESS');
            }else{
                $noOfIconUserSelected = count($teenagerSelectedIcon);
            }
        }

        $totalL1Points = $totalL1QsPoints[0]->totalL1QsPoints+$totalIconSelectionPoints;
        $totalL1AttemptedPoints = $userL1TotalAttemptedPoints[0]->totalattemptedpoints+($noOfIconUserSelected*Helpers::getConfigValueByKey('LEVEL1_ICON_SELECTION_POINTS'));

        $finalL1Progress = round(($totalL1AttemptedPoints*100)/$totalL1Points);


        $toalAndAttemptedQuestionCount = DB::select(DB::raw("select (SELECT count(*) FROM " . config::get('databaseconstants.TBL_LEVEL1_ACTIVITY') . " where deleted=1) as 'NoOfTotalQuestions', (SELECT count(*) FROM " . config::get('databaseconstants.TBL_LEVEL2_ACTIVITY') . " where deleted=1) as 'NoOfTotalQuestionsLevel2', (select count(*) from " . config::get('databaseconstants.TBL_LEVEL1_ANSWERS') . " where l1ans_teenager=" . $teenagerId . ") as 'NoOfAttemptedQuestions',(select count(*) from " . config::get('databaseconstants.TBL_LEVEL2_ANSWERS') . " where l2ans_teenager=" . $teenagerId . ") as 'NoOfAttemptedQuestionsLevel2',(select count(*) from " . config::get('databaseconstants.TBL_PROFESSIONS') . " where deleted=1) as 'NoOftotalProfession',(select count(*) from " . config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " where tpa_teenager=" . $teenagerId . ") as 'NoOfAttemptedProfessions' "), array());
        $attemptedProfessions = DB::table(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED'))->select(DB::raw('tpa_peofession_id'))->where('tpa_teenager', $teenagerId)->get();
        $totalL4Point = 0;
        if (isset($attemptedProfessions[0]) && !empty($attemptedProfessions[0])) {
            foreach ($attemptedProfessions as $pId) {
                $professionId = $pId->tpa_peofession_id;
                $basicTotalPoints = $total_points_intermediate = $totaladvancePoint = 0;
                $totalBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY'))
                        ->select(DB::raw('SUM(points) as total_points'))
                        ->where('profession_id', $professionId)
                        ->where('deleted', 1)
                        ->get();
                $basicTotalPoints = (isset($totalBasicPoints[0]->total_points) && $totalBasicPoints[0]->total_points != '' && $totalBasicPoints[0]->total_points > 0) ? $totalBasicPoints[0]->total_points : 0;
                $level4IntermediatePoint = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))
                        ->distinct()
                        ->where(['deleted' => 1, 'l4ia_profession_id' => $professionId])
                        ->selectRaw('l4ia_question_template')
                        ->get();
                if (isset($level4IntermediatePoint) && !empty($level4IntermediatePoint)) {
                    $intermediateTotalQuestion = $total_points_intermediate = 0;
                    foreach ($level4IntermediatePoint as $key => $templateId) {
                        $templateTotalPoints[$templateId->l4ia_question_template] = DB::select(DB::raw("select (SELECT SUM(l4ia_question_point) FROM " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " where deleted=1 and l4ia_profession_id = $professionId and l4ia_question_template = $templateId->l4ia_question_template) as 'total_points'"));
                        $array[] = $templateTotalPoints[$templateId->l4ia_question_template];
                        $getIntermediateQuestionI = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_ic.id)) FROM " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_ic join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS') . " AS l4_ao on l4_ic.id = l4_ao.l4iao_question_id where l4_ic.deleted=1 AND l4_ic.l4ia_profession_id = " . $professionId . " AND l4_ic.l4ia_question_template = " . $templateId->l4ia_question_template . ") as 'NoOfTotalQuestions'"), array());
                        $intermediateTotalQuestion += (isset($getIntermediateQuestionI[0]->NoOfTotalQuestions) && $getIntermediateQuestionI[0]->NoOfTotalQuestions > 0 ) ? $getIntermediateQuestionI[0]->NoOfTotalQuestions : 0;
                        $ptotal_points = 0;
                        foreach ($templateTotalPoints as $key2 => $value2) {
                            $ptotal_points = (isset($templateTotalPoints[$key2][0]->total_points)) ? $templateTotalPoints[$key2][0]->total_points : 0;
                        }
                        $total_points_intermediate += $ptotal_points;
                    }
                } else {
                    $total_points_intermediate = 0;
                }
                $totaladvancePoint = config::get('constant.TOTAL_L4_ADVANCE_POINT');
                //$totaladvancePoint = 0;
                $totalL4Point += $basicTotalPoints + $total_points_intermediate + $totaladvancePoint ;
            }
        }
        if (!empty($toalAndAttemptedQuestionCount)) {
            $levelPartA = round((Config::get('constant.LEVEL1A_PROGRESS') * $toalAndAttemptedQuestionCount[0]->NoOfAttemptedQuestions) / $toalAndAttemptedQuestionCount[0]->NoOfTotalQuestions);
            //get teenager icon
            $teenagerSelectedIcon = $this->getTeenagerSelectedIcon($teenagerId);
            
            if (isset($teenagerSelectedIcon) && !empty($teenagerSelectedIcon)) {
                $levelPartB = floor(Config::get('constant.LEVEL1B_PROGRESS'));
            } else {
                $levelPartB = 0;
            }

            $zeroBoosterLevel['Level1Progress'] = $finalL1Progress;
            $zeroBoosterLevel['Level2Progress'] = round((100 * $toalAndAttemptedQuestionCount[0]->NoOfAttemptedQuestionsLevel2) / $toalAndAttemptedQuestionCount[0]->NoOfTotalQuestionsLevel2);
            $zeroBoosterLevel['Level3Progress'] = round((100 * $toalAndAttemptedQuestionCount[0]->NoOfAttemptedProfessions) / $toalAndAttemptedQuestionCount[0]->NoOftotalProfession);
            if($totalL4Point > 0){
                $level4ProgressInfo = (isset($boosterArray['Level4']))?($boosterArray['Level4']/$totalL4Point)*100 : 0 ;
            }else{
                $level4ProgressInfo = 0;
            }
            $zeroBoosterLevel['Level4Progress'] = round($level4ProgressInfo);
        }
        $total['total'] = array_sum($boosterArray);
        $boosterArray = array_merge($boosterArray, $zeroBoosterLevel);
        $finalArray = array_merge($boosterArray, $total);
        return $finalArray;
    }

    public function deleteTeenagerData($userId) {
        //$teenager = $this->model->find($userId);
        //$teenager->deleted = config::get('constant.DELETED_FLAG');
        //$response = $teenager->save();
        //Remove basket attempted data
        DB::table('pro_tba_teenager_basket_attempted')->where('tba_teenager', '=', $userId)->delete();

        //Remove profession attempted data
        DB::table('pro_tpa_teenager_profession_attempted')->where('tpa_teenager', '=', $userId)->delete();

        //Remove profession attempted data
        DB::table('pro_tlb_teenager_level_boosters')->where('tlb_teenager', '=', $userId)->delete();

        //Remove Level 1 answer given by teenager
        DB::table('pro_l1ans_level1_answers')->where('l1ans_teenager', '=', $userId)->delete();

        //Remove Level 2 answer given by teenager
        DB::table('pro_l2ans_level2_answers')->where('l2ans_teenager', '=', $userId)->delete();
    }

    public function updateTeenagerToSelfSponsor($userId) {
        $teenager = $this->model->find($userId);
        $teenager->t_sponsor_choice = 1;
        $response = $teenager->save();
    }

    public function saveLevel1Part2BoosterPoints($userId, $points) {
        $levelBoosterPoints = DB::table('pro_tlb_teenager_level_boosters')->where('tlb_teenager', $userId)->where('tlb_level', config::get('constant.LEVEL1_ID'))->first();
        if (!empty($levelBoosterPoints)) {
            $availablePoints = $levelBoosterPoints->tlb_points;
            $totalPoints = $availablePoints + $points;

            DB::table('pro_tlb_teenager_level_boosters')
                    ->where('tlb_teenager', $userId)->where('tlb_level', config::get('constant.LEVEL1_ID'))
                    ->update(['tlb_points' => $totalPoints]);
        } else {
            $data = array('tlb_teenager' => $userId, 'tlb_level' => config::get('constant.LEVEL1_ID'), 'tlb_points' => $points, 'tlb_profession' => 0);
            DB::table('pro_tlb_teenager_level_boosters')
                    ->insert($data);
        }
    }

    public function saveLevel2BoosterPoints($userId, $totalPoints) {
        $levelBoosterPoints = DB::table('pro_tlb_teenager_level_boosters')->where('tlb_teenager', $userId)->where('tlb_level', config::get('constant.LEVEL2_ID'))->first();

        if (!empty($levelBoosterPoints)) {
            $point = DB::table('pro_tlb_teenager_level_boosters')->where('tlb_teenager', $userId)->where('tlb_level', config::get('constant.LEVEL2_ID'))->update(['tlb_points' => $totalPoints]);
        }
        return $point;
    }

    public function getTeenagerSelectedIcon($userId) {
        $teenagerFictionIcon = DB::select(DB::raw("SELECT
                                            pro_ti_teenager_icons.*,pro_ci_cartoon_icons.ci_image as fiction_image,ci_name,cic_name
                                            FROM pro_ti_teenager_icons
                                            LEFT JOIN pro_ci_cartoon_icons ON pro_ti_teenager_icons.ti_icon_id = pro_ci_cartoon_icons.id
                                            LEFT JOIN pro_cic_cartoon_icons_category ON pro_cic_cartoon_icons_category.id = pro_ci_cartoon_icons.ci_category
                                            where ti_icon_type = 1 AND ti_icon_id != 0 AND ti_teenager=" . $userId));

        $teenagerNonFictionIcon = DB::select(DB::raw("SELECT
                                            pro_ti_teenager_icons.*,pro_hi_human_icons.hi_image as nonfiction_image,hi_name,hic_name
                                            FROM pro_ti_teenager_icons
                                            LEFT JOIN pro_hi_human_icons ON pro_ti_teenager_icons.ti_icon_id = pro_hi_human_icons.id
                                            LEFT JOIN pro_hi_human_icons_category ON pro_hi_human_icons_category.id = pro_hi_human_icons.hi_category
                                            where ti_icon_type = 2 AND ti_icon_id != 0 AND ti_teenager=" . $userId));

        $teenagerRelationIcon = DB::select(DB::raw("SELECT
                                            pro_ti_teenager_icons.*,rel_name
                                            FROM pro_ti_teenager_icons
                                            LEFT JOIN pro_rel_relations ON pro_rel_relations.id = pro_ti_teenager_icons.ti_icon_relation
                                            where ti_icon_type = 3 AND ti_teenager=" . $userId));

        $finalIcon = array_merge($teenagerFictionIcon, $teenagerNonFictionIcon, $teenagerRelationIcon);
        return $finalIcon;
    }

    public function saveParentTeenVerification($data) {
        $verificationData = DB::table('pro_ptp_parent_teen_pair')->insert($data);
        return $verificationData;
    }

    /**
     * update image and nickname from Level1part2
     */
    public function updateTeenagerImageAndNickname($userid, $teenDetail) {
        $teenDetail = $this->model->where('id', $userid)->update($teenDetail);
        return $teenDetail;
    }

    /* Get teenager selected sponsor */

    public function getTeenagerSelectedSponsor($userId) {
        $teenagerSponsors = DB::table('pro_ts_teenager_sponsors')->where('ts_teenager', $userId)->get();
        return $teenagerSponsors;
    }

    /*
     * addTeenagerLevelCompleteRecord
     */

    public function addTeenagerLevelCompleteRecord($teenagerId, $level, $timer) {
        $data['tlcr_teenager'] = $teenagerId;
        $data['tlcr_level'] = $level;
        $data['tlcr_timer'] = $timer;
        $data['tlcr_booster_flag'] = 1;

        $vData = DB::table(config::get('databaseconstants.TBL_TEENAGER_LEVEL_COMPLETE_RECORD'))->insert($data);
        return $vData;
    }

    public function getActiveSchoolStudentsDetail($id) {
        
        
        if((\Session::has('currentPage'))){
            $currentPage = \Session::get('currentPage'); 
            
            if(isset($currentPage) && $currentPage > 0){
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });
            }
        }
        
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))->where('t_school', $id)->where('deleted','!=',3)->orderBy('created_at','ACE')->paginate(15);
        return $result;
    }

    /*
     * Save teenager meta data like education, achievement 
     */

    public function saveTeenagerMetaData($data) {
        // $metaData = DB::table(config::get('databaseconstants.TBL_TEENAGER_META_DATA'))->insert($data);
        // return $metaData;

        if ($data['id'] != '' && $data['id'] > 0) {
            $metaData = DB::table(config::get('databaseconstants.TBL_TEENAGER_META_DATA'))->where('id', $data['id'])->update($data);
        } else {
            $metaData = DB::table(config::get('databaseconstants.TBL_TEENAGER_META_DATA'))->insert($data);
        }
        return $metaData;
    }

    /*
     * Get teenager meta data 
     */

    public function deleteTeenagerMetadata($teenagerid, $meta_value_id) {
        $data = DB::table(config::get('databaseconstants.TBL_TEENAGER_META_DATA'))->where('tmd_teenager', '=', $teenagerid)->where('id', '=', $meta_value_id)->delete();
        return $data;
    }

    /*
     * get all parents of teen
     */

    public function getTeenParents($teenagerid) {
        $detail = DB::select(DB::raw("SELECT
                                            parent.id,parent.p_first_name,parent.p_last_name,parent.p_email,parent.p_gender,parent.p_photo,parent.p_user_type,pair.ptp_is_verified as pair_status
                                            FROM pro_ptp_parent_teen_pair as pair join pro_p_parent as parent on pair.ptp_parent_id=parent.id where pair.deleted = 1 AND pair.ptp_teenager = " . $teenagerid));
        return $detail;
    }
    
    public function getParentTeens($parentId) {
        $detail = DB::select(DB::raw("SELECT
                                            teen.id,teen.t_name,teen.t_nickname,teen.t_email,teen.t_gender,teen.t_photo,pair.ptp_is_verified as pair_status
                                            FROM pro_ptp_parent_teen_pair as pair join pro_t_teenagers as teen on pair.ptp_teenager = teen.id where pair.deleted = 1 AND teen.deleted = 1 AND pair.ptp_parent_id = " . $parentId));
        return $detail;
    }
    
    /*
     * Get teenager detail by unique id 
     */

    public function getTeenagerByUniqueId($teenagerUniqueId) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))->where('deleted', '1')->where('t_uniqueid', $teenagerUniqueId)->first();
        return $result;
    }

    public function savePassword($password, $uniqueid) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))->where('t_uniqueid', $uniqueid)->update(['password' => $password]);
        return $result;
    }

    public function getEmailDataOfStudent($schoolid) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))->where('t_school', $schoolid)->where('t_isverified', 0)->where('deleted', '!=', 3)->get();
        return $result;
    }

    public function checkMailSentOrNot($userid) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_EMAIL_VERICATION'))->where('tev_teenager', $userid)->get();
        return $result;
    }

    public function updateTeenagerPopupHistory($data) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_POPUP_SHOW'))->insert($data);
        return $result;
    }

    public function getTeenDetailByParentId($id) {
       // $result = DB::table(config::get('databaseconstants.TBL_PARENT_TEEN_PAIR'))->where('ptp_parent_id', $id)->where('ptp_is_verified', 1)->where('deleted', 1)->first();
       // return $result;
        
        $result = DB::select(DB::raw("SELECT
                                            pair.*
                                            FROM pro_ptp_parent_teen_pair as pair join pro_t_teenagers as teen on pair.ptp_teenager = teen.id where pair.deleted = 1 AND teen.deleted = 1 AND pair.ptp_is_verified = 1 AND pair.ptp_parent_id = " . $id));
        return $result;
    }

    /*
     * Update payment status
     */

    public function updatePaymentStatus($userid, $teenagerPaymentDetail) {
        $teenDetail = $this->model->where('id', $userid)->update($teenagerPaymentDetail);
        return $teenDetail;
    }

    /* Get phone code by country id */

    public function getCountryPhoneCode($countryId) {
        $result = DB::table(config::get('databaseconstants.TBL_COUNTRIES'))->where('id', $countryId)->first();
        return $result;
    }
    
    /*Get teenager icon with qualities*/
    public function getTeenSelectedIconWithQualities($teenagerId,$iconType)
    {      
        $query = DB::table(config::get('databaseconstants.TBL_TEENAGER_ICON') . " AS tc ");
        $query->join(config::get('databaseconstants.TBL_TEENAGER_ICON_QUALITIES') . " AS tcq ", 'tcq.tiqa_ti_id', '=', 'tc.id');
        $query->join(config::get('databaseconstants.TBL_LEVEL1_QUALITY') . " AS q ", 'tcq.tiqa_quality_id', '=', 'q.id');
        $query->selectRaw('count(tcq.id) as sum,q.l1qa_name,q.id as qualityid');        
        $query->groupBy('tcq.tiqa_quality_id');
        if(isset($teenagerId) && $teenagerId != ''){
           $query->where('tc.ti_teenager', $teenagerId);  
        }
        $query->whereIn('tc.ti_icon_type', $iconType);
        $teenSelectedIcon = $query->get();

        return $teenSelectedIcon;
    }

    public function getAllActiveTeenagersForNotification(){
        $teenagers = DB::table(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager")
                    ->leftjoin(config::get('databaseconstants.TBL_COUNTRIES') . " AS country", 'country.id', '=', 'teenager.t_country')
                    ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_DEVICE_TOKEN') . " AS device_token", 'teenager.id', '=', 'device_token.tdt_user_id')
                    ->select('teenager.*' , 'country.c_name',DB::raw('GROUP_CONCAT(device_token.tdt_device_type) AS tdt_device_type'))
                    ->where('teenager.deleted', '=', 1)
                    ->where('teenager.t_name', '!=', '')
                    ->groupBy('teenager.id')
                    ->get();
        return $teenagers;
    }

    public function saveTeenagerActivityDetail($userId) {
        $response = $this->model->where('id', $userId)->update(['t_last_activity' => strtotime(date('Y-m-d H:i:s'))]);
        return $response;
    }

    public function getInactiveTeenDetailForNotification() {
        $teenagersDetail = DB::table(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager ")
                        ->leftjoin(config::get('databaseconstants.TBL_TEENAGER_DEVICE_TOKEN') . " AS device_token ", 'teenager.id', '=', 'device_token.tdt_user_id')
                        ->selectRaw('teenager.t_last_activity,device_token.tdt_device_type,device_token.tdt_device_token,teenager.id,teenager.is_notify')
                        ->where('teenager.deleted' , '=', 1)
                        ->where('teenager.t_name' , '!=', '')
                        ->get();
        return $teenagersDetail;
    }

    public function getAllTeenagersByClass($id) {

        $whereArray[] = 'teenager.deleted IN (1,2)';
        $whereArray[] = 'teenager.t_name != "" ';

        $teenagers = DB::table(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager ")
                    ->leftjoin(config::get('databaseconstants.TBL_SCHOOLS') . " AS school ", 'teenager.t_school', '=', 'school.id')
                    ->selectRaw('teenager.*,school.sc_name')
                    ->where('teenager.deleted','=', 1)
                    ->where('teenager.t_name','!=', '')
                    ->where('teenager.t_class','=', $id)
                    ->get();

        return $teenagers;
    }

    public function getTeenagerSelectedIconByClass($classId) {
        $teenagerFictionIcon = DB::select(DB::raw("SELECT
                                            pro_ti_teenager_icons.*,pro_ci_cartoon_icons.ci_image as fiction_image,ci_name,cic_name
                                            FROM pro_ti_teenager_icons
                                            LEFT JOIN pro_t_teenagers ON pro_t_teenagers.id = pro_ti_teenager_icons.ti_teenager
                                            LEFT JOIN pro_ci_cartoon_icons ON pro_ti_teenager_icons.ti_icon_id = pro_ci_cartoon_icons.id
                                            LEFT JOIN pro_cic_cartoon_icons_category ON pro_cic_cartoon_icons_category.id = pro_ci_cartoon_icons.ci_category
                                            where ti_icon_type = 1 AND ti_icon_id != 0 AND pro_t_teenagers.t_class=" . $classId));

        $teenagerNonFictionIcon = DB::select(DB::raw("SELECT
                                            pro_ti_teenager_icons.*,pro_hi_human_icons.hi_image as nonfiction_image,hi_name,hic_name
                                            FROM pro_ti_teenager_icons
                                            LEFT JOIN pro_t_teenagers ON pro_t_teenagers.id = pro_ti_teenager_icons.ti_teenager
                                            LEFT JOIN pro_hi_human_icons ON pro_ti_teenager_icons.ti_icon_id = pro_hi_human_icons.id
                                            LEFT JOIN pro_hi_human_icons_category ON pro_hi_human_icons_category.id = pro_hi_human_icons.hi_category
                                            where ti_icon_type = 2 AND ti_icon_id != 0 AND pro_t_teenagers.t_class=" . $classId));

        $teenagerRelationIcon = DB::select(DB::raw("SELECT
                                            pro_ti_teenager_icons.*,rel_name
                                            FROM pro_ti_teenager_icons
                                            LEFT JOIN pro_t_teenagers ON pro_t_teenagers.id = pro_ti_teenager_icons.ti_teenager
                                            LEFT JOIN pro_rel_relations ON pro_rel_relations.id = pro_ti_teenager_icons.ti_icon_relation
                                            where ti_icon_type = 3 AND pro_t_teenagers.t_class=" . $classId));

        $finalIcon = array_merge($teenagerFictionIcon, $teenagerNonFictionIcon, $teenagerRelationIcon);
        return $finalIcon;
    }

    public function getTeenagerAllTypeBadgesByClass($classId, $professionId) {
        $array = [];
        $totalBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY'))
                ->select(DB::raw('SUM(points) as total_points'))
                ->where('profession_id', $professionId)
                ->where('deleted', 1)
                ->get();

        $totalEarnBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_act ")
                ->join(config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS l4_ans ", 'l4_act.id', '=', 'l4_ans.activity_id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen ", 'teen.id', '=', 'l4_ans.teenager_id')
                ->select(DB::raw('l4_ans.earned_points AS earned_points'))
                ->where('l4_act.profession_id', $professionId)
                ->where('teen.t_class', $classId)
                ->where('l4_ans.answer_id','!=',0)
                ->groupBy('l4_ans.activity_id')
                ->orderBy('earned_points', 'desc')
                ->get();
        $basicTotalPoints = (isset($totalBasicPoints[0]->total_points) && $totalBasicPoints[0]->total_points != '') ? $totalBasicPoints[0]->total_points : 0;
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
        $basicArray['badges'] = $basicImagePoint;
        $basicArray['badgesStarCount'] = $basicBadgeRating;
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
                $totalEarnIntermediatePoints[$templateId->l4ia_question_template] = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_act ")
                        ->join(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER') . " AS l4_ans ", 'l4_act.id', '=', 'l4_ans.l4iaua_activity_id')
                        ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen ", 'teen.id', '=', 'l4_ans.l4iaua_teenager')
                        ->select(DB::raw('l4_ans.l4iaua_earned_point AS earned_points'), 'l4_ans.l4iaua_teenager')
                        ->where('l4_act.l4ia_profession_id', $professionId)
                        ->where('teen.t_class', $classId)
                        ->where('l4_ans.l4iaua_template_id', $templateId->l4ia_question_template)
                        ->groupBy('l4_ans.l4iaua_activity_id')
                        ->orderBy('earned_points', 'desc')
                        ->get();
                //$getIntermediateQuestionI = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_ic.id)) FROM " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_ic join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS') . " AS l4_ao on l4_ic.id = l4_ao.l4iao_question_id where l4_ic.deleted=1 AND l4_ic.l4ia_profession_id = " . $professionId . " AND l4_ic.l4ia_question_template = " . $templateId->l4ia_question_template . ") as 'NoOfTotalQuestions', (select count(DISTINCT(L4_I_ANS.l4iaua_activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS L4_I_AC join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER') . " AS L4_I_ANS on L4_I_AC.id = L4_I_ANS.l4iaua_activity_id  where L4_I_ANS.l4iaua_teenager=" . $teenagerId . " AND L4_I_AC.l4ia_profession_id = " . $professionId . " AND L4_I_AC.l4ia_question_template=" . $templateId->l4ia_question_template . ") as 'NoOfAttemptedQuestions' "), array());
                $getIntermediateQuestionI = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_ic.id))
                                            FROM " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_ic
                                            join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS') . " AS l4_ao on l4_ic.id = l4_ao.l4iao_question_id
                                            where l4_ic.deleted=1 AND l4_ic.l4ia_profession_id = " . $professionId . "
                                            AND l4_ic.l4ia_question_template = " . $templateId->l4ia_question_template . ") as 'NoOfTotalQuestions',
                                            (select count(DISTINCT(L4_I_ANS.l4iaua_activity_id))
                                            from " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS L4_I_AC
                                            join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER') . " AS L4_I_ANS on L4_I_AC.id = L4_I_ANS.l4iaua_activity_id
                                            join ". config::get('databaseconstants.TBL_TEENAGERS') . " AS teen on teen.id = L4_I_ANS.l4iaua_teenager
                                            where teen.t_class=" . $classId . " AND L4_I_AC.l4ia_profession_id = " . $professionId . "
                                            AND L4_I_AC.l4ia_question_template=" . $templateId->l4ia_question_template . ") as 'NoOfAttemptedQuestions' "), array());
                $intermediateTotalQuestion += (isset($getIntermediateQuestionI[0]->NoOfTotalQuestions) && $getIntermediateQuestionI[0]->NoOfTotalQuestions > 0 ) ? $getIntermediateQuestionI[0]->NoOfTotalQuestions : 0;
                $intermediateAttemptedQuestion += (isset($getIntermediateQuestionI[0]->NoOfAttemptedQuestions) && $getIntermediateQuestionI[0]->NoOfAttemptedQuestions > 0 ) ? $getIntermediateQuestionI[0]->NoOfAttemptedQuestions : 0;
                if (isset($totalEarnIntermediatePoints) && !empty($totalEarnIntermediatePoints)) {
                    foreach ($totalEarnIntermediatePoints as $key2 => $value2) {
                        $earned_points = $ptotal_points = 0;
                        if (isset($value2) && !empty($value2)) {
                            foreach ($value2 as $key3 => $value3) {
                                $earned_points += $value3->earned_points;
                            }
                            $earned_points = $earned_points;
                        } else {
                            $earned_points = 0;
                        }
                        $ptotal_points = (isset($templateTotalPoints[$key2][0]->total_points)) ? $templateTotalPoints[$key2][0]->total_points : 0;
                        $templateWiseEarnedPoint[$templateId->l4ia_question_template] = $earned_points;
                        $templateWiseTotalPoint[$templateId->l4ia_question_template] = $ptotal_points;
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
        $intermediateArray['badges'] = $intermediateImagePoint;
        $intermediateArray['badgesCount'] = $intermediateBadgeRating;
        $array['level4Intermediate'] = $intermediateArray;
        $level4AdvancePoint = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA')." As l4_aaus")
                ->distinct()
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen ", 'teen.id', '=', 'l4_aaus.l4aaua_teenager')
                ->where(['l4_aaus.deleted' => 1, 'teen.t_class' => $classId, 'l4_aaus.l4aaua_profession_id' => $professionId, 'l4_aaus.l4aaua_is_verified' => 2])
                ->selectRaw('l4_aaus.l4aaua_media_type, l4_aaus.id, l4_aaus.l4aaua_earned_points')
                ->get();
        $data = [];
        $advanceEarnedPoint = 0;
        if (isset($level4AdvancePoint) && !empty($level4AdvancePoint)) {
            foreach ($level4AdvancePoint as $key => $value) {
                if ($value->l4aaua_media_type != '') {
                    $data[] = $value->l4aaua_media_type;
                    $advanceEarnedPoint += (isset($value->l4aaua_earned_points) && $value->l4aaua_earned_points != '') ? $value->l4aaua_earned_points : 0;
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
        $advanceArray['badges'] = $advanceImagePoint;
        $advanceArray['advanceBadgeStar'] = $advanceBadgeRating;
        $array['level4Advance'] = $advanceArray;

        return $array;
    }

    public function getAllUserDataForSendNotification($userId) {
        $userData = $this->model->where('id', '=', $userId)->where('deleted', 1)->get()->toArray();

        if (isset($userData)) {
            $data = [];
            $data['user_name'] = $userData[0]['t_name'];
        }
        return $data;
    }


    public function checkSecretKey($secret_key) 
    {
        $deviceDetail = $this->model->where('t_uniqueid', $secret_key)->first();
        return $deviceDetail;
    }

    public function getUserDataForCoinsDetail($userId) {
        $userData = $this->model->where('id', '=', $userId)->first();
        
        $data = [];
        if (isset($userData)) {
            $data['t_coins'] = $userData['t_coins'];
        }
        return $data;
    }

    public function updateTeenagerCoinsDetail($userid, $Coins) {
        $userDetail = $this->model->where('id', $userid)->update(['t_coins' => $Coins]);
        return $userDetail;
    }

    public function updateExpiredCoinsField($userid,$type) {
        $userDetail = DB::table(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION'))->where('tn_userid', $userid)->where('tn_user_type', $type)->update(['tn_coins_expired' => 1]);
        return $userDetail;
    }

    public function getAllUsersCoinsDetail() {
        $userData = $this->model->where('deleted', 1)->get()->toArray();
        return $userData;
    }

    public function getActiveSchoolStudentsDetailForSearch($id,$search) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))
                ->where('t_school', $id)
                ->where('deleted','!=',3)
                ->where(function ($query) use ($search) {
                    $query->where('t_name','like', '%'.$search.'%')
                        ->orWhere('t_email','like', '%'.$search.'%')
                        ->orWhere('t_class','=',$search)
                        ->orWhere('t_division','=',$search);
                })
                ->orderBy('created_at','ASC')
                ->paginate(10);
        return $result;
    }

    public function getEmailDataOfStudentForSearch($schoolid,$search) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGERS'))
                ->where('t_school', $schoolid)
                ->where('t_isverified', 0)
                ->where('deleted', '!=', 3)
                ->where(function ($query) use ($search) {
                    $query->where('t_name','like', '%'.$search.'%')
                        ->orWhere('t_email','like', '%'.$search.'%')
                        ->orWhere('t_class','=',$search)
                        ->orWhere('t_division','=',$search);
                })
                ->get();
        return $result;
    }

    public function getTeenagerAllTypeBadgesForReport($teenagerId, $professionId,$genderid) {
        $array = [];
        $totalBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY'))
                ->select(DB::raw('SUM(points) as total_points'))
                ->where('profession_id', $professionId)
                ->where('deleted', 1)
                ->get();
        $basicTotalPoints = (isset($totalBasicPoints[0]->total_points) && $totalBasicPoints[0]->total_points != '') ? $totalBasicPoints[0]->total_points : 0;
        $finalbasicAttemptedPoints = 0;
        $whereStr = '';
        $whereArray = [];
        $whereArray[] = 'l4_act.profession_id = '.$professionId;
        if (isset($genderid) && $genderid != '') {
            $whereArray[] = 'teenager.t_gender ='.$genderid;
        }
        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }
        //get total points for attempted questions
        $basicAttemptedTotalPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_act ")
                ->join(config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS l4_ans ", 'l4_act.id', '=', 'l4_ans.activity_id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'l4_ans.teenager_id', '=', 'teenager.id')
                ->select(DB::raw('l4_act.points as attemptedpoint'))
                ->whereRaw($whereStr)
                ->groupBy('l4_ans.teenager_id' , 'l4_ans.activity_id')
                ->get();

        if(isset($basicAttemptedTotalPoints) && !empty($basicAttemptedTotalPoints))
        {
            foreach ($basicAttemptedTotalPoints as $b1Key => $b1Value) {
                $finalbasicAttemptedPoints += (isset($b1Value->attemptedpoint) && $b1Value->attemptedpoint > 0) ? $b1Value->attemptedpoint : 0;
            }
        }

        $totalEarnBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_act ")
                ->join(config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS l4_ans ", 'l4_act.id', '=', 'l4_ans.activity_id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'l4_ans.teenager_id', '=', 'teenager.id')
                ->select(DB::raw('l4_ans.earned_points AS earned_points'),'l4_act.points as attemptedpoint')
                ->whereRaw($whereStr)
                ->where('l4_ans.answer_id','!=',0)
                ->groupBy('l4_ans.teenager_id' , 'l4_ans.activity_id')
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
        $basicAttemptedQuestion = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_aa.id)) FROM " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_aa join " . config::get('databaseconstants.TBL_LEVEL4_OPTIONS') . " AS l4_an on l4_aa.id = l4_an.activity_id where l4_aa.deleted=1 and l4_aa.profession_id = $professionId ) as 'NoOfTotalQuestions', (select count(DISTINCT(L4_ANS.activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS L4_AC join " . config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS L4_ANS on L4_AC.id = L4_ANS.activity_id  where  L4_AC.profession_id = " . $professionId . " ) as 'NoOfAttemptedQuestions' "), array());

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
            $whereArray = [];
            $whereArray[] = 'l4_act.l4ia_profession_id = '.$professionId;
            if (isset($genderid) && $genderid != '') {
                $whereArray[] = 'teenager.t_gender ='.$genderid;
            }
            if (!empty($whereArray)) {
                $whereStr = implode(" AND ", $whereArray);
            }
            foreach ($level4IntermediatePoint as $key => $templateId) {
                $templateTotalPoints[$templateId->l4ia_question_template] = DB::select(DB::raw("select (SELECT SUM(l4ia_question_point) FROM " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " where deleted=1 and l4ia_profession_id = $professionId and l4ia_question_template = $templateId->l4ia_question_template) as 'total_points'"));
                $totalEarnIntermediatePoints[$templateId->l4ia_question_template] = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_act ")
                        ->join(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER') . " AS l4_ans ", 'l4_act.id', '=', 'l4_ans.l4iaua_activity_id')
                        ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'l4_ans.l4iaua_teenager', '=', 'teenager.id')
                        ->select(DB::raw('l4_ans.l4iaua_earned_point AS earned_points'), 'l4_ans.l4iaua_teenager', 'l4_act.l4ia_question_point')
                        ->whereRaw($whereStr)
                        ->where('l4_ans.l4iaua_template_id', $templateId->l4ia_question_template)
                        ->groupBy('l4_ans.l4iaua_teenager','l4_ans.l4iaua_activity_id')
                        ->orderBy('earned_points', 'desc')
                        ->get();
                $getIntermediateQuestionI = DB::select(DB::raw("select (SELECT count(DISTINCT(l4_ic.id)) FROM " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_ic join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_OPTIONS') . " AS l4_ao on l4_ic.id = l4_ao.l4iao_question_id where l4_ic.deleted=1 AND l4_ic.l4ia_profession_id = " . $professionId . " AND l4_ic.l4ia_question_template = " . $templateId->l4ia_question_template . ") as 'NoOfTotalQuestions', (select count(DISTINCT(L4_I_ANS.l4iaua_activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS L4_I_AC join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER') . " AS L4_I_ANS on L4_I_AC.id = L4_I_ANS.l4iaua_activity_id  where L4_I_AC.l4ia_profession_id = " . $professionId . " AND L4_I_AC.l4ia_question_template=" . $templateId->l4ia_question_template . ") as 'NoOfAttemptedQuestions' "), array());
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
        $whereArray = [];
        $whereArray[] = 'l4_adv.l4aaua_profession_id = '.$professionId;
        if (isset($genderid) && $genderid != '') {
            $whereArray[] = 'teenager.t_gender ='.$genderid;
        }
        if (!empty($whereArray)) {
            $whereStr = implode(" AND ", $whereArray);
        }
        $level4AdvancePoint = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA'). " AS l4_adv ")
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teenager", 'l4_adv.l4aaua_teenager', '=', 'teenager.id')
                ->distinct()
                ->whereRaw($whereStr)
                ->where(['l4_adv.deleted' => 1, 'l4_adv.l4aaua_is_verified' => 2])
                ->selectRaw('l4_adv.l4aaua_media_type, l4_adv.id, l4_adv.l4aaua_earned_points')
                ->get();

        $data = [];
        $advanceEarnedPoint = 0;
        $advanceTotalPoints = 0;

        if (isset($level4AdvancePoint) && !empty($level4AdvancePoint)) {
            foreach ($level4AdvancePoint as $key => $value) {
                if ($value->l4aaua_media_type != '') {
                    $data[] = $value->l4aaua_media_type;
                    $advanceEarnedPoint += (isset($value->l4aaua_earned_points) && $value->l4aaua_earned_points != '') ? $value->l4aaua_earned_points : 0;
                    if ($value->l4aaua_media_type == 1) {
                        $advanceTotalPoints += Config::get('constant.USER_L4_VIDEO_POINTS');
                    } else if ($value->l4aaua_media_type == 2) {
                        $advanceTotalPoints += Config::get('constant.USER_L4_DOCUMENT_POINTS');
                    } else if ($value->l4aaua_media_type == 3) {
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

    public function getParentListByTeenagerId($teenId,$type) {
        $result = DB::table(config::get('databaseconstants.TBL_PARENT_TEEN_PAIR') . " AS parent_teen ")
                ->join(config::get('databaseconstants.TBL_PARENTS') . " AS parent ", 'parent.id', '=', 'parent_teen.ptp_parent_id')
                ->select(DB::raw('parent_teen.*,parent.p_first_name,parent.p_last_name,parent.p_photo'))
                ->where('parent_teen.ptp_teenager', $teenId)
                ->where('parent_teen.ptp_is_verified', 1)
                ->where('parent.p_user_type', $type)
                ->where('parent_teen.deleted', 1)
                ->get();

        return $result;
    }

    public function getAllUserDeatilForTeenager() {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION'))
                    ->selectRaw('*')
                    ->where('tn_order_status','=','Success')
                    ->where('tn_user_type',1)
                    ->where('tn_coins_expired',0)
                    ->orderBy('created_at','desc')
                    ->get();
        return $result;
    }

    public function getAllUserDeatilForParent() {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION'))
                    ->selectRaw('*')
                    ->where('tn_order_status','=','Success')
                    ->where('tn_user_type',2)
                    ->where('tn_coins_expired',0)
                    ->orderBy('created_at','asc')
                    ->get();
        return $result;
    }

    public function getAllUserDeatilForSponsor() {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION'))
                    ->selectRaw('*')
                    ->where('tn_order_status','=','Success')
                    ->where('tn_user_type',4)
                    ->where('tn_coins_expired',0)
                    ->orderBy('created_at','desc')
                    ->get();
        return $result;
    }

    public function getTeenagerTotalBoosterPoints($teenId) {
        $boosterPoints = DB::select(DB::raw("select SUM(tlb_points) as points from " . config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS') . " where tlb_teenager=" . $teenId), array());
        if(count($boosterPoints) > 0) {
            return $boosterPoints[0]->points;
        } else {
            return 0;
        }
    }

    public function getTeenagerTotalBoosterPointsForLevel1($teenId) {
        $boosterPoints = DB::select(DB::raw("select tlb_points as points from " . config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS') . " where tlb_teenager=" . $teenId . " and tlb_level=" . 1), array());
        if(count($boosterPoints) > 0) {
            return $boosterPoints[0]->points;
        } else {
            return 0;
        }
    }

    public function getTeenagerTotalBoosterPointsForLevel2($teenId) {
        $boosterPoints = DB::select(DB::raw("select tlb_points as points from " . config::get('databaseconstants.TBL_TEENAGER_LEVEL_BOOSTERS') . " where tlb_teenager=" . $teenId . " and tlb_level=" . 2), array());
        if(count($boosterPoints) > 0) {
            return $boosterPoints[0]->points;
        } else {
            return 0;
        }
    }

    public function updateTeenagerRollNumber($teenId,$rollno) {
        $return = $this->model->where('id',$teenId )->update(['t_rollnum' => $rollno]);
        return $return;
    }

    public function getAllUserDeatilForTeenagerByUserId($id,$type) {
        $result = DB::table(config::get('databaseconstants.TBL_TEENAGER_TRANSACTION'))
                    ->selectRaw('*')
                    ->where('tn_userid',$id)
                    ->where('tn_order_status','=','Success')
                    ->where('tn_user_type',$type)
                    ->where('tn_coins_expired',0)
                    ->orderBy('created_at','desc')
                    ->first();
        return $result;
    }
}