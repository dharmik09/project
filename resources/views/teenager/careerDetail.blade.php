@extends('layouts.teenager-master')

@push('script-header')
    <title>Career Detail - {{$professionsData->pf_name}}</title>
@endpush

@section('content')
    <div class="bg-offwhite">
    <!-- mid section starts-->
    <!-- mid section-->
    <div class="container">
        <div class="col-xs-12">
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
            @if ($message = Session::get('error'))
            <div class="row">
                <div class="col-md-8 col-md-offset-2 invalid_pass_error">
                    <div class="box-body">
                        <div class="alert alert-error alert-dismissable danger">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                            <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                            {{ $message }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if (count($errors) > 0)
            <div class="alert alert-danger danger">
                <strong>{{trans('validation.whoops')}}</strong>
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                {{trans('validation.someproblems')}}<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <section class="career-detail">
            <h1>{{$professionsData->pf_name}}</h1>
           
            <div class="career-banner banner-landing">
                <img id="profession_image" src="{{Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$professionsData->pf_logo)}}">
                <div>
                    <div class="play-icon"><a href="javascript:void(0);" class="play-btn" id="iframe-video-click"><img src="{{ Storage::url('img/play-icon.png') }}" alt="play icon"></a></div>
                </div>
                <?php $videoCode = Helpers::youtube_id_from_url($professionsData->pf_video); ?>
                @if($videoCode == '')
                    <video id="dropbox_video_player" poster="{{Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$professionsData->pf_logo)}}" oncontextmenu="return false;"  controls loop style="width: 100%; min-width: 100%;">
                        <source src="{{$professionsData->pf_video}}" type="video/mp4"  />
                        Your browser does not support HTML5 video.
                    </video>
                @else
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/{{$videoCode}}?autohide=1&amp;showinfo=0&amp;modestBranding=1&amp;start=0&amp;rel=0&amp;enablejsapi=1&amp;loop=1&amp;playlist={{$videoCode}}" frameborder="0" allowfullscreen id="iframe-video"></iframe>
                @endif   
            </div>
            <div class="detail-content">
                <div class="row">
                    <div class="col-md-8">
                        <div class="career-stat">
                            <div class="row">
                                <div class="col-sm-6">
                                    <ul class="color-1">
                                        <li class="icon"><?php echo (isset($countryId) && !empty($countryId) && $countryId == 1) ? '₹' : '<i class="icon-dollor"></i>' ?></li>
                                        <?php
                                            $average_per_year_salary = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'average_per_year_salary';
                                            })->first();
                                        ?>
                                        <li>
                                            <h4><?php echo (isset($average_per_year_salary->pfic_content)) ? $average_per_year_salary->pfic_content : '' ?></h4>
                                            <p>Average per year</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="color-2">
                                        <li class="icon"><i class="icon-clock"></i></li>
                                        <?php
                                            $work_hours_per_week = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'work_hours_per_week';
                                            })->first();
                                        ?>
                                        <li>
                                            <h4><?php echo (isset($work_hours_per_week->pfic_content)) ? $work_hours_per_week->pfic_content : '' ?></h4>
                                            <p>Hours per week</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="color-3">
                                        <li class="icon"><i class="icon-pro-user"></i></li>
                                        <?php
                                            $positions_current = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'positions_current';
                                            })->first();
                                        ?>
                                        <li>
                                            <h4><?php echo (isset($positions_current->pfic_content)) ? $positions_current->pfic_content : '' ?></h4>
                                            <?php echo (isset($countryId) && !empty($countryId) && $countryId == 1) ? '<p>Industry Employment 2017</p>' : '<p>Employment 2017</p>' ?>                                            
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-sm-6">
                                    <ul class="color-4">
                                        <li class="icon"><i class="icon-pro-user"></i></li>
                                        <?php
                                            $positions_projected = $professionsData->professionHeaders->filter(function($item) {
                                                return $item->pfic_title == 'positions_projected';
                                            })->first();
                                        ?>
                                        <li>
                                            <h4><?php echo (isset($positions_projected->pfic_content)) ? $positions_projected->pfic_content : '' ?></h4>
                                            <?php echo (isset($countryId) && !empty($countryId) && $countryId == 1) ? '<p>Projected for 2022</p>' : '<p>Projected for 2026</p>' ?>                                            
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="description">
                            <div class="heading">
                                <h4>{{$professionsData->pf_name}}</h4>
                                <div class="list-icon">
                                    <span class="star-icon">
                                        <?php
                                        if (count($professionsData->starRatedProfession)>0) { ?>
                                            <a href="javascript:void(0)" class="addto pull-left selected" title="Added as favourite">
                                                <img src="{{ Storage::url('img/star-active.png') }}" class="hover-img">
                                            </a>
                                        <?php } else { ?>
                                            <a id="add-to-star" href="javascript:void(0)" title="Add as favourite" class=""><i class="icon-star"></i></a>
                                        <?php } ?>
                                        <div id="favourite_message" class="favourite-text">Career has been selected as favourite</div>
                                    </span>
                                    
                                    <span>
                                        <div id="print_loader">
                                            <a href="{{url('teenager/get-career-pdf/'.$professionsData->pf_slug)}}" target="_blank" title="print"><i class="icon-print"></i></a>
                                        </div> 
                                    </span>
                                </div>
                            </div>
                            <?php
                                $profession_description = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_description';
                                })->first();
                            ?>
                            <p><?php echo (isset($profession_description->pfic_content)) ? $profession_description->pfic_content : '' ?></p>
                        </div>
                        <div class="career-detail-tab bg-white">
                            <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                                <li class="active custom-tab col-xs-6 tab-color-1">
                                    <a data-toggle="tab" href="#menu1">
                                        <span class="dt"><span class="dtc">Career Details</span></span>
                                    </a>
                                </li>
                                <li class="custom-tab col-xs-6 tab-color-2">
                                    <a data-toggle="tab" href="#menu2">
                                        <span class="dt">
                                            <span class="dtc">Role Play <span class="tab-complete">
                                                {{ (isset($professionCompletePercentage) && $professionCompletePercentage > 0) ? $professionCompletePercentage : 0}}% Complete
                                                </span>
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div id="menu1" class="tab-pane fade in active">
                                    @include('teenager/basic/careerDetailInfoSection')
                                </div>
                                <div id="menu2" class="tab-pane fade in">
                                    <!-- Section for booster scale --> 
                                    <div class="explore-table table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Competitors</th>
                                                        <th>Score</th>
                                                        <th>Rank</th>
                                                        <th>Points</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id="load-user-profession-competitor">
                                                        <td colspan="4">Calculating Score...</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                    </div>
                                    <!-- Section for promise plus --> 
                                    <div class="promise-plus-outer">
                                        @include('teenager/basic/careerPromisePlusSection')
                                    </div>
                                    <!-- Section start with virtual play role --> 
                                    <div class="virtual-plus text-center">
                                        <h4><span>Virtual Role Play</span></h4>
                                        <p>Instructions: The more you play, the better informed you will be experientially. Some sections will require ProCoins to attempt.</p>
                                    </div>
                                    <!-- Section for basic, intermediate quiz with seprate blade --> 
                                    <div class="quiz-sec ">
                                        <div class="row flex-container">
                                            <div class="col-sm-12">
                                                <div class="quiz-box quiz-basic">
                                                    <div class="sec-show quiz-basic-sec-show">
                                                        <h3>Quiz</h3>
                                                        <p>Warm up with this basic profession quiz! Better research career detail section before you attempt this!!</p>
                                                        <span title="{{ (isset($level4BasicPlayed) && $level4BasicPlayed) ? 'Played!' : 'Play' }}" class="btn-play btn btn-basic">{{ (isset($level4BasicPlayed) && $level4BasicPlayed) ? "Played!" : "Play" }}</span>
                                                        <span class="btn-play btn-play-basic" style="display:none;"><img src="{{Storage::url('img/loading.gif')}}"></span>
                                                    </div>
                                                    <div class="basic-quiz-area sec-hide" id="basicLevelData">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="flexSeprator" style="padding:10px;"></div>
                                        <div class="row flex-container">
                                            <div class="col-sm-12">
                                                <div class="quiz-intermediate">
                                                    <div class="sec-show clearfix quiz-intermediate-sec-show">
                                                        <div class="loading-screen loading-wrapper-sub intermediate-first-question-loader" style="display:none;">
                                                            
                                                            <div class="loading-content"></div>
                                                        </div>
                                                        <div id="intermediateTemplateList" class="flex-container">
                                                                    
                                                        </div>
                                                    </div>
                                                    <div class="quiz-area sec-hide intermediate-question" id="intermediateLevelData">
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>  
                                    <!-- Section for real world --> 
                                    <div class="virtual-plus text-center real-world">
                                        <h4><span>Real-world role Play</span></h4>
                                        <p>Instructions: Experience real world tasks in professions roleplay. Compete for a position on the professions leaderboards!</p>
                                    </div>
                                    <!-- Section for advance level -->
                                    <div class="alert l4-advance-div l4-advance-div-demo" style="display: none;">
                                        <span id="l4AdvanceMessage" class="fontWeight"></span>
                                    </div>
                                    <div class="quiz-advanced quiz-sec">
                                        @include('teenager/basic/careerAdvanceQuizSection')
                                    </div>
                                    <!-- Section for competitive role play -->
                                    <div class="virtual-plus text-center competitive-role">
                                        <h4><span>competitive role Play</span></h4>
                                        <p>Instructions: Here are tasks by your real world sponsors. Impress them & build your professional reputation early on!</p>
                                        <div class="alert competitive-div" style="display: none;">
                                            <span id="competitive-message" class="fontWeight"></span>
                                        </div>
                                        <div class="competitive-list quiz-sec">
                                            @include('teenager/basic/careerCompetitiveRoleSection')        
                                        </div>
                                    </div>
                                    <!-- Section for challenge play -->
                                    <div class="virtual-plus text-center challenge-play">
                                        <h4><span>challenge Play</span></h4>
                                        <p>Instructions: Collaborate for guidance from your mentors or simply have fun role playing professions with your parents. Challenge them!!</p>
                                        <div class="form-challenge">
                                            @include('teenager/basic/careerChallengePlaySection')            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="connect-block sec-progress color-swap">
                            <h2>Connect</h2>
                            <div class="bg-white">
                                <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                                    <li class="active custom-tab col-xs-6 tab-color-1"><a data-toggle="tab" href="#menu3"><span class="dt"><span class="dtc">Leaderboard</span></span></a></li>
                                    <li class="custom-tab col-xs-6 tab-color-3"><a data-toggle="tab" href="#menu4" onclick="getFansTeenForCareerFromTabButton();"><span class="dt"><span class="dtc">Fans of this career</span></span></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div id="menu3" class="tab-pane fade in active">
                                        @include('teenager/basic/careerDetailsLeaderBoard')
                                    </div>
                                    <div id="menu4" class="tab-pane fade in">
                                        <div id="fav-teenager-list"></div>
                                        <div class="text-center load-more" id="loadMoreButton">
                                            <div id="loader_con"></div>
                                            <p class="text-center">
                                                <a href="javascript:void(0)" id="load-more-data" title="load more">load more</a>
                                                <input type="hidden" id="pageValue" value="0">
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="bannerAdsSection">
                            <div class="ad-slider owl-carousel">
                                @forelse ($bannerAdImages as $bannerAdImage)
                                <div class="ad-sec-h">
                                    <div class="d-table">
                                        <img src="{{$bannerAdImage['image']}}">
                                    </div>
                                </div>
                                @empty
                                <div class="ad-sec-h">
                                    <div class="t-table">
                                        <div class="table-cell">
                                            No Ads available!
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="sec-match">
                            <div class="data-explainations clearfix">
                                    <div class="data"><span class="small-box career-data-color-1"></span><span>Strong match</span></div>
                                    <div class="data"><span class="small-box career-data-color-2"></span><span>Potential match</span></div>
                                    <div class="data"><span class="small-box career-data-color-3"></span><span>Unlikely match</span></div>
                                </div>
                            <?php 
                                $matchScoreArray = ['match' => 100, 'nomatch' => 33, 'moderate' => 66];
                                $matchScalePoint = ( isset($professionsData->id) && isset($getTeenagerHML[$professionsData->id]) && isset($matchScoreArray[$getTeenagerHML[$professionsData->id]]) ) ? $matchScoreArray[$getTeenagerHML[$professionsData->id]] : 0;
                            ?>
                            
                            <?php if($matchScalePoint == 33)
                                {$matchName = 'Unlikely'; $class = 'bar-no-match'; $h3class = 'no-match'; $percentage = '100';}
                                elseif($matchScalePoint == 66){$matchName = 'Potential'; $class = 'bar-moderate';$h3class = 'moderate-match';$percentage = '100';}
                                elseif($matchScalePoint == 100){$matchName = 'Strong'; $class = 'bar'; $h3class = 'strong-match';$percentage = '100';} 
                                else{
                                    $matchName = 'No Attempt'; $class = ''; $h3class = '';$percentage = '0';
                                }
                            ?>
                            
                            <div class="progress-match">
                                <div class="sec-popup">
                                    <!--<a id="career-detail-arc-view" href="javascript:void(0);" onmouseover="getHelpText('career-detail-arc-view')" data-trigger="hover" data-popover-content="#arc-view-sec" class="help-icon custompop" rel="popover" data-placement="bottom">
                                            <i class="icon-question"></i>
                                        </a>-->
                                    <div class="hide" id="arc-view-sec">
                                        <div class="popover-data">
                                            <a class="close popover-closer"><i class="icon-close"></i></a>
                                            <span class="career-detail-arc-view"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="barOverflow">
                                    <div class="bar {{$class}}"></div>
                                </div>
                                <span>{{$percentage}}%</span>
                            </div>
                            <h3 class="{{$h3class}}">{{$matchName}}</h3>
                        </div>
                        <div class="advanced-sec">
                            <div class="panel-group" id="accordion">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <?php 
                                                if($remainingDaysForActivity > 0) {
                                                    $collapseClass = "collapse";
                                                    $tooltipadv = "";
                                                } else {
                                                    $collapseClass = "";
                                                    $tooltipadv = "Please consume procoins to see Advanced View";
                                                } ?>
                                            <a data-parent="#accordion" title="{{$tooltipadv}}" data-toggle="{{$collapseClass}}" href="#accordion1" class="collapsed">Advanced View</a>
                                            <div class="sec-popup">
                                                    <a id="career-detail-advanced-view" href="javascript:void(0);" onmouseover="getHelpText('career-detail-advanced-view')" data-trigger="hover" data-toggle="clickover" data-popover-content="#advanced-view-sec" class="help-icon custompop" rel="popover" data-placement="bottom">
                                                        <i class="icon-question"></i>
                                                    </a>
                                                    <div class="hide" id="advanced-view-sec">
                                                        <div class="popover-data">
                                                            <a class="close popover-closer"><i class="icon-close"></i></a>
                                                            <span class="career-detail-advanced-view"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse" id="accordion1">
                                        <div class="panel-body">
                                            <div class="data-explainations clearfix data-interest">
                                                <div class="content">
                                                    <div class="data"><span class="small-box career-data-color-1"></span><span>Maximum</span></div>
                                                    <div class="data"><span class="small-box career-data-color-2"></span><span>Ideal</span></div>
                                                    <div class="data"><span class="small-box career-data-color-3"></span><span>Your Strength</span></div>
                                                </div>
                                            </div>
                                            @forelse($teenagerStrength as $key => $value)
                                                <div class="progress-block">
                                                    <div class="skill-name">{{$value['name']}}</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin = "0" aria-valuemax = "100" style="width: {{$value['score']}}%;">
                                                        </div>
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{$value['lowscoreH']}}%; background-color:#65c6e6;" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            @empty
                                            <div class="progress-block">
                                                Please attempt at least one section of Profile Builder to view your strength Advanced View!
                                            </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-left">
                                <div class="unbox-btn">
                                    <a id="activity_unbox" href="javascript:void(0);" title="Unlock Me" @if($remainingDaysForActivity == 0) onclick="getCoinsConsumptionDetails('{{$componentsData->pc_required_coins}}', '{{$componentsData->pc_element_name}}', '{{$remainingDaysForActivity}}');" @endif class="btn-primary">
                                        @if($remainingDaysForActivity > 0)                                        
                                        <span class="unbox-me open_advance_view">See Now!</span>
                                        @else
                                        <span class="unbox-me" id="advanced_unbox">Unlock Me</span>
                                        @endif
                                        
                                        <span class="coins-outer activity_coins">
                                            <span class="coins"></span> {{ ($remainingDaysForActivity > 0) ? $remainingDaysForActivity . ' days left' : $componentsData->pc_required_coins }}
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="sec-tags">
                            <h4>Hobbies</h4>
                            <div class="sec-popup">
                                <a id="career-tags" href="javascript:void(0);" onmouseover="getHelpText('career-tags')" data-trigger="hover" data-popover-content="#tags-sec" class="help-icon custompop" rel="popover" data-placement="bottom">
                                    <i class="icon-question"></i>
                                </a>
                                <div class="hide" id="tags-sec">
                                    <div class="popover-data">
                                        <a class="close popover-closer"><i class="icon-close"></i></a>
                                        <span class="career-tags"></span>
                                    </div>
                                </div>
                            </div>
                            <ul class="tag-list">
                                @forelse($professionsData->professionTags as $professionTags)
                                    <li><a href="{{ url('/teenager/career-tag/'.$professionTags->tag['pt_slug']) }}" title="Lorem ipsum">{{$professionTags->tag['pt_name']}}</a></li>
                                @empty
                                @endforelse
                            </ul>
                        </div>
                        <div id="sidebarAdsSection">
                            <div class="ad-slider owl-carousel">
                                @forelse ($mediumAdImages as $mediumAdImage)
                                <div class="ad-v">
                                    <div class="d-table">
                                        <img src="{{$mediumAdImage['image']}}">
                                    </div>
                                </div>
                                @empty
                                <div class="ad-v">
                                    <div class="t-table">
                                        <div class="table-cell">
                                            No Ads available!
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <div class="ad-slider owl-carousel">
                                @forelse ($largeAdImages as $largeAdImage)
                                <div class="ad-v-2">
                                    <div class="d-table">
                                        <img src="{{$largeAdImage['image']}}">
                                    </div>
                                </div>
                                @empty
                                <div class="ad-v-2">
                                    <div class="t-table">
                                        <div class="table-cell">
                                            No Ads available!
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<div class="modal fade" id="coinsConsumption" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content custom-modal">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                <h4 id="activity_title" class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <input id="activity_coins" type="hidden" value="">
                <input id="activity_name" type="hidden" value="">
                <p id="activity_message"></p>
                <p id="activity_sub_message"></p>
            </div>
            <div class="modal-footer">
                <a id="activity_buy" href="{{ url('teenager/buy-procoins') }}" type="submit" class="btn btn-primary btn-next" style="display: none;">buy</a>
                <button id="activity_consume_coin" type="submit" class="btn btn-primary btn-next" data-dismiss="modal" onclick="saveConsumedCoins();" style="display: none;" >ok </button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="scoreModal" role="dialog">
    
