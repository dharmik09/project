@extends('layouts.teenager-master')

@push('script-header')
    <title>Careers</title>
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
                    <img src="{{Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$professionsData->pf_logo)}}">
                    <div>
                        <div class="play-icon"><a href="javascript:void(0);" class="play-btn" id="iframe-video-click"><img src="{{ Storage::url('img/play-icon.png') }}" alt="play icon"></a></div>
                    </div>
                    <?php $videoCode = Helpers::youtube_id_from_url($professionsData->pf_video);?>
                    @if($videoCode == '')
              
                    <video id="dropbox_video_player" poster="{{Storage::url(Config::get('constant.PROFESSION_ORIGINAL_IMAGE_UPLOAD_PATH').$professionsData->pf_logo)}}" oncontextmenu="return false;"  controls style="width: 100%;min-width: 100%;">
                            <!-- MP4 must be first for iPad! -->
                            <source src="{{$professionsData->pf_video}}" type="video/mp4"  /><!-- Safari / iOS, IE9 -->  
                            Your browser does not support HTML5 video.
                    </video>
               
                    @else
                    <iframe width="100%" height="100%" src="https://www.youtube.com/embed/{{Helpers::youtube_id_from_url($professionsData->pf_video)}}?autohide=1&amp;showinfo=0&amp;modestBranding=1&amp;start=0&amp;rel=0&amp;enablejsapi=1" frameborder="0" allowfullscreen id="iframe-video"></iframe>
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
                                            <h4><?php echo (isset($average_per_year_salary->pfic_content) && !empty($average_per_year_salary->pfic_content)) ? $average_per_year_salary->pfic_content : '' ?></h4>
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
                                            <h4><?php echo (isset($work_hours_per_week->pfic_content) && !empty($work_hours_per_week->pfic_content)) ? $work_hours_per_week->pfic_content : '' ?></h4>
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
                                            <h4><?php echo (isset($positions_current->pfic_content) && !empty($positions_current->pfic_content)) ? $positions_current->pfic_content : '' ?></h4>
                                            <p>Employment 2017</p>
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
                                            <h4><?php echo (isset($positions_projected->pfic_content) && !empty($positions_projected->pfic_content)) ? $positions_projected->pfic_content : '' ?></h4>
                                            <p>Projected for 2026</p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="description">
                            <div class="heading">
                                <h4>{{$professionsData->pf_name}}</h4>
                                <div class="list-icon"><span><a id="add-to-star" href="javascript:void(0)" title="Like" class="<?php echo (count($professionsData->starRatedProfession)>0) ? "favourite-career" : '' ?>"><i class="icon-star"></i></a><div class="favourite-text">Career has been selected as favourite</div></span><span><a href="#" title="print"><i class="icon-print"></i></a></span></div>
                            </div>
                            <?php
                                $profession_description = $professionsData->professionHeaders->filter(function($item) {
                                    return $item->pfic_title == 'profession_description';
                                })->first();
                            ?>
                            <p><?php echo (isset($profession_description->pfic_content) && !empty($profession_description->pfic_content)) ? $profession_description->pfic_content : '' ?></p>
                        </div>
                        <div class="career-detail-tab bg-white">
                            <ul class="nav nav-tabs custom-tab-container clearfix bg-offwhite">
                                <li class="active custom-tab col-xs-6 tab-color-1"><a data-toggle="tab" href="#menu1"><span class="dt"><span class="dtc">Career Details</span></span></a></li>
                                <li class="custom-tab col-xs-6 tab-color-2"><a data-toggle="tab" href="#menu2"><span class="dt"><span class="dtc">Explore <span class="tab-complete">21% Complete</span></span></span></a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="menu1" class="tab-pane fade in active">
                                    @include('teenager/basic/careerDetailInfoSection')
                                </div>
                                <div id="menu2" class="tab-pane fade in ">
                                    <!-- Section for booster scale --> 
                                    <div class="explore-table table-responsive">
                                        @include('teenager/basic/careerBoosterScaleSection')
                                    </div>
                                    <!-- Section for promise plus --> 
                                    <div class="promise-plus-outer">
                                        @include('teenager/basic/careerPromisePlusSection')
                                    </div>
                                    <!-- Section start with virtual play role --> 
                                    <div class="virtual-plus text-center">
                                        <h4><span>Virtual Role Play</span></h4>
                                        <p>Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                    </div>
                                    <!-- Section for basic, intermediate quiz with seprate blade --> 
                                    <div class="quiz-sec ">
                                        <div class="row flex-container">
                                            @include('teenager/basic/careerBasicQuizSection')
                                        </div>
                                        <div class="row flex-container">
                                            @include('teenager/basic/careerIntermediateQuizSection')
                                        </div>
                                    </div>  
                                    <!-- Section for real world --> 
                                    <div class="virtual-plus text-center real-world">
                                        <h4><span>Real-world role Play</span></h4>
                                        <p>Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                    </div>
                                    <!-- Section for advance level -->
                                    <div class="quiz-advanced quiz-sec">
                                        @include('teenager/basic/careerAdvanceQuizSection')
                                    </div>
                                    <!-- Section for competitive role play -->
                                    <div class="virtual-plus text-center competitive-role">
                                        <h4><span>competitive role Play</span></h4>
                                        <p>Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
                                        <div class="competitive-list quiz-sec">
                                            @include('teenager/basic/careerCompetitiveRoleSection')        
                                        </div>
                                    </div>
                                    <!-- Section for challenge play -->
                                    <div class="virtual-plus text-center challenge-play">
                                        <h4><span>challenge Play</span></h4>
                                        <p>Instructions: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor. Nam nec placerat nunc. Suspendisse scelerisque luctus libero, ut tincidunt mi. Fusce quis tincidunt justo, at bibendum lorem.</p>
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
                                        <div class="team-list">
                                            <div class="flex-item">
                                                <div class="team-detail">
                                                    <div class="team-img">
                                                        <img src="{{ Storage::url('img/ellen.jpg') }}" alt="team">
                                                    </div>
                                                    <a href="#" title="Ellen Ripley"> Ellen Ripley</a>
                                                </div>
                                            </div>
                                            <div class="flex-item">
                                                <div class="team-point">
                                                    520,000 points
                                                    <a href="#" title="Chat">
                                                        <i class="icon-chat">
                                                            <!-- -->
                                                        </i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="team-list">
                                            <div class="flex-item">
                                                <div class="team-detail">
                                                    <div class="team-img">
                                                        <img src="{{ Storage::url('img/alex.jpg') }}" alt="team">
                                                    </div>
                                                    <a href="#" title="Alex Murphy">Alex Murphy</a>
                                                </div>
                                            </div>
                                            <div class="flex-item">
                                                <div class="team-point">
                                                    515,000 points
                                                    <a href="#" title="Chat">
                                                        <i class="icon-chat">
                                                            <!-- -->
                                                        </i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-center"><a href="#" title="load more" class="load-more">load more</a></p>
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
                        <div class="ad-sec-h">
                            <div class="t-table">
                                <div class="table-cell">
                                    Ad 850 x 90
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="sec-match">
                            <div class="progress-match">
                                <div class="barOverflow">
                                    <div class="bar"></div>
                                </div>
                                <?php 
                                    $matchScoreArray = ['match' => 100, 'nomatch' => 33, 'moderate' => 66];
                                    $matchScalePoint = ( isset($professionsData->id) && isset($getTeenagerHML[$professionsData->id]) && isset($matchScoreArray[$getTeenagerHML[$professionsData->id]]) ) ? $matchScoreArray[$getTeenagerHML[$professionsData->id]] : 0;
                                ?>
                                <span>{{$matchScalePoint}}%</span>
                            </div>
                            <h3>Match</h3>
                        </div>
                        <div class="advanced-sec">
                            <div class="panel-group" id="accordion">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-parent="#accordion" data-toggle="collapse" href="#accordion1" class="">Advanced View</a>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse" id="accordion1">
                                        <div class="panel-body">
                                            @foreach($teenagerStrength as $key => $value)
                                                <div class="progress-block">
                                                    <div class="skill-name">{{$value['name']}}</div>
                                                    <div class="progress">
                                                        <div class="progress-bar progress-bar-primary" role="progressbar" data-width="{{$value['score']}}">
                                                        </div>
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: 30%; background-color:#65c6e6;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-left">
                                <div class="unbox-btn"><a href="javascript:void(0);" title="Unbox Me" class="btn-primary"><span class="unbox-me">Unbox Me</span><span class="coins-outer"><span class="coins"></span> 25000</span></a></div>
                            </div>
                        </div>
                        <div class="sec-tags">
                            <h4>Tags</h4>
                            <div class="sec-popup">
                                <a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom">
                                    <i class="icon-question"></i>
                                </a>
                                <div class="hide" id="pop1">
                                    <div class="popover-data">
                                        <a class="close popover-closer"><i class="icon-close"></i></a>
                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
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
                        <div class="ad-v">
                            <div class="t-table">
                                <div class="table-cell">
                                    Ad 343 x 400
                                </div>
                            </div>
                        </div>
                        <div class="ad-v-2">
                            <div class="t-table">
                                <div class="table-cell">
                                    Ad 343 x 800
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
        <!-- mid section end-->
    </div>
@stop
@section('script')
<script src="{{ asset('backend/js/highchart.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.play-icon').click(function() {
           $(this).hide();
                $('video').show();
                $('img').hide();
                
        })
        $('#iframe-video-click').on('click', function(ev) {
            var youtubeVideo = '{{$videoCode}}';
            if(youtubeVideo == ''){
                $("#dropbox_video_player")[0].play();
            }else{
                $('img').hide();
                $('iframe').show();
                $("#iframe-video")[0].src += "&autoplay=1";
                ev.preventDefault();
            }
            
        });
        $('.btn-next').click(function() {
            $('.front_page').hide();
            $('.promise-plus-overlay').show(500);
        })
        $('.promise-plus-overlay .close').click(function() {
            $('.promise-plus-overlay').hide();
            $('.front_page').show(500);
        })
        $('.btn-basic').click(function() {
            $('.quiz-basic .sec-show').addClass('hide');
            $('.quiz-basic .basic-quiz-area').addClass('active');
        })
        $('.quiz-box .close').click(function() {
            $('.sec-show').removeClass('hide');
            $('.sec-hide').removeClass('active');
        });
        $('.btn-intermediate').click(function(){
            $('.quiz-intermediate .sec-show').addClass('hide');
            $('.quiz-intermediate .sec-hide').addClass('active');
        })
        $('.quiz-area .close').click(function() {
             $('.sec-show').removeClass('hide');
            $('.sec-hide').removeClass('active');
        });
        $('.btn-advanced').click(function(){
            $('.quiz-advanced .sec-show').addClass('hide');
            $('.quiz-advanced .sec-hide').addClass('active');
        })
        $('.upload-screen .close').click(function() {
             $('.sec-show').removeClass('hide');
            $('.sec-hide').removeClass('active');
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
    // timer
    jQuery(document).ready(function($) {
        var count = 1;
        var counter = setInterval(timer, 1000);
        function secondPassed() {
            var minutes = Math.round((count - 30) / 60);
            var remainingcount = count % 60;
            if (remainingcount < 10) {
                remainingcount = "0" + remainingcount;
            }
            $('.time-tag,.time-tag').text(minutes + ":" + remainingcount);
            $('.time-tag').show();
        }
        function timer() {
            if (count < 0) {
            }
            else {
                secondPassed();
            }
            count = count + 1;
            if (count == 60)
            {
                //saveBoosterPoints(teenagerId, professionId, 2,isyoutube);
            }
        }
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
                    $('#add-to-star').addClass('favourite-career');
                    $(".favourite-text").show();
                    setTimeout(function () {
                        $(".favourite-text").hide();
                    }, 2500);
                }
            }
        });
    });
    
    var youtubeVideo = '{{$videoCode}}';
    if(youtubeVideo == ''){
      var isYouTube = 0;
    }
    else{
      var isYouTube = 1;  
    }
    
    setTimeout(function() {
       saveBoosterPoints({{$professionsData->id}},2,isYouTube);
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
            $("#fav-teenager-list").html('');
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
</script>
@stop