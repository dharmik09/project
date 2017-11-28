<?php
    if (isset($response['level4Booster']) && !empty($response['level4Booster'])) {
        $level1Lable = (!isset($response['level4ParentBooster']) && empty($response['level4ParentBooster'])) ? trans('labels.level1InLevel4'): trans('labels.Parentlevel1InLevel4');
        $level2Lable = (!isset($response['level4ParentBooster']) && empty($response['level4ParentBooster'])) ? trans('labels.level2InLevel4'): trans('labels.Parentlevel2InLevel4');
        $level3Lable = (!isset($response['level4ParentBooster']) && empty($response['level4ParentBooster'])) ? trans('labels.level3InLevel4'): trans('labels.Parentlevel3InLevel4');
        $level4Lable = (!isset($response['level4ParentBooster']) && empty($response['level4ParentBooster'])) ? trans('labels.level4InLevel4'): trans('labels.Parentlevel4InLevel4');
        if(isset($response['profession_id']) && $response['profession_id'] > 0){
            $getCompetingUserList = Helpers::getCompetingUserList($response['profession_id']);
            $displayPopupCompeting = true;
        }else{
            $getCompetingUserList = [];
            $displayPopupCompeting = false;
        }
        if(isset($response['teen_id']) && $response['teen_id'] > 0){
            if(!empty($getCompetingUserList) && isset($getCompetingUserList[$response['teen_id']])){
                  $teenRank = (isset($getCompetingUserList[$response['teen_id']]['rank']))?$getCompetingUserList[$response['teen_id']]['rank'] : 0;
              }else{
                  $teenRank = 0;
              }
        } else {
          if(Auth::teenager()->check() && Auth::teenager()->id() > 0){
              if(!empty($getCompetingUserList) && isset($getCompetingUserList[Auth::teenager()->id()])){
                  $teenRank = (isset($getCompetingUserList[Auth::teenager()->id()]['rank']))?$getCompetingUserList[Auth::teenager()->id()]['rank'] : 0;
              }else{
                  $teenRank = 0;
              }
          }else{
              $teenRank = 0;
          }
        }

        $level1BoosterPointScaleDisplay = (isset($response['level4Booster']['competing']) && $response['level4Booster']['competing'] != '') ? $response['level4Booster']['competing'] : "0";
        $highScoreForLevel4Display = (isset($response['level4Booster']['highestScore']) && $response['level4Booster']['highestScore'] != '') ? $response['level4Booster']['highestScore'] : 0;
        $yourScoreForLevel4Display = (isset($response['level4Booster']['yourScore']) && $response['level4Booster']['yourScore'] != '') ? $response['level4Booster']['yourScore'] : 0;
        $totalPobScore = (isset($response['level4Booster']['totalPobScore']) && $response['level4Booster']['totalPobScore'] != '') ? $response['level4Booster']['totalPobScore'] : 0;

        $level2BoosterPointScaleDisplay = $yourScoreForLevel4Display;
        $level2BoosterPointScaleDisplay2 = $totalPobScore;
        //$level3BoosterPointScaleDisplay2 = (isset($response['level4Booster']['yourRank']) && $response['level4Booster']['yourRank'] != '') ? $response['level4Booster']['yourRank'] : "0";
        $level3BoosterPointScaleDisplay2 = $teenRank;
        $level3BoosterPointScaleDisplay3 = (isset($response['level4Booster']['yourRank']) && $response['level4Booster']['yourRank'] != '') ? $response['level4Booster']['yourRank'] : "0";
        //$level3BoosterPointScaleDisplay = $level3BoosterPointScaleDisplay2." / ".$level1BoosterPointScaleDisplay;
        $level3BoosterPointScaleDisplay = $level3BoosterPointScaleDisplay2;
        $level4BoosterPointScaleDisplay2 = (isset($response['level4Booster']['yourScore']) && $response['level4Booster']['yourScore'] != '') ? $response['level4Booster']['yourScore'] : "0";
        $level4BoosterPointScaleDisplay =  $highScoreForLevel4Display;
        $totalBoosterPointScaleDisplay = (isset($response['level4Booster']['total']) && $response['level4Booster']['total'] != '') ? $response['level4Booster']['total'] : "0";
        $class = "l4_box";
        $competingClass = "compitant_rank";
        $progressShow = false;

    } else {
        $getCompetingUserList = [];
        $level4BoosterPointScaleDisplay2 = '';
        $displayPopupCompeting = false;
        $level1Lable = trans('labels.level1');
        $level2Lable = trans('labels.level2');
        $level3Lable = trans('labels.level3');
        $level4Lable = trans('labels.level4');
        $level1BoosterPointScaleDisplay = (isset($response['boosterPoints']['Level1']) && $response['boosterPoints']['Level1'] != '') ? $response['boosterPoints']['Level1'] : "0";
        $level2BoosterPointScaleDisplay = (isset($response['boosterPoints']['Level2']) && $response['boosterPoints']['Level2'] != '') ? $response['boosterPoints']['Level2'] : "0";
        $level3BoosterPointScaleDisplay = (isset($response['boosterPoints']['Level3']) && $response['boosterPoints']['Level3'] != '') ? $response['boosterPoints']['Level3'] : "0";
        $level4BoosterPointScaleDisplay = (isset($response['boosterPoints']['Level4']) && $response['boosterPoints']['Level4'] != '') ? $response['boosterPoints']['Level4'] : "0";
        $totalBoosterPointScaleDisplay = (isset($response['boosterPoints']['total']) && $response['boosterPoints']['total'] != '') ? $response['boosterPoints']['total'] : "0";
        $class = "";
        $level2BoosterPointScaleDisplay2 = '';
        $competingClass = "";
        $progressShow = true;
    }
    ?>
