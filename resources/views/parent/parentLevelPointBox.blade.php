<?php
    if (isset($response['level4ParentBooster']) && !empty($response['level4ParentBooster'])) {
        $level1Lable = trans('labels.level1InLevel4');
        $level2Lable = trans('labels.level2InLevel4');
        $level3Lable = trans('labels.level3InLevel4');
        $level4Lable = trans('labels.level4InLevel4');
        if(isset($response['profession_id']) && $response['profession_id'] > 0){
            $getCompetingUserList = Helpers::getCompetingUserList($response['profession_id']);
            $displayPopupCompeting = true;
        }else{
            $getCompetingUserList = [];
            $displayPopupCompeting = false;
        }

        $teenRank = 0;

        $level1BoosterPointScaleDisplay = (isset($response['level4ParentBooster']['competing']) && $response['level4ParentBooster']['competing'] != '') ? $response['level4ParentBooster']['competing'] : "0";
        $highScoreForLevel4Display = (isset($response['level4ParentBooster']['highestScore']) && $response['level4ParentBooster']['highestScore'] != '') ? $response['level4ParentBooster']['highestScore'] : 0;
        $yourScoreForLevel4Display = (isset($response['level4ParentBooster']['yourScore']) && $response['level4ParentBooster']['yourScore'] != '') ? $response['level4ParentBooster']['yourScore'] : 0;
        $totalPobScore = (isset($response['level4ParentBooster']['totalPobScore']) && $response['level4ParentBooster']['totalPobScore'] != '') ? $response['level4ParentBooster']['totalPobScore'] : 0;

        $level2BoosterPointScaleDisplay = $yourScoreForLevel4Display;
        $level2BoosterPointScaleDisplay2 = $totalPobScore;

        $level3BoosterPointScaleDisplay2 = $teenRank;
        $level3BoosterPointScaleDisplay3 = (isset($response['level4ParentBooster']['yourRank']) && $response['level4ParentBooster']['yourRank'] != '') ? $response['level4ParentBooster']['yourRank'] : "0";

        $level3BoosterPointScaleDisplay = $level3BoosterPointScaleDisplay3;
        $level4BoosterPointScaleDisplay2 = (isset($response['level4ParentBooster']['yourScore']) && $response['level4ParentBooster']['yourScore'] != '') ? $response['level4ParentBooster']['yourScore'] : "0";
        $level4BoosterPointScaleDisplay =  $highScoreForLevel4Display;
        $totalBoosterPointScaleDisplay = (isset($response['level4ParentBooster']['total']) && $response['level4ParentBooster']['total'] != '') ? $response['level4ParentBooster']['total'] : "0";
        $class = "l4_box";
        $competingClass = "compitant_rank";
        $progressShow = false;

    }
    ?>
<div class="clearfix level_indicator <?php echo $class; ?>">
    <div class="level_container_left">
        <div class="user_basic_profile">
            <?php
            $image = Helpers::getParentOriginalImageUrl(Auth::parent()->get()->p_photo);
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
            <div class="container_inner_level1">
                <span class="lev_1">{{$level1Lable}}</span>
                <div class="level-1">
                    @if($displayPopupCompeting)
                    <span class="center_detial {{$competingClass}}" data-toggle="modal" >{{$level1BoosterPointScaleDisplay}}&nbsp;&nbsp;<i class="fa fa-users" aria-hidden="true"></i></span>
                    @else
                    <span class="center_detial">{{$level1BoosterPointScaleDisplay}}</span>
                    @endif
                    <?php $L1Progress = (isset($response['boosterPoints']['Level1Progress']) && $response['boosterPoints']['Level1Progress'] > 0)?$response['boosterPoints']['Level1Progress']:0; ?>
                    @if($progressShow)
                    <span class="booster_bottom">
                        <div class="h_boost" style="width:{{$L1Progress}}%;"></div>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="level_container_inner">
            <div class="container_inner_level1">
                <span class="lev_2">{{$level2Lable}}</span>
                <div class="level-2">
                    <span class="center_detial">
                        <?php
//                            if($level2BoosterPointScaleDisplay2 != '' && $level2BoosterPointScaleDisplay2 > 0){
//                                echo "<span>".$level2BoosterPointScaleDisplay." <strong>/</strong></span> <span>".$level2BoosterPointScaleDisplay2."</span>";
//                            }else{
//                                echo $level2BoosterPointScaleDisplay;
//                            }
                            echo $level2BoosterPointScaleDisplay;
                        ?>
                    </span>
                    <?php $L2Progress = (isset($response['boosterPoints']['Level2Progress']) && $response['boosterPoints']['Level2Progress'] > 0)?$response['boosterPoints']['Level2Progress']:0; ?>
                    @if($progressShow)
                    <span class="booster_bottom">
                        <div class="h_boost" style="width:{{$L2Progress}}%;"></div>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="level_container_inner">
            <div class="container_inner_level1">
                <span class="lev_3">{{$level3Lable}}</span>
                <div class="level-3">
                    <span class="center_detial">{{$level3BoosterPointScaleDisplay}}</span>
                    <?php $L3Progress = (isset($response['boosterPoints']['Level3Progress']) && $response['boosterPoints']['Level3Progress'] > 0)?$response['boosterPoints']['Level3Progress']:0; ?>
                    @if($progressShow)
                    <span class="booster_bottom">
                        <div class="h_boost" style="width:{{$L3Progress}}%;"></div>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="level_container_inner">
            <div class="container_inner_level1">
                <span class="lev_4">{{$level4Lable}}</span>
                <div class="level-4">
                    <span class="center_detial padTop10">
                        <?php
                            if($level4BoosterPointScaleDisplay2 != ''){
                              //  echo "<span>".$level4BoosterPointScaleDisplay2." <strong>/</strong></span> <span>".$level4BoosterPointScaleDisplay."</span>";
                                echo "<span>".$level4BoosterPointScaleDisplay2." <strong>/</strong></span> <span>".$level2BoosterPointScaleDisplay2."</span>";
                            }else{
                                echo $level4BoosterPointScaleDisplay;
                            }
                        ?>
                    </span>
                    <?php $L4Progress = (isset($response['boosterPoints']['Level4Progress']) && $response['boosterPoints']['Level4Progress'] > 0)?$response['boosterPoints']['Level4Progress']:0; ?>
                    @if($progressShow)
                    <span class="booster_bottom">
                        <div class="h_boost" style="width:{{$L4Progress}}%;"></div>
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>