@extends('layouts.teenager-master')

@push('script-header')
    <title>Teenager : Dashboard Home</title>
@endpush

@section('content')
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
                                <h4><a href="{{ url('/teenager/edit-profile') }}" title="Edit Profile">edit</a></h4>
                                <div class="your_progress">
                                    <h6>Your Progress</h6>
                                    <h2>23%</h2>
                                    <h5>10,000 points</h5>
                                    <p>You advanced 7% on your last visit. Well done you!</p>
                                </div>
                                <!-- your_progress End -->
                            </div>
                        </div>
                        <!-- das_your_profile End -->
                        <div class="das_your_profile my_interests">
                            <h2>My Interests <span></span>
                                <span class="sec-popup"><a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span>
                            </h2>
                            <div class="hide" id="pop1">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                                </div>
                            </div>
                            <div class="row flex-container">
                            	<?php 
                                    $interestFlag = ''; 
		                            if (!empty(array_filter($teenagerInterest))) {
		                            	$interestFlag = true;
		                            } else {
		                            	$interestFlag = false;
		                            }
		                        ?>
		                        @if (isset($interestFlag) && $interestFlag == true)
	                                <?php $countInterest = 0; ?>
	                                @forelse($teenagerInterest as $interestKey => $interestValue)
	                                <?php
	                                    $countInterest++; 
	                                    if($interestValue < 1) {
	                                        $countInterest--; 
	                                        $elementClass = '';
	                                        $key = 'none';
	                                        continue; 
	                                    } else {
	                                        if ($countInterest > 4) {
	                                            $key = 'none';
	                                            $elementClass = "expandElement";
	                                        } else {
	                                            $key = 'block';
	                                            $elementClass = '';
	                                        }
	                                    } $imageSelection = "img/my-interest-".$interestValue.".png"; ?>
	                                    <div class="col-md-6 col-sm-6 col-xs-6 flex-items {{ $elementClass }}" style="display: {{ $key }};" > 
	                                        <div class="my_chart">
	                                            <div class="progress-radial progress-20">
	                                            </div>
	                                            <h4>
	                                                <a href="{{ url('teenager/interest/') }}/{{$interestKey}}"><?php echo Helpers::getInterestBySlug($interestKey); ?>
	                                                </a>
	                                            </h4>
	                                        </div>
	                                    </div>
	                                @empty
	                                	<div class="col-md-6 col-sm-6 col-xs-6 flex-items">
		                                	<center>
		                                		<h3>No Records Found</h3>
		                                	</center>
	                                	</div>
	                                @endforelse
                                @else
                                	<div class="col-md-6 col-sm-6 col-xs-6 flex-items">
	                                	<center>
	                                		<h3>No Records Found</h3>
	                                	</center>
                                	</div>
                                @endif
                            </div>
                            @if (count(array_filter($teenagerInterest)) > 4 && !empty($teenagerInterest))
                            	<p>
                            		<a id="interest" href="javascript:void(0);" class="interest-section">Expand</a>
                            	</p>
                            @endif
                        </div>
                        <!-- das_your_profile End -->
                        <div class="das_your_profile my_interests">
                            <h2>My Strengths <span></span><span class="sec-popup"><a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span></h2>
                            <div class="row flex-container">
                                <?php $countStrength = 0; ?>
                                @forelse($teenagerStrength as $strengthKey => $strengthValue)
                                	<?php
                                		$countStrength++;
                                		if ($countStrength > 4) {
                                            $key = 'none';
                                            $elementClass = "expandStrength";
                                        } else {
                                            $key = 'block';
                                            $elementClass = '';
                                        } ?>
                                    <div class="col-md-6 col-sm-6 col-xs-6 flex-items {{ $elementClass }}" style="display: {{ $key }};">
                                        <div class="my_chart">
                                            <div class="progress-radial progress-20">
                                            </div>
                                            <h4><a href="/teenager/multi-intelligence/{{$strengthValue['type']}}/{{$strengthKey}}"> {{ $strengthValue['name'] }}</a></h4>
                                        </div>
                                    </div>
                                @empty
                                	<div class="col-md-6 col-sm-6 col-xs-6 flex-items">
	                                	<center>
	                                		<h3>No Records Found</h3>
	                                	</center>
                                	</div>
                                @endforelse
                            </div>
                            @if(count($teenagerStrength) > 4 && !empty($teenagerStrength))
                            	<p>
                            		<a id="strength" href="javascript:void(0);" >Expand</a>
                            	</p>
                            @endif
                        </div>
                        <!-- das_your_profile End -->
                        <div class="das_your_profile my_interests">
                            <h2><a href="{{ url('/teenager/my-careers') }}" title="My Careers" class="heading-tag">My Careers </a><span></span><span class="sec-popup"><a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span></h2>
                            <div class="my_career_tab">
                                <div class="panel-group" id="accordion">
                                    <?php $countCareers = 0; ?>
                                    @forelse ($teenagerCareers as $teenagerCareer)
                                    <?php $teenagerCareer->matched = rand(0,2); 
                                        switch($teenagerCareer->matched) {
                                            case 0:
                                                $careerClass = 'career-data-color-1';
                                                break;

                                            case 1:
                                                $careerClass = 'career-data-color-2';
                                                break;

                                            case 2:
                                                $careerClass = 'career-data-color-3';
                                                break;

                                            default:
                                                $careerClass = '';
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
                                                <a href="{{ url('/teenager/career-detail') }}/{{$teenagerCareer->id}}" class="{{$careerClass}} collapsed" style="display: block;">{{$teenagerCareer->pf_name}}</a>
                                            </h4>
                                        </div>
                                    </div>
                                    @empty
                                        <h3>No Records Found.</h3>
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
                            <a href="{{ url('/teenager/my-network') }}" title="My Network" class="heading-tag">My Network</a>
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
                                    } ?>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 flex-items {{ $networkClass }}" style="display: {{ $networkStyle }};">
                                    <div class="my_net_view">
                                        <?php 
                                            if ($network->t_photo != '' && Storage::size($teenThumbImageUploadPath . $network->t_photo) > 0) {
                                                $teenPhoto = Storage::url($teenThumbImageUploadPath . $network->t_photo);
                                            } else {
                                                $teenPhoto = Storage::url($teenThumbImageUploadPath . 'proteen-logo.png');
                                            } ?>
                                        <img src="{{ $teenPhoto }}" alt="my_net_view">
                                        <h4><a href="{{ url('/teenager/network-member') }}/{{$network->t_uniqueid}}">{{ $network->t_name }}</a></h4>
                                    </div>
                                    <!-- my_net_view End -->
                                </div>
                                @empty
                                <div class="col-sm-12">
                                    <h3>No Records Found</h3>
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
                        <h2 class="das_title">Activities</h2>
                        <div class="my_career_tab active_tab_view">
                            <div class="panel-group" id="accordionx">
                                <div class="panel panel-default factual quiz1">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-parent="#accordionx" data-toggle="collapse" href="#accordion{{Config::get('constant.LEVEL2_SECTION_1')}}" class="collapsed career-cl" id="{{Config::get('constant.LEVEL2_SECTION_1')}}" onclick="fetch2ActiityQuestion(this.id)">Quiz 1<span id="percentageSection{{Config::get('constant.LEVEL2_SECTION_1')}}">{{$section1}}</span></a>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse" id="accordion{{Config::get('constant.LEVEL2_SECTION_1')}}">
                                        <div class="panel-body" id="section{{Config::get('constant.LEVEL2_SECTION_1')}}">
                                            Quiz 1
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default factual quiz2">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-parent="#accordionx" data-toggle="collapse" href="#accordion{{Config::get('constant.LEVEL2_SECTION_2')}}" class="collapsed career-cl" id="{{Config::get('constant.LEVEL2_SECTION_2')}}" onclick="fetch2ActiityQuestion(this.id)">Quiz 2<span id="percentageSection{{Config::get('constant.LEVEL2_SECTION_2')}}">{{$section2}}</span></a>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse" id="accordion{{Config::get('constant.LEVEL2_SECTION_2')}}">
                                        <div class="panel-body" id="section{{Config::get('constant.LEVEL2_SECTION_2')}}">
                                            Quiz 2
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default factual quiz3">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-parent="#accordionx" data-toggle="collapse" href="#accordion{{Config::get('constant.LEVEL2_SECTION_3')}}" class="collapsed career-lc" id="{{Config::get('constant.LEVEL2_SECTION_3')}}" onclick="fetch2ActiityQuestion(this.id)">Quiz 3<span id="percentageSection{{Config::get('constant.LEVEL2_SECTION_3')}}">{{$section3}}</span></a>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse" id="accordion{{Config::get('constant.LEVEL2_SECTION_3')}}">
                                        <div class="panel-body" id="section{{Config::get('constant.LEVEL2_SECTION_3')}}">
                                            Quiz 3
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- active_tab_view End -->
                        <h2 class="das_title custom-section">Careers</h2>
                        <div class="das_your_profile my_interests">
                            <h2>Careers to consider <span></span><span class="sec-popup"><a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span></h2>
                            <div class="careers-container">
                                <div class="career-data career-data-color-1">
                                    <h2>Career 1</h2>
                                    <div class="clearfix">
                                        <a href="#" class="addto pull-left text-uppercase">add to my careers</a>
                                        <span class="status-career pull-right">Complete</span>
                                    </div>
                                </div>
                                <div class="career-data career-data-color-1">
                                    <h2>Career 2</h2>
                                    <div class="clearfix">
                                        <a href="#" class="addto pull-left text-uppercase">add to my careers</a>
                                        <a href="#" class="status-career pull-right">Explore ></a>
                                    </div>
                                </div>
                                <div class="career-data career-data-color-2">
                                    <h2>Career 3</h2>
                                    <div class="clearfix">
                                        <a href="#" class="addto pull-left text-uppercase">add to my careers</a>
                                        <a href="#" class="status-career pull-right">Explore ></a>
                                    </div>
                                </div>
                                <div class="career-data career-data-color-2">
                                    <h2>Career 4</h2>
                                    <div class="clearfix">
                                        <a href="#" class="addto pull-left text-uppercase">add to my careers</a>
                                        <span class="status-career pull-right">Complete</span>
                                    </div>
                                </div>
                                <div class="career-data career-data-color-3">
                                    <h2>Career 5</h2>
                                    <div class="clearfix">
                                        <a href="#" class="addto pull-left text-uppercase">add to my careers</a>
                                        <span class="status-career pull-right">Complete</span>
                                    </div>
                                </div>
                                <div class="career-data career-data-color-3">
                                    <h2>Career 6</h2>
                                    <div class="clearfix">
                                        <a href="#" class="addto pull-left text-uppercase">add to my careers</a>
                                        <span class="status-career pull-right">Complete</span>
                                    </div>
                                </div>
                                <div class="data-explainations clearfix">
                                    <div class="data"><span class="small-box career-data-color-1"></span><span>Strong match</span></div>
                                    <div class="data"><span class="small-box career-data-color-2"></span><span>Potential match</span></div>
                                    <div class="data"><span class="small-box career-data-color-3"></span><span>Unlikely match</span></div>
                                </div>
                            </div>
                            <p><a href="">Expand</a></p>
                        </div>
                    </div>
                    <!-- dashbord_view_right End -->
                </div>
                <!-- Col End -->
            </div>
            <!-- Row End -->
        </div>
    </div>
    