<div class="clearfix level_indicator <?php echo $class; ?>">
    <div class="level_container_left">
        <div class="user_basic_profile">
            <?php
            $currentRoute = Route::getFacadeRoot()->current()->uri();
            if (isset($response['teen_id']) && $response['teen_id'] != '') {
                $image = Helpers::getTeenagerImageUrl($response['teenDetail']['t_photo'], 'original');
            } else {
                if ($currentRoute == 'teenager/LILProfile/{userid}') {
                    $image = Helpers::getTeenagerImageUrl($response['teenagerData']->t_photo, 'original');
                }elseif ($currentRoute == 'teenager/playLevel1ActivityPart2IconType') {
                    $image = Helpers::getTeenagerImageUrl($response['user_self_image'], 'original');
                }
                else {
                    $image = Helpers::getTeenagerImageUrl(Auth::teenager()->get()->t_photo, 'original');
                }
            }
            ?>
            <img src="{{$image}}" alt="">
        </div>
        <?php
        if (isset($response['timer']) && $response['timer'] != '') {
            $timer = $response['timer'];
            ?>
            <span class="count_down_time cst_fix_cst_fix" id="timer_countdown"><span></span><span class="animation"></span></span>
        <?php } else {
            $timer = 0;
            ?>
            <span class="count_down_time">&nbsp;</span>
        <?php }
        ?>
    </div>
    <div class="level_container">
        <div class="level_container_inner">
            @if(empty($getCompetingUserList))<a href="{{url('teenager/level1ActivityLanding')}}"> @endif
            <div class="container_inner_level1">
                <span class="lev_1">{{$level1Lable}}</span>
                <div class="level-1">
                    @if($displayPopupCompeting)
                    <span class="center_detial {{$competingClass}}" data-toggle="modal" data-target="#rank_list_global"><b class="animation_number level1_point">{{$level1BoosterPointScaleDisplay}}</b>&nbsp;&nbsp;<i class="fa fa-users" aria-hidden="true"></i></span>
                    @else
                    <span class="center_detial level1_point"><b class="animation_number">{{$level1BoosterPointScaleDisplay}}</b></span>
                    @endif
                    <?php $L1Progress = (isset($response['boosterPoints']['Level1Progress']) && $response['boosterPoints']['Level1Progress'] > 0)?$response['boosterPoints']['Level1Progress']:0; ?>
                    @if($progressShow)
                    <span class="booster_bottom">
                        <div class="h_boost level1_progress" style="width:{{$L1Progress}}%;"></div>
                    </span>
                    @endif
                </div>
            </div>
            @if(empty($getCompetingUserList))</a> @endif
        </div>
        <div class="level_container_inner">
            @if(empty($getCompetingUserList))<a href="{{url('teenager/level2Activity')}}"> @endif
            <div class="container_inner_level1">
                <span class="lev_2">{{$level2Lable}}</span>
                <div class="level-2">
                    <span class="center_detial level2_point"><b class="animation_number">
                        <?php
  //                            if($level2BoosterPointScaleDisplay2 != '' && $level2BoosterPointScaleDisplay2 > 0){
  //                                echo "<span>".$level2BoosterPointScaleDisplay." <strong>/</strong></span> <span>".$level2BoosterPointScaleDisplay2."</span>";
  //                            }else{
  //                                echo $level2BoosterPointScaleDisplay;
  //                            }
                            echo $level2BoosterPointScaleDisplay;
                        ?>
                    </b></span>
                    <?php $L2Progress = (isset($response['boosterPoints']['Level2Progress']) && $response['boosterPoints']['Level2Progress'] > 0)?$response['boosterPoints']['Level2Progress']:0; ?>
                    @if($progressShow)
                    <span class="booster_bottom">
                        <div class="h_boost level2_progress" style="width:{{$L2Progress}}%;"></div>
                    </span>
                    @endif
                </div>
            </div>
            @if(empty($getCompetingUserList))</a>@endif
        </div>
        <div class="level_container_inner">
            @if(empty($getCompetingUserList))<a href="{{url('teenager/level3Activity')}}">@endif
            <div class="container_inner_level1">
                <span class="lev_3">{{$level3Lable}}</span>
                <div class="level-3">
                    <span class="center_detial level3_point"><b class="animation_number">{{$level3BoosterPointScaleDisplay}}</b></span>
                    <?php $L3Progress = (isset($response['boosterPoints']['Level3Progress']) && $response['boosterPoints']['Level3Progress'] > 0)?$response['boosterPoints']['Level3Progress']:0; ?>
                    @if($progressShow)
                    <span class="booster_bottom">
                        <div class="h_boost level3_progress" style="width:{{$L3Progress}}%;"></div>
                    </span>
                    @endif
                </div>
            </div>
            @if(empty($getCompetingUserList))</a>@endif
        </div>
        <div class="level_container_inner">
            @if(empty($getCompetingUserList))<a href="{{url('teenager/level4Inclination')}}">@endif
            <div class="container_inner_level1">
                <span class="lev_4">{{$level4Lable}}</span>
                <div class="level-4">
                    <span class="center_detial level4_point"><b class="animation_number">
                        <?php
                            if($level4BoosterPointScaleDisplay2 != ''){
                              //  echo "<span>".$level4BoosterPointScaleDisplay2." <strong>/</strong></span> <span>".$level4BoosterPointScaleDisplay."</span>";
                                echo "<span>".$level4BoosterPointScaleDisplay2." <strong>/</strong></span> <span>".$level2BoosterPointScaleDisplay2."</span>";
                            }else{
                                echo $level4BoosterPointScaleDisplay;
                            }
                        ?>
                    </b></span>
                    <?php $L4Progress = (isset($response['boosterPoints']['Level4Progress']) && $response['boosterPoints']['Level4Progress'] > 0)?$response['boosterPoints']['Level4Progress']:0; ?>
                    @if($progressShow)
                    <span class="booster_bottom">
                        <div class="h_boost level4_progress" style="width:{{$L4Progress}}%;"></div>
                    </span>
                    @endif
                </div>
            </div>
            @if(empty($getCompetingUserList))</a>@endif
        </div>
    </div>
    <div class="level_container_right">
        <div class="user_basic_profile" id="booster_animation">
            <span class="point total"><b class="animation_number">{{$totalBoosterPointScaleDisplay}}</b></span>
            <span class="point_anime_bg">
                <span class="inner_point">
                    <span class="most_inner_point"></span>
                </span>
            </span>
        </div>
    </div>
