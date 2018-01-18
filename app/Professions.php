<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use config;

class Professions extends Model {

    protected $table = 'pro_pf_profession';
    protected $guarded = [];

    public function getActiveProfessions() {
        $result = $this->select('*')
                ->where('deleted', '1')
                ->get();
        return $result;
    }

    public function getProfessionByName($name) {
        $result = $this->select('*')
                ->where('deleted', '1')
                ->where('pf_name', $name)
                ->first();
        return $result;
    }
    
    public function getProfessionBySlug($slug) {
        $result = $this->select('*')
                ->where('deleted', '1')
                ->where('pf_slug', $slug)
                ->first();
        return $result;
    }    
    
    public function getProfessionBySlugWithHeadersAndCertificatesAndTags($slug,$countryId) {
        $this->country_id = $countryId;
        $result = $this->select('*')
                // ->with('professionHeaders')
                ->with(['professionHeaders' => function ($query) {
                            $query->where('country_id',$this->country_id);
                        }])
                ->with('certificates')
                ->with('tags')
                ->where('deleted', '1')
                ->where('pf_slug', $slug)
                ->first();
        return $result;
    }

    public function professionHeaders(){
        return $this->hasMany(ProfessionHeaders::class, 'pfic_profession');
    }

    public function certificates(){
        return $this->hasMany(ProfessionWiseCertification::class, 'profession_id');
    }

    public function tags(){
        return $this->hasMany(ProfessionWiseTag::class, 'profession_id');
    }

    public function getProfessionDetail($professionId) {
        $result = $this->select('*')
                ->where('id', $professionId)
                ->where('deleted', '1')
                ->first();
        return $result;
    }

    public function getTemlpateAnswerType($templateId) {
        $result = DB::table(config::get('databaseconstants.TBL_GAMIFICATION_TEMPLATE'))->where('id', $templateId)->pluck('gt_temlpate_answer_type');
        return $result[0];
    }

