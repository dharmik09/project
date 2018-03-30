@extends('layouts.teenager-master')

@push('script-header')
    <title>Teenager : Chat/Notification/Forum</title>
    <link href="{{asset('chat/css/applozic.combined.min.css')}}" rel="stylesheet">
    <!-- AutoSuggest Plugin CSS -->
    <link href="{{asset('chat/css/jquery.atwho.min.css')}}" rel="stylesheet">

    <link href="{{asset('chat/css/applozic.fullview.css')}}" rel="stylesheet">

    <!-- Custom JS -->
    <link rel="stylesheet" href="{{asset('chat/css/applozic.plugin.css')}}">
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
                <h2 style="text-align:center;" class="font-blue">Notifications, Forum and Chat</h2>
            </div>
             <!-- sec notification-->
            <div class="sec-notification">
                <h2 class="font-blue">Notifications
                <span class="sec-popup"><a id="engage-notification" href="javascript:void(0);" onmouseover="getHelpText('engage-notification')" data-trigger="hover" data-popover-content="#engage-noti" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span>
                </h2>
                <div class="hide" id="engage-noti">
                    <div class="popover-data">
                        <a class="close popover-closer"><i class="icon-close"></i></a>
                        <span class="engage-notification"></span>
                    </div>
                </div>
                <div class="notification-list">
                    <div id="pageWiseNotifications"></div>
                        <div class="text-center load-more" id="loadMoreButton">
                            <div id="loader_con"></div>
                            <button class="btn btn-primary" title="Load More" id="pageNo" value="0" onclick="fetchNotification()">Load More</button>
                        </div>
                </div>
            </div>
            <!-- sec notification end-->
            <!--sec forum start-->
            <!--<div class="sec-forum">
                <span>Forum module</span>
            </div>-->
            <div class="forum-module">
                <h2 class="font-blue">Forum
                <span class="sec-popup">
                    <a id="engage-forum" href="javascript:void(0);" onmouseover="getHelpText('engage-forum')" data-trigger="hover" data-popover-content="#engage-forums" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span>
                </h2>
                <div class="hide" id="engage-forums">
                    <div class="popover-data">
                        <a class="close popover-closer"><i class="icon-close"></i></a> 
                        <span class="engage-forum"></span>
                    </div>
                </div>
                @if(isset($forumQuestionData) && count($forumQuestionData)>0)
                    <div class="forum-container">
                        @foreach($forumQuestionData as $key => $value)
                        <div class="single-article" style="background:#eeeeef;">
                            <div class="forum-que-block t-table">
                                <div class="author-img t-cell">
                                    <a href="javascript:void(0);"><i class="icon-hand-simple"></i></a>
                                </div>
                                <div class="forum-que t-cell">
                                    <h4><a href="{{url('teenager/forum-question/'.Crypt::encrypt($value->id))}}" title="{{$value->fq_que}}">{{$value->fq_que}}</a></h4>
                                    <ul class="que-detail">
                                        <li class="author-name"><a href="#" title="ProTeen Posted">ProTeen Posted</a></li>
                                        <li class="posted-date">{{date('d M Y',strtotime($value->created_at))}}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="forum-ans full-text">
                                <div class="ans-detail t-table">
                                    <div class="ans-author-detail t-cell no-padding">
                                        <?php
                                            $teenagerName = '';
                                            $answerTime = '';
                                            $answerText = '';
                                            $answerTextPart1 = '';
                                            $answerTextPart2 = '';
                                            
                                            if(isset($value->latestAnswer)){
                                                $answerText = $value->latestAnswer->fq_ans;
                                                $answerTime = date('jS M Y',strtotime($value->latestAnswer->created_at));
                                            }

                                            if(isset($value->latestAnswer->teenager)){
                                                $teenagerName = ucfirst($value->latestAnswer->teenager->t_name).' '.ucfirst($value->latestAnswer->teenager->t_lastname);
                                            }
                                        ?>
                                        <h4><a href="#" title="{{$teenagerName}}">{{$teenagerName}}</a></h4>
                                        <span class="ans-posted-date">{{$answerTime}}</span>
                                    </div>
                                </div>
                                @if(strlen($answerText)>0)
                                    <div class="forum-answer text-overflow">
                                        <div class="text-full">
                                            <p>{{$answerText}}</p>
                                        </div>
                                    </div>
                                    @if(strlen($answerText) > 100) <span><a href="#" title="Read More" class="read-more">Read More</a></span> @endif
                                @else
                                    <div class="sec-forum bg-offwhite"><span>The first five contributors will win ProCoins! Answer now!!</span></div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                        <p class="text-center"><a href="{{url('teenager/forum-questions')}}" title="View All" class="btn btn-primary load-more">View All Question</a></p>
                    </div>
                @else
                <div class="sec-forum bg-offwhite"><span>No question found</span></div>
                @endif
            </div>
            <!--sec forum end-->
            <div class="chat-heading">
                <h2 class="font-blue">Chat
                    <span class="sec-popup"><a id="engage-chat" href="javascript:void(0);" onmouseover="getHelpText('engage-chat')" data-trigger="hover" data-popover-content="#engage-chats" class="help-icon custompop" rel="popover" data-placement="bottom"><i class="icon-question"></i></a></span>
                </h2>
                <div class="hide" id="engage-chats">
                    <div class="popover-data">
                        <a class="close popover-closer"><i class="icon-close"></i></a> 
                        <span class="engage-chat"></span>
                    </div>
                </div>
            </div>
            <div class="sec-chat clearfix">
               @include('teenager/basic/fullViewChat')
            </div>
            <!-- sec chat end-->
           
            
        </div>
    </div>    
