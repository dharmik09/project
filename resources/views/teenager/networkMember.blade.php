@extends('layouts.teenager-master')

@push('script-header')
    <title>Member : {{$teenDetails->t_name}}</title>
@endpush

@section('content')
    <!--mid section-->
    <!-- profile section-->
    <section class="sec-profile sec-member">
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
            <div class="sec-popup">
                <a id="member-details" href="javascript:void(0);" onmouseover="getHelpText('member-details')" data-trigger="hover" data-popover-content="#member-sec" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a>
                <div class="hide" id="member-sec">
                    <div class="popover-data">
                        <a class="close popover-closer"><i class="icon-close"></i></a> 
                        <span class="member-details"></span>
                    </div>
                </div>
            </div>
            <!--profile detail-->
            <div class="profile-detail member-detail">
                <div class="row">
                    <div class="col-sm-3">
                        <?php
                        if($teenDetails->t_photo != '' && Storage::size(Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').$teenDetails->t_photo) > 0) {
                            $teenPhoto = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH').$teenDetails->t_photo;
                        } else {
                            $teenPhoto = Config::get('constant.TEEN_PROFILE_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                        }
                        ?>
                        <div class="profile-img" style="background-image: url('{{ Storage::url($teenPhoto) }}')">
                        </div>
                    </div>
                    <?php
                        if ($teenDetails->t_location != "") {
                            $getCityArea = $teenDetails->t_location;
                        } else if($teenDetails->t_pincode != "") {
                            $getLocation = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$teenDetails->t_pincode.'&sensor=true');
                            $getCityArea = ( isset(json_decode($getLocation)->results[0]->address_components[1]->long_name) && json_decode($getLocation)->results[0]->address_components[1]->long_name != "" ) ? json_decode($getLocation)->results[0]->address_components[1]->long_name : "Default";
                        } else {
                            $getCityArea = ( $teenDetails->getCountry->c_name != "" ) ? $teenDetails->getCountry->c_name : "Default";
                        }
                        ?>
                    <div class="col-sm-9">
                        <h1>{{$teenDetails->t_name}}</h1>
                        <ul class="area-detail">
                            <li>{{ $getCityArea }} Area</li>
                            <li>{{ $myConnectionsCount }} {{ ($myConnectionsCount == 1) ? "Connection" : "Connections" }} </li>
                        </ul>
                        <ul class="social-media">
                            <li><a href="https://facebook.com/{{$teenDetails->t_fb_social_identifier}}" title="facebook" target="_blank"><i class="icon-facebook"></i></a></li>
                            <li><a href="https://plus.google.com/{{$teenDetails->t_social_identifier}}" title="google plus" target="_blank"><i class="icon-google"></i></a></li>
                        </ul>
                        @if ($connectionStatus['count'] == 0)
	                        <div class="chat-icon add-icon sent-icon">
	                            <a href="javascript:void(0)" title="Invitation Sent">
	                            	<img class="request-send" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8AQMAAAAAMksxAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAAA9JREFUeNpjYBgFowA7AAACHAABV3wEvQAAAABJRU5ErkJggg==" alt="Invitation Sent"><em>Invitation Sent</em>
	                            </a>
	                        </div>
                        @elseif($connectionStatus['count'] == 1)
                            <div class="chat-icon add-icon accepted-icon">
                                <a href="javascript:void(0)" title="Connected">
                                    <img class="accepted-member" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8AQMAAAAAMksxAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAAA9JREFUeNpjYBgFowA7AAACHAABV3wEvQAAAABJRU5ErkJggg==" alt="Invitation Accepted"><em>Connected</em>
                                </a>
                            </div>
                        @elseif($connectionStatus['count'] == 3)
                            @if($connectionStatus['connectionDetails']->tc_status == 0)
    							<div class="chat-icon add-icon respond-request" >
                                    <a href="javascript:void(0)" class="custompop" rel="popover" data-popover-content="#pop2" data-placement="bottom">
                                        <img class="respond-icon" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8AQMAAAAAMksxAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAAA9JREFUeNpjYBgFowA7AAACHAABV3wEvQAAAABJRU5ErkJggg==">
                                        <em>Respond</em>
                                    </a>
                                </div>
                                <div class="hide" id="pop2">
                                    <div class="request-function">
                                        <ul class="clearfix">
                                            <li><a href="{{url('teenager/accept-request').'/'.$connectionStatus['connectionDetails']->id}}" title="Accept" class="accept"><i class="icon-thumb"></i>Accept</a></li>
                                            <li><a href="{{url('teenager/decline-request').'/'.$connectionStatus['connectionDetails']->id}}" title="decline" class="decline"><i class="icon-thumb"></i>Decline</a></li>
                                        </ul>
                                    </div>
                                </div>
                            @elseif($connectionStatus['connectionDetails']->tc_status == 1)
                                <div class="chat-icon add-icon accepted-icon">
                                    <a href="javascript:void(0)" title="Connected">
                                        <img class="accepted-member" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8AQMAAAAAMksxAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAAA9JREFUeNpjYBgFowA7AAACHAABV3wEvQAAAABJRU5ErkJggg==" alt="Invitation Accepted"><em>Connected</em>
                                    </a>
                                </div>
                            @elseif($connectionStatus['connectionDetails']->tc_status == 2)
                                <div class="chat-icon add-icon icon-add">
                                    <a href="{{ url('teenager/send-request-to-teenager') }}/{{ $teenDetails->t_uniqueid }}" title="Add Connection">
                                        <img class="add-member" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8AQMAAAAAMksxAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAAA9JREFUeNpjYBgFowA7AAACHAABV3wEvQAAAABJRU5ErkJggg==" alt="Add"><em>Add Connection</em>
                                    </a>
                                </div>
                            @endif
                        @else
							<div class="chat-icon add-icon icon-add">
	                            <a href="{{ url('teenager/send-request-to-teenager') }}/{{ $teenDetails->t_uniqueid }}" title="Add Connection">
	                            	<img class="add-member" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8AQMAAAAAMksxAAAAA1BMVEX///+nxBvIAAAAAXRSTlMAQObYZgAAAA9JREFUeNpjYBgFowA7AAACHAABV3wEvQAAAABJRU5ErkJggg==" alt="Add"><em>Add Connection</em>
	                            </a>
	                        </div>
	                    @endif
                        <div class="chat-icon">
                            <a href="{{ url('/teenager/chat') }}/{{$teenDetails->t_uniqueid}}" title="Chat"><i class="icon-chat"></i></a>
                        </div>
                        <p>{{ ($teenDetails->t_about_info != "") ? $teenDetails->t_about_info : "" }}</p>
                    </div>
                </div>
                @if(isset($teenagerTrait[0]))
                <div class="text-center">
                    <ul class="sec-traits row flex-container">
                        @forelse($teenagerTrait as $key => $teenTrait)
							<?php if($loop->index > 2) { break; } ?>
                            <li class="col-sm-4 col-xs-12 flex-items">
                                <div class="ck-button">
                                    {{ $teenTrait->options_text }} <?php echo ($teenTrait->options_count > 1) ? '<span class="traits-badge">'.$teenTrait->options_count.'</span>' : ''; ?>
                                </div>
                            </li>
                        @empty
                            
                        @endforelse
                    </ul>
                    @if(count($teenagerTrait) > 3)
	                    <div class="traits-expand">
	                        <ul class="sec-traits row flex-container">
	                            @forelse($teenagerTrait as $key => $teenTrait)
									<?php if($loop->index < 3) { continue; } ?>
		                            <li class="col-sm-4 col-xs-12 flex-items">
		                                <div class="ck-button">
		                                    {{ $teenTrait->options_text }} <?php echo ($teenTrait->options_count > 1) ? '<span class="traits-badge">'.$teenTrait->options_count.'</span>' : ''; ?>
		                                </div>
		                            </li>
		                        @empty
		                            
		                        @endforelse
	                        </ul>
	                    </div>
	                    <div class="text-right"><span class="expand-btn less">Expand</span></div>
                    @endif
                </div>
                @endif
            </div>
            <!--profile detail end-->
        </div>
    </section>
    <!-- profile section-->
    <!-- sec personal survey-->
    <div class="sec-survey describe-traits">
        <div class="container">
            <h2>{{ucfirst($teenDetails->t_name)}} {{ucfirst($teenDetails->t_lastname)}} Survey</h2>
            <div id="traitErrorGoneMsg"></div>
            <div class="traitsLoader">
                <div id="traitsData"></div>
            </div>
        </div>
    </div>
    <!-- sec personal survey end-->
    <!--sec progress-->
    <section class="sec-progress sec-tab">
        <div class="container">
            <div class="bg-white my-progress border-tab">
                <ul class="nav nav-tabs custom-tab-container clearfix">
                    <li class="active custom-tab col-xs-4 tab-color-1"><a data-toggle="tab" href="#menu1"><span class="dt"><span class="dtc">Interests</span></span></a></li>
                    <li class="custom-tab col-xs-4 tab-color-2 tab-2"><a data-toggle="tab" href="#menu2"><span class="dt"><span class="dtc">Strengths</span></span></a></li>
                    <li class="custom-tab col-xs-4 tab-color-3"><a data-toggle="tab" href="#menu3"><span class="dt"><span class="dtc">Connections</span></span></a></li>
                </ul>
                <div class="tab-content">
                    <div id="menu1" class="tab-pane fade in active">
                        <div class="sec-popup">
                            <a id="member-interest" href="javascript:void(0);" onmouseover="getHelpText('member-interest')" data-trigger="hover" data-popover-content="#member-interest-sec" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a>
                            <div class="hide" id="member-interest-sec">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a> 
                                    <span class="member-interest"></span>
                                </div>
                            </div>
                        </div>
                        <ul class="badge-list interest-list clearfix">
                            @forelse($teenagerInterest as $interestKey => $interestValue)
                                <li>
                                    <figure>
                                        <a href="{{ url('teenager/interest/') }}/{{$interestKey}}" title="{{ $interestValue['name']}}">
                                            <div class="progress-radial progress-{{$interestValue['score']}} progress-orange"></div>
                                        </a>
                                        <a href="{{ url('teenager/interest/') }}/{{$interestKey}}" title="{{ $interestValue['name']}}">
                                            <figcaption>{{ $interestValue['name']}}</figcaption>
                                        </a>
                                    </figure>
                                </li>
                            @empty
                                <center>
                                    <h3>No any Interest found!</h3>
                                </center>
                            @endforelse
                        </ul>
                    </div>
                    <div id="menu2" class="tab-pane fade">
                        <div class="sec-popup">
                            <a id="member-strength" href="javascript:void(0);" onmouseover="getHelpText('member-strength')" data-trigger="hover" data-popover-content="#member-strength-sec" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a>
                            <div class="hide" id="member-strength-sec">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a> 
                                    <span class="member-strength"></span>
                                </div>
                            </div>
                        </div>
                        <div class="strength-list">
                            <ul class="badge-list interest-list clearfix">
                                @forelse($teenagerStrength as $strengthKey => $strengthValue)
                                <li>
                                    <figure>
                                        <a href="/teenager/multi-intelligence/{{$strengthValue['type']}}/{{$strengthKey}}" title="{{ $strengthValue['name'] }}">
                                            <div class="progress-radial progress-{{$strengthValue['score']}}"></div>
                                        </a>
                                        <figcaption><a href="/teenager/multi-intelligence/{{$strengthValue['type']}}/{{$strengthKey}}" title="{{ $strengthValue['name'] }}"> {{ $strengthValue['name'] }} </a></figcaption>
                                    </figure>
                                </li>
                                @empty
                                <center>
                                    <h3>No Records found.</h3>
                                </center>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <div id="menu3" class="tab-pane fade my-connection">
                        <div class="sec-popup">
                            <a id="member-connections" href="javascript:void(0);" onmouseover="getHelpText('member-connections')" data-trigger="hover" data-popover-content="#member-connections-sec" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a>
                            <div class="hide" id="member-connections-sec">
                                <div class="popover-data">
                                    <a class="close popover-closer"><i class="icon-close"></i></a> 
                                    <span class="member-connections"></span>
                                </div>
                            </div>
                        </div>
                        @include('teenager/loadMoreMyConnections')
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--sec progress end-->
    <!--mid section end-->
@stop
@section('script')
<script type="text/javascript">

    $(window).on("load", function(e) {
        e.preventDefault();
        fetchLevel1TraitQuestion();
    });
    
    function fetchLevel1TraitQuestion() {
        var CSRF_TOKEN = "{{ csrf_token() }}";
        var toUserId = '{{$teenDetails->t_uniqueid}}';
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/get-level1-trait')}}",
            dataType: 'html',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'toUserId':toUserId},
            success: function (response) {
                $("#traitsData").html(response);
            }
        });
    }

    function saveLevel1TraitQuestion() {

        var answerId = [];
        $.each($("input[name='traitAns']:checked"), function(){            
            answerId.push($(this).val());
        });
        var queId = $('#traitQue').val();
        var toUserId = '{{$teenDetails->t_uniqueid}}';
        $('.traitsLoader .loading-wrapper-sub').parent().toggleClass('loading-screen-parent');
        $('.traitsLoader .loading-wrapper-sub').show();
        $("#traitErrorGoneMsg").html('');
        
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/save-level1-trait')}}",
            dataType: 'html',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'answerID':answerId,'questionID':queId,'toUserId':toUserId},
            success: function (response) {
                try {
                    var valueOf = $.parseJSON(response); 
                } catch (e) {
                    // not json
                }
                if (typeof valueOf !== "undefined" && typeof valueOf.status !== "undefined" && valueOf.status == 0) {
                    $('#traitErrorGoneMsg').html("");
                    $('.traitsLoader .loading-wrapper-sub').hide();
                    $('.traitsLoader .loading-wrapper-sub').parent().removeClass('loading-screen-parent');

                    $("html, body").animate({
                        scrollTop: $('#traitErrorGoneMsg').offset().top 
                    }, 300);
                    $("#traitErrorGoneMsg").append('<div class="col-md-12 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">'+valueOf.message+'</span></div></div></div>');
                } else {
                    $('#traitErrorGoneMsg').html("");
                    $('.traitsLoader .loading-wrapper-sub').hide();
                    $('.traitsLoader .loading-wrapper-sub').parent().removeClass('loading-screen-parent');
                    $("#traitsData").html(response).fadeIn('slow');
                }
                //$("#traitsData").removeClass('loading-screen-parent');
                //$("#traitsData").html(response).fadeIn('slow');
            }
        });
    }
    function checkAnswerChecked() {
        var answerId = [];
        $.each($("input[name='traitAns']:checked"), function(){            
            answerId.push($(this).val());
        });
        if(answerId.length != 0){
            $("#btnSaveTrait").attr("disabled", false);
        }else{
            $("#btnSaveTrait").attr("disabled", true);
        }
    }
    $(document).on('click','#load-more-connection',function(){
        $("#menu2-loader-con").show();
        var lastTeenId = $(this).data('id');
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var form_data = 'lastTeenId=' + lastTeenId + "&teenId=" + '{{$teenDetails->id}}';
        $.ajax({
            url : '{{ url("teenager/load-more-member-connections") }}',
            method : "POST",
            data: form_data,
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            dataType : "text",
            success : function (data) {
                $("#menu2-loader-con").hide();
                if(data != '') {
                    $('.remove-my-connection-row').remove();
                    $('.my-connection').append(data);
                } 
            }
        });
    });
</script>
@stop