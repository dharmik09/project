@extends('layouts.parent-master')

@section('content')

<div class="col-xs-12">
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
    @if($message = Session::get('error'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-dismissable success">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($message = Session::get('success'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-dismissable success">
                    <button aria-hidden="true" data-dismiss="success" class="close" type="button">X</button>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
<div class="centerlize">
    <div class="container_padd">
        <div class="container">
            <div class="inner_container">
            @include('teenager/teenagerLevelPointBox')
            <a class="back_me" href="{{url('parent/my-challengers-accept')}}/{{$professionDetail[0]->id}}/{{$response['teen_id']}}"><i class="fa fa-chevron-left" aria-hidden="true"></i> &nbsp; <span>Back</span></a>
                @if(isset($professionDetail) && !empty($professionDetail))
                <div class="level_icon">
                    <h2>{{$professionDetail[0]->pf_name}}</h2>
                    @if($response['showCongrats'] == 'yes')
                    <div class="cong_container animation-element in-view">
                        <canvas id="canvas">Canvas is not supported in your browser.</canvas>
                        <div class="cong cong_hero"><img src="{{asset('frontend/images/jumping.gif')}}" alt=""></div>
                        <div class="cong cong_top"><p>Congratulations!</p></div>
                        <div class="cong cong_bottom">
                            <div class="cong_outer">
                                <div class="cong_middle">
                                    <p>
                                        Great work on your real world assignment! Check out our rankings and make a choice if being {{$professionDetail[0]->pf_name}} is what will
                                        give you joy in life and become your passion career. Exploring..Experience..Enjoy...more professions in ProTeen.
                                        <?php
                                        $desc = "Great work on your real world assignment! Check out our rankings and make a choice if being ".$professionDetail[0]->pf_name." is what will give you joy in life and become your passion career. Exploring..Experience..Enjoy...more professions in ProTeen.";
                                        $image = Helpers::getParentOriginalImageUrl(Auth::guard('parent')->user()->p_photo);
                                        ?>
                                        <a href="javascript:void(0);" onclick="shareFacebook('{{url('/')}}','{{$desc}}','{{$desc}}','{{$image}}')" class="fb_congratulation"><i class="fa fa-facebook-official" aria-hidden="true"></i></a>
                                        <a href="https://plus.google.com/share?url={{url('/')}}&image={{$image}}" target="_blank"  class="google_congratulation"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="cong cong_btn" style="margin-top: 30px;">
                            <a class="rlink" style="font-size: 20px;font-weight: bold;"  href="https://goo.gl/forms/sOFyvbWqzBH98S2A3" target="_blank">Please give us feedback | Earn 5000 ProCoins&nbsp;&nbsp;<i class="fa fa-external-link" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    @endif
                    <div class="advance_step_1">
                        <a title="Click to upload image" href="javascript:void(0)" onclick="getQuestionDataAdvanceLevel(3, <?php echo $professionDetail[0]->id; ?>, <?php echo $response['teen_id'];?>);" class="icon_cate picture_pop">
                            <span class="advance_level_title">Upload your Image</span>
                            <img src="{{asset('/frontend/images/picture.png')}}" alt="">
                            <span><i class="fa fa-upload" aria-hidden="true"></i></span>
                        </a>
                        
                        <a title="Click to upload video" href="javascript:void(0)" class="video_pop icon_cate" onclick="getQuestionDataAdvanceLevel(1, <?php echo $professionDetail[0]->id; ?>, <?php echo $response['teen_id'];?>);">
                            <span class="advance_level_title">Upload your video</span>
                            <img src="{{asset('/frontend/images/video-camera.png')}}" alt="">
                            <span><i class="fa fa-upload" aria-hidden="true"></i></span>
                        </a>
                                              
                        <a title="Click to upload document" href="javascript:void(0)" class="file_pop icon_cate" onclick="getQuestionDataAdvanceLevel(2, <?php echo $professionDetail[0]->id; ?>, <?php echo $response['teen_id'];?>);">
                            <span class="advance_level_title">Upload your document</span>
                            <img src="{{asset('/frontend/images/file.png')}}" alt="">
                            <span><i class="fa fa-upload" aria-hidden="true"></i></span>
                        </a>
                    </div>
                </div>
                @else
                <div class="no_data_page">
                    <span class="nodata_outer">
                        <span class="nodata_middle">
                            No such any action available!
                        </span>
                    </span>
                </div>
                @endif
            </div>            
        </div>           
    </div>
</div>
<div id="myModal" class="modal fade cst_modals" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content level4_basic_pop_up">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div id="ajax_data">

            </div>

        </div>
    </div>
</div>
<audio id="congrats_sound" src="{{ asset('frontend/audio/Congratulation.mp3')}}"></audio>

<div id="myModalAdvance" class="modal fade info_modal default_popup" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
                <button type="button" class="close close_next" data-dismiss="modal">Next</button>
                <div class="default_logo"><img src="{{asset('/frontend/images/proteen_logo.png')}}" alt=""></div>
				<div class="sticky_pop_head"><h2 class="title"><span class="l-4"><span class="level_label">L-4</span></span></h2></div>

                <div class="modal-body">
                    <div class="default_content">
                        <p>Upload images, videos & documents</p><br/>
                    </div>
                    <div class="begin_play_section default_footer">

                        <div class="default_action pull-left">
                            <div class="dont_show_me_again default_box">
                                <input type="checkbox" name="sponsor" id="login_intro_popup" class="squere" onchange="dontshowmodel({{Auth::guard('parent')->user()->id}}, 9)"/>
                            <label for="login_intro_popup" id="mychoice_lable"><span></span>Don't show me again</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

@stop
@section('script')
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $(".table_container_outer").mCustomScrollbar({
            axis: "yx"
        });
        $('.info_modal').modal('show');    
        $('#login_intro_popup').click(function(){
                    setTimeout(function() {
                    $('#myModalAdvance').modal('hide');
                }, 1000);

        });
    });
    function getQuestionDataAdvanceLevel(activity_type, professionId, teenId)
    {
        $.ajax({
            url: "{{ url('parent/get-question-data-advance-level') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "activity_type": activity_type,
                "professionId": professionId,
                "teenId": teenId
            },
            success: function(response) {
                $('#ajax_data').html(response);
                $('#myModal').modal('show');
            }
        });
    }

</script>

<script src="{{ asset('frontend/js/congo_animation.js')}}"></script>
@if($response['showCongrats'] == 'yes')
<script>

    window.fbAsyncInit = function() {
            FB.init({
                appId: FACEBOOK_CLIENT_ID,
                xfbml: true,
                version: 'v2.3'
            });
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {
                return;
            }
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));

        function shareFacebook(url, title, desc, image)
        {
            var obj = {method: 'feed', link: url, name: title, description: desc, picture: image};
            function callback(response) {
                if (response) {
                    console.log(response);
                }
            }
            FB.ui(obj, callback);
        }
    jQuery(document).ready(function($) {
        var $animation_elements = $('.animation-element');
        var $window = $(window);
        function check_if_in_view() {
            var window_height = $window.height();
            var window_top_position = $window.scrollTop();
            var window_bottom_position = (window_top_position + window_height);
            $.each($animation_elements, function() {
                var $element = $(this);
                var element_height = $element.outerHeight();
                var element_top_position = $element.offset().top;
                var element_bottom_position = (element_top_position + element_height);
                if ((element_bottom_position >= window_top_position) &&
                        (element_top_position <= window_bottom_position)) {
                    $element.removeClass('in-view');
                }
            });
        }
        $window.on('scroll resize', check_if_in_view);
        $window.trigger('scroll');
    });
</script>
@endif
@stop