</div>
<span id="setResponse" value="0"></span>
<span id="setResponseIntermediate" value="0"></span>
<audio id="audio_0" src="{{ Storage::url('frontend/audio/L1A_0.wav')}}"></audio>
<audio id="audio_1" src="{{ Storage::url('frontend/audio/L1A_1.wav')}}"></audio>
<audio id="audio_2" src="{{ Storage::url('frontend/audio/L1A_2.wav')}}"></audio>
@stop

@section('script')
<script src="{{ asset('backend/js/highchart.js')}}"></script>
<script>
    $(document).ready(function() {
                
        var counterIntermediate = 0;
        $(function() {
            $(".sortable").sortable();
            $(".sortable").disableSelection();
        });

        $('.play-icon').click(function() {
            $(this).hide();
            $('video').show();
            $('#profession_image').hide();
        });
        
        $('#iframe-video-click').on('click', function(ev) {
            var youtubeVideo = '{{$videoCode}}';
            if(youtubeVideo == '') {
                $("#dropbox_video_player")[0].play();
            } else {
                $('#profession_image').hide();
                $('iframe').show();
                $("#iframe-video")[0].src += "&autoplay=1";
                ev.preventDefault();
            }
        });
        
        $('.promise-plus-overlay .close').click(function() {
            $('.promise-plus-overlay').hide();
            $('.front_page').show(500);
        });
        
        $('.btn-advanced').click(function(){
            $('.quiz-advanced .sec-show').addClass('hide');
            $('.quiz-advanced .sec-hide').addClass('active');
        });
        
        $(".progress-match").each(function(){
            var $bar = $(this).find(".bar");
            var $val = $(this).find("span");
            var perc = parseInt( $val.text(), 10);
            $({p:0}).animate({p:perc}, {
                duration: 3000,
                easing: "swing",
                step: function(p) {
                    $bar.css({
                        transform: "rotate("+ (45+(p*1.8)) +"deg)", // 100%=180° so: ° = % * 1.8
                        // 45 is to add the needed rotation to have the green borders at the bottom
                    });
                    $val.text(p|0);
                }
            });
        });
    });

    $(document).on('click','#add-to-star', function(){
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'careerId=' + '{{$professionsData->id}}';
        $.ajax({
            url : '{{ url("teenager/add-star-to-career") }}',
            method : "POST",
            data: form_data,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            dataType: "json",
            success : function (response) {
                if (response != '') {
                    $("#favourite_message").css('display', 'block');
                    $('#add-to-star').html('<img src="{{ Storage::url('img/star-active.png') }}" class="hover-img">');
                    $('#add-to-star').attr('title', 'Added as favourite');
                    setTimeout(function () {
                        $("#favourite_message").hide();
                    }, 2500);
                }
            }
        });
    });
    
    var youtubeVideo = '{{$videoCode}}';
    if(youtubeVideo == '') {
        var isYouTube = 0;
    } else {
        var isYouTube = 1;
    }
    
    setTimeout(function() {
        saveBoosterPoints({{$professionsData->id}}, 2, isYouTube);
    }, 60000);
    
    function saveBoosterPoints(professionId, type, isYouTube)
    {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = '&professionId=' + professionId + '&type=' + type + '&isYouTube=' + isYouTube;
        $.ajax({
            url : '{{ url("teenager/teen-l3-career-research") }}',
            method : "POST",
            data: form_data,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            success : function (response) {
            }
        });
    }
    
    <?php
        $high_school_req = $professionsData->professionHeaders->filter(function($item) {
            return $item->pfic_title == 'high_school_req';
        })->first();
        $junior_college_req = $professionsData->professionHeaders->filter(function($item) {
            return $item->pfic_title == 'junior_college_req';
        })->first();
        $bachelor_degree_req = $professionsData->professionHeaders->filter(function($item) {
            return $item->pfic_title == 'bachelor_degree_req';
        })->first();
        $masters_degree_req = $professionsData->professionHeaders->filter(function($item) {
            return $item->pfic_title == 'masters_degree_req';
        })->first();
        $PhD_req = $professionsData->professionHeaders->filter(function($item) {
            return $item->pfic_title == 'phd_req';
        })->first();

        if(isset($high_school_req->pfic_content)){
            if($countryId == 1){ // India
                if(strip_tags($high_school_req->pfic_content) == 0){
                    $high_school = 10;
                }elseif(strip_tags($high_school_req->pfic_content) == 1){
                    $high_school = 20;
                }else{
                    $high_school = strip_tags($high_school_req->pfic_content);
                }
            }
            elseif($countryId == 2){ // United States
                $high_school = strip_tags($high_school_req->pfic_content);
            }
        } else {
            $high_school = 0;
        }

        if(isset($junior_college_req->pfic_content)){
            if($countryId == 1){ // India
                if(strip_tags($junior_college_req->pfic_content) == 0){
                    $junior_college = 10;
                }elseif(strip_tags($junior_college_req->pfic_content) == 1){
                    $junior_college = 20;
                }else{
                    $junior_college = strip_tags($junior_college_req->pfic_content);
                }
            }
            elseif($countryId == 2){ // United States
                $junior_college = strip_tags($junior_college_req->pfic_content);
            }
        } else {
            $junior_college = 0;
        }

        if(isset($bachelor_degree_req->pfic_content)){
            if($countryId == 1){ // India
                if(strip_tags($bachelor_degree_req->pfic_content) == 0){
                    $bachelor_degree = 10;
                }elseif(strip_tags($bachelor_degree_req->pfic_content) == 1){
                    $bachelor_degree = 20;
                }else{
                    $bachelor_degree = strip_tags($bachelor_degree_req->pfic_content);
                }
            }
            elseif($countryId == 2){ // United States
                $bachelor_degree = strip_tags($bachelor_degree_req->pfic_content);
            }
        } else {
            $bachelor_degree = 0;
        }

        if(isset($masters_degree_req->pfic_content)){
            if($countryId == 1){ // India
                if(strip_tags($masters_degree_req->pfic_content) == 0){
                    $masters_degree = 10;
                }elseif(strip_tags($masters_degree_req->pfic_content) == 1){
                    $masters_degree = 20;
                }else{
                    $masters_degree = strip_tags($masters_degree_req->pfic_content);
                }
            }
            elseif($countryId == 2){ // United States
                $masters_degree = strip_tags($masters_degree_req->pfic_content);
            }
        }else{
            $masters_degree = 0;
        }

        if(isset($PhD_req->pfic_content)){
            if($countryId == 1){ // India
                if(strip_tags($PhD_req->pfic_content) == 0){
                    $phd_degree = 10;
                }elseif(strip_tags($PhD_req->pfic_content) == 1){
                    $phd_degree = 20;
                }else{
                    $phd_degree = strip_tags($PhD_req->pfic_content);
                }
            }
            elseif($countryId == 2){ // United States
                $phd_degree = strip_tags($PhD_req->pfic_content);
            }
        }else{
            $phd_degree = 0;
        }
        if($high_school == 0 && $junior_college == 0 && $bachelor_degree == 0 && $masters_degree == 0 && $phd_degree == 0)
        {    
    ?>
        $('#education_chart').html('');
    <?php
        }
        else
        {
            $chartArray[] = array('y'=> (int) $high_school, 'name' => 'High School', 'color' => '#ff5f44');
            $chartArray[] = array('y'=> (int) $junior_college, 'name' => 'Junior College', 'color' => '#65c6e6');
            $chartArray[] = array('y'=> (int) $bachelor_degree, 'name' => 'Bachelors Degree', 'color' => '#73376d');
            $chartArray[] = array('y'=> (int) $masters_degree, 'name' => 'Masters', 'color' => '#27a6b5');
            $chartArray[] = array('y'=> (int) $phd_degree, 'name' => 'PhD', 'color' => '#00caa7');
    ?>
        var educationChartData = <?php echo json_encode($chartArray);  ?>;
        loadChart('column','',educationChartData,'education_chart');

        function loadChart(chartType,total,chartData,loadDiv){
            $('#'+loadDiv).highcharts({
                chart: {
                    type: chartType,
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    type: 'category',
                    title: {
                        text : 'Note : <?php echo (isset($countryId) && !empty($countryId) && $countryId == 1) ? "Level of education attained by people in this career in the US" : "Level of education attained by people currently working in this career" ?>',
                        style: {
                            fontSize:'16px'
                        }
                    },
                    labels: {
                        style: {
                            fontSize:'13px'
                        }
                    }
                },
                legend: {
                    enabled:false
                },
                yAxis: {                
                    title: {
                        text: ''
                    },                
                    lineWidth: 0                
                },
                            
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: false,
                            format: ''
                        }
                    }
                },
                tooltip: {
                    pointFormat: ''
                },
                series: [{
                        colorByPoint: true,
                        data: chartData
                    }]
               
            });
        }
    <?php
        }
    ?>

    $(document).on('click','#load-more-data', function(){    
        getFansTeenForCareer();
    });

    function getFansTeenForCareerFromTabButton(){
        if( !$('#menu4').hasClass('active') ){
            $("#menu4").html('<div id="fav-teenager-list"></div><div class="text-center load-more" id="loadMoreButton"><div id="loader_con"></div><p class="text-center"><a href="javascript:void(0)" id="load-more-data" title="load more">load more</a><input type="hidden" id="pageValue" value="0"></p></div>');
            getFansTeenForCareer();
        }
    }

    function getFansTeenForCareer(){
        $("#loader_con").html('<img src="{{Storage::url('img/loading.gif')}}">');
        var pageNo = $('#pageValue').val();
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/get-teenagers-for-starrated')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'page_no':pageNo,'professionId':{{$professionsData->id}}},
            success: function (response) {
                if(response.teenagersCount == 0){
                    $("#fav-teenager-list").html('<div class="sec-forum"><span>No result Found</span></div>');
                }
                if(response.teenagersCount != 10){
                    $('#loadMoreButton').removeClass('text-center');
                    $('#loadMoreButton').removeClass('load-more');
                    $('#loadMoreButton').addClass('notification-complete');
                    $('#loadMoreButton').html("");
                }
                else{
                }
                $('#pageValue').val(response.pageNo);
                $("#fav-teenager-list").append(response.teenagers);
                $("#loader_con").html('');
            }
        });
    }

    //Basic level data query
    var basicCount, col_count;
    jQuery(document).ready(function($) {
        var counter = setInterval(basicTimer, 1000);
        function basicSecondPassed() {
            var minutes = Math.round((basicCount - 30) / 60);
            var remainingcount = basicCount % 60;
            if (remainingcount < 10) {
                remainingcount = "0" + remainingcount;
            }
            $('.basic-time-tag, .basic-time-tag').text(minutes + ":" + remainingcount);
            $('.time-tag').show();
        }
        function basicTimer() {
            if (basicCount < 0) { }
            else {
                basicSecondPassed();
            }
            basicCount = basicCount - 1;
            $("#blackhole").val(basicCount);           
            if (basicCount == -1) {
                autoSubmitBasicAnswer();
            }
        }
    });

    $(document).on('click', '.quiz-basic .btn-basic', function(e) {
        e.preventDefault();
        $(".btn-play-basic").show();
        $(".btn-basic").hide();

        //Close Intermediate activity if working
        $('.quiz-intermediate .sec-show').removeClass('hide');
        $('.quiz-intermediate .intermediate-quiz-area').removeClass('active');
        $('.quiz-intermediate .intermediate-question').removeClass('active');
        $('#intermediateLevelData').html('');
        $(".btn-intermediate").show();
        
        getBasicQuestions('{{$professionsData->id}}');
    });

    function getNextIntermediateQuestion(templateId) {
        $(".btn-play-intermediate").show();
        $(".next-intermediate .btn-intermediate").hide();
        getIntermediateQuestions(templateId);
    }

    function getBasicQuestions(professionId) {
        $.ajax({
            url: "{{url('teenager/play-basic-level-activity')}}",
            type : 'POST',
            data : { 'professionId' : '{{$professionsData->id}}' },
            headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' },
            success: function(data){
                $("#setResponse").val("0");
                $('.quiz-basic .sec-show').addClass('hide');
                $('.quiz-basic .basic-quiz-area').addClass('active');
                $('#basicLevelData').html(data);
                var basicCompleted = $(".basicCompleted").val();
                if (basicCompleted != '' && basicCompleted == 1) {
                    getProfessionCompletionPercentage(professionId);
                }
            }
        }); 
    }
    
    //Intermediate level data query
    //var intermediateCount;
    
    function intermediateSecondPassed() {
        var minutes = Math.round((intermediateCount - 30) / 60);
        var remainingcount = intermediateCount % 60;
        if (remainingcount < 10) {
            remainingcount = "0" + remainingcount;
        }
        $('.intermediate-time-tag, .intermediate-time-tag').text(minutes + ":" + remainingcount);
        $('.time-tag').show();
    }
    function intermediateTimer() {
        if (intermediateCount < 0) { }
        else {
            intermediateSecondPassed();
        }
        intermediateCount = intermediateCount - 1;
        $("#blackholeIntermediate").val(intermediateCount);           
        if (intermediateCount == -1) {
            autoSubmitIntermediateAnswer();
        }
    }

    function getConceptData(templateId) {
        //Hide Basic level quiz data
        $('.quiz-basic .sec-show').removeClass('hide');
        $('.quiz-basic .basic-quiz-area').removeClass('active');
        $('#basicLevelData').html('');
        $(".btn-basic").show();
        $(".btn-play-basic").hide();
        
        getIntermediateQuestions(templateId);
    }

    function getIntermediateQuestions(templateId) {
        $('.intermediate-first-question-loader').parent().toggleClass('loading-screen-parent');
        $('.intermediate-first-question-loader').show();
            
        $.ajax({
            url: "{{url('teenager/play-intermediate-level-activity')}}",
            type : 'POST',
            data : { 'professionId' : '{{$professionsData->id}}', 'templateId' : templateId },
            headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' },
            success: function(data){
                $("#setResponseIntermediate").val("0");
                $('.quiz-intermediate .sec-show').addClass('hide');
                $('.quiz-intermediate .intermediate-question').addClass('active');
                $("html, body").animate({
                    scrollTop: $('#flexSeprator').offset().top 
                }, 300);
                $('#intermediateLevelData').html(data);
                if($('.intermediateCompleted').val() != '' && $('.intermediateCompleted').val() == 1) {
                    getProfessionCompletionPercentage('{{$professionsData->id}}');
                } 
                $('.intermediate-first-question-loader').hide();
                $('.intermediate-first-question-loader').parent().removeClass('loading-screen-parent');
                
                if( typeof counterIntermediate !== "undefined") { clearInterval(counterIntermediate); }
                //Manage timer for question #####START#####
                var time_out_question = setPopupTime * 1000;
                if ( $("#quiz_material_popup").length == 0 ) {
                    counterIntermediate = setInterval(intermediateTimer, 1000);
                } else {
                    $('#quiz_material_popup').on('hidden.bs.modal', function() {
                        counterIntermediate = setInterval(intermediateTimer, 1000);
                    });
                }
                if (time_out_question > 0) {
                    $('#quiz_material_popup').modal('show');
                    setTimeout(function() {
                        $('#quiz_material_popup').modal('hide');
                    }, time_out_question);
                    //Progressbar logic should be here
                    function color_gen() {
                        var hue = 'rgb(' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ',' + (Math.floor(Math.random() * 256)) + ')';
                        return hue;
                    }
                    $(".time_out_cst").css("background-color", color_gen());
                    var current_width = 90;
                    for (i = 0; i < 10; i++) {
                        $(".time_out_cst").animate({
                            width: current_width + "%",
                            backgroundColor: color_gen()
                        }, time_out_question / 10, "linear");

                        current_width = current_width - 10;

                        if (current_width < 10) {
                            $(".time_out_cst").animate({
                                width: current_width + "%",
                                backgroundColor: "red"
                            }, time_out_question / 10, "linear");
                        }
                    }
                }
                //Timer for question #####END#####
                
                $('#single_line_answer_box').focus();

                $(".sortable").sortable();
                $(".sortable").disableSelection();
                adjusting_box_size();
                //var col_count = $('.drg_section').data('col');
                $(".drag_drp li span").draggable({
                    opacity: "0.5",
                    helper: "clone",
                    containment: "document"
                });
                $(".drag_drp li").droppable({
                    hoverClass: "ui-state-active",
                    drop: function(event, ui) {
                        if ($(this).find('img').length == 0) {
                            ui.draggable.detach().appendTo($(this));
                        }
                    }
                });
            }
        }); 
    }

    $(document).on('click', '#basicLevelData .quiz_view .close', function(e) {
        $('.quiz-basic .sec-show').removeClass('hide');
        $('.quiz-basic .basic-quiz-area').removeClass('active');
        basicCount = -2;
        $('#basicLevelData').html('');
        $(".btn-basic").show();
        $(".btn-play-basic").hide();
    });

    $(document).on('click', '#intermediateLevelData .quiz_view .close', function(e) {
        $('.quiz-intermediate .sec-show').removeClass('hide');
        $('.quiz-intermediate .intermediate-quiz-area').removeClass('active');
        $('.quiz-intermediate .intermediate-question').removeClass('active');
        intermediateCount = -2;

        if( typeof counterIntermediate !== "undefined") { clearInterval(counterIntermediate); }
        $('#intermediateLevelData').html('');
        $(".btn-intermediate").show();
        $(".btn-play-intermediate").hide();
    });
    
    $('.ad-slider').owlCarousel({
        loop: true,
        margin: 10,
        items: 1,
        nav: false,
        dots: false,
        smartSpeed: 500,
        autoplay:true,
    });
    
    function saveBasicAnswer() {
        $("#basicErrorGoneMsg").html('');
        <?php if(Auth::guard('teenager')->user()->is_sound_on == 1){ ?>
            var audio = document.getElementById('audio_1');
            audio.play();
        <?php } ?>
        var validCheck = 0;

        if ($('.optionSelection [name="' + optionName + '"]:checked').length > 0) {
            validCheck = 1;
            $(".basic-time-tag").css('visibility', 'hidden');
        }
        
        if (validCheck == 1) {
            $("#setResponse").val("1");
            var form_data = $("#level4_activity_ans").serialize();
            $('.basic-question-loader').parent().toggleClass('loading-screen-parent');
            $('.basic-question-loader').show();
            $('.saveMe').css('visibility', 'hidden');
            
            $.ajax({
                type: 'POST',
                data: form_data,
                dataType: 'html',
                url: "{{ url('/teenager/save-basic-level-activity')}}",
                headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' },
                cache: false,
                success: function(data) {
                    $('.basic-question-loader').hide();
                    $('.basic-question-loader').parent().removeClass('loading-screen-parent');
                    var obj = $.parseJSON(data);
                    if (obj.status == 1) {
                        $.each(obj.data, function(key, value) {
                            if (value == 1) {
                                $('.class' + key).addClass("correct");
                            } else {
                                $('.class' + key).addClass("incorrect");
                            }
                        });
                        setTimeout( function() {
                            getBasicQuestions('{{$professionsData->id}}'); 
                        }, 3500);
                    } else {
                        $("#setResponse").val("0");
                        $('.saveMe').css('visibility', 'visible');
                        $(".basic-time-tag").css('visibility', 'visible');
                        $("html, body").animate({
                            scrollTop: $('#basicErrorGoneMsg').offset().top 
                        }, 300);
                        $("#basicErrorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">'+obj.message+'</span></div></div></div>');
                        setTimeout( function() {
                            getBasicQuestions('{{$professionsData->id}}'); 
                        }, 3500);
                    }
                }
            });
        } else {
            $('.basic-question-loader').hide();
            $('.basic-question-loader').parent().removeClass('loading-screen-parent');
            $("html, body").animate({
                scrollTop: $('#basicErrorGoneMsg').offset().top 
            }, 300);
            $("#basicErrorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please, select at-least one answer!</span></div></div></div>');
        }
    }

    function autoSubmitBasicAnswer() {
        if ($("#setResponse").val() == 0) {
            $("#setResponse").val("1");
            var questionID = $("#questionID").val();
            var answerID = 0;
            var timer = 0;
            var form_data = 'questionID=' + questionID + '&answerID[0]=' + answerID + '&timer=' + timer;
            $('.basic-question-loader').parent().toggleClass('loading-screen-parent');
            $('.basic-question-loader').show();
            $('.saveMe').css('visibility', 'hidden');
                        
            $.ajax({
                type: 'POST',
                data: form_data,
                dataType: 'html',
                url: "{{ url('/teenager/save-basic-level-activity')}}",
                headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' },
                cache: false,
                success: function(data) {
                    $('.basic-question-loader').hide();
                    $('.basic-question-loader').parent().removeClass('loading-screen-parent');
                    var obj = $.parseJSON(data);
                    if (obj.status == 1) {
                        $('.saveMe').css('visibility', 'hidden');
                        $.each(obj.data, function(key, value) {
                            if (value == 1) {
                                $('.class' + key).addClass("correct");
                            } else {
                                $('.class' + key).addClass("incorrect");
                            }
                        });
                        setTimeout( function() {
                            getBasicQuestions('{{$professionsData->id}}'); 
                        }, 3500);
                    } else {
                        $('.saveMe').css('visibility', 'visible');
                        $("#setResponse").val("0");
                        $(".basic-time-tag").css('visibility', 'visible');
                        $("html, body").animate({
                            scrollTop: $('#basicErrorGoneMsg').offset().top 
                        }, 300);
                        $("#basicErrorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">'+obj.message+'</span></div></div></div>');
                        setTimeout( function() {
                            getBasicQuestions('{{$professionsData->id}}'); 
                        }, 3500);
                    }
                }
            });
        }
    }

    function saveIntermediateAnswer() {
        $("#intermediateErrorGoneMsg").html('');
        
        $('.stopOnSubmit iframe').attr("src", jQuery(".stopOnSubmit iframe").attr("src"));
        var isAudio = $("#checkAudio").val();
        if(typeof isAudio !== "undefined"){
            var audioStop = document.getElementById('onOffAudio');
            audioStop.pause();
            $("#onOffAudio").prop('muted',true);
        }
        <?php if(Auth::guard('teenager')->user()->is_sound_on == 1){ ?>
            var audio = document.getElementById('audio_1');
            audio.play();
        <?php } ?>
        var validCheck = 0;
        var setSMsg = 0;
        if ($("#singleLineCheck").attr('value') === "yes") {
            setSMsg = 1;            
            if ($('[name="answer[0]"]').val().trim() !== '') {
                validCheck = 1;
                $(".intermediate-time-tag").css('visibility', 'hidden');
            }
        } else {
            if ($('.optionSelectionIntermediate [name="' + optionName + '"]:checked').length > 0) {
                validCheck = 1;
                $(".intermediate-time-tag").css('visibility', 'hidden');
            }
        }

        if (validCheck === 1) {
            var form_data = $("#level4_intermediate_activity_ans").serialize();
            $('.intermediate-question-loader').parent().toggleClass('loading-screen-parent');
            $('.intermediate-question-loader').show();
            $("#setResponseIntermediate").val("1");
            $('.saveIntMe').css('visibility', 'hidden');
            
            $.ajax({
                type: 'POST',
                data: form_data,
                //async: false,
                dataType: 'html',
                url: "{{ url('/teenager/save-intermediate-level-activity')}}",
                headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' },
                cache: false,
                success: function(data) {
                    if( typeof counterIntermediate !== "undefined") { clearInterval(counterIntermediate); }
                    $('.intermediate-question-loader').hide();
                    $('.intermediate-question-loader').parent().removeClass('loading-screen-parent');
                    var obj = $.parseJSON(data);
                    if (obj.status == 1) {
                        if (obj.answerType == "single_line_answer") {
                            $("#answerRightWrongMsg").text(obj.answerRightWrongMsg + ". Correct Answer Is : " + obj.systemCorrectAnswerText + "");
                            if (obj.systemCorrectAnswer == 1) {
                                $(".response_message_outer").addClass("correct");
                            } else {
                                $(".response_message_outer").addClass("incorrect");
                            }
                        } else if (obj.answerType === "option_choice_with_response" || obj.answerType === "filling_blank" || obj.answerType === "option_choice" || obj.answerType === "true_false") {
                            $("#answerRightWrongMsg").text(obj.answerRightWrongMsg);
                            if (obj.systemCorrectAnswer == 1) {
                                $(".response_message_outer").addClass("correct");
                            } else {
                                $(".response_message_outer").addClass("incorrect");
                            }
                            $.each(obj.systemCorrectAnswer2, function(key, value) {
                                if (value == 1) {
                                    $('.class' + key).addClass("correct");
                                } else {
                                    $('.class' + key).addClass("incorrect");
                                }
                            });
                            if (obj.answerType === "option_choice") {
                                if (obj.questionAnswerText !== '') {
                                    var phtml = "<p>" + obj.questionAnswerText + "</p>";
                                    $('#showResponseMessage').html(phtml);
                                }
                            }
                            if (obj.answerType === "option_choice_with_response") {
                                if (obj.questionAnswerText && obj.questionAnswerText !== '') {
                                    var phtmlImg = '';
                                    if (obj.questionAnswerImage && obj.questionAnswerImage !== '') {
                                        phtmlImg = "<img src=" + obj.questionAnswerImage + " />";
                                    }
                                    var phtml = "<div class='t-table'><div class='t-cell'>" + phtmlImg + "</div><div class='t-cell'><p>" + obj.questionAnswerText + "</p></div></div>";
                                    $('#showResponseMessage').html(phtml);
                                }
                            }
                        } else {
                        }
                        $('.saveIntMe').css('visibility', 'hidden');
                        $('.next-intermediate').show();
                    } else {
                        $("#showResponseMessage").text(obj.message);
                        var urlSet = obj.redirect;
                        //setTimeout("location.reload(true);", 3000);
                    }
                }
            });
        } else {
            if (setSMsg === 1) {
                $('.intermediate-question-loader').hide();
                $('.intermediate-question-loader').parent().removeClass('loading-screen-parent');
                $("html, body").animate({
                    scrollTop: $('#intermediateErrorGoneMsg').offset().top 
                }, 300);
                $("#intermediateErrorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please, Fillup the answer!</span></div></div></div>');
            } else {
                $('.intermediate-question-loader').hide();
                $('.intermediate-question-loader').parent().removeClass('loading-screen-parent');
                $("html, body").animate({
                    scrollTop: $('#intermediateErrorGoneMsg').offset().top 
                }, 300);
                $("#intermediateErrorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please, select at-least one answer!</span></div></div></div>');
            }
        }
    }

    function saveDropDownIntermediateAnswer() {
        $("#intermediateErrorGoneMsg").html('');
        $('.stopOnSubmit iframe').attr("src", jQuery(".stopOnSubmit iframe").attr("src"));
        var isAudio = $("#checkAudio").val();
        if(typeof isAudio !== "undefined"){
            var audioStop = document.getElementById('onOffAudio');
            audioStop.pause();
            $("#onOffAudio").prop('muted',true);
        }
        <?php if(Auth::guard('teenager')->user()->is_sound_on == 1){ ?>
            var audio = document.getElementById('audio_1');
            audio.play();
        <?php } ?>
        var validCheckAll = 0;
        var answerValue = $("#dropDownSelection").val();
        var answerTypeValue = $("#dropDownTypeSelection").val();
        
        if (answerValue > 0 && answerTypeValue != '') {
            validCheckAll = 1;
            $(".intermediate-time-tag").css('visibility', 'hidden');
        }

        if (validCheckAll === 1) {
            var form_data = $("#level4_intermediate_activity_ans").serializeArray();
            $('.intermediate-question-loader').parent().toggleClass('loading-screen-parent');
            $('.intermediate-question-loader').show();
            $("#setResponseIntermediate").val("1");
            $('.saveIntMe').css('visibility', 'hidden');
            
            $.ajax({
                type: 'POST',
                data: form_data,
                //async: false,
                dataType: 'html',
                url: "{{ url('/teenager/save-intermediate-level-activity')}}",
                headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' },
                cache: false,
                success: function(data) {
                    if( typeof counterIntermediate !== "undefined") { clearInterval(counterIntermediate); }
                    $('.intermediate-question-loader').hide();
                    $('.intermediate-question-loader').parent().removeClass('loading-screen-parent');
                    var obj = $.parseJSON(data);
                    if (obj.status == 1) {
                        if (obj.answerType == "select_from_dropdown_option") {
                            if (obj.systemCorrectAnswer == 1) {
                                $(".response_message_outer").addClass("correct");
                                $("#answerRightWrongMsg").text(obj.answerRightWrongMsg);
                                $(".dropdown-selection-order").addClass("correct");
                            } else {
                                $("#answerRightWrongMsg").text(obj.answerRightWrongMsg + ", Correct answer is given below");
                                $(".response_message_outer").addClass("incorrect");
                                $("#dropDownTypeSelection option[value='" + obj.systemCorrectOptionOrder + "']").attr("selected", "selected");
                                $("#dropDownSelection option[value=" + obj.systemCorrectOptionId + "]").attr("selected", "selected");
                                $(".dropdown-selection-order").addClass("correct");
                            }
                        } else {
                            $("#answerRightWrongMsg").text("Invalid answer type");
                        }
                        $('.saveIntMe').css('visibility', 'hidden');
                        $('.next-intermediate').show();
                    } else {
                        $("#showResponseMessage").text(obj.message);
                        var urlSet = obj.redirect;
                        //setTimeout("location.reload(true);", 3000);
                    }
                }
            });
        } else {
            $("#intermediateErrorGoneMsg").html('');
            $('.intermediate-question-loader').hide();
            $('.intermediate-question-loader').parent().removeClass('loading-screen-parent');
            $("html, body").animate({
                scrollTop: $('#intermediateErrorGoneMsg').offset().top 
            }, 300);
            $("#intermediateErrorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please select order and answer!</span></div></div></div>');
        }
    }

    function saveOptionReorderIntermediateAnswer() {
        $("#intermediateErrorGoneMsg").html('');
        $('.stopOnSubmit iframe').attr("src", jQuery(".stopOnSubmit iframe").attr("src"));
        var isAudio = $("#checkAudio").val();
        if(typeof isAudio !== "undefined"){
            var audioStop = document.getElementById('onOffAudio');
            audioStop.pause();
            $("#onOffAudio").prop('muted',true);
        }
        <?php if(Auth::guard('teenager')->user()->is_sound_on == 1){ ?>
            var audio = document.getElementById('audio_1');
            audio.play();
        <?php } ?>
        var countBox = 1;
        var arra_ans = [];
        var validCheckAll = 0;
        
        $('#sortable .ui-state-default').each(function() {
            var id_string = $(this).attr('id');
            arra_ans.push(id_string);
        });
        if (arra_ans.length < 1) {
            validCheckAll = 0;
        } else {
            validCheckAll = 1;
            $(".intermediate-time-tag").css('visibility', 'hidden');
        }

        if (validCheckAll === 1) {
            var form_data = $("#level4_intermediate_activity_ans").serializeArray();
            form_data.push({name: 'answer[0]', value: arra_ans});
            $('.intermediate-question-loader').parent().toggleClass('loading-screen-parent');
            $('.intermediate-question-loader').show();
            $("#setResponseIntermediate").val("1");
            $('.saveIntMe').css('visibility', 'hidden');
            
            $.ajax({
                type: 'POST',
                data: form_data,
                //async: false,
                dataType: 'html',
                url: "{{ url('/teenager/save-intermediate-level-activity')}}",
                headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' },
                cache: false,
                success: function(data) {
                    if( typeof counterIntermediate !== "undefined") { clearInterval(counterIntermediate); }
                    $('.intermediate-question-loader').hide();
                    $('.intermediate-question-loader').parent().removeClass('loading-screen-parent');
                    var obj = $.parseJSON(data);
                    if (obj.status == 1) {
                        if (obj.answerType == "option_reorder") {
                        	$("#answerRightWrongMsg").text(obj.answerRightWrongMsg + " Correct order is given below");
                            if (obj.systemCorrectAnswer == 1) {
                                $(".response_message_outer").addClass("correct");
                            } else {
                                $(".response_message_outer").addClass("incorrect");
                            }
                            if (obj.systemCorrectOptionOrder) {
                                var htmlLI = '';
                                $.each(obj.systemCorrectOptionOrder, function(key, value) {
                                    htmlLI += '<li class="ui-state-default right" id="' + value['optionId'] + '"><span class="sortable_outer_container"><span class="sortable_container"><span class="drag_me_text">' + value['optionText'] + '</span></span></span></li>';
                                });
                                $("#sortable").html(htmlLI);
                            } else {
                                $('.intermediate-question-loader').hide();
            					$('.intermediate-question-loader').parent().removeClass('loading-screen-parent');
            					$("#intermediateErrorGoneMsg").html('');
                                if ($("#intermediateErrorGoneMsg").hasClass('intermediateErrorGoneMsg')) {
                                    $("html, body").animate({
						                scrollTop: $('#intermediateErrorGoneMsg').offset().top 
						            }, 300);
						        }
						        $("#intermediateErrorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Refresh this page!</span></div></div></div>');
                            }
                        } else {
                            $("#answerRightWrongMsg").text("Invalid answer type");
                        }
                        $('.saveIntMe').css('visibility', 'hidden');
                        $('.next-intermediate').show();
                    } else {
                        $("#showResponseMessage").text(obj.message);
                        var urlSet = obj.redirect;
                    }
                }
            });
        } else {
            $("#intermediateErrorGoneMsg").html('');
            $('.intermediate-question-loader').hide();
            $('.intermediate-question-loader').parent().removeClass('loading-screen-parent');
            $("html, body").animate({
                scrollTop: $('#intermediateErrorGoneMsg').offset().top 
            }, 300);
            $("#intermediateErrorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please select order and answer!</span></div></div></div>');
        }
    }

    function saveDropDragIntermediateAnswer() {
        $("#intermediateErrorGoneMsg").html('');
        $('.stopOnSubmit iframe').attr("src", jQuery(".stopOnSubmit iframe").attr("src"));
        var isAudio = $("#checkAudio").val();
        if(typeof isAudio !== "undefined"){
            var audioStop = document.getElementById('onOffAudio');
            audioStop.pause();
            $("#onOffAudio").prop('muted',true);
        }
        <?php if(Auth::guard('teenager')->user()->is_sound_on == 1){ ?>
            var audio = document.getElementById('audio_1');
            audio.play();
        <?php } ?>
        var countBox = 1;
        var optionLength = $('#d_d_count').attr('value');
        var arra_ans = [];
        var validCheckAll = 0;
        
        $('.drp_section li').each(function() {
            if ($(this).find('img').length == 0) {
                //alert("Please fill box no:" + countBox);
                return false;
            } else {
                var ans_array = $(this).find('img').data('imageid');
                arra_ans.push(ans_array);
            }
            countBox++;
        });
        if (arra_ans.length < optionLength) {
            validCheckAll = 0;
        } else {
            validCheckAll = 1;
            $(".intermediate-time-tag").css('visibility', 'hidden');
        }

        if (validCheckAll === 1) {
            var form_data = $("#level4_intermediate_activity_ans").serializeArray();
            form_data.push({name: 'answer[0]', value: arra_ans});
            $('.intermediate-question-loader').parent().toggleClass('loading-screen-parent');
            $('.intermediate-question-loader').show();
            $("#setResponseIntermediate").val("1");
            $('.saveIntMe').css('visibility', 'hidden');
            $.ajax({
                type : 'POST',
                data : form_data,
                //async: false,
                dataType: 'html',
                url: "{{ url('/teenager/save-intermediate-level-activity')}}",
                headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' },
                cache: false,
                success: function(data) {
                    if( typeof counterIntermediate !== "undefined") { clearInterval(counterIntermediate); }
                    $('.intermediate-question-loader').hide();
                    $('.intermediate-question-loader').parent().removeClass('loading-screen-parent');
                    var obj = $.parseJSON(data);
                    if (obj.status == 1) {
                        if (obj.answerType == "image_reorder") {
                            $("#answerRightWrongMsg").text(obj.answerRightWrongMsg);
                            if (obj.systemCorrectAnswer == 1) {
                                $(".response_message_outer").addClass("correct");
                            } else {
                                $(".response_message_outer").addClass("incorrect");
                            }
                        } else {
                            $("#answerRightWrongMsg").text("Invalid answer type");
                        }
                        $('.saveIntMe').css('visibility', 'hidden');
                        $('.next-intermediate').show();
                    } else {
                        $("#showResponseMessage").text(obj.message);
                        var urlSet = obj.redirect;
                    }
                }
            });
        } else {
            $("#intermediateErrorGoneMsg").html('');
            $('.intermediate-question-loader').hide();
            $('.intermediate-question-loader').parent().removeClass('loading-screen-parent');
            $("html, body").animate({
                scrollTop: $('#intermediateErrorGoneMsg').offset().top 
            }, 300);
            $("#intermediateErrorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please fill boxes!</span></div></div></div>');
        }
    }

    function autoSubmitIntermediateAnswer() {
        if ($("#setResponseIntermediate").val() == 0) {
            if (ansTypeSet == "option_choice_with_response" || ansTypeSet == "select_from_dropdown_option" || ansTypeSet == "single_line_answer" || ansTypeSet == "filling_blank" || ansTypeSet == "option_choice" || ansTypeSet == "image_reorder" || ansTypeSet == "option_reorder" || ansTypeSet == "true_false") {
                var intermediateTimer2 = 0;
                $("#setResponseIntermediate").val("1");           
                $("#intermediateErrorGoneMsg").html('');
                var questionID = $("#questionID").val();
                var answerId = 0;
                var answer_order = 0;
                var form_data = 'questionID=' + questionID + '&answer[0]=' + answerId + '&timer=' + intermediateTimer2 + '&answer_order[0]=' + answer_order;
                
                $('.intermediate-question-loader').parent().toggleClass('loading-screen-parent');
                $('.intermediate-question-loader').show();
            
                $(".intermediate-time-tag").css('visibility', 'hidden');
                $.ajax({
                    type: 'POST',
                    data: form_data,
                    dataType: 'html',
                    url: "{{ url('/teenager/save-intermediate-level-activity')}}",
                    headers: { 'X-CSRF-TOKEN': '{{csrf_token()}}' },
                    cache: false,
                    success: function(data) {
                        if( typeof counterIntermediate !== "undefined") { clearInterval(counterIntermediate); }
                        $('.intermediate-question-loader').hide();
                        $('.intermediate-question-loader').parent().removeClass('loading-screen-parent');
                        
                        var obj = $.parseJSON(data);
                        
                        if (obj.status == 1) {
                            if (obj.answerType == "single_line_answer") {
                                $("#answerRightWrongMsg").text(obj.answerRightWrongMsg + ". Correct Answer Is : " + obj.systemCorrectAnswerText + "");
                                if (obj.systemCorrectAnswer == 1) {
                                    $(".response_message_outer").addClass("correct");
                                } else {
                                    $(".response_message_outer").addClass("incorrect");
                                }
                            } else if (obj.answerType === "option_choice_with_response" || obj.answerType === "filling_blank" || obj.answerType === "option_choice" || obj.answerType === "true_false" || obj.answerType === "image_reorder") {
                                $("#answerRightWrongMsg").text(obj.answerRightWrongMsg);
                                if (obj.systemCorrectAnswer == 1) {
                                    $(".response_message_outer").addClass("correct");
                                } else {
                                    $(".response_message_outer").addClass("incorrect");
                                }
                                if(obj.systemCorrectAnswer2){
                                    $.each(obj.systemCorrectAnswer2, function(key, value) {
                                        if (value == 1) {
                                            $('.class' + key).addClass("correct");
                                        } else {
                                            $('.class' + key).addClass("incorrect");
                                        }
                                    });
                                }
                                if (obj.answerType === "option_choice") {
                                    if (obj.questionAnswerText !== '') {
                                        var phtml = "<p>" + obj.questionAnswerText + "</p>";
                                        $('#showResponseMessage').html(phtml);
                                    }
                                }
                                if (obj.answerType === "option_choice_with_response") {
                                    if (obj.questionAnswerText && obj.questionAnswerText !== '') {
                                        var phtmlImg = '';
                                        if (obj.questionAnswerImage && obj.questionAnswerImage !== '') {
                                            phtmlImg = "<img src=" + obj.questionAnswerImage + " />";
                                        }
                                        var phtml = "<div class='t-table'><div class='t-cell'>" + phtmlImg + "</div><div class='t-cell'><p>" + obj.questionAnswerText + "</p></div></div>";
                                        $('#showResponseMessage').html(phtml);
                                    }
                                }
                                if (obj.answerType == "image_reorder") {
                                    //$("#showResponseMessage").text(obj.answerRightWrongMsg);
                                    if (obj.systemCorrectAnswer == 1) {
                                        $(".response_message_outer").addClass("correct");
                                    } else {
                                        $(".response_message_outer").addClass("incorrect");
                                    }
                                }
                            } else if (obj.answerType == "select_from_dropdown_option") {
                                if (obj.systemCorrectAnswer == 1) {
                                    $(".response_message_outer").addClass("correct");
                                    $("#answerRightWrongMsg").text(obj.answerRightWrongMsg);
                                    $(".dropdown-selection-order").addClass("correct");
                                } else {
                                    $("#answerRightWrongMsg").text(obj.answerRightWrongMsg + ", Correct answer is given below");
                                    $(".response_message_outer").addClass("incorrect");
                                    $("#dropDownTypeSelection option[value='" + obj.systemCorrectOptionOrder + "']").attr("selected", "selected");
                                    $("#dropDownSelection option[value=" + obj.systemCorrectOptionId + "]").attr("selected", "selected");
                                    $(".dropdown-selection-order").addClass("correct");
                                }
                            } else if (obj.answerType == "option_reorder") {
                                $("#answerRightWrongMsg").text(obj.answerRightWrongMsg + " Correct order is given below");
                                if (obj.systemCorrectAnswer == 1) {
                                    $(".response_message_outer").addClass("correct");
                                } else {
                                    $(".response_message_outer").addClass("incorrect");
                                }
                                if (obj.systemCorrectOptionOrder) {
                                    var htmlLI = '';
                                    $.each(obj.systemCorrectOptionOrder, function(key, value) {
                                        htmlLI += '<li class="ui-state-default right" id="' + value['optionId'] + '"><span class="sortable_outer_container"><span class="sortable_container"><span class="drag_me_text">' + value['optionText'] + '</span></span></span></li>';
                                    });
                                    $("#sortable").html(htmlLI);
                                } else {
                                    alert("Refresh the page");
                                }
                            } else {
                            }
                            $('.saveIntMe').css('visibility', 'hidden');
                            $('.next-intermediate').show();
                            //setTimeout("location.reload(true);", 2000);
                        } else {
                            $("#showResponseMessage").text(obj.message);
                            var urlSet = obj.redirect;
                        }
                    }
                });
            }
        }
    }

    $(document).on('change', '.selectionCheck', function(evt) { 
        if (limitSelect > 1) {
            if ($('input.multiCast:checked').length > limitSelect) {
                this.checked = false;
                
                if($("#basicErrorGoneMsg").hasClass("basicErrorGoneMsg")) {
                    $("#basicErrorGoneMsg").html('');
                    $("html, body").animate({
                        scrollTop: $('#basicErrorGoneMsg').offset().top 
                    }, 300);
                    $("#basicErrorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">You can select maximum '+ limitSelect +' options</span></div></div></div>');
                }

                if($("#intermediateErrorGoneMsg").hasClass("intermediateErrorGoneMsg")) {
                    $("#intermediateErrorGoneMsg").html('');
                    $("html, body").animate({
                        scrollTop: $('#intermediateErrorGoneMsg').offset().top 
                    }, 300);
                    $("#intermediateErrorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">You can select maximum '+ limitSelect +' options</span></div></div></div>');
                }
            }
        }    
    });

    if ($('.competitive-sec').children().length > 2 ) {
            $('.competitive-sec').owlCarousel({
                loop: true,
                margin: 10,
                items: 2,
                nav: false,
                dots: true,
                smartSpeed: 500,
                autoplay:false,
                autoHeight: true,
                responsive : {
                    0: {
                        items: 1
                    },
                    768: {
                        items: 2
                    },
                }
            });
    }

    function applyForScholarshipProgram(activityId)
    {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'activityId=' + activityId + '&professionId=' + '{{$professionsData->id}}';
        $.ajax({
            url : '{{ url("teenager/apply-for-scholarship-program") }}',
            method : "POST",
            data: form_data,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            success : function (response) {
                if (response.status != 0) {
                    if (response.message == 'applied') {
                        $("#scholarship_message_"+activityId).text("You have already applied for this program");
                    } else {
                        $("#scholarship_message_"+activityId).text("You successfully applied for this scholarship program");
                        $("#apply_"+activityId).prop('onclick',null).off('click');
                    }
                    $("#scholarship_message_"+activityId).show();
                    setTimeout(function () {
                        $("#scholarship_message_"+activityId).hide();
                    }, 2500)
                    $("#apply_"+activityId).text("Applied");
                    $("#apply_"+activityId).attr("disabled","disabled");
                } else {
                    $(".competitive-div").addClass('alert-error danger');
                    $(".competitive-div").show();
                    $("#competitive-message").text(response.message);
                    setTimeout(function () {
                        $(".competitive-div").hide();
                    }, 5000);
                }
            }
        });
    }

    function getCoinsConsumptionDetails(consumedCoins, componentName, activityRemainingDays) {
        var teenagerCoins = parseInt("{{Auth::guard('teenager')->user()->t_coins}}");
        var consumeCoins = parseInt(consumedCoins);
        if (parseInt(activityRemainingDays) > 0) { 
            $("#activity_coins").html('<span class="coins"></span>' + activityRemainingDays + " days left");
        } else { 
            if (consumeCoins > teenagerCoins) {
                $("#activity_buy").show();
                $("#activity_title").text("Notification!");
                $("#activity_message").text("You don't have enough ProCoins. Please Buy more.");
            } else {
                $("#activity_consume_coin").show();
                $("#activity_title").text("Congratulations!");
                $("#activity_message").text("You have " + format(teenagerCoins) + " ProCoins available.");
                $("#activity_sub_message").text("Click OK to consume your " + format(consumeCoins) + " ProCoins and play on");
                $("#activity_coins").val(consumedCoins);
                $("#activity_name").val(componentName);
            }
            $('#coinsConsumption').modal('show');
        } 
    }

    function format(x) {
        return isNaN(x)?"":x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function saveConsumedCoins() {
        
        var consumedCoins = $("#activity_coins").val();
        var componentName = $("#activity_name").val();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        if (componentName == "{{Config::get('constant.INSTITUTE_FINDER')}}") {
            var professionId = 0;
        } else {
            var professionId = '{{$professionsData->id}}';
        }
        var form_data = "consumedCoins=" + consumedCoins + "&componentName=" + componentName + "&professionId=" + professionId;
        $.ajax({
            type: 'POST',
            data: form_data,
            url: "{{ url('/teenager/save-consumed-coins-details') }}",
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            cache: false,
            success: function(response) {
                if (response > 0) {
                    if (componentName == "{{Config::get('constant.ADVANCE_ACTIVITY')}}") {
                        $(".activity_coins").html('<span class="coins"></span> ' + response + " days left");  
                        $(".panel-heading a").attr("data-toggle", "collapse");
                        $("#advanced_unbox").html("See Now!");
                        $("#advanced_unbox").addClass("open_advance_view");
                        $("#activity_unbox").prop('onclick',null).off('click');
                    } else if (componentName == "{{Config::get('constant.INSTITUTE_FINDER')}}") {
                        $(".institute_coins").html('<span class="coins"></span> ' + response + " days left");  
                        $("#institute_activity").prop('onclick',null).off('click');
                        $("#institute_activity").attr('href', '{{url("teenager/institute") }}');
                        $(".institute_list li").removeClass('disabled');
                    } else {
                        $(".promise-plus-coins").html('<span class="coins"></span> ' + response + " days left");  
                        $("#promise_plus").prop('onclick',null).off('click');
                        $("#promisespan").text('See Now!');
                        getPromisePlusData({{$professionsData->id}});
                    }
                } else {
                    if (componentName == "{{Config::get('constant.ADVANCE_ACTIVITY')}}") {
                        $(".activity_coins").html('<span class="coins"></span> ' + consumedCoins);
                    } else if (componentName == "{{Config::get('constant.INSTITUTE_FINDER')}}") {
                        $(".institute_coins").html('<span class="coins"></span> ' + consumedCoins);  
                    } else {
                        $(".promise-plus-coins").html('<span class="coins"></span> ' + consumedCoins);
                        $("#promisespan").text('Unlock Me');
                    }
                }
            }
        });
    }

    function challengeToParentAndMentor() {
        var parent = $("#listParent").val();
        if (parent === "") {
            $(".challenge_message").text("Please select at least one parent or mentor from list");
            return false;
        } else {
            $(".challenge_message").text("");
            $("#parentChallenge").toggleClass('sending').blur();
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var form_data = "parentId=" + parent + "&professionId=" + "{{$professionsData->id}}";
            $.ajax({
                type: 'POST',
                data: form_data,
                url: "{{ url('/teenager/challenge-to-parent-and-mentor') }}",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                cache: false,
                success: function(response) {
                    $("#parentChallenge").removeClass('sending').blur();
                    $("#challenge-text").show();
                    $("#challenge-text").text(response.message);
                    setTimeout(function () {
                        $("#challenge-text").hide();
                    }, 2500);
                    getChallengedParentAndMentorList();
                }
            });
        } 
    }
    
    $('.mentor-list ul').owlCarousel({
        loop: false,
        margin: 0,
        items: 4,
        autoplay: false,
        autoplayTimeout: 3000,
        smartSpeed: 1000,
        nav: false,
        dots: true,
        responsive: {
            0: {
                items: 2
            },
            768: {
                items: 4
            },
        }
    });

    function getChallengedParentAndMentorList() {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = "professionId=" + "{{$professionsData->id}}";
        $.ajax({
            type: 'POST',
            data: form_data,
            url: "{{ url('/teenager/get-challenged-parent-and-mentor-list') }}",
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            cache: false,
            success: function(response) {
                $(".form-challenge").html(response);
                $(".mentor-list ul").owlCarousel();
            }
        });
    }

    function getChallengeScoreDetails(parentId) {
        $("#"+parentId).addClass('deactive');
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = "parentId=" + parentId + "&professionId=" + "{{$professionsData->id}}";
        $.ajax({
            url: "{{ url('/teenager/get-challenge-score-details') }}",
            type: 'POST',
            data: form_data,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            cache: false,
            success: function(response) {
                $("#"+parentId).removeClass('deactive');
                $('#scoreModal').html(response);
                $('#scoreModal').modal('show');
                //$('.ajax-loader').hide();
            }
        });
    }

    function getQuestionDataAdvanceLevel(activityType) {
        $(".quiz-advanced").append('<div id="advance_quiz_loader" class="loading-screen loading-wrapper-sub"><div id="loading-content"></div></div>');
        $('#advance_quiz_loader').parent().addClass('loading-screen-parent');
        $('#advance_quiz_loader').show();
        $.ajax({
            url: "{{ url('teenager/get-question-data-advance-level') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "activityType": activityType,
                "professionId": '{{$professionsData->id}}'
            },
            success: function(response) {
                $('.quiz-advanced').html(response);
                $('#advance_quiz_loader').hide();
                $('#advance_quiz_loader').parent().removeClass('loading-screen-parent');
            }
        });
    }

    function getMediaUploadSection() {
        $(".quiz-advanced").append('<div id="advance_quiz_loader" class="loading-screen loading-wrapper-sub"><div id="loading-content"></div></div>');
        $('#advance_quiz_loader').parent().addClass('loading-screen-parent');
        $('#advance_quiz_loader').show();
        $.ajax({
            url: "{{ url('teenager/get-media-upload-section') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
            },
            success: function(response) {
                $('.quiz-advanced').html(response);
                $('#advance_quiz_loader').hide();
                $('#advance_quiz_loader').parent().removeClass('loading-screen-parent');
            }
        });
    }

    function getLevel4AdvanceStep2Details(professionId, type) {
        $(".quiz-advanced").append('<div id="advance_quiz_loader" class="loading-screen loading-wrapper-sub"><div id="loading-content"></div></div>');
        $('#advance_quiz_loader').parent().addClass('loading-screen-parent');
        $('#advance_quiz_loader').show();
        $.ajax({
            url: "{{ url('teenager/get-level4-advance-step2-details') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "professionId": professionId,
                "type": type
            },
            success: function(response) {
                if (response.status == 0) {
                    $(".l4-advance-div").show();
                    if ($(".l4-advance-div").hasClass('l4-advance-div-demo')) {
                        $("html, body").animate({
                            scrollTop: $('.l4-advance-div-demo').offset().top 
                        }, 300);
                    }
                    $(".l4-advance-div").addClass('alert-error danger');
                    $("#l4AdvanceMessage").text(response.message);
                    getMediaUploadSection();
                } else {
                    $('.quiz-advanced').html(response);
                }
                setTimeout(function () {
                    $(".l4-advance-div").hide();
                }, 5000);
                $('#advance_quiz_loader').hide();
                $('#advance_quiz_loader').parent().removeClass('loading-screen-parent');
            }
        });
    }

    function readURL(input_file) {
        var taskType = $("#activityTasks li.active").attr('id');
        var tabData = $("#activityTasks li.active a").attr('href');
        if (input_file.files && input_file.files[0]) {
            $("#mediaErr").text('');
            $("#taskSave").removeAttr('disabled');
            var formData = $(tabData + " .add_advance_task");
            var reader = new FileReader();
            reader.onload = function(e) {
                var fileType = input_file.files[0];
                if (taskType == 3) {
                    if (fileType.type == 'image/jpeg' || fileType.type == 'image/jpg' || fileType.type == 'image/png' || fileType.type == 'image/bmp') {
                        if (input_file.files[0].size > 6000000) {
                            formData.find("[id='mediaErr']").text("Maximum File Upload size is 6MB");
                            formData.find("[id='taskSave']").attr('disabled', 'disabled');
                            formData.find("#file-input").val('');
                        }else{
                            formData.find("[id='mediaErr']").text(fileType.name);
                        }
                    } else {
                        formData.find("[id='mediaErr']").text("File type not allowed");
                        formData.find("[id='taskSave']").attr('disabled', 'disabled');
                        formData.find("#file-input").val('');
                    }
                } else if (taskType == 2) {
                    if (fileType.type == 'application/vnd.openxmlformats-officedocument.presentationml.presentation' || fileType.type == 'application/pdf' || fileType.type == 'application/msword' || fileType.type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || fileType.type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || fileType.type == 'application/vnd.ms-powerpoint') {
                        if (input_file.files[0].size > 6000000) {
                            formData.find("[id='mediaErr']").text("Maximum File Upload size is 6MB");
                            formData.find("[id='taskSave']").attr('disabled', 'disabled');
                            formData.find("#file-input").val('');
                        }else{
                            formData.find("[id='mediaErr']").text(fileType.name);
                        }
                    } else {
                        formData.find("[id='mediaErr']").text("File type not allowed");
                        formData.find("[id='taskSave']").attr('disabled', 'disabled');
                        formData.find("#file-input").val('');
                    }
                } else if (taskType == 1) {
                    if (fileType.type == 'video/mp4' || fileType.type == 'video/x-m4a' || fileType.type == 'video/3gpp' || fileType.type == 'video/mkv' || fileType.type == 'video/avi' || fileType.type == 'video/flv' || fileType.type == 'video/quicktime'){
                        if (input_file.files[0].size > 6000000) {
                            formData.find("[id='mediaErr']").text("Maximum File Upload size is 6MB");
                            formData.find("[id='taskSave']").attr('disabled', 'disabled');
                            formData.find("#file-input").val('');
                        }else{
                            formData.find("[id='mediaErr']").text(fileType.name);
                        }
                    }else{
                        formData.find("[id='mediaErr']").text("File type not allowed");
                        formData.find("[id='taskSave']").attr('disabled', 'disabled');
                        formData.find("#file-input").val('');
                    }
                } else {
                    formData.find("[id='mediaErr']").text("File type not allowed");
                    formData.find("[id='taskSave']").attr('disabled', 'disabled');
                    formData.find("#file-input").val('');
                }
            };
            reader.readAsDataURL(input_file.files[0]);
        }
    }

    $(document).on('submit','.add_advance_task', function(){
        var clikedForm = $(this); // Select Form
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        if (clikedForm.find("[name='media']").val() == '') {
            clikedForm.find("[id='mediaErr']").text("Please upload appropriate media file");
            return false;
        } else {
            clikedForm.find("[id='taskSave']").toggleClass('sending').blur();
            $.ajax({
                url: "{{ url('teenager/submit-level4-advance-activity') }}",
                type: "POST",
                data: new FormData(this),
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    clikedForm.find("[id='taskSave']").removeClass('sending').blur();
                    if (data.status == 1) {
                        var taskType = $("#activityTasks li.active").attr('id');
                        getLevel4AdvanceStep2Details('{{$professionsData->id}}', taskType);
                    } 
                    clikedForm.find("[id='mediaErr']").html(data.message).fadeIn();
                },
                error: function(e)
                {
                    clikedForm.find("[id='taskSave']").removeClass('sending').blur();
                    clikedForm.find("[id='mediaErr']").html(e).fadeIn();
                }
            });
            return false;
        }
    });

    $(document).on('submit','.advance_task_review',function(){
        var clikedForm = $(this); // Select Form
        clikedForm.find("[id='mediaSubmit']").toggleClass('sending').blur();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: "{{ url('teenager/submit-level4-advance-activity-for-review') }}",
            type: "POST",
            data: new FormData(this),
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                clikedForm.find("[id='mediaSubmit']").removeClass('sending').blur();
                if (response.status != 0) {
                    $(".l4-advance-div").addClass('alert-success');
                } else {
                    $(".l4-advance-div").addClass('alert-error danger');
                }
                $(".l4-advance-div").show();
                if ($(".l4-advance-div").hasClass('l4-advance-div-demo')) {
                    $("html, body").animate({
                        scrollTop: $('.l4-advance-div-demo').offset().top 
                    }, 300);
                }
                $("#l4AdvanceMessage").text(response.message);
                getLevel4AdvanceStep2Details('{{$professionsData->id}}', response.mediaType);
                setTimeout(function () {
                    $(".l4-advance-div").hide();
                }, 5000);
            }
        });
        return false;
    });

    function deleteLevel4AdvanceTaskUser(mediaId, mediaName, mediaType) {
        resdelete = confirm('Are you sure you want to delete this record?');
        if (resdelete) {
            $.ajax({
                url: "{{ url('teenager/delete-user-advance-task') }}",
                type: 'post',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "taskId": mediaId,
                    "mediaName": mediaName,
                    "mediaType": mediaType
                },
                success: function(response) {
                    if (response.status != 0) {
                        $(".l4-advance-div").addClass('alert-success');
                    } else {
                        $(".l4-advance-div").addClass('alert-error danger');
                    }
                    $(".l4-advance-div").show();
                    if ($(".l4-advance-div").hasClass('l4-advance-div-demo')) {
                        $("html, body").animate({
                            scrollTop: $('.l4-advance-div-demo').offset().top 
                        }, 300);
                    }
                    $("#l4AdvanceMessage").text(response.message);
                    getLevel4AdvanceStep2Details('{{$professionsData->id}}', mediaType);
                    setTimeout(function () {
                        $(".l4-advance-div").hide();
                    }, 5000);
                }
            });
        } else {
            return false;
        }
    }

    function viewImage(taskId) {
        var modal = document.getElementById('l4advanceImage');
        // Get the image and insert it inside the modal - use its "alt" text as a caption
        var imgSrc = $('.l4advance'+taskId).attr('src');
        var imgAlt = $('.l4advance'+taskId).attr('alt');
        var modalImg = $("#img01");
        var captionText = document.getElementById("caption");
        //$('.myImg').click(function() {
            modal.style.display = "block";
            //var newSrc = img;
            modalImg.attr('src', imgSrc);
            captionText.innerHTML = imgAlt;
        //});
    }

    $(document).on("click", '.close-modal', function(event) { 
        var modal = document.getElementById('l4advanceImage');
        modal.style.display = "none";
    });

    var slotCount = 1;
    $(document).on('click','#load-more-leaderboard', function(){
        $("#menu3 .loader_con").show();
        var slot = slotCount++;
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'slot=' + slot + '&professionId=' + '{{$professionsData->id}}';
        $.ajax({
            url : '{{ url("teenager/load-more-leaderboard-teenagers") }}',
            method : "POST",
            data: form_data,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            dataType : "text",
            success : function (data) {
                $("#menu3 .loader_con").hide();
                if(data != '') {
                    $('#menu3 .remove-row').remove();
                    $('#menu3').append(data);
                } 
            }
        });
    });

    //get promise plus data 
    function getPromisePlusData(professionId)
    { 
        $('#promisespan').addClass('sending');
        $.ajax({
            url: "{{ url('teenager/get-teen-profession-promiseplus') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                'professionId':professionId
            },
            success: function(response) {               
                $('#showPromisePlusData').html(response);
                $('.promise-plus-overlay').show();              
                $('#hidepromiseplus').hide(); 
                $('#promisespan').removeClass('sending');
            }
        });
    }
    
    function hidePromisePlusModal()
    {
        $('.promise-plus-overlay').hide();              
        $('#hidepromiseplus').show();  
    }
    
    //get profession competitors data
    function getUserProfessionCompetitor(professionId)
    {
        $.ajax({
            url: "{{ url('teenager/get-teen-profession-competitor') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                'professionId':professionId
            },
            success: function(response) {               
                $('#load-user-profession-competitor').html(response);
            }
        });
    }
    
    $(window).on("load", function(e) {
        e.preventDefault();
        getChallengedParentAndMentorList("{{Auth::guard('teenager')->user()->id}}");
        getUserProfessionCompetitor({{$professionsData->id}});   
        getPageAdsDetail();
        getQuestionTemplateForProfession({{$professionsData->id}});
        $(document).on( "click", ".open_advance_view", function() {
           $("#accordion1").collapse('show');
        })
        getProfessionCompletionPercentage({{$professionsData->id}});
    });

    function adjusting_box_size() {
        var col_width = $('.drg_section').width();
        var finale_width = col_width / col_count;
        $('.drag_drp li').height(finale_width).width(finale_width - 4);
    }
    
    function getTemplateConceptData(professionId, templateId) {
        $.ajax({
            url: "{{ url('/teenager/get-coins-for-template') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}',
                "professionId": professionId,
                "templateId": templateId
            },
            success: function(response) {
                if (response.status == 1 ) {
                    $("#myModal"+templateId).modal('show');
                    if(response.return == 1) {
                        $("#myModal"+templateId+" .my-coins-info").text("You have "+response.coins+" ProCoins available.");
                    } else {
                        $("#myModal"+templateId+" .modal-title").text("Notification!");
                        $("#myModal"+templateId+" .no-coins-availibility").html("<div class='modal-body'><p>You don\'t have enough ProCoins. Please Buy more.</p></div><div class='modal-footer'><button type='button' class='btn btn-primary' onclick=\" location.href = '{{url('/teenager/buy-procoins')}}' \" title='Buy Coins'>Buy Coins</button><button type='button' class='btn btn-primary' data-dismiss='modal'>Close</button></div>");
                    }
                } else {
                    $("#myModal"+templateId).modal('show');
                    $("#myModal"+templateId+" .modal-title").text("Authentication Failed!");
                    $("#myModal"+templateId+" .no-coins-availibility").html("<div class='modal-body'><p>"+response.message+"</p></div>");
                    location.href = response.redirect;
                }
            }
        });
    }

    function saveCoinsForTemplateData(professionId, templateId, attempted) {
        $.ajax({
            url: "{{ url('/teenager/save-coins-for-template-data') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}',
                "professionId": professionId,
                "templateId": templateId,
                "attempted":attempted
            },
            success: function(response) {
                if(response.status == 1) {
                    getConceptData(templateId);
                    if(typeof response.remainingDays !== "undefined" && response.remainingDays > 0) {
                        $(".set-template-"+templateId).html("<a href='javascript:void(0);' title='Play now!' class='btn-primary' onclick='getConceptData("+templateId+")' ><span class='unbox-me'>Play now!</span><span class='coins-outer'><span class='coins'></span> "+ response.remainingDays+" Days Left</span></a>");
                    }
                } else {
                    location.href = "{{url('/')}}";
                }
            }
        });
    }

    function getPageAdsDetail() {
        $.ajax({
            url: "{{ url('/teenager/get-career-detail-ads') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}'
            },
            success: function(response) {
                $("#bannerAdsSection").html(response.bannerAds);
                $("#sidebarAdsSection").html(response.sidebarAds);
                $('.ad-slider').owlCarousel({
                    loop: true,
                    margin: 10,
                    items: 1,
                    nav: false,
                    dots: false,
                    smartSpeed: 500,
                    autoplay:true,
                });
            }
        });
    }

    function getQuestionTemplateForProfession(professionId) {
        $('.intermediate-first-question-loader').parent().toggleClass('loading-screen-parent');
        $('.intermediate-first-question-loader').show();
        
        $.ajax({
            url: "{{ url('/teenager/get-intermediate-profession-template') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}',
                "professionId" : professionId
            },
            success: function(response) {
                $("#intermediateTemplateList").html(response);
                $('.intermediate-first-question-loader').hide();
                $('.intermediate-first-question-loader').parent().removeClass('loading-screen-parent');
            }
        });   
    }

    function getProfessionCompletionPercentage(professionId) {
        $.ajax({
            url: "{{ url('/teenager/get-profession-completion-percentage') }}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}',
                "professionId" : professionId
            },
            success: function(response) {
                if (response.status != 0) {
                    if (response.percentage >= 100) {
                        $("#competitiveSection").remove();
                    }
                    $(".tab-complete").html(response.percentage+'% Complete');
                }
            }
        });   
    }
</script>

@stop