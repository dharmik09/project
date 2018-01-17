<div class="survey-list">
    <form id="level1ActivityPart2" action="{{url('/teenager/save-first-level-icon-quality')}}" onsubmit="return checkQualityData()" method="post" enctype="multipart/form-data" >
        {{csrf_field()}}
        <div class="qualities-sec">
            <img src="{{$data['icon_image']}}" alt="" class="icon_img">
            <span class="icon_name">
                <span class="subtitle">Select Qualities For</span>
                <span class="title">{{$data['icon_name']}}</span>
            </span>
            <div class="row">
                @if(isset($response['qualityList']) && count($response['qualityList']) > 0)
                    @foreach($response['qualityList'] as $key => $qualityValue)
                        <div class="col-md-4 col-sm-6 col-xs-6">
                            <div class="ck-button">
                                <label>
                                    <input class="iconCheck" type="checkbox" value="{{$qualityValue['id']}}" id="icon[{{$qualityValue['id']}}]" name="icon[{{$qualityValue['id']}}]">
                                    <span>{{$qualityValue['quality']}}</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                @endif
                <input type="hidden" name="category_type" value="{{ $categoryType }}">
                @if($categoryType == "2" || $categoryType == "1")
                    <input type="hidden" name="category_id" value="{{ $categoryId }}">
                @elseif($categoryType == "3")
                    <input type="hidden" name="relation_category" value="{{ $relation_category }}">
                    <input type="hidden" name="relation_id" value="{{ $lastInterIdRelation }}">
                @elseif($categoryType == "4")
                    <input type="hidden" name="self_id" value="{{ $lastInterIdSelf }}">
                @endif
            </div>
        </div>
        <div class="form-btn">
            <span class="icon"><i class="icon-arrow-spring"></i></span>
            <br/>
            <input type="submit" class="btn primary_btn" value="Submit">
        </div>
    </form>
</div>





<div class="inner_container">
    <div class="landing_container">
        <h1><span class="title_border">Vote</span></h1>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-6 borderright">
                <a href="<?php if($isQuestionCompleted == 0) { ?> {{ url('/teenager/playLevel1Activity') }} <?php } else { echo "javascript:void(0)"; }?>" class="landing_box landing_l1" onclick="checkLevel1Questions({{$isQuestionCompleted}});">
                    <span class="img_container">
                        <span class="landing_icon">
                            <span class="vote"></span>
                        </span>
                    </span>
                    <span class="title_container">
                        <span class="main_title">Opinions</span>
                    </span>
                </a>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 borderbottom">
                <a href="javascript:void(0);" onClick="playFirstLevelWorldType(1)" class="landing_box landing_l2">
                    <span class="img_container">
                        <span class="landing_icon">
                            <span class="fiction"></span>
                        </span>
                    </span>
                    <span class="title_container">
                        <span class="main_title">Fictional World</span>
                    </span>
                </a>
            </div>
            <br/>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6 bordertop">
                    <a onClick="playFirstLevelWorldType(2)" href="javascript:void(0);" class="landing_box landing_l3 right">
                        <span class="img_container">
                            <span class="landing_icon">
                                <span class="real"></span>
                            </span>
                        </span>
                        <span class="title_container">
                            <span class="main_title">Real World</span>
                        </span>
                    </a>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 borderleft">
                    <a onClick="playFirstLevelWorldType(3)" href="javascript:void(0);" class="landing_box landing_l4">
                        <span class="img_container">
                            <span class="landing_icon">
                                <span class="family"></span>
                            </span>
                        </span>
                        <span class="title_container">
                            <span class="main_title">Your World</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>