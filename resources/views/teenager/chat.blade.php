@extends('layouts.teenager-master')

@push('script-header')
    <title>Teenager : Dashboard Home</title>
@endpush

@section('content')
<!--mid content-->
<div class="bg-offwhite">
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
            <!-- sec chat -->
            <div class="chat-heading">
                <h2 class="font-blue">Chat</h2>
            </div>
            <div class="sec-chat clearfix">
               
            </div>
            <!-- sec chat end-->
            <!-- sec notification-->
            <div class="sec-notification">
                <h2 class="font-blue">All Notifications</h2>
                @if(count($notificationData)>0)
                    <div class="notification-list">
                        @foreach($notificationData as $key => $value)
                        <div class="notification-block <?php echo ($value->n_read_status == 1) ? 'read' : 'unread' ?>" id="{{$value->id}}notification-block" onclick="readNotification('{{$value->id}}')">
                            <div class="notification-img">
                                <?php
                                    if(isset($value->senderTeenager) && $value->senderTeenager != '') {
                                        $teenPhoto = Config::get('constant.TEEN_ORIGINAL_IMAGE_UPLOAD_PATH').$value->senderTeenager->t_photo;
                                    } else {
                                        $teenPhoto = Config::get('constant.TEEN_THUMB_IMAGE_UPLOAD_PATH').'proteen-logo.png';
                                    }
                                ?>
                                <img src="{{ Storage::url($teenPhoto) }}" alt="notification img">
                            </div>
                            <div class="notification-content"><a href="#">{!!$value->n_notification_text!!}</a><span class="date">{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->diffForHumans()}}</span>
                                @if($value->n_record_id != 0)
                                <ul class="btn-list text-right">
                                    @if($value->community->tc_status == 1)
                                        <li><a href="#" title="accept" class="accept">Accepted</a></li>
                                    @elseif($value->community->tc_statsus == 2)
                                        <li><a href="#" title="decline" class="decline">Declined</a></li>
                                    @elseif($value->community->tc_status == 0)
                                        <li><a href="{{url('teenager/accept-request').'/'.$value->n_record_id}}" title="accept" class="accept">Accept</a></li>
                                        <li><a href="{{url('teenager/decline-request').'/'.$value->n_record_id}}" title="decline" class="decline">Decline</a></li>
                                    @endif
                                </ul>
                                @endif
                            </div>
                            <div class="close"><i class="icon-close" onclick="removeNotificationBlock({{$value->id}});"></i></div>
                        </div>
                        @endforeach

                        <div id="pageWiseNotifications"></div>
                        @if(count($notificationData) > 9)
                            <div class="text-center load-more" id="loadMoreButton">
                                <div id="loader_con"></div>
                                <button class="btn btn-primary" title="Load More" id="pageNo" value="1" onclick="fetchNotification(this.value)">Load More</button>
                            </div>
                        @else
                            <div class="notification-complete">
                                <p>No more notifications<p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="sec-forum"><span>No result Found</span></div>
                @endif
            </div>
            <!-- sec notification end-->
            <!--sec forum start-->
            <!--<div class="sec-forum">
                <span>Forum module</span>
            </div>-->
            <div class="forum-module">
                <h2 class="font-blue">Forum Module</h2>
                <div class="forum-container">
                    <div class="single-article">
                        <div class="forum-que-block t-table">
                            <div class="author-img t-cell"><a href="#" title="Kelly Cheng"><img src="img/notification-img-2.png" alt="author img"></a></div>
                            <div class="forum-que t-cell">
                                <h4><a href="javascript:void(0);" title="Lorem ipsum dolor sit amet, consectetur adipisicing elit.">“It’s not always easy ?nding a talented Architect who allows you to dip into their skills like Stephanie does.</a></h4>
                                <ul class="que-detail">
                                    <li class="author-name"><a href="#" title="Kelly Cheng">Kelly Cheng</a></li>
                                    <li class="posted-date">25th july 2017</li>
                                </ul>
                            </div>
                            <!--<ul class="que-detail">
                                <li class="author-name">Kelly Cheng</li>
                                <li class="posted-date">25th july 2017</li>
                            </ul>-->
                        </div>
                        <div class="forum-ans full-text">
                            <div class="ans-detail t-table">
                                <!--<div class="answer-img t-cell"><a href="#" title="Kelly Cheng"><img src="img/profile.png" alt="author img"></a></div>-->
                                <div class="ans-author-detail t-cell no-padding">
                                    <h4><a href="#" title="Kelly Cheng">Kelly Cheng</a></h4>
                                    <span class="ans-posted-date">25th july 2017</span>
                                </div>
                            </div>
                            <div class="forum-answer text-overflow">
                                <div class="text-full">
                                   <!-- <p>I work in Real Estate and have always enjoyed renovating and project managing the build – it’s the design part that we need to outsource.</p>-->
                                    <p>I work in Real Estate and have always enjoyed renovating and project managing the build – it’s the design part that we need to outsource. We commissioned Stephanie to complete our sketch designs and the results were fantastic. Stephanie just has an innate sense of where to position the house - knowing the direction of the prevailing winds in a suburb for example… Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur libero dolores maxime quam sint, veniam nemo, esse, aliquam, ipsum earum autem aperiam. Delectus officia repellat, ad maxime non eius natus!</p>
                                </div>
                            </div>
                                <span><a href="#" title="Read More" class="read-more">Read More</a></span>
                        </div>
                    </div>
                    <div class="single-article">
                        <div class="forum-que-block t-table">
                            <div class="author-img t-cell"><a href="#" title="Kelly Cheng"><img src="img/notification-img-2.png" alt="author img"></a></div>
                            <div class="forum-que t-cell">
                                <h4><a href="javascript:void(0);" title="Lorem ipsum dolor sit amet, consectetur adipisicing elit.">Working from an ordinary base, Stephanie came up with a very workable design concept for our office. It is now a space we’re now proud to share with our clients and colleagues.</a></h4>
                                <ul class="que-detail">
                                    <li class="author-name"><a href="#" title="Kelly Cheng">Kelly Cheng</a></li>
                                    <li class="posted-date">25th july 2017</li>
                                </ul>
                            </div>
                            <!--<ul class="que-detail">
                                <li class="author-name">Kelly Cheng</li>
                                <li class="posted-date">25th july 2017</li>
                            </ul>-->
                        </div>
                        <div class="forum-ans full-text">
                            <div class="ans-detail t-table">
                                <!--<div class="answer-img t-cell"><a href="#" title="Kelly Cheng"><img src="img/diana.jpg" alt="author img"></a></div>-->
                                <div class="ans-author-detail t-cell no-padding">
                                    <h4><a href="#" title="Kelly Cheng">Kelly Cheng</a></h4>
                                    <span class="ans-posted-date">25th july 2017</span>
                                </div>
                            </div>
                            <div class="forum-answer text-overflow">
                                <div class="text-full">
                                    <p>I work in Real Estate and have always enjoyed renovating and project managing the build – it’s the design part that we need to outsource.</p>
                                    <p>We commissioned Stephanie to complete our sketch designs and the results were fantastic. Stephanie just has an innate sense of where to position the house - knowing the direction of the prevailing winds in a suburb for example… Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur libero dolores maxime quam sint, veniam nemo, esse, aliquam, ipsum earum autem aperiam. Delectus officia repellat, ad maxime non eius natus!</p>
                                </div>
                            </div>
                                <span><a href="#" title="Read More" class="read-more">Read More</a></span>
                        </div>
                    </div>
                    <div class="single-article">
                        <div class="forum-que-block t-table">
                            <div class="author-img t-cell"><a href="#" title="Kelly Cheng"><img src="img/mike.jpg" alt="author img"></a></div>
                            <div class="forum-que t-cell">
                                <h4><a href="javascript:void(0);" title="Lorem ipsum dolor sit amet, consectetur adipisicing elit.">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</a></h4>
                                <ul class="que-detail">
                                    <li class="author-name"><a href="#" title="Kelly Cheng">Kelly Cheng</a></li>
                                    <li class="posted-date">25th july 2017</li>
                                </ul>
                            </div>
                            <!--<ul class="que-detail">
                                <li class="author-name">Kelly Cheng</li>
                                <li class="posted-date">25th july 2017</li>
                            </ul>-->
                        </div>
                        <div class="forum-ans full-text">
                            <div class="ans-detail t-table">
                                    <!--<div class="answer-img t-cell"><a href="#" title="Kelly Cheng"><img src="img/notification-img-2.png" alt="author img"></a></div>-->
                                <div class="ans-author-detail t-cell no-padding">
                                    <h4><a href="#" title="Kelly Cheng">Kelly Cheng</a></h4>
                                    <span class="ans-posted-date">25th july 2017</span>
                                </div>
                            </div>
                            <div class="forum-answer text-overflow">
                                <div class="text-full">
                                    <p>I work in Real Estate and have always enjoyed renovating and project managing the build – it’s the design part that we need to outsource.</p>
                                    <p>We commissioned Stephanie to complete our sketch designs and the results were fantastic. Stephanie just has an innate sense of where to position the house - knowing the direction of the prevailing winds in a suburb for example… Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur libero dolores maxime quam sint, veniam nemo, esse, aliquam, ipsum earum autem aperiam. Delectus officia repellat, ad maxime non eius natus!</p>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Pariatur natus in ea, veniam consectetur eos. Atque commodi nam laborum, sapiente minima voluptatem quam exercitationem ducimus quis excepturi. Enim modi, id?</p>
                                </div>
                            </div>
                                <span><a href="#" title="Read More" class="read-more">Read More</a></span>
                        </div>
                    </div>
                    <p class="text-center"><a href="#" title="Read More" class="btn btn-primary load-more">Read More</a></p>
                </div>
            </div>
            <!--sec forum end-->
        </div>
    </div>    