    public function getProfessionsByBasketId($basketid) {
        $professions = DB::table(config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession")
                ->join(config::get('databaseconstants.TBL_BASKETS') . " AS basket", 'profession.pf_basket', '=', 'basket.id')
                ->select('profession.*', 'basket.b_name')
                ->where('profession.pf_basket', $basketid)
                ->where('profession.deleted', '1')
                ->get();
        return $professions;
    }

    public function getattepmtedQuestionOfProfession($teenagerId, $professionId) {
        $totalQuestion = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_act")
                ->join(config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS l4_ans", 'l4_act.id', '=', 'l4_ans.activity_id')
                ->select('l4_act.id', 'l4_ans.earned_points')
                ->where('l4_ans.earned_points', '>', 0)
                ->where('l4_ans.teenager_id', $teenagerId)
                ->where('l4_act.profession_id', $professionId)
                ->get();
        return $totalQuestion;
    }

    public function getLevel4AllScore($professionId) {
        $totalBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_act")
                ->join(config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS l4_ans", 'l4_act.id', '=', 'l4_ans.activity_id')
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS l4_teen", 'l4_teen.id', '=', 'l4_ans.teenager_id')
                ->select(DB::raw('l4_ans.earned_points AS total_points'), 'l4_ans.teenager_id')
                ->where('l4_act.profession_id', $professionId)
                ->where('l4_act.deleted', 1)
                ->where('l4_teen.deleted', 1)
                ->where('l4_ans.answer_id','!=',0)
                ->groupBy('l4_ans.teenager_id')
                ->groupBy('l4_ans.activity_id')
                ->orderBy('total_points', 'desc')
                ->get();
        $totalBasicPoints3 = $totalBasicPoints4 = $totalBasicPoints2 = [];
        if (isset($totalBasicPoints) && !empty($totalBasicPoints)) {
            foreach ($totalBasicPoints as $keyPoint => $valuePoint) {
                $totalBasicPoints2[$valuePoint->teenager_id][] = $valuePoint->total_points;
            }
            foreach ($totalBasicPoints2 as $k => $p) {
                $totalBasicPoints3['total_points'] = array_sum($p);
                $totalBasicPoints3['teenager_id'] = $k;
                $totalBasicPoints4[] = $totalBasicPoints3;
            }
        }
        $data['teen_points'] = [];
        if (isset($totalBasicPoints4) && !empty($totalBasicPoints4)) {
            foreach ($totalBasicPoints4 as $key => $point) {
                $p = $key + 1;
                $teenagerData['teenager_id'] = $point['teenager_id'];
                $teenagerData['total_points'] = $point['total_points'];
                $data['teenager_id'][$p] = $point['teenager_id'];
                $data['total_points'][$p] = $point['total_points'];
                $data['teen_points'][$point['teenager_id']] = $point['total_points'];
            }
            arsort($data['teen_points']);
        } else {
            $data = array();
        }

        $totalIntermediatePoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_I_act")
                ->join(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER') . " AS l4_I_ans", 'l4_I_act.id', '=', 'l4_I_ans.l4iaua_activity_id')
                ->select(DB::raw('l4_I_ans.l4iaua_earned_point AS total_points'), 'l4_I_ans.l4iaua_teenager', 'l4_I_ans.id', 'l4_I_ans.l4iaua_profession_id', 'l4_I_ans.l4iaua_template_id')
                ->where('l4_I_act.l4ia_profession_id', $professionId)
                ->groupBy('l4_I_ans.l4iaua_teenager')
                ->groupBy('l4_I_ans.l4iaua_activity_id')
                ->orderBy('total_points', 'desc')
                ->get();

        if (isset($totalIntermediatePoints) && !empty($totalIntermediatePoints)) {
            foreach ($totalIntermediatePoints as $interM) {
                $q = [];
                $q['total_points'] = $interM->total_points;
                $total_points = $interM->total_points;
                $intermediateDetailData[$interM->l4iaua_teenager][$interM->l4iaua_profession_id][$interM->l4iaua_template_id][] = $total_points;
            }
        } else {
            $intermediateDetailData = [];
        }
        if (isset($intermediateDetailData) && !empty($intermediateDetailData)) {
            $q = 0;
            foreach ($intermediateDetailData as $keyTeenager => $pPoint) {
                $q++;
                $templatePoints = 0;
                if (isset($pPoint[$professionId]) && !empty($pPoint[$professionId])) {
                    foreach ($pPoint[$professionId] as $pKey => $fPoints) {
                        $templatePoints = $templatePoints + array_sum($fPoints);
                    }
                }
                $intermediateMediumDetail['teenager_id'][$q] = $keyTeenager;
                $intermediateMediumDetail['total_points'][$q] = $templatePoints;
            }
        } else {
            $intermediateMediumDetail = [];
        }

        $totalAdvancePoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA') . " AS l4_A_ans")
                ->select(DB::raw('sum(l4_A_ans.l4aaua_earned_points) AS total_points'), 'l4_A_ans.l4aaua_teenager')
                ->where('l4_A_ans.l4aaua_profession_id', $professionId)
                ->groupBy('l4_A_ans.l4aaua_teenager')
                ->groupBy('l4_A_ans.l4aaua_profession_id')
                ->orderBy('total_points', 'desc')
                ->get();
        $advancePoint = [];
        if (isset($totalAdvancePoints) && !empty($totalAdvancePoints)) {
            foreach ($totalAdvancePoints as $aKey => $aValue) {
                $advancePoint[$aValue->l4aaua_teenager] = $aValue->total_points;
            }
        }
        if (isset($data) && !empty($data)) {
            $basicTeenager = (isset($data['teenager_id']) && !empty($data['teenager_id'])) ? array_flip($data['teenager_id']) : [];
            $basicTotalPoints = (isset($data['total_points']) && !empty($data['total_points'])) ? $data['total_points'] : [];
            $intermediateTeenager = (isset($intermediateMediumDetail['teenager_id']) && !empty($intermediateMediumDetail['teenager_id'])) ? array_flip($intermediateMediumDetail['teenager_id']) : [];
            $intermediateTotalPoints = (isset($intermediateMediumDetail['total_points']) && !empty($intermediateMediumDetail['total_points'])) ? $intermediateMediumDetail['total_points'] : [];
            $arrayTotalPoints = [];
            if (!empty($basicTeenager)) {
                foreach ($basicTeenager as $biTeenager => $biValue) {
                    $i1Points = 0;
                    $i1Points = (isset($intermediateTeenager[$biTeenager]) && isset($intermediateTotalPoints[$intermediateTeenager[$biTeenager]])) ? $intermediateTotalPoints[$intermediateTeenager[$biTeenager]] : 0;
                    $totalBIPoints = $basicTotalPoints[$biValue] + $i1Points;

                    $teenSearchAdvancePoint = (isset($advancePoint[$biTeenager])) ? $advancePoint[$biTeenager] : 0;
                    $totalBIPoints = $teenSearchAdvancePoint + $totalBIPoints;

                    $arrayTotalPoints['teenager_id'][] = $biTeenager;
                    $arrayTotalPoints['total_points'][] = $totalBIPoints;
                }
            } else {
                $arrayTotalPoints = [];
            }
        } else if (isset($data) && !empty($data)) {
            $arrayTotalPoints = $data;
        } else if (isset($intermediateMediumDetail) && !empty($intermediateMediumDetail)) {
            $arrayTotalPoints = $intermediateMediumDetail;
        } else {
            $arrayTotalPoints = [];
        }
        $arrayTotalPoints2 = [];
        if (isset($arrayTotalPoints['teenager_id']) && !empty($arrayTotalPoints['teenager_id'])) {
            foreach ($arrayTotalPoints['teenager_id'] as $arrayKey => $arrayValue) {
                $arrayTotalPoints2[$arrayValue] = (isset($arrayTotalPoints['total_points'][$arrayKey]) ? $arrayTotalPoints['total_points'][$arrayKey] : 0);
            }
            arsort($arrayTotalPoints2);
        }
        $array['level4TotalPoints'] = $arrayTotalPoints2;
        return $array;
    }

    public function getProfessionAllScore($professionId) {
        $totalBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_act")
                ->join(config::get('databaseconstants.TBL_LEVEL4_ANSWERS') . " AS l4_ans", 'l4_act.id', '=', 'l4_ans.activity_id')
                ->select(DB::raw('l4_ans.earned_points AS total_points'), 'l4_ans.teenager_id')
                ->where('l4_act.profession_id', $professionId)
                ->groupBy('l4_ans.teenager_id')
                ->groupBy('l4_ans.activity_id')
                ->orderBy('total_points', 'desc')
                ->get();
        $totalBasicPoints3 = $totalBasicPoints4 = $totalBasicPoints2 = [];
        if (isset($totalBasicPoints) && !empty($totalBasicPoints)) {
            foreach ($totalBasicPoints as $keyPoint => $valuePoint) {
                $totalBasicPoints2[$valuePoint->teenager_id][] = $valuePoint->total_points;
            }
            foreach ($totalBasicPoints2 as $k => $p) {
                $totalBasicPoints3['total_points'] = array_sum($p);
                $totalBasicPoints3['teenager_id'] = $k;
                $totalBasicPoints4[] = $totalBasicPoints3;
            }
        }
        $data['teen_points'] = [];
        if (isset($totalBasicPoints4) && !empty($totalBasicPoints4)) {
            foreach ($totalBasicPoints4 as $key => $point) {
                $p = $key + 1;
                $teenagerData['teenager_id'] = $point['teenager_id'];
                $teenagerData['total_points'] = $point['total_points'];
                $data['teenager_id'][$p] = $point['teenager_id'];
                $data['total_points'][$p] = $point['total_points'];
                $data['teen_points'][$point['teenager_id']] = $point['total_points'];
            }
            arsort($data['teen_points']);
        } else {
            $data = array();
        }

        $totalIntermediatePoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_I_act")
                ->join(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_ANSWER') . " AS l4_I_ans", 'l4_I_act.id', '=', 'l4_I_ans.l4iaua_activity_id')
                ->select(DB::raw('l4_I_ans.l4iaua_earned_point AS total_points'), 'l4_I_ans.l4iaua_teenager', 'l4_I_ans.id', 'l4_I_ans.l4iaua_profession_id', 'l4_I_ans.l4iaua_template_id')
                ->where('l4_I_act.l4ia_profession_id', $professionId)
                ->groupBy('l4_I_ans.l4iaua_teenager')
                ->groupBy('l4_I_ans.l4iaua_activity_id')
                ->orderBy('total_points', 'desc')
                ->get();

        $totalAdvancePoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_USER_DATA') . " AS l4_A_ans")
                ->select(DB::raw('sum(l4_A_ans.l4aaua_earned_points) AS total_points'), 'l4_A_ans.l4aaua_teenager')
                ->where('l4_A_ans.l4aaua_profession_id', $professionId)
                ->groupBy('l4_A_ans.l4aaua_teenager')
                ->groupBy('l4_A_ans.l4aaua_profession_id')
                ->orderBy('total_points', 'desc')
                ->get();
        $advancePoint = [];
        if (isset($totalAdvancePoints) && !empty($totalAdvancePoints)) {
            foreach ($totalAdvancePoints as $aKey => $aValue) {
                $advancePoint[$aValue->l4aaua_teenager] = $aValue->total_points;
            }
        }

        if (isset($totalIntermediatePoints) && !empty($totalIntermediatePoints)) {
            foreach ($totalIntermediatePoints as $interM) {
                $q = [];
                $q['total_points'] = $interM->total_points;
                $total_points = $interM->total_points;
                $intermediateDetailData[$interM->l4iaua_teenager][$interM->l4iaua_profession_id][$interM->l4iaua_template_id][] = $total_points;
            }
        } else {
            $intermediateDetailData = [];
        }
        if (isset($intermediateDetailData) && !empty($intermediateDetailData)) {
            $q = 0;
            foreach ($intermediateDetailData as $keyTeenager => $pPoint) {
                $q++;
                $templatePoints = 0;
                foreach ($pPoint[$professionId] as $pKey => $fPoints) {
                    $templatePoints = $templatePoints + array_sum($fPoints);
                }
                $intermediateMediumDetail['teenager_id'][$q] = $keyTeenager;
                $intermediateMediumDetail['total_points'][$q] = $templatePoints;
            }
        } else {
            $intermediateMediumDetail = [];
        }

        if ((isset($data) && !empty($data)) && (isset($intermediateMediumDetail) && !empty($intermediateMediumDetail))) {
            $basicTeenager = (isset($data['teenager_id']) && !empty($data['teenager_id'])) ? array_flip($data['teenager_id']) : [];
            $basicTotalPoints = (isset($data['total_points']) && !empty($data['total_points'])) ? $data['total_points'] : [];
            $intermediateTeenager = (isset($intermediateMediumDetail['teenager_id']) && !empty($intermediateMediumDetail['teenager_id'])) ? array_flip($intermediateMediumDetail['teenager_id']) : [];
            $intermediateTotalPoints = (isset($intermediateMediumDetail['total_points']) && !empty($intermediateMediumDetail['total_points'])) ? $intermediateMediumDetail['total_points'] : [];
            $arrayTotalPoints = [];
            if (!empty($basicTeenager) && !empty($intermediateTeenager)) {
                foreach ($basicTeenager as $biTeenager => $biValue) {
                    $i1Points = 0;
                    $i1Points = (isset($intermediateTeenager[$biTeenager]) && isset($intermediateTotalPoints[$intermediateTeenager[$biTeenager]])) ? $intermediateTotalPoints[$intermediateTeenager[$biTeenager]] : 0;
                    $totalBIPoints = $basicTotalPoints[$biValue] + $i1Points;

                    $teenSearchAdvancePoint = (isset($advancePoint[$biTeenager])) ? $advancePoint[$biTeenager] : 0;
                    $totalBIPoints = $teenSearchAdvancePoint + $totalBIPoints;

                    $arrayTotalPoints['teenager_id'][] = $biTeenager;
                    $arrayTotalPoints['total_points'][] = $totalBIPoints;
                }
            } else {
                $arrayTotalPoints = [];
            }
        } else if (isset($data) && !empty($data)) {
            $arrayTotalPoints = $data;
        } else if (isset($intermediateMediumDetail) && !empty($intermediateMediumDetail)) {
            $arrayTotalPoints = $intermediateMediumDetail;
        } else {
            $arrayTotalPoints = [];
        }
        $arrayTotalPoints2 = [];
        if (isset($arrayTotalPoints['teenager_id']) && !empty($arrayTotalPoints['teenager_id'])) {
            foreach ($arrayTotalPoints['teenager_id'] as $arrayKey => $arrayValue) {
                $arrayTotalPoints2[$arrayValue] = (isset($arrayTotalPoints['total_points'][$arrayKey]) ? $arrayTotalPoints['total_points'][$arrayKey] : 0);
            }
            arsort($arrayTotalPoints2);
        }

        $array['basic'] = $data;
        $array['intermediateDetailData'] = $intermediateDetailData;
        $array['intermediate'] = $intermediateMediumDetail;
        $array['totalPoints'] = $arrayTotalPoints;
        $array['level4TotalPoints'] = $arrayTotalPoints2;
        return $array;
    }

    public function getTemplateNo($teenagerId, $professionId) {
        $array = $completed = $result = $attempted = $data = [];

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
        $array['totalIntermediateTemplate'] = count($totalIntermediateTemplate);
        $array['totalCompletedTemplate'] = count($completed);
        $array['totalAttemptedTemplate'] = count($attempted);
        return $array;
    }

    public function getTotalCompetingFromLevel3($professionId) {
        $getTotalCompetingFromLevel3 = DB::table(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " AS tp_att")
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'tp_att.tpa_teenager', '=', 'teen.id')
                ->select(DB::raw('COUNT(DISTINCT(tp_att.tpa_teenager)) as competing'))
                ->where('tp_att.tpa_peofession_id', $professionId)
                ->where('teen.deleted', 1)
                ->get();
        return $getTotalCompetingFromLevel3;
    }

    public function getProfessionLevel4AllTypeTotalPoints($professionId) {
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
        $array = [];
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
        $data = [];
        $data['totalBasic'] = $basicTotalPoints;
        $data['totalIntermediate'] = $total_points_intermediate;
        $data['totalAdvance'] = $totaladvancePoint;
        return $data;
    }

    public function getTotalCompetingOfProfession($professionId) {
        $getTotalCompetingFromLevel3 = DB::table(config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " AS tp_att")
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'tp_att.tpa_teenager', '=', 'teen.id')
                ->select(DB::raw('DISTINCT(tp_att.tpa_teenager) as teenager_id, teen.t_photo, teen.t_name, teen.t_uniqueid,teen.is_search_on, teen.t_phone, teen.t_email'))
                ->where('tp_att.tpa_peofession_id', $professionId)
                ->where('teen.deleted', 1)
                ->get();
        return $getTotalCompetingFromLevel3;
    }

    public function getTeenagerAttemptedProfession($userid) {
        $professionattempt = DB::select(DB::raw("select profession.pf_name,profession.pf_logo,profession.id from " . config::get('databaseconstants.TBL_TEENAGER_PROFESSION_ATTEMPTED') . " AS attempted
                                                      join " . config::get('databaseconstants.TBL_PROFESSIONS') . " AS profession on attempted.tpa_peofession_id = profession.id
                                                      where attempted.tpa_teenager=" . $userid));
        return $professionattempt;
    }

    public function getProfessionIdByName($professionName) {
        $result = Professions::select('id')
                ->where('pf_name', $professionName)
                ->where('deleted', '1')
                ->first();
        return $result;
    }


    public function getLevel4AllScoreForParent($professionId) {
        $totalBasicPoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ACTIVITY') . " AS l4_act")
                ->join(config::get('databaseconstants.TBL_LEVEL4_ANSWERS_PARENT') . " AS l4_ans", 'l4_act.id', '=', 'l4_ans.lbac_activity_id')
                ->join(config::get('databaseconstants.TBL_PARENTS') . " AS parent", 'parent.id', '=', 'l4_ans.lbac_parent_id')
                ->select(DB::raw('l4_ans.lbac_earned_points AS total_points'), 'l4_ans.lbac_parent_id')
                ->where('l4_act.profession_id', $professionId)
                ->where('l4_act.deleted', 1)
                ->where('parent.deleted', 1)
                ->where('l4_ans.lbac_answer_id','!=',0)
                ->groupBy('l4_ans.lbac_parent_id')
                ->groupBy('l4_ans.lbac_activity_id')
                ->orderBy('total_points', 'desc')
                ->get();
        $totalBasicPoints3 = $totalBasicPoints4 = $totalBasicPoints2 = [];
        if (isset($totalBasicPoints) && !empty($totalBasicPoints)) {
            foreach ($totalBasicPoints as $keyPoint => $valuePoint) {
                $totalBasicPoints2[$valuePoint->lbac_parent_id][] = $valuePoint->total_points;
            }
            foreach ($totalBasicPoints2 as $k => $p) {
                $totalBasicPoints3['total_points'] = array_sum($p);
                $totalBasicPoints3['lbac_parent_id'] = $k;
                $totalBasicPoints4[] = $totalBasicPoints3;
            }
        }
        $data['teen_points'] = [];
        if (isset($totalBasicPoints4) && !empty($totalBasicPoints4)) {
            foreach ($totalBasicPoints4 as $key => $point) {
                $p = $key + 1;
                $teenagerData['lbac_parent_id'] = $point['lbac_parent_id'];
                $teenagerData['total_points'] = $point['total_points'];
                $data['lbac_parent_id'][$p] = $point['lbac_parent_id'];
                $data['total_points'][$p] = $point['total_points'];
                $data['teen_points'][$point['lbac_parent_id']] = $point['total_points'];
            }
            arsort($data['teen_points']);
        } else {
            $data = array();
        }

        $totalIntermediatePoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS l4_I_act")
                ->join(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT') . " AS l4_I_ans", 'l4_I_act.id', '=', 'l4_I_ans.l4iapa_activity_id')
                ->select(DB::raw('l4_I_ans.l4iapa_earned_point AS total_points'), 'l4_I_ans.l4iapa_parent_id', 'l4_I_ans.id', 'l4_I_ans.l4iapa_profession_id', 'l4_I_ans.l4iapa_template_id')
                ->where('l4_I_act.l4ia_profession_id', $professionId)
                ->groupBy('l4_I_ans.l4iapa_parent_id')
                ->groupBy('l4_I_ans.l4iapa_activity_id')
                ->orderBy('total_points', 'desc')
                ->get();

        if (isset($totalIntermediatePoints) && !empty($totalIntermediatePoints)) {
            foreach ($totalIntermediatePoints as $interM) {
                $q = [];
                $q['total_points'] = $interM->total_points;
                $total_points = $interM->total_points;
                $intermediateDetailData[$interM->l4iapa_parent_id][$interM->l4iapa_profession_id][$interM->l4iapa_template_id][] = $total_points;
            }
        } else {
            $intermediateDetailData = [];
        }
        if (isset($intermediateDetailData) && !empty($intermediateDetailData)) {
            $q = 0;
            foreach ($intermediateDetailData as $keyTeenager => $pPoint) {
                $q++;
                $templatePoints = 0;
                if (isset($pPoint[$professionId]) && !empty($pPoint[$professionId])) {
                    foreach ($pPoint[$professionId] as $pKey => $fPoints) {
                        $templatePoints = $templatePoints + array_sum($fPoints);
                    }
                }
                $intermediateMediumDetail['lbac_parent_id'][$q] = $keyTeenager;
                $intermediateMediumDetail['total_points'][$q] = $templatePoints;
            }
        } else {
            $intermediateMediumDetail = [];
        }

        $totalAdvancePoints = DB::table(config::get('databaseconstants.TBL_LEVEL4_ADVANCE_ACTIVITY_PARENT_DATA') . " AS l4_A_ans")
                ->select(DB::raw('sum(l4_A_ans.l4aapa_earned_points) AS total_points'), 'l4_A_ans.l4aapa_parent_id')
                ->where('l4_A_ans.l4aapa_profession_id', $professionId)
                ->groupBy('l4_A_ans.l4aapa_parent_id')
                ->groupBy('l4_A_ans.l4aapa_profession_id')
                ->orderBy('total_points', 'desc')
                ->get();
        $advancePoint = [];
        if (isset($totalAdvancePoints) && !empty($totalAdvancePoints)) {
            foreach ($totalAdvancePoints as $aKey => $aValue) {
                $advancePoint[$aValue->l4aapa_parent_id] = $aValue->total_points;
            }
        }
        if (isset($data) && !empty($data)) {
            $basicTeenager = (isset($data['lbac_parent_id']) && !empty($data['lbac_parent_id'])) ? array_flip($data['lbac_parent_id']) : [];
            $basicTotalPoints = (isset($data['total_points']) && !empty($data['total_points'])) ? $data['total_points'] : [];
            $intermediateTeenager = (isset($intermediateMediumDetail['lbac_parent_id']) && !empty($intermediateMediumDetail['lbac_parent_id'])) ? array_flip($intermediateMediumDetail['lbac_parent_id']) : [];
            $intermediateTotalPoints = (isset($intermediateMediumDetail['total_points']) && !empty($intermediateMediumDetail['total_points'])) ? $intermediateMediumDetail['total_points'] : [];
            $arrayTotalPoints = [];
            if (!empty($basicTeenager)) {
                foreach ($basicTeenager as $biTeenager => $biValue) {
                    $i1Points = 0;
                    $i1Points = (isset($intermediateTeenager[$biTeenager]) && isset($intermediateTotalPoints[$intermediateTeenager[$biTeenager]])) ? $intermediateTotalPoints[$intermediateTeenager[$biTeenager]] : 0;
                    $totalBIPoints = $basicTotalPoints[$biValue] + $i1Points;

                    $teenSearchAdvancePoint = (isset($advancePoint[$biTeenager])) ? $advancePoint[$biTeenager] : 0;
                    $totalBIPoints = $teenSearchAdvancePoint + $totalBIPoints;

                    $arrayTotalPoints['lbac_parent_id'][] = $biTeenager;
                    $arrayTotalPoints['total_points'][] = $totalBIPoints;
                }
            } else {
                $arrayTotalPoints = [];
            }
        } else if (isset($data) && !empty($data)) {
            $arrayTotalPoints = $data;
        } else if (isset($intermediateMediumDetail) && !empty($intermediateMediumDetail)) {
            $arrayTotalPoints = $intermediateMediumDetail;
        } else {
            $arrayTotalPoints = [];
        }
        $arrayTotalPoints2 = [];
        if (isset($arrayTotalPoints['lbac_parent_id']) && !empty($arrayTotalPoints['lbac_parent_id'])) {
            foreach ($arrayTotalPoints['lbac_parent_id'] as $arrayKey => $arrayValue) {
                $arrayTotalPoints2[$arrayValue] = (isset($arrayTotalPoints['total_points'][$arrayKey]) ? $arrayTotalPoints['total_points'][$arrayKey] : 0);
            }
            arsort($arrayTotalPoints2);
        }
        $array['level4TotalPoints'] = $arrayTotalPoints2;

        return $array;
    }

    public function getTotalCompetingOfProfessionForParent($professionId,$parentId) {
        $getTotalCompetingFromLevel3 = DB::table(config::get('databaseconstants.TBL_TEENAGER_PARENT_CHALLENGE') . " AS tp_cha")
                ->join(config::get('databaseconstants.TBL_TEENAGERS') . " AS teen", 'tp_cha.tpc_teenager_id', '=', 'teen.id')
                ->select(DB::raw('DISTINCT(tp_cha.tpc_teenager_id) as teenager_id, teen.t_photo, teen.t_name, teen.t_uniqueid,teen.is_search_on, teen.t_phone, teen.t_email'))
                ->where('tp_cha.tpc_profession_id', $professionId)
                ->where('tp_cha.tpc_parent_id', $parentId)
                ->where('teen.deleted', 1)
                ->get();

        return $getTotalCompetingFromLevel3;
    }

    public function getTemplateNoForParent($parentId, $professionId) {
        $array = $completed = $result = $attempted = $data = [];

        $totalIntermediateTemplate = DB::table(config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY'))
                ->distinct()
                ->where(['deleted' => 1, 'l4ia_profession_id' => $professionId])
                ->selectRaw('l4ia_question_template')
                ->get();
        if (isset($totalIntermediateTemplate) && !empty($totalIntermediateTemplate)) {
            foreach ($totalIntermediateTemplate as $templateId) {
                $result[$templateId->l4ia_question_template] = DB::select(DB::raw("select (SELECT count(*) FROM " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " where deleted=1 and l4ia_profession_id = $professionId and l4ia_question_template = $templateId->l4ia_question_template) as 'NoOfTotalQuestions', (select count(DISTINCT(L4_I_ANS.l4iapa_activity_id)) from " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY') . " AS L4_I_AC join " . config::get('databaseconstants.TBL_LEVEL4_INTERMEDIATE_ACTIVITY_PARENT') . " AS L4_I_ANS on L4_I_AC.id = L4_I_ANS.l4iapa_activity_id  where L4_I_ANS.l4iapa_parent_id=" . $parentId . " AND L4_I_AC.l4ia_profession_id = " . $professionId . " AND L4_I_AC.l4ia_question_template=" . $templateId->l4ia_question_template . ") as 'NoOfAttemptedQuestions' "), array());
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
        $array['totalIntermediateTemplate'] = count($totalIntermediateTemplate);
        $array['totalCompletedTemplate'] = count($completed);
        $array['totalAttemptedTemplate'] = count($attempted);
        return $array;
    }

    public function getCompetingUserListForTeenager($professionId,$teenId) {
        $getTotalCompeting = DB::table(config::get('databaseconstants.TBL_TEENAGER_PARENT_CHALLENGE') . " AS tp_cha")
                ->join(config::get('databaseconstants.TBL_PARENTS') . " AS parent", 'tp_cha.tpc_parent_id', '=', 'parent.id')
                ->select(DB::raw('DISTINCT(tp_cha.tpc_parent_id) as parent_id, parent.p_photo, parent.p_first_name'))
                ->where('tp_cha.tpc_profession_id', $professionId)
                ->where('tp_cha.tpc_teenager_id', $teenId)
                ->where('parent.deleted', 1)
                ->get();

        return $getTotalCompeting;
    }

}
