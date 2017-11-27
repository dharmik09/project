@extends('layouts.parent-master')

@section('content')

<div class="col-xs-12">
    <div class="row" id="errorGoneMsg"> </div>
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
<div class="centerlize">
    <div class="container_padd">
        <div class="container">
            <div class="inner_container">
                <div id="changeOnResponse">
                    @include('parent/level4ActivityQuestionResponse')
                </div>
            </div>
            <div class="loader ajax-loader" style="display:none;">
                <div class="cont_loader">
                    <div class="img1"></div>
                    <div class="img2"></div>
                </div>
            </div>
        </div>
        <?php
        if (isset($response['data']->activityID) && $response['data']->activityID != '') {
            $hint = Helpers::getHint('Level2', $response['data']->activityID);
            $hintArray = $hint->toArray();
            shuffle($hintArray);
            $hintImagePath = Config::get('constant.HINT_ORIGINAL_IMAGE_UPLOAD_PATH');
        } else {
            $hint = [];
        }
        ?>
        @if(!empty($hint))
        <div class="proteen_hint">
            <div class="container">
                <div class="hero_div clearfix">
                    <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">                                                  
                        <p class="hint_bottom">&nbsp;</p>
                    </div>
                    <span class="hero">
                        <span class="hero_outer">
                            <span class="hero_inner">
                                <img src="{{ Storage::url($hintImagePath.$hint[0]->hint_image)}}" alt="">
                            </span>
                        </span>
                    </span>
                </div>
            </div>
        </div>
        @else
        <div class="proteen_hint hide_bg_hint"></div>
        @endif
    </div>
</div>
<span id="setResponse" value="0"></span>
<audio id="audio_1" src="{{ asset('frontend/audio/L1A_1.wav')}}"></audio>
<audio id="audio_0" src="{{ asset('frontend/audio/L1A_0.wav')}}"></audio>
<audio id="audio_2" src="{{ asset('frontend/audio/L1A_2.wav')}}"></audio>
<audio id="congrats_sound" src="{{ asset('frontend/audio/Congratulation.mp3')}}"></audio>

@stop
@section('script')  

<script>
    
    $(window).bind("load", function() {
        var counter = setInterval(timer, 1000);
        function secondPassed() {
            var minutes = Math.round((count - 30) / 60);
            var remainingcount = count % 60;
            if (remainingcount < 10) {
                remainingcount = "0" + remainingcount;
            }
            $('#timer_countdown span,#timer_countdown span.animation').text(minutes + ":" + remainingcount);
            $('#timer_countdown span.animation').show();
        }
        function timer() {
            if (count < 0) {
            } else {
                secondPassed();
            }
            count = count - 1;
            $("#blackhole").val(count);           
            if (count == -1) {
                autoSubmitAnswer();
            }
        }
    });
    jQuery(document).ready(function($) {
            $(".table_container_outer").mCustomScrollbar({
                axis: "yx"
            });
        <?php if(empty($response['data'])){ ?>
            var audio = document.getElementById('congrats_sound');
            audio.play();
        <?php } ?>
        var allQuestionCompleted = '<?php echo $response['setCanvas'] ?>';
        if (allQuestionCompleted == 'no') {
            $('.info_modal').modal('show');
        }
    });
    if (limitSelect > 1) {
        $('[name="' + optionName + '"]').on('change', function(evt) {
            if ($('input.multiCast:checked').length > limitSelect) {
                this.checked = false;
                window.scrollTo(0,0);
                if ($("#useForClass").hasClass('r_after_click')) {
                    $("#errorGoneMsg").html('');
                }
                $("#errorGoneMsg").fadeIn();
                $("#errorGoneMsg").append('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">You can select maximum '+ limitSelect +' options</span></div></div></div>');
                setTimeout(function() {
                    $("#errorGoneMsg").fadeOut();
                }, 3000);
            }
        });
    }



    function saveAnswer() {
        var validCheck = 0;
        if ($('[name="' + optionName + '"]:checked').length > 0) {
            validCheck = 1;
            $("#timer_countdown").css('visibility', 'hidden');
        }
        if (validCheck == 1) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var form_data = $("#level4_activity_ans").serialize();
            $('.ajax-loader').show();
            $('.saveMe').attr('disabled', 'disabled');
            $.ajax({
                type: 'POST',
                data: form_data,
                dataType: 'html',
                url: "{{ url('/parent/play-level4-activity')}}",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                cache: false,
                success: function(data) {
                    $('.ajax-loader').hide();
                    var obj = $.parseJSON(data);
                    if (obj.status == 1) {
                        $("#setResponse").val("1");
                        $.each(obj.data, function(key, value) {
                            if (value == 1) {
                                $('.class' + key).addClass("right2_answer");
                            } else {
                                $('.class' + key).addClass("wrong2_answer");
                            }
                        });
                        setTimeout("location.reload(true);", 1000);
                    } else {
                        location.reload(true);
                    }
                }
            });
        } else {
            window.scrollTo(0, 0);
            if ($("#useForClass").hasClass('r_after_click')) {
                $("#errorGoneMsg").html('');
            }
            $("#errorGoneMsg").fadeIn();
            $("#errorGoneMsg").append('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please, select at-least one answer</span></div></div></div>');
            setTimeout(function(){$("#errorGoneMsg").fadeOut();},3000);
        }
    }
    function autoSubmitAnswer() {
        if ($("#setResponse").val() == 0) {
            var questionID = $("#questionID").val();
            var answerID = 0;
            var timer = 0;
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var form_data = 'questionID=' + questionID + '&answerID[0]=' + answerID + '&timer=' + timer;
            $('.ajax-loader').show();

            $.ajax({
                type: 'POST',
                data: form_data,
                dataType: 'html',
                url: "{{ url('/parent/play-level4-activity') }}",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                cache: false,
                success: function(data) {
                    $('.ajax-loader').hide();
                    var obj = $.parseJSON(data);
                    $("#setResponse").val("1");
                    $.each(obj.data, function(key, value) {
                        if (value == 1) {
                            $('.class' + key).addClass("right2_answer");
                        } else {
                            $('.class' + key).addClass("wrong2_answer");
                        }
                    });
                    setTimeout("location.reload(true);", 1000);
                    //location.reload(true);
                }
            });
        } else {
            return false;
            location.reload(true);
        }
    }

</script>
<?php if ($response['setCanvas'] == "yes") { ?>
    <script src="{{ asset('frontend/js/congo_animation.js')}}"></script>
    <script>

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
<?php } else { ?>
    <script>
        var cst_fix_cst_fix = $(".cst_fix_cst_fix");
        var position_cst_fix = cst_fix_cst_fix.offset();
        $(window).scroll(function(event) {
            var scroll_cst_fix = $(window).scrollTop();
            if (scroll_cst_fix > position_cst_fix.top) {
                $('.cst_fix_cst_fix').addClass('fix_time');
            } else {
                $('.cst_fix_cst_fix').removeClass('fix_time');
            }
        });
    </script>
<?php } ?>

@stop