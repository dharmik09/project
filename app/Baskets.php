<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Professions;
use Config;

class Baskets extends Model
{
    protected $table = 'pro_b_baskets';
    protected $guarded = [];

    public function getActiveBaskets()
    {
        $result = Baskets::select('*')
                        ->where('deleted' ,'1')
                        ->get();
        return $result;
    }

    public function getActiveBasketsOrderByName()
    {
        $result = Baskets::select('*')
                        ->orderBy('b_name')
                        ->where('deleted' ,'1')
                        ->get();
        return $result;
    }


    public function profession(){
        return $this->hasMany(Professions::class, 'pf_basket');
    }

    public function getBasketsAndProfessionWithAttemptedProfessionByProfessionId($professionId, $userId, $countryId){
        $this->professionId = $professionId;
        $this->userId = $userId;
        $this->countryId = $countryId;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionHeaders' => function ($query) {
                        $query->where('country_id',$this->countryId);
                    }])
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                    ->where('id',$this->professionId);
                }])
                ->whereHas('profession', function ($query) {
                    $query->where('id',$this->professionId)
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                })
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }

    public function getBasketsAndProfessionWithAttemptedProfessionByBasketId($basketId, $userId, $countryId){
        $this->basketId = $basketId;
        $this->userId = $userId;
        $this->countryId = $countryId;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionHeaders' => function ($query) {
                        $query->where('country_id',$this->countryId);
                    }])
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                }])
                ->where('id',$this->basketId)
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }

    public function getAllBasketsAndProfessionWithAttemptedProfession($userId, $countryId){
        $this->userId = $userId;
        $this->countryId = $countryId;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionAttempted' => function ($query) {
                        $query->where('tpa_teenager', $this->userId);
                    }])
                    ->with(['professionHeaders' => function ($query) {
                        $query->where('country_id',$this->countryId);
                    }])
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                }])
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }

    public function getBasketsAndProfessionWithAttemptedProfessionByUserId($userId, $countryId = ''){
        $this->userId = $userId;
        //$this->countryId = (isset($countryId) && !empty($countryId)) ? $countryId : '';
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionAttempted' => function ($query) {
                        $query->where('tpa_teenager', $this->userId);
                    }])
                    ->with(['starRatedProfession' => function ($query) {
                        $query->where('srp_teenager_id', $this->userId);
                    }])
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                }])
                ->whereHas('profession', function ($query) {
                    $query->whereHas('starRatedProfession', function ($query){
                        $query->where('srp_teenager_id', $this->userId);
                    })
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                })
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }


    public function getBasketsAndProfessionWithAttemptedProfessionByBasketIdForUser($basketId, $userId, $countryId, $searchText = ''){
        $this->basketId = $basketId;
        $this->userId = $userId;
        $this->countryId = $countryId;
        $this->searchText = ($searchText != '') ? $searchText : '';
        // $return = $this->select('*')
        //         ->with(['profession' => function ($query) {
        //             $query->with(['professionHeaders' => function ($query) {
        //                 $query->where('country_id',$this->countryId);
        //             }]);
        //             if (isset($this->searchText) && !empty($this->searchText)) {
        //                 $query->where('pf_name', 'like', '%'.$this->searchText.'%');
        //             }
        //             $query->with('starRatedProfession')
        //             ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
        //         }])
        //         ->whereHas('profession', function ($query) {
        //             if (isset($this->searchText) && !empty($this->searchText)) {
        //                 $query->where('pf_name', 'like', '%'.$this->searchText.'%');
        //             }
        //             $query->whereHas('starRatedProfession')
        //             ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
        //         })
        //         ->where('id', $this->basketId)
        //         ->where('deleted' ,'1')
        //         ->get();
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query
                    ->whereHas('starRatedProfession')
                    ->with(['professionHeaders' => function ($query) {
                        $query->where('country_id',$this->countryId);
                    }])
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                }])
                ->whereHas('profession', function ($query) {
                    $query->whereHas('starRatedProfession')
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                })
                ->where('deleted' ,'1')
                ->where('id', $this->basketId)
                ->get();
        return $return;
    }

    public function getBasketsAndProfessionWithAttemptedProfessionByProfessionIdForUser($professionId, $userId, $countryId, $searchText = ''){
        $this->professionId = (isset($professionId) && !empty($professionId)) ? $professionId : '';
        $this->userId = $userId;
        $this->countryId = $countryId;
        $this->searchText = ($searchText != '') ? $searchText : '';
        $qry = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionAttempted' => function ($query) {
                        $query->where('tpa_teenager', $this->userId);
                    }])
                    ->with(['professionHeaders' => function ($query) {
                        $query->where('country_id',$this->countryId);
                    }]);
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                    $query->with('starRatedProfession')
                    ->where('id', $this->professionId)
                    ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                }])
                ->whereHas('profession', function ($query) {
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                    $query->whereHas('starRatedProfession')
                    ->where('id',$this->professionId)
                    ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                });
                
                
        $return = $qry->where('deleted', '1')->get();
        return $return;
    }

    public function getProfessionBasketsByTagForUser($tagId, $userId, $countryId, $searchText = ''){
        $this->tagId = $tagId;
        $this->userId = $userId;
        $this->countryId = $countryId;
        $this->searchText = ($searchText != '') ? $searchText : '';  
        $qry = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionAttempted' => function ($query) {
                        $query->where('tpa_teenager', $this->userId);
                    }])
                    ->with(['professionHeaders' => function ($query) {
                        $query->where('country_id',$this->countryId);
                    }])
                    ->whereHas('professionTags', function ($query) {
                        $query->where('tag_id', $this->tagId)
                        ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                    });
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                    $query->with('starRatedProfession')
                    ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                }])
                ->whereHas('profession', function ($query) {
                    $query->whereHas('starRatedProfession')
                    ->whereHas('professionTags', function ($query) {
                        $query->where('tag_id', $this->tagId)
                        ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                    });
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                    $query->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                });
                
                
        $return = $qry->where('deleted', '1')->get();
        return $return;
    }

    public function getProfessionBasketsBySubjectForUser($subjectId, $userId, $countryId, $searchText = ''){
        $this->subjectId = (isset($subjectId) && $subjectId != "") ? $subjectId : '';
        $this->userId = $userId;
        $this->countryId = $countryId;
        $this->searchText = ($searchText != '') ? $searchText : '';
        $qry = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionAttempted' => function ($query) {
                        $query->where('tpa_teenager', $this->userId);
                    }])
                    ->with(['professionHeaders' => function ($query) {
                        $query->where('country_id',$this->countryId);
                    }])
                    ->whereHas('professionSubject', function ($query) {
                        $query->where('subject_id', $this->subjectId)
                        ->where('deleted', '1');
                    });
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                    $query->with('starRatedProfession')
                    ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                }])
                ->whereHas('profession', function ($query) {
                    $query->whereHas('starRatedProfession')
                    ->whereHas('professionSubject', function ($query) {
                        $query->where('subject_id', $this->subjectId)
                        ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                    });
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                    $query->where('deleted', '1');
                });
        $return = $qry->where('deleted', '1')->get();
        return $return;
    }

    public function getProfessionBasketsByInterestDetailsForUser($interestSlug, $userId, $countryId, $searchText = '')
    {
        $this->interestSlug = '';
        if ($interestSlug != '' && strpos($interestSlug, 'it_') !== false) {
            $subSlug = explode('it_', $interestSlug);
            $this->interestSlug = $subSlug[1];
        } 
        $this->userId = $userId;
        $this->countryId = $countryId;
        $this->searchText = ($searchText != '') ? $searchText : '';
        $qry = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionAttempted' => function ($query) {
                        $query->where('tpa_teenager', $this->userId);
                    }])
                    ->with(['professionHeaders' => function ($query) {
                        $query->where('country_id',$this->countryId);
                    }])
                    ->whereHas('professionSubject', function ($query) {
                        $query->whereHas('subject', function ($query) {
                            $query->where('ps_slug', $this->interestSlug);
                        })
                        ->whereIn('parameter_grade', ['M', 'H'])
                        ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                    });
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                    $query->with('starRatedProfession')
                    ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                }])
                ->whereHas('profession', function ($query) {
                    $query->whereHas('starRatedProfession')
                    ->whereHas('professionSubject', function ($query) {
                        $query->whereHas('subject', function ($query) {
                            $query->where('ps_slug', $this->interestSlug);
                        })
                        ->whereIn('parameter_grade', ['M', 'H'])
                        ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                    });
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                    $query->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                });
                
                
        $return = $qry->where('deleted', '1')->get();
        return $return;
    }

    public function getProfessionBasketsByStrengthDetailsForUser($strengthSlug, $userId, $countryId, $searchText = '')
    {
        $this->strengthSlug = $strengthSlug;
        $this->userId = $userId;
        $this->countryId = $countryId;
        $this->searchText = ($searchText != '') ? $searchText : '';
        $qry = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionHeaders' => function ($query) {
                        $query->where('country_id',$this->countryId);
                    }]) 
                    ->whereHas('careerMapping', function ($query) {
                            $query->whereIn($this->strengthSlug, ['M', 'H']);
                        });
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                    $query->whereHas('starRatedProfession')
                    ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                }])
                ->whereHas('profession', function ($query) {
                    $query->whereHas('starRatedProfession')
                    ->whereHas('careerMapping', function ($query) {
                        $query->whereIn($this->strengthSlug, ['M', 'H']);
                    });
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                    $query->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                });
                
                
        $return = $qry->where('deleted', '1')->get();
        return $return;
    }

    public function getStarredBasketsAndProfessionByUserId($userId, $countryId, $searchText = ''){
        $this->userId = $userId;
        $this->searchText = (isset($searchText) && $searchText != '') ? $searchText : '';
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query
                    ->with(['professionHeaders' => function ($query) {
                        $query->where('country_id',$this->countryId);
                    }]) 
                    ->whereHas('starRatedProfession')
                    ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                }])
                ->whereHas('profession', function ($query) {
                    $query->whereHas('starRatedProfession')
                    ->where('deleted', Config::get('constant.ACTIVE_FLAG'));
                    if (isset($this->searchText) && !empty($this->searchText)) {
                        $query->where('pf_name', 'like', '%'.$this->searchText.'%');
                    }
                })
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }

    public function getBasketsAndProfessionWithAttemptedProfessionByUserIdAndSearchValue($userId, $searchText){
        $this->userId = $userId;
        $this->searchText = $searchText;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->with(['professionAttempted' => function ($query) {
                        $query->where('tpa_teenager', $this->userId);
                    }])
                    ->where('pf_name', 'like', '%'.$this->searchText.'%')
                    ->with('starRatedProfession')
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                }])
                ->whereHas('profession', function ($query) {
                    $query->where('pf_name', 'like', '%'.$this->searchText.'%')
                    ->whereHas('starRatedProfession')
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                })
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }

    public function getBasketsAndProfessionBySearchValue($searchText){
        $this->value = $searchText;
        $return = $this->with(['profession' => function ($query) {
                        $query->where('pf_name', 'like', '%'.$this->value.'%')
                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                    }])
                    ->whereHas('profession', function ($query) {
                        $query->where('pf_name', 'like', '%'.$this->value.'%')
                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                    })
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                    ->get();
        return $return;
    }

    public function getBasketsAndProfessionByBaketIdAndCountryId($basketId,$countryId){
        $this->countryId = $countryId;
        $return = $this->with(['profession' => function ($query) {
                            $query->with(['professionHeaders' => function ($query) {
                                $query->where('country_id',$this->countryId);
                            }])->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                        }])
                        ->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                        ->find($basketId);
        return $return;
    }

    public function getBasketsAndProfessionByProfessionId($professionId, $userId, $countryId){
        $this->professionId = $professionId;
        $this->userId = $userId;
        $this->countryId = $countryId;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->where('deleted' ,config::get('constant.ACTIVE_FLAG'))
                    ->where('id',$this->professionId);
                }])
                ->whereHas('profession', function ($query) {
                    $query->where('id',$this->professionId)
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                })
                ->where('deleted' ,'1')
                ->first();
        return $return;
    }

    public function getBasketsAndStarRatedProfessionByUserIdAndSearchValue($userId, $searchText){
        $this->userId = $userId;
        $this->searchText = $searchText;
        $return = $this->select('*')
                ->with(['profession' => function ($query) {
                    $query->where('pf_name', 'like', '%'.$this->searchText.'%')
                    ->whereHas('starRatedProfession')
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                }])
                ->whereHas('profession', function ($query) {
                    $query->where('pf_name', 'like', '%'.$this->searchText.'%')
                    ->whereHas('starRatedProfession')
                    ->where('deleted' ,config::get('constant.ACTIVE_FLAG'));
                })
                ->where('deleted' ,'1')
                ->get();
        return $return;
    }
}