<!--mid content end-->
        
@stop
@section('script')
<script>
    var $original;
    if (typeof jQuery !== 'undefined') {
            $original = jQuery.noConflict(true);
            $ = $original;
            jQuery = $original;
    }
</script>

<!-- Video Call dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.0.2/howler.min.js"></script>
<script type="text/javascript" src="{{asset('chat/js/mck-ringtone-service.js')}}"></script>
<script type="text/javascript" src="{{asset('chat/js/twilio-video.js')}}"></script>
<script type="text/javascript" src="{{asset('chat/js/videocall.js')}}"></script>

<!-- Video Call dependencies -->
<script type="text/javascript" src="{{asset('chat/js/applozic.plugins.min.js')}}"></script>
<script type="text/javascript" src="{{asset('chat/js/applozic.widget.min.js')}}"></script>
<script type="text/javascript" src="{{asset('chat/js/applozic.emojis.min.js')}}"></script>
<script type="text/javascript" src="{{asset('chat/js/applozic.socket.min.js')}}"></script>

<!-- JS for location sharing plugin, remove it if location sharing not required -->
<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyDKfWHzu9X7Z2hByeW4RRFJrD9SizOzZt4&libraries=places"></script>
<script type="text/javascript" src="{{asset('chat/js/locationpicker.jquery.min.js')}}"></script>

<!--JS for auto suggest plugin, use it if auto suggestions required -->
	<!-- 	<script type="text/javascript" src="autosuggest/js/jquery.caret.min.js"></script>
	    <script type="text/javascript" src="autosuggest/js/jquery.atwho.min.js"></script> -->


<script type="text/javascript" src="{{asset('chat/js/applozic.common.js')}}"></script>
<script type="text/javascript" src="{{asset('chat/js/applozic.fullview.js')}}"></script>
<script type="text/javascript">
    var oModal = "";
    if (typeof $original !== 'undefined') {
            $ = $original;
            jQuery = $original;
            if (typeof $.fn.modal === 'function') {
                    oModal = $.fn.modal.noConflict();
            }
    } else {
            $ = $applozic;
            jQuery = $applozic;
            if (typeof $applozic.fn.modal === 'function') {
                    oModal = $applozic.fn.modal.noConflict();
            }
    }
</script>

<script src="{{asset('chat/js/recorder.js')}}"></script>
<script src="{{asset('chat/js/Fr.voice.js')}}"></script>
<script src="{{asset('chat/js/app.js')}}"></script>

