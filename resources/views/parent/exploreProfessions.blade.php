@extends('layouts.parent-master')

@section('content')

<div class="col-xs-12">
    @if ($message = Session::get('error'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-info-msg alert-dismissable info">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
<!--                    <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>-->
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
    @if ($message = Session::get('success'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-success alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.successlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
    @if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>{{trans('validation.whoops')}}</strong> {{trans('validation.someproblems')}}<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
<?php
    if (isset($response['level4Booster']) && !empty($response['level4Booster'])) {
        if(isset($response['profession_id']) && $response['profession_id'] > 0){
            $getCompetingUserList = Helpers::getCompetingUserListForParent($response['profession_id'],Auth::guard('parent')->user()->id);
        }else{
            $getCompetingUserList = [];
        }
    } else {
        $getCompetingUserList = [];
    }
?>
<div class="centerlize">
    <div class="container_padd">
        <div class="container">
            <div class="inner_container">
                @include('teenager/teenagerLevelPointBox')
                <div class="challenger_list_icons">
                    <div class="clearfix">
                        @if(isset($getCompetingUserList) && !empty($getCompetingUserList))
                            <h2>My Challengers :<br /> <span>Click for scores</span></h2>
                            <ul>
                                @foreach($getCompetingUserList as $competingValue)
                                    <?php $loggedInUser = Auth::guard('parent')->user()->id; ?>
                                    <li>
                                        <a href="javascript:void(0)" onclick="showCompetitorData('{{$competingValue['teenager_id']}}','{{$response['profession_id']}}', '{{$loggedInUser}}');">
                                            <img src="{{ Storage::url($competingValue['profile_pic']) }}" alt="">
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                <a class="back_me" href="{{url('parent/my-challengers')}}"><i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp; <span>Your Challengers</span></a>
                <div class="level_icon">
                    <span><h2><a href="">{{ucfirst($response['profession_name'])}}</a></h2></span>
                    <div class="outer_difi">
                        <div class="dificulty_meter basic_dif">
                            <div>
                                <span class="img_cont"><span>Basic</span><img src="{{ Storage::url('frontend/images/low.gif') }}" alt=""></span>
                                <span class="detail_cont">
                                    <?php
                                    if (isset($response['level4Basic']) && !empty($response['level4Basic'])) {
                                        $level4BasicEarnedPoints = (isset($response['level4Basic']['earnedPoints']) && $response['level4Basic']['earnedPoints'] > 0) ? $response['level4Basic']['earnedPoints'] : 0;
                                        $level4BasicTotalPoints = (isset($response['level4Basic']['totalPoints']) && $response['level4Basic']['totalPoints'] > 0) ? $response['level4Basic']['totalPoints'] : 0;
                                        $noOfTotalBasicQuestion = (isset($response['level4Basic']['noOfTotalQuestion']) && $response['level4Basic']['noOfTotalQuestion'] > 0) ? $response['level4Basic']['noOfTotalQuestion'] : 0;
                                        $noOfBasicAttemptedQuestion = (isset($response['level4Basic']['noOfAttemptedQuestion']) && $response['level4Basic']['noOfAttemptedQuestion'] > 0) ? $response['level4Basic']['noOfAttemptedQuestion'] : 0;
                                        $basicBadges = (isset($response['level4Basic']['badges']) && $response['level4Basic']['badges'] != '') ? 'Win' : 'None';
                                        $basicButton = (isset($response['level4Basic']['basicButton']) && $response['level4Basic']['basicButton'] != '') ? $response['level4Basic']['basicButton'] : 'Play now';
                                    } else {
                                        $level4BasicEarnedPoints = $level4BasicTotalPoints = $noOfBasicAttemptedQuestion = $noOfTotalBasicQuestion = 0;
                                        $basicBadges = 'None';
                                        $basicButton = "Play now";
                                    }
                                    ?>
                                    <span class="outer_val">
                                        <span class="heading">Played</span>
                                        <span class="data_1 double_val">
                                            <span class="uper_half">{{$noOfBasicAttemptedQuestion}}</span>
                                            <span class="bottom_half">{{$noOfTotalBasicQuestion}}</span>
                                        </span>
                                    </span>
                                    <span class="outer_val">
                                        <span class="heading">Score</span>
                                        <span class="data_1 double_val">
                                            <span class="uper_half">{{$level4BasicEarnedPoints}}</span>
                                            <span class="bottom_half">{{$level4BasicTotalPoints}}</span>
                                        </span>
                                    </span>
                                    <span class="outer_val">
                                        <span class="heading">Badge</span>
                                        <span class="data_1 single_val">{{$basicBadges}}</span>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <a href="{{url('parent/level4-activity')}}/{{$response['profession_id']}}/{{$response['teen_id']}}" class="button3d social_button play_difi_basic"><span>{{$basicButton}}</span></a>
                    </div>
                    <?php
                    if (isset($response['level4Intermediate'])) {
                        $level4IntermediateEarnedPoints = (isset($response['level4Intermediate']['earnedPoints']) && $response['level4Intermediate']['earnedPoints'] > 0) ? $response['level4Intermediate']['earnedPoints'] : 0;
                        $level4IntermediateTotalPoints = (isset($response['level4Intermediate']['totalPoints']) && $response['level4Intermediate']['totalPoints'] > 0) ? $response['level4Intermediate']['totalPoints'] : 0;
                        $noOfTotalIntermediateQuestion = (isset($response['level4Intermediate']['noOfTotalQuestion']) && $response['level4Intermediate']['noOfTotalQuestion'] > 0) ? $response['level4Intermediate']['noOfTotalQuestion'] : 0;
                        $noOfIntermediateAttemptedQuestion = (isset($response['level4Intermediate']['noOfAttemptedQuestion']) && $response['level4Intermediate']['noOfAttemptedQuestion'] > 0) ? $response['level4Intermediate']['noOfAttemptedQuestion'] : 0;
                        $IntermediateBadges = (isset($response['level4Intermediate']['badges']) && $response['level4Intermediate']['badges'] != '') ? 'Win' : 'None';
                        $intermediateButton = (isset($response['level4Intermediate']['intermediateButton']) && $response['level4Intermediate']['intermediateButton'] != '')?$response['level4Intermediate']['intermediateButton'] : 'Play now';
                    } else {
                        $level4IntermediateEarnedPoints = $level4IntermediateTotalPoints = $noOfTotalIntermediateQuestion = $noOfIntermediateAttemptedQuestion = 0;
                        $IntermediateBadges = 'None';
                        $intermediateButton = "Play now";
                    }
                    ?>
                    <div class="outer_difi">
                        <div class="dificulty_meter intermid_dif">
                            <div>
                                <span class="img_cont"><span>Intermediate</span><img src="{{ Storage::url('frontend/images/mid.gif') }}" alt=""></span>
                                <span class="detail_cont">
                                    <span class="outer_val">
                                        <span class="heading">Played</span>
                                        <span class="data_1 double_val">
                                            <span class="uper_half">{{$noOfIntermediateAttemptedQuestion}}</span>
                                            <span class="bottom_half">{{$noOfTotalIntermediateQuestion}}</span>
                                        </span>
                                    </span>
                                    <span class="outer_val">
                                        <span class="heading">Score</span>
                                        <span class="data_1 double_val">
                                            <span class="uper_half">{{$level4IntermediateEarnedPoints}}</span>
                                            <span class="bottom_half">{{$level4IntermediateTotalPoints}}</span>
                                        </span>
                                    </span>
                                    <span class="outer_val">
                                        <span class="heading">Badge</span>
                                        <span class="data_1 single_val">{{$IntermediateBadges}}</span>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <a href="{{url('parent/level4-play-more')}}/{{$response['profession_id']}}/{{$response['teen_id']}}" class="button3d social_button play_difi_intermid"><span>{{$intermediateButton}}</span></a>
                    </div>
                    <?php
                    if (isset($response['level4Advance'])) {
                        $snap = (isset($response['level4Advance']['snap']) && $response['level4Advance']['snap'] > 0) ? $response['level4Advance']['snap'] : 0;
                        $shoot = (isset($response['level4Advance']['shoot']) && $response['level4Advance']['shoot'] > 0) ? $response['level4Advance']['shoot'] : 0;
                        $report = (isset($response['level4Advance']['report']) && $response['level4Advance']['report'] > 0) ? $response['level4Advance']['report'] : 0;
                        $totalPointsAdvance = (isset($response['level4Advance']['totalPoints']) && $response['level4Advance']['totalPoints'] > 0) ? $response['level4Advance']['totalPoints'] : 0;
                        $earnedPointsAdvance = (isset($response['level4Advance']['earnedPoints']) && $response['level4Advance']['earnedPoints'] > 0) ? $response['level4Advance']['earnedPoints'] : 0;
                        $advanceBadges = (isset($response['level4Advance']['badges']) && $response['level4Advance']['badges'] > 0) ? "Win" : "None";
                    } else {
                        $snap = $totalPointsAdvance = $shoot = $report = $earnedPointsAdvance = 0;
                        $advanceBadges = 'None';
                    }
                    ?>
                    <div class="outer_difi">
                        <div class="dificulty_meter advanced_dif">
                            <div>
                                <span class="img_cont"><span>Advanced</span><img src="{{ Storage::url('frontend/images/high.gif') }}" alt=""></span>
                                <span class="detail_cont">
                                    <!--<span class="booster_point">55</span>-->
                                    <span class="outer_val">
                                        <span class="heading">Photo</span>
                                        <span class="data_1 single_val">{{$snap}}</span>
                                    </span>
                                    <span class="outer_val">
                                        <span class="heading">Video</span>
                                        <span class="data_1 single_val">{{$shoot}}</span>
                                    </span>
                                    <span class="outer_val">
                                        <span class="heading">Research</span>
                                        <span class="data_1 single_val">{{$report}}</span>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <a href="{{url('parent/level4-advance')}}/{{$response['profession_id']}}/{{$response['teen_id']}}" class="button3d social_button play_difi_advanced"><span>Play now</span></a>
                    </div>
                </div>
            </div>
            <div class="width_container"></div>
        </div>
    </div>
</div>
<div id="myModalz" class="modal fade info_modal default_popup" role="dialog" style="display: none;">
    <div class="modal-dialog" id="competitor_data">

    </div>
</div>

<div class="loader ajax-loader" style="display:none;">
    <div class="cont_loader">
        <div class="img1"></div>
        <div class="img2"></div>
    </div>
</div>
@stop
@section('script')
<script>
    $(".table_container_outer").mCustomScrollbar({
        axis: "yx"
    });
    function showCompetitorData(teenId,profession_id,parentId)
    {
        $('.ajax-loader').show();
        $.ajax({
          url: "{{ url('/parent/show-competitor-data') }}",
          type: 'POST',
          data: {
              "_token": '{{ csrf_token() }}',
              "teenId": teenId,
              "parentId": parentId,
              "profession_id": profession_id
          },
          success: function(response) {
               $('#competitor_data').html(response);
               $('#myModalz').modal('show');
               $('.ajax-loader').hide();
          }
      });
    }
</script>
@stop