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
                                    <div class="panel panel-default factual">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a href="{{ url('/teenager/career-detail/1') }}" class="career-cl collapsed">Career 1</a>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="panel panel-default factual">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a href="{{ url('/teenager/career-detail/2') }}" class="career-cl collapsed">Career 2</a>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="panel panel-default factual">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a href="{{ url('/teenager/career-detail/3') }}" class="career-lc collapsed">Career 3</a>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p><a href="#">Expand</a></p>
                        </div>
                        <!-- das_your_profile End -->
                        <div class="das_your_profile my_interests my_network_cont">
                            <h2>
                            <a href="{{ url('/teenager/my-network') }}" title="My Careers" class="heading-tag">My Network</a>
                            </h2>
                            <div class="row">
                                <?php $countNetwork = 0;
                                echo "<pre>"; print_r($teenagerNetwork); exit; ?>
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
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="display: {{ $networkStyle }};">
                                    <div class="my_net_view">
                                        <?php 
                                            if ($network->t_photo != '' && Storage::size($teenThumbImageUploadPath . $network->t_photo) > 0) {
                                                $teenPhoto = Storage::url($teenThumbImageUploadPath . $network->t_photo);
                                            } else {
                                                $teenPhoto = Storage::url($teenThumbImageUploadPath . 'proteen-logo.png');
                                            } ?>
                                        <img src="{{ $teenPhoto }}" alt="my_net_view">
                                        <h4><a href="{{ url('/teenager/network-member') }}/{{$network->id}}">{{ $network->t_name }}</a></h4>
                                    </div>
                                    <!-- my_net_view End -->
                                </div>
                                @empty
                                    <h3>No Records Found</h3>
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
                                            <a data-parent="#accordionx" data-toggle="collapse" href="#accordion10" class="collapsed career-cl">Quiz 1<span>100% Complete</span></a>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse" id="accordion10">
                                        <div class="panel-body">
                                            Quiz 1
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default factual quiz2">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-parent="#accordionx" data-toggle="collapse" href="#accordion20" class="collapsed career-cl">Quiz 2<span>35% Complete</span></a>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse in" id="accordion20">
                                        <div class="panel-body">
                                            <div class="quiz_view">
                                                <div class="clearfix time_noti_view">
                                                    <span class="time_type pull-left">
                                                    <i class="icon-alarm"></i>
                                                    <span class="time-tag">58:32</span>
                                                    </span>
                                                    <span class="sec-popup help_noti"><a href="javascript:void(0);" data-toggle="clickover" data-popover-content="#pop1" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span>
                                                    <div class="hide popoverContent">
                                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nisi eos, earum ipsum illum libero, beatae vitae, quos sit cum voluptate iste placeat distinctio porro nobis incidunt rem nesciunt. Cupiditate, animi.
                                                    </div>
                                                </div>
                                                <div class="quiz-que">
                                                    <p class="que">
                                                        <i class="icon-arrow-simple"></i>Curabitur congue velit vel nisi vulputate, eu faucibus eros porttitor?
                                                    </p>
                                                    <div class="quiz-ans">
                                                        <div class="radio">
                                                            <label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label>
                                                            <label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label>
                                                            <label><input type="radio" name="gender"><span class="checker"></span><em>Lorem ipsum dolor sit amet</em></label>
                                                        </div>
                                                        <div class="clearfix"><span class="next-que pull-right"><i class="icon-hand"></i></span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel panel-default factual quiz3">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-parent="#accordionx" data-toggle="collapse" href="#accordion30" class="collapsed career-lc">Quiz 3<span>Begin now</span></a>
                                        </h4>
                                    </div>
                                    <div class="panel-collapse collapse" id="accordion30">
                                        <div class="panel-body">
                                            Career 3
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
                if (count < 0) {} else {
                    secondPassed();
                }
                count = count + 1;
                if (count == 60) {
                    //saveBoosterPoints(teenagerId, professionId, 2, isyoutube);
                }
            }
            $(".expandStrength").hide();
        });
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
    </script>
@stop