<script type="text/javascript">
//callback function execute after plugin initialize.
function onInitialize(response,data) {
        if (response.status === 'success') {
                // $applozic.fn.applozic('loadContacts', {'contacts':contactsJSON});
                // $applozic.fn.applozic('loadTab', 'shanki.connect');
                //write your logic exectute after plugin initialize.
        } else {
                alert(response.errorMessage);
        }
}
// Examples shows how to define variable for auto suggest

var enableOtherUserChat = '{{$otherChat}}';   
if(enableOtherUserChat > 0){
   otherUserId = '{{(isset($otherTeenDetails->t_uniqueid) && $otherTeenDetails->t_uniqueid != '')?$otherTeenDetails->t_uniqueid:''}}';
   otherUserName = '{{(isset($otherTeenDetails->t_name) && $otherTeenDetails->t_name != '')?$otherTeenDetails->t_name:''}}';
   normalChat();
} else {
   normalChat();
}

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

// Function to initialize auto suggest plugin on message textbox
function normalChat() {
    //Function to initialize plugin
    $applozic.fn
            .applozic({
                baseUrl : 'https://apps.applozic.com',
                userId : '<?php echo Auth::guard('teenager')->user()->t_uniqueid ?>', //TODO: replace userId with actual UserId
                userName : '<?php echo Auth::guard('teenager')->user()->t_name ?>',			//TODO: replace userId with actual UserName
                appId : '<?php echo Config::get('constant.APP_LOGIC_CHAT_API_KEY') ?>',			//TODO: replace appId with your applicationId
                accessToken: '',								//TODO: set user access token.for new user it will create new access token

                ojq : $original,
                obsm : oModal,

                //  optional, leave it blank for testing purpose, read this if you want to add additional security by verifying password from your server https://www.applozic.com/docs/configuration.html#access-token-url
                //  authenticationTypeId: 1,    //1 for password verification from Applozic server and 0 for access Token verification from your server
                //  autoTypeSearchEnabled : false,
                //  messageBubbleAvator: true,
                notificationIconLink : "https://www.applozic.com/resources/images/applozic_icon.png",
                notificationSoundLink : "",
                readConversation : readMessage, // readMessage function defined above
                onInit : onInitialize, //callback function execute on plugin initialize
                maxAttachmentSize : 25, //max attachment size in MB
                desktopNotification : true,
                locShare : true,
                video:true,
                topicBox : true,
                mapStaticAPIkey: "AIzaSyCWRScTDtbt8tlXDr6hiceCsU83aS2UuZw",
                googleApiKey : "AIzaSyBhgs2TAiLfkjI3MCgrkbtVFwZDBxsyBAM", // replace it with your Google API key
                loadOwnContacts : true,
                olStatus: true,        
            // initAutoSuggestions : initAutoSuggestions //  function to enable auto suggestions
            });

            // var contactjson = {"contacts": [{"userId": "user1", "displayName": "Devashish", "imageLink": "https://www.applozic.com/resources/images/applozic_icon.png"}, {"userId": "user2", "displayName": "Adarsh", "imageLink": "https://www.applozic.com/resources/images/applozic_icon.png"}, {"userId": "user3", "displayName": "Shanki", "imageLink": "https://www.applozic.com/resources/images/applozic_icon.png"}]};
            // To load contact list use below function and pass contacts json in format shown above in variable 'contactjson'.
            getContacts(function(output){
                // here you use the output
                $applozic.fn.applozic('loadContacts', {"contacts": output});
           });
    }
</script>

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
   
    function fetchNotification(){
        var pageNo = $('#pageNo').val();
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
                if(response.notificationCount != 20){
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
                $('#pageNo').val(0);
                var pageNo = $('#pageNo').val();
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
                        if(response.notificationCount != 20){
                            $('#loadMoreButton').removeClass('text-center');
                            $('#loadMoreButton').removeClass('load-more');
                            $('#loadMoreButton').addClass('notification-complete');
                            $('#loadMoreButton').html("<p>No more notifications<p>");
                        }
                        else{
                            $('#pageNo').val(response.pageNo);
                        }
                        $("#pageWiseNotifications").html(response.notifications);
                    }
                });
            }
        });
    }

    function readNotification(id){
        if(!$("#"+id+"notification-block").hasClass('read')){
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
    }
    
    fetchNotification();

    $(window).bind("load", function() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    });
    
</script>
@stop