</div>
@if($displayPopupCompeting)
<div id="rank_list_global" class="modal fade cst_modals" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content rank_list_global">
            <button type="button" class="close close_next" data-dismiss="modal">Close</button>
            <div class="modal-header">
                <strong><span style="font-size: 20px;">Leaderboard</span><br/></strong>
                <span style="font-size:20px;"><strong>@if(isset($response['profession_name']) && $response['profession_name'] != ''){{ucfirst($response['profession_name'])}}@else {{'Competing users'}}@endif</strong></span>
            </div>
            <div class="modal-body">
                <div class="table_container_outer">
                    <table cellspacing="100">
                        <tr>
                            <th class="icon_title">Icon</th>
                            <th>Name</th>
                            <th>Score</th>
                            <th>Rank</th>
                            @if(!isset($response['level4ParentBooster']) && empty($response['level4ParentBooster']))<th>Chat</th>@endif
                            @if(!isset($response['level4ParentBooster']) && empty($response['level4ParentBooster']))
                                @if(Auth::teenager()->get()->is_search_on == 1)
                                    <th class="icon_title_lil">Profile</th>
                                @endif
                            @endif
                        </tr>
                        <?php
                        if(isset($getCompetingUserList) && !empty($getCompetingUserList)){
                            foreach($getCompetingUserList as $competingValue){
                        ?>
                        <tr>
                            @if(!isset($response['level4ParentBooster']) && empty($response['level4ParentBooster']))
                                <td>
                                  <span class="img_contianer_outer">
                                      <a href="{{url('teenager/LILProfile')}}/{{$competingValue['teenager_id']}}" target="_blank"><img src="{{$competingValue['profile_pic']}}"></a>
                                  </span>
                                </td>
                            @else
                                <td>
                                  <span class="img_contianer_outer">
                                      <img src="{{$competingValue['profile_pic']}}">
                                  </span>
                                </td>
                            @endif
                            <td>{{ucfirst($competingValue['name'])}}</td>
                            <td>{{$competingValue['yourScore']}}</td>
                            <td>{{$competingValue['rank']}}</td>
                            @if(!isset($response['level4ParentBooster']) && empty($response['level4ParentBooster']))
                                <td>
                                @if (isset($response['teen_id']) && $response['teen_id'] != '')
                                    @if($response['teenDetail']['id'] == $competingValue['teenager_id'])
                                        -
                                    @else
                                        <a href="javascript:void(0)" onclick="onetoOneChat('{{$competingValue['teenager_unique_id']}}','{{$competingValue['name']}}');"><i class="fa fa-comments-o" aria-hidden="true"></i></a>
                                    @endif
                                @else
                                    @if(Auth::teenager()->get()->id == $competingValue['teenager_id'])
                                    -
                                    @else
                                    <a href="javascript:void(0)" onclick="onetoOneChat('{{$competingValue['teenager_unique_id']}}','{{$competingValue['name']}}');"><i class="fa fa-comments-o" aria-hidden="true"></i></a>
                                    @endif
                                @endif
                                </td>
                            @endif
                            @if(!isset($response['level4ParentBooster']) && empty($response['level4ParentBooster']))
                                @if(Auth::teenager()->get()->is_search_on == 1)
                                <td class="link_to_lil">
                                    @if($competingValue['is_search_on'] == 1)
                                    <a href="{{url('teenager/LILProfile')}}/{{$competingValue['teenager_id']}}" target="_blank"><i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                                    @else
                                    -
                                    @endif
                                </td>
                                @endif
                            @endif
                        </tr>

                        <?php
                        }
                        }else{
                            echo "<tr><td colspan='5'><h3>No Any Record Found</h3></td></tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif