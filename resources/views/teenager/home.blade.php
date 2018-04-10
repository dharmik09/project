@extends('layouts.teenager-master')

@push('script-header')
    <title>Teenager : Dashboard</title>
@endpush

@section('content')
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
            <div class="col-md-12">
                <div class="box-body">
                    <div class="alert alert-error alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                        <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
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
    <div class="dashbord_view">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 profile_section">
                    <div class="dashbord_view_left">
                        <h2 class="das_title">Your Profile</h2>
                        <div class="das_your_profile">
                            <div class="your_profile_img">
                                <img src="{{ $data['user_profile_thumb'] }}" alt="{{ $user->t_name }} {{ $user->t_lastname }}">
                                <h6>{{ $user->t_name }} {{ $user->t_lastname }}</h6>
                            </div>
                            <!-- your_profile_img End -->
                            <div class="your_profile_view">
                                <span class="sec-popup help_noti"><a id="dashboard-profile" href="javascript:void(0);" onmouseover="getHelpText('dashboard-profile')" data-trigger="hover" data-popover-content="#profile-sec" class="help-icon" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span>
                                <div class="hide" id="profile-sec">
                                    <div class="popover-data">
                                        <a class="close popover-closer"><i class="icon-close"></i></a> 
                                        <span class="dashboard-profile"></span>
                                    </div>
                                </div>
                                <h4><a href="{{ url('/teenager/edit-profile') }}" title="Edit Profile">edit</a></h4>
                                <div class="your_progress">
                                    <h6>Your Progress</h6>
                                    <h2 id="user_progress">{{Helpers::calculateProfileComplete(Auth::guard('teenager')->user()->id)}}%</h2>
                                    <h5><span id="user_total_point"> {{ ( isset($basicBoosterPoint['Total']) && $basicBoosterPoint['Total'] > 0) ? number_format($basicBoosterPoint['Total']) : 0 }}</span> points</h5>
                                    <p>{{ (isset($profileMessage) && !empty($profileMessage)) ? $profileMessage : '' }}<!--  You advanced 7% on your last visit. Well done you! --></p>
                                </div>
                                <!-- your_progress End -->
                            </div>
                        </div>
                        <!-- das_your_profile End -->
                        <div class="das_your_profile my_interests">
                            <div class="data-explainations clearfix data-interest">
                                <div class="content">
                                    <div class="data"><span class="small-box career-data-color-1"></span><span>High</span></div>
                                    <div class="data"><span class="small-box career-data-color-2"></span><span>Moderate</span></div>
                                    <div class="data"><span class="small-box career-data-color-3"></span><span>Low</span></div>
                                </div>
                            </div>
                            <h2>My Interests <span></span>
                                <span class="sec-popup"><a id="dashboard-interest" href="javascript:void(0);" onmouseover="getHelpText('dashboard-interest')" data-trigger="hover" data-popover-content="#interest" class="help-icon" rel="popover" data-placement="bottom">
                                <i class="icon-question"></i></a></span>
                            </h2>
                            <div class="hide" id="interest">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a>
                                    <span class="dashboard-interest"></span>
                                </div>
                            </div>
                            <div class="row flex-container dashboard-interest-detail">
                        	   <div style="display: block;" class="loading-screen-data loading-wrapper-sub bg-offwhite">
                                    
                                    <div class="loading-content"><img src="{{ Storage::url('img/Bars.gif') }}"></div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6 flex-items">
                                    <center>
                                        <h3 class="dashboard-interest-error-message">No Records Found</h3>
                                    </center>
                                </div>
                            </div>
                        </div>
                        <!-- das_your_profile End -->
                        <div class="das_your_profile my_interests">
                            <div class="data-explainations clearfix data-interest">
                                <div class="content">
                                    <div class="data"><span class="small-box career-data-color-1"></span><span>High</span></div>
                                    <div class="data"><span class="small-box career-data-color-2"></span><span>Moderate</span></div>
                                    <div class="data"><span class="small-box career-data-color-3"></span><span>Low</span></div>
                                </div>
                            </div>
                            <h2>My Strengths <span></span><span class="sec-popup"><a id="dashboard-strength" href="javascript:void(0);" onmouseover="getHelpText('dashboard-strength')" data-trigger="hover" data-popover-content="#strength" class="help-icon" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span></h2>
                            <div class="hide" id="strength">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a>
                                    <span class="dashboard-strength"></span>
                                </div>
                            </div>
                            <div class="row flex-container dashboard-strength-detail">
                                <div style="display: block;" class="loading-screen-data loading-wrapper-sub bg-offwhite">
                                    
                                    <div class="loading-content"><img src="{{ Storage::url('img/Bars.gif') }}"></div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6 flex-items">
                                    <center>
                                        <h3 class="dashboard-interest-error-message">No Records Found</h3>
                                    </center>
                                </div>
                            </div>
                        </div>
                        <!-- das_your_profile End -->
                        <div class="das_your_profile my_interests">
                            <div class="data-explainations clearfix data-interest">
                                <div class="content">
                                    <div class="data"><span class="small-box career-data-color-1"></span><span>High</span></div>
                                    <div class="data"><span class="small-box career-data-color-2"></span><span>Moderate</span></div>
                                    <div class="data"><span class="small-box career-data-color-3"></span><span>Low</span></div>
                                </div>
                            </div>
                            <h2><a href="{{ url('/teenager/my-careers') }}" title="My Careers" class="heading-tag">My Careers </a><span></span><span class="sec-popup"><a id="dashboard-my-career" href="javascript:void(0);" onmouseover="getHelpText('dashboard-my-career')" data-trigger="hover" data-popover-content="#dash-my-career" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span></h2>
                            <div class="hide" id="dash-my-career">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a>
                                    <span class="dashboard-my-career"></span>
                                </div>
                            </div>
                            <div class="my_career_tab">
                                <div class="panel-group" id="accordion">
                                    <?php $countCareers = 0; ?>
                                    @forelse ($teenagerCareers as $teenagerCareer)
                                    <?php $teenagerCareer->matched = ( isset($getTeenagerHML[$teenagerCareer->id]) ) ? $getTeenagerHML[$teenagerCareer->id] : ''; 
                                        switch($teenagerCareer->matched) {
                                            case 'match':
                                                $careerClass = 'career-data-color-1';
                                                break;
                                            case 'moderate':
                                                $careerClass = 'career-data-color-2';
                                                break;
                                            case 'nomatch':
                                                $careerClass = 'career-data-color-3';
                                                break;
                                            default:
                                                $careerClass = 'career-data-nomatch';
                                                break; 
                                        };
                                        $countCareers++;
                                        if ($countCareers > 3) {
                                            $carrerStyle = 'none';
                                            $careerExpandClass = "expandCareer";
                                        } else {
                                            $carrerStyle = 'block';
                                            $careerExpandClass = '';
                                        } 
                                    ?>
                                    <div class="panel panel-default actual {{ $careerExpandClass }}" style="display: {{ $carrerStyle }};">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a href="{{ url('/teenager/career-detail') }}/{{$teenagerCareer->pf_slug}}" class="{{$careerClass}} collapsed" style="display: block;">{{$teenagerCareer->pf_name}}</a>
                                            </h4>
                                        </div>
                                    </div>
                                    @empty
                                    <h3>
                                        <i class="icon-star"></i> Careers you wish to shortlist
                                    </h3>
                                    @endforelse
                                </div>
                            </div>
                            @if(count($teenagerCareers) > 3 && !empty($teenagerCareers))
                                <p><a id="career" href="javascript:void(0);" >Expand</a></p>
                            @endif
                        </div>
                        <!-- das_your_profile End -->
                        <div class="das_your_profile my_interests my_network_cont">
                            <h2>
                            <a href="{{ url('/teenager/my-network') }}" title="My Network" class="heading-tag">My Network
                            </a>
                            <span class="sec-popup">
                                <a id="dashboard-network" href="javascript:void(0);" onmouseover="getHelpText('dashboard-network')" data-trigger="hover" data-popover-content="#network" class="help-icon" rel="popover" data-placement="bottom">
                                    <i class="icon-question"></i>
                                </a>
                            </span>
                            <div class="hide" id="network">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a> 
                                    <span class="dashboard-network"></span>
                                </div>
                            </div>
                            </h2>
                            <div class="row flex-container">
                                <?php $countNetwork = 0; ?>
                                @forelse ($teenagerNetwork as $network)
                                <?php 
                                    $countNetwork++;
                                    if ($countNetwork > 6) {
                                        $networkStyle = 'none';
                                        $networkClass = "expandNetwork";
                                    } else {
                                        $networkStyle = 'block';
                                        $networkClass = '';
                                    } 
                                ?>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 flex-items {{ $networkClass }}" style="display: {{ $networkStyle }};">
                                    <div class="my_net_view">
                                        <?php 
                                            if ($network->t_photo != '' && Storage::size($teenThumbImageUploadPath . $network->t_photo) > 0) {
                                                $teenPhoto = Storage::url($teenThumbImageUploadPath . $network->t_photo);
                                            } else {
                                                $teenPhoto = Storage::url($teenThumbImageUploadPath . 'proteen-logo.png');
                                            } ?>
                                        <a href="{{ url('/teenager/network-member') }}/{{$network->t_uniqueid}}">
                                        <img src="{{ $teenPhoto }}" alt="my_net_view">
                                        <h4>{{ $network->t_name }} {{ $network->t_lastname }}</h4></a>
                                    </div>
                                    <!-- my_net_view End -->
                                </div>
                                @empty
                                <div class="col-sm-12">
                                    <h3>Explore Community to build your networks!</h3>
                                </div>
                                @endforelse
                            </div>
                            @if(count($teenagerNetwork) > 6 && !empty($teenagerNetwork))
                            <p>
                                <a id="network" href="javascript:void(0);" >Expand</a>
                            </p>
                            @endif
                        </div>
                        <!-- das_your_profile End -->
                    </div>
                    <!-- dashbord_view_left End -->
                </div>
                <!-- Col End -->
                <div class="col-md-6 col-sm-6 col-xs-12 activity_section">
                    <div class="dashbord_view_left dashbord_view_right">
                        <h2 class="das_title profile-title">Build My Profile</h2>
                        <span class="sec-popup help_noti"><a id="dashboard-profile-builder" href="javascript:void(0);" onmouseover="getHelpText('dashboard-profile-builder')" data-trigger="hover" data-popover-content="#home-profile-builder" class="help-icon" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span>
                        
                        
                        <div class="hide" id="home-profile-builder">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a>
                                    <span class="dashboard-profile-builder"></span>
                                </div>
                            </div>
                        
                        <div class="my_career_tab active_tab_view">
                            <div class="panel-group" id="accordionx">
                                <div class="panel panel-default factual quiz1">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-parent="#accordionx" data-toggle="collapse" href="#accordion{{Config::get('constant.LEVEL2_SECTION_1')}}" class="collapsed career-cl" id="{{Config::get('constant.LEVEL2_SECTION_1')}}" @if($secComplete1 != 1) onclick="fetch2ActivityQuestion(this.id)" @endif>Profile Builder 1<span id="percentageSection{{Config::get('constant.LEVEL2_SECTION_1')}}">{{$section1}}</span></a>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse" id="accordion{{Config::get('constant.LEVEL2_SECTION_1')}}">
                                        <div class="panel-body" id="section{{Config::get('constant.LEVEL2_SECTION_1')}}">
                                            <center><h3>Aha! You Earned ProCoins!!</h3></center>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default factual quiz2">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-parent="#accordionx" data-toggle="collapse" href="#accordion{{Config::get('constant.LEVEL2_SECTION_2')}}" class="collapsed career-cl" id="{{Config::get('constant.LEVEL2_SECTION_2')}}" @if($secComplete2 != 1) onclick="fetch2ActivityQuestion(this.id)" @endif >Profile Builder 2<span id="percentageSection{{Config::get('constant.LEVEL2_SECTION_2')}}">{{$section2}}</span></a>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse" id="accordion{{Config::get('constant.LEVEL2_SECTION_2')}}">
                                        <div class="panel-body" id="section{{Config::get('constant.LEVEL2_SECTION_2')}}">
                                            <center><h3>Aha! You Earned ProCoins!!</h3></center>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default factual quiz3">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-parent="#accordionx" data-toggle="collapse" href="#accordion{{Config::get('constant.LEVEL2_SECTION_3')}}" class="collapsed career-lc" id="{{Config::get('constant.LEVEL2_SECTION_3')}}" @if($secComplete3 != 1) onclick="fetch2ActivityQuestion(this.id)" @endif >Profile Builder 3<span id="percentageSection{{Config::get('constant.LEVEL2_SECTION_3')}}">{{$section3}}</span></a>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse" id="accordion{{Config::get('constant.LEVEL2_SECTION_3')}}">
                                        <div class="panel-body" id="section{{Config::get('constant.LEVEL2_SECTION_3')}}">
                                            <center><h3>Aha! You Earned ProCoins!!</h3></center>
                                        </div>
                                    </div>
                                </div>
                                @if(isset($section4Collection[0]->NoOfTotalQuestions) && $section4Collection[0]->NoOfTotalQuestions > 0 && $user->t_school_status == 1)
                                    <div class="panel panel-default factual quiz4">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-parent="#accordionx" data-toggle="collapse" href="#accordion{{Config::get('constant.LEVEL2_SECTION_4')}}" class="collapsed career-lc" id="{{Config::get('constant.LEVEL2_SECTION_4')}}" @if($secComplete4 != 1) onclick="fetch2ActivityQuestion(this.id)" @endif >Profile Builder 4<span id="percentageSection{{Config::get('constant.LEVEL2_SECTION_4')}}">{{$section4}}</span></a>
                                            </h4>
                                        </div>
                                        <div class="panel-collapse collapse" id="accordion{{Config::get('constant.LEVEL2_SECTION_4')}}">
                                            <div class="panel-body" id="section{{Config::get('constant.LEVEL2_SECTION_4')}}">
                                                <center><h3>Aha! You Earned ProCoins!!</h3></center>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- active_tab_view End -->
                        
                        <h2 class="das_title custom-section"><a href="{{url('teenager/list-career')}}" title="Careers">Explore Careers</a></h2>
                        <div class="das_your_profile my_interests career-consider">
                            <div class="data-explainations clearfix data-interest">
                             <div class="content">
                                <div class="data"><span class="small-box career-data-color-1"></span><span>Strong match</span></div>
                                <div class="data"><span class="small-box career-data-color-2"></span><span>Potential match</span></div>
                                <div class="data"><span class="small-box career-data-color-3"></span><span>Unlikely match</span></div>
                             </div>
                            </div>
                            <h2>Suggestions<span></span><span class="sec-popup"><a id="dashboard-career-consider" href="javascript:void(0);" onmouseover="getHelpText('dashboard-career-consider')" data-trigger="hover" data-popover-content="#home-career-consider" class="help-icon" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span></h2>
                            <div class="hide" id="home-career-consider">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a>
                                    <span class="dashboard-career-consider"></span>
                                </div>
                            </div>
                            <div class="careers-container consideration-section consideration-section-data">
                                <!-- <div class="career-data">
                                    <h3 href="javascript:void(0);" class="interest-section">Build your profile to know careers to consider!</h3>
                                </div> -->        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row End -->
            <div class="ad-slider owl-carousel">
                @forelse ($advertisements as $ad)
                <div class="ad-sec-h">
                    <div class="t-table">
                        <img src="{{$ad['image']}}">
                    </div>
                </div>
                @empty
                <div class="ad-sec-h">
                    <div class="t-table">
                        <div class="table-cell">
                            No Ads available
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="modal fade" id="coinsConsumption" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content custom-modal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                    <h4 id="career_title" class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <p id="career_message"></p>
                    <p id="career_sub_message"></p>
                </div>
                <div class="modal-footer">
                    <a id="career_buy" href="{{ url('teenager/buy-procoins') }}" type="submit" class="btn btn-primary btn-next" style="display: none;">buy</a>
                    <button id="career_consume_coin" type="submit" class="btn btn-primary btn-next" data-dismiss="modal" onclick="saveConsumedCoins({{$componentsCareerConsider->pc_required_coins}});" style="display: none;" >ok </button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <audio id="audio_0" src="{{ Storage::url('frontend/audio/L1A_0.wav')}}"></audio>
    <audio id="audio_1" src="{{ Storage::url('frontend/audio/L1A_1.wav')}}"></audio>
    <audio id="audio_2" src="{{ Storage::url('frontend/audio/L1A_2.wav')}}"></audio>
    <audio id="login_success_audio" src="{{ Storage::url('frontend/audio/Login_Success.wav')}}"></audio>
@stop

@section('script')
<script>
    var count;
    jQuery(document).ready(function($) {
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
            if (count < 0) {} else {
                secondPassed();
            }
            count = count - 1;
            //$("#blackhole").val(count);
        }
        $(".expandStrength").hide();
    });

    function fetch2ActivityQuestion(id) {
        if ( !$("#accordion"+id).hasClass("in") ) {
            $("#section"+id).html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen bg-offwhite"><div id="loading-content"><img src="{{ Storage::url("img/Bars.gif") }}"></div></div>');
            $("#section"+id).addClass('loading-screen-parent loading-large');

            var CSRF_TOKEN = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: "{{url('teenager/get-level2-activity')}}",
                //dataType: 'html',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: {'section_id':id},
                success: function (response) {
                    //count = response.timer;
                    $("#section"+id).removeClass('loading-screen-parent loading-large');
                    //$("#section"+id).hide().html(response.activities).fadeIn('slow');
                    //$("#percentageSection"+id).html(response.sectionPercentage);
                    $(".quiz"+id).hide().html(response).fadeIn('slow');
                }
            });
        }
    }
    
    function saveAns(queId, key = '') {
        <?php if ($user->is_sound_on == Config::get('constant.SOUND_FLAG_ON')) { ?>
            var audio = $("#audio_"+key);
            audio.trigger('play');
        <?php } ?>
        var section = $('#'+queId+'l2AnsSection').val();
        var answerId = $('input[name='+queId+'l2AnsId]:checked').val();
        var timer = count;
        $("#section"+section).fadeOut('slow', function() {
            $("#section"+section).html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen bg-offwhite"><div id="loading-content"><img src="{{ Storage::url("img/Bars.gif") }}"></div></div>');
            $("#section"+section).fadeIn('slow');
        });
        $("#section"+section).addClass('loading-screen-parent loading-large');

        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/save-level2-activity')}}",
            //dataType: 'json',
            //async: false,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'section_id':section,'answerID':answerId,'questionID':queId,'timer':timer},
            success: function (response) {
                count = response.timer;
                $("#section"+section).removeClass('loading-screen-parent loading-large');
                $(".quiz"+section).hide().html(response).fadeIn('slow');
                //$("#section"+section).hide().html(response.activities).fadeIn('slow');
                //$("#percentageSection"+section).html(response.sectionPercentage);
                getTeenagerInterestData("{{Auth::guard('teenager')->user()->id}}");
                getTeenagerStrengthData("{{Auth::guard('teenager')->user()->id}}");
                if(isSectionCompleted){
                    getUserScoreAndPoint();
                    getCareerConsideration("{{Auth::guard('teenager')->user()->id}}");
                }
            }
        });
    }

    $(document).on('click', '.interest-section', function() {
        $('.expandElement').slideToggle('medium', function() {
            if ($(this).is(':visible')) {
                $(this).css('display','block');
                $("#interest_expand").text("Collapse");
            } else {
                $("#interest_expand").text("Expand");
            }
        });
        return false;
    });

    $(document).on('click', '#strength_expand', function() {
        $('.expandStrength').slideToggle('medium', function() {
            if ($(this).is(':visible')) {
                $(this).css('display','block');
                $("#strength_expand").text("Collapse");
            } else {
                $("#strength_expand").text("Expand");
            }
        });
        return false;
    });

    $('#network').click(function() {
        $('.expandNetwork').slideToggle('medium', function() {
            if ($(this).is(':visible')) {
                $(this).css('display','block');
                $("#network").text("Collapse");
            } else {
                $("#network").text("Expand");
            }
        });
        return false;
    });

    $(document).on('click', '.expand-4', function() {
    	$('.sec-wrap-5').slideToggle("slow");
        if ($(this).hasClass('less')) {
            $(this).removeClass('less');
            $(this).addClass('more');
            $(this).text('Collapse');
        } else {
            $(this).addClass('less');
            $(this).removeClass('more');
            $(this).text('Expand');
        }
        return false;
    });

    $('#career').click(function() {
        $('.expandCareer').slideToggle('medium', function() {
            if ($(this).is(':visible')) {
                $(this).css('display','block');
                $("#career").text("Collapse");
            } else {
                $("#career").text("Expand");
            }
        });
        return false;
    });

    $(window).on("load", function(e) {
        e.preventDefault();
        getTeenagerInterestData("{{Auth::guard('teenager')->user()->id}}");
        getTeenagerStrengthData("{{Auth::guard('teenager')->user()->id}}");
        getCareerConsideration("{{Auth::guard('teenager')->user()->id}}");
   });

    function getTeenagerInterestData(teenagerId) {
        $('.dashboard-interest-detail .loading-screen-data').parent().toggleClass('loading-screen-parent');
        $('.dashboard-interest-detail .loading-screen-data').show();
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/get-interest-detail')}}",
            dataType: 'html',
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            data: {'teenagerId':teenagerId},
            success: function (response) {
                try {
                    var valueOf = $.parseJSON(response); 
                } catch (e) {
                    // not json
                }
                if (typeof valueOf !== "undefined" && typeof valueOf.status !== "undefined" && valueOf.status == 0) {
                    $('.dashboard-interest-error-message').text(valueOf.message);
                } else {
                    $(".dashboard-interest-detail").html(response).fadeIn('slow');
                }
                $('.dashboard-interest-detail .loading-screen-data').hide();
                $('.dashboard-interest-detail').removeClass('loading-screen-parent');
            }
        });
    }

    function getTeenagerStrengthData(teenagerId) {
        $('.dashboard-strength-detail .loading-screen-data').parent().toggleClass('loading-screen-parent');
        $('.dashboard-strength-detail .loading-screen-data').show();
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/get-strength-detail')}}",
            dataType: 'html',
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            data: {'teenagerId':teenagerId},
            success: function (response) {
                try {
                    var valueOf = $.parseJSON(response);
                } catch (e) {
                    // not json
                }
                if (typeof valueOf !== "undefined" && typeof valueOf.status !== "undefined" && valueOf.status == 0) {
                    $('.dashboard-strength-error-message').text(valueOf.message);
                } else {
                    $(".dashboard-strength-detail").html(response).fadeIn('slow');
                }
                $('.dashboard-strength-detail .loading-screen-data').hide();
                $('.dashboard-strength-detail').removeClass('loading-screen-parent');
            }
        });
    }

    function getCareerConsideration(teenagerId) {
        $(".consideration-section").html('<div id="considerLoader" style="display: block;" class="loading-screen loading-wrapper-sub bg-offwhite"><div id="loading-text"></div><div id="loading-content"><img src="{{ Storage::url("img/Bars.gif") }}"></div></div>');
        $(".consideration-section").addClass('loading-screen-parent');
        $("#considerLoader").show();
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/get-career-consideration')}}",
            dataType: 'html',
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            data: {'teenagerId':teenagerId},
            success: function (response) {
                $("#considerLoader").hide();
                $(".consideration-section").removeClass('loading-screen-parent');
                try {
                    var valueOf = $.parseJSON(response);
                } catch (e) {
                    // not json
                }
                if (typeof valueOf !== "undefined" && typeof valueOf.status !== "undefined" && valueOf.status == 0) {
                    $('.consideration-section').html('<div class=""><h3 href="javascript:void(0);" class="interest-section">'+ valueOf.message +'</h3></div>');
                } else {
                    $(".consideration-section").html(response).fadeIn('slow');
                }
            }
        });
    }   

    function addToMyCareerProfession(professionId) {
    	$.ajax({
            url : '{{ url("teenager/add-star-to-career") }}',
            method : "POST",
            data: 'careerId=' + professionId,
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            dataType: "json",
            success : function (response) {
                if (typeof response !== "undefined" && typeof response.message !== "undefined" && response.message != "") {
                    //$(".prof_sec_"+professionId).html(response.message).fadeIn('slow');
                    $(".prof_sec_"+professionId).removeAttr('onclick');
                    $(".prof_sec_"+professionId).addClass('selected');
                    $(".prof_sec_"+professionId).attr('title', 'In My Careers');
                    $(".prof_sec_"+professionId).html('<img src="{{ Storage::url('img/star-active.png') }}" class="hover-img"><div class="favourite-text career-message-'+professionId+'">Career has been added in My careers</div>');
                    $(".career-message-"+professionId).css('display', 'block');
                    setTimeout(function () {
                        $(".career-message-"+professionId).hide();
                    }, 2500);
                }
            }
        });
    } 

    $('.ad-slider').owlCarousel({
        loop: true,
        margin: 10,
        items: 1,
        nav: false,
        dots: false,
        smartSpeed: 500,
        autoplay:true,
    });
    
    function getUserScoreAndPoint() {
    	$.ajax({
            url : '{{ url("teenager/get-user-score-progress") }}',
            method : "POST",
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
            dataType: "json",
            success : function (response) {
                if (typeof response.progress !== "undefined" && response.progress != "") {
                    $('#user_progress').html(response.progress);
                    $('#user_total_point').html(response.totalpoint);
                    $('#user_procoins').html(response.procoins);
                }
            }
        });
    }

    function getCareersConsiderDetails(teenCoins, consumeCoins) {
        var teenagerCoins = parseInt(teenCoins);
        var consumeCoins = parseInt(consumeCoins);
        <?php 
        if ($remainingDaysForCareerConsider > 0) { ?>
            $("#career_coins").html("");
            $("#career_coins").html('<span class="coins"></span>' + "{{$remainingDaysForCareerConsider}}" + " days left");
        <?php 
        } else { ?>
            if (consumeCoins > teenagerCoins) {
                $("#career_buy").show();
                $("#career_title").text("Notification!");
                $("#career_message").text("You don't have enough ProCoins. Please Buy more.");
            } else {
                $("#career_consume_coin").show();
                $("#career_title").text("Congratulations!");
                $("#career_message").text("You have " + format(teenagerCoins) + " ProCoins available.");
                $("#career_sub_message").text("Click OK to consume your " + format(consumeCoins) + " ProCoins and play on");
            }
            $('#coinsConsumption').modal('show');
        <?php } ?>
    }

    function format(x) {
        return isNaN(x)?"":x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function saveConsumedCoins(consumedCoins) {
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = "consumedCoins=" + consumedCoins + "&componentName=" + "{{Config::get('constant.CAREER_TO_CONSIDER')}}" + "&professionId=" + 0;
        $.ajax({
            type: 'POST',
            data: form_data,
            url: "{{ url('/teenager/save-consumed-coins-details') }}",
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            cache: false,
            success: function(response) {
                $(".career_coins").html("");
                if (response > 0) {
                    $(".career_coins").html('<span class="coins"></span> ' + response + " days left"); 
                    $(".career_text").text('See Now!'); 
                    $("#career_unbox").prop('onclick', null).off('click');
                    getCareerConsideration('{{Auth::guard('teenager')->user()->t_coins}}');
                } else {
                    $(".career_text").text('Unlock Me');
                    $(".career_coins").html('<span class="coins"></span> ' + consumedCoins);
                }
            }
        });
    }

    <?php $login_success = Session::has('login_success') ? true : false; ?>
    "{{ Session::forget('login_success') }}";
    var login_success = '<?php echo $login_success ?>';
    if (login_success){
        var audio = document.getElementById('login_success_audio');
        audio.play();
    }
</script>
@stop