<!--mid content end-->
        
@stop
@section('script')
<script>
   var ischat = '<?php echo Auth::guard('teenager')->user()->is_chat_initialized?>';
   if(ischat == 0){
        registerUserInAppLozic();
   }
   
   //Register user in applozic if not presents in applozic
    function registerUserInAppLozic()
    {
        $.ajax({
            url: "{{ url('teenager/registerUserInAppLozic') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}'
            },
            success: function(response)
            {

            }
        });
    } 
    
   (function(d, m){var s, h;       
   s = document.createElement("script");
   s.type = "text/javascript";
   s.async=true;
   s.src="https://apps.applozic.com/sidebox.app";
   h=document.getElementsByTagName('head')[0];
   h.appendChild(s);
   window.applozic=m;
   m.init=function(t){m._globals=t;}})(document, window.applozic || {});
    
    window.applozic.init({
                     appId: '<?php echo Config::get('constant.APP_LOGIC_CHAT_API_KEY') ?>',      //Get your application key from https://www.applozic.com
                     userId: '<?php echo Auth::guard('teenager')->user()->t_uniqueid ?>',                     //Logged in user's id, a unique identifier for user
                     userName: '<?php echo Auth::guard('teenager')->user()->t_name ?>',                 //User's display name
                     imageLink : '<?php echo $user_profile_thumb_image?>',                     //User's profile picture url
                     email : '',                         //optional
                     contactNumber: '',                  //optional, pass with internationl code eg: +16508352160
                     desktopNotification: true,
                     source: '1',                          // optional, WEB(1),DESKTOP_BROWSER(5), MOBILE_BROWSER(6)
                     notificationIconLink: 'https://www.applozic.com/favicon.ico',    //Icon to show in desktop notification, replace with your icon
                     authenticationTypeId: '1',          //1 for password verification from Applozic server and 0 for access Token verification from your server
                     accessToken: '',                    //optional, leave it blank for testing purpose, read this if you want to add additional security by verifying password from your server https://www.applozic.com/docs/configuration.html#access-token-url
                     locShare: true,
                     googleApiKey: "AIzaSyBm-n8IiGLN5c9orHBZw58zDEO6Qb7ckOQ",   // your project google api key
                     googleMapScriptLoaded : true,   // true if your app already loaded google maps script
                     autoTypeSearchEnabled : false,     // set to false if you don't want to allow sending message to user who is not in the contact list
                     loadOwnContacts : true,
                     olStatus: true,
                     onInit : function(response) {
                       $applozic.fn.applozic('getUserDetail', {callback: function(dataresponse) {
                            if(dataresponse.status === 'success') {
                               // write your logic                          
                               //$applozic.fn.applozic('loadTab', '');
                               getContacts(function(output){
                                    // here you use the output
                                    $applozic.fn.applozic('loadContacts', {"contacts": output});
                               });
                            }
                         }
                      });
                    }
                });
    function getContacts(handleData)
    {
        $.ajax({
            url: "{{ url('/teenager/getChatUsers') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}'
            },
            success: function(response)
            {
                var contactsJSON = JSON.parse(response);
                handleData(contactsJSON);
            }
        });
    }

    function fetchNotification(pageNo){
        $("#loader_con").html('<img src="{{Storage::url('img/loading.gif')}}">');
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/get-page-wise-notification')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'page_no':pageNo},
            success: function (response) {
                if(response.notificationCount != 10){
                    $('#loadMoreButton').removeClass('text-center');
                    $('#loadMoreButton').removeClass('load-more');
                    $('#loadMoreButton').addClass('notification-complete');
                    $('#loadMoreButton').html("<p>No more notifications<p>");
                }
                else{
                    $('#pageNo').val(response.pageNo);
                }
                $("#pageWiseNotifications").append(response.notifications);
                $("#loader_con").html('');
            }
        });
    }

    function removeNotificationBlock(id){
        $('#'+id+'notification-block').fadeOut('slow', function(){ $('#'+id+'notification-block').remove(); });
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/delete-notification')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'id':id},
            success: function (response) {
            }
        });
    }

    function readNotification(id){
        var CSRF_TOKEN = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{url('teenager/read-notification')}}",
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            data: {'notification_id':id},
            success: function (response) {
                $("#"+id+"notification-block").removeClass('unread');
                $("#"+id+"notification-block").addClass('read');
            }
        });
    }
</script>
@stop