@stop

@section('script')
<script>
    
    var timeCount = '600';
    var count = timeCount;

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
            if (count == 60) {
                //saveBoosterPoints(teenagerId, professionId, 2, isyoutube);
            }
        }
        $(".expandStrength").hide();

    });

    function fetch2ActiityQuestion(id) {
        if ( !$("#accordion"+id).hasClass("in") ) {
            $("#section"+id).html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen"><div id="loading-text"><img src="{{Storage::url('img/ProTeen_Loading_edit.gif')}}" alt="loader img"></div><div id="loading-content"></div></div>');
            $("#section"+id).addClass('loading-screen-parent');

            var CSRF_TOKEN = "{{ csrf_token() }}";
            $.ajax({
                type: 'POST',
                url: "{{url('teenager/get-level2-activity')}}",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                data: {'section_id':id},
                success: function (response) {
                    count = response.timer;
                    $("#section"+id).removeClass('loading-screen-parent');
                    $("#section"+id).html(response.activities);
                    $("#percentageSection"+id).html(response.sectionPercentage);
                }
            });
        }
    }
    function saveAns(queId) {

        var section = $('#'+queId+'l2AnsSection').val();
        var answerId = $('input[name='+queId+'l2AnsId]:checked').val();
        var timer = count;
        $("#section"+section).html('<div id="loading-wrapper-sub" style="display: block;" class="loading-screen"><div id="loading-text"><img src="{{Storage::url('img/ProTeen_Loading_edit.gif')}}" alt="loader img"></div><div id="loading-content"></div></div>');
        $("#section"+section).addClass('loading-screen-parent');

        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/save-level2-activity')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'section_id':section,'answerID':answerId,'questionID':queId,'timer':timer},
            success: function (response) {
                count = response.timer;
                $("#section"+section).removeClass('loading-screen-parent');
                $("#section"+section).html(response.activities);
                $("#percentageSection"+section).html(response.sectionPercentage);
            }
        });
    }



    $('.interest-section').click(function() {
        $('.expandElement').slideToggle('medium', function() {
            if ($(this).is(':visible')) {
                $(this).css('display','block');
                $("#interest").text("Collapse");
            } else {
            	$("#interest").text("Expand");
            }
        });
        return false;
    });
    $('#strength').click(function() {
        $('.expandStrength').slideToggle('medium', function() {
            if ($(this).is(':visible')) {
                $(this).css('display','block');
                $("#strength").text("Collapse");
            } else {
            	$("#strength").text("Expand");
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
</script>
@stop
