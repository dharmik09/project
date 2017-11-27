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
                    @include('parent/level4ActivityIntermediateQuestionResponse')
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
        @if(isset($hint[0]))
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
<script src="{{asset('frontend/js/jquery.ui.touch-punch.min.js')}}"></script>
<script>
    $(".table_container_outer").mCustomScrollbar({
        axis: "yx"
    });
    <?php if(empty($response['data'])){ ?>
        var audio = document.getElementById('congrats_sound');
        audio.play();
    <?php } ?>
    var col_count = $('.drg_section').data('col');
    function adjusting_box_size() {
        var col_width = $('.drg_section').width();
        var finale_width = col_width / col_count;
        $('.drag_drp li').height(finale_width).width(finale_width - 4);
    }
    $(window).resize(function(event) {
        adjusting_box_size();
    });
    
    $('.zoom_me').click(function(event) {
        var image_src = $(this).siblings('img').attr('src');
        $('#opption_zoom_image').modal('show');
        $('#opption_zoom_image img').attr('src', image_src);
    });
    if (limitSelect > 1) {
        $("[name='" + optionName + "']").on('change', function(evt) {
            if ($('input.multiCast:checked').length > limitSelect) {
                this.checked = false;
                window.scrollTo(0, 0);
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
//    jQuery(document).ready(function($) {
    $(window).bind("load", function() {
        adjusting_box_size();
        $('#single_line_answer_box').focus();

        var time_out_question = setPopupTime * 1000;
        var count = timeCount;
        if ($("#quiz_material_popup").length == 0) {
            var counter = setInterval(timer, 1000);
        } else {
            $('#quiz_material_popup').on('hidden.bs.modal', function() {
                var counter = setInterval(timer, 1000);
            });
        }

        //var counter = setInterval(timer, 1000);
        function secondPassed() {
            var minutes = Math.round((count - 30) / 60);
            var remainingcount = count % 60;
            if (remainingcount < 10) {
                remainingcount = "0" + remainingcount;
            }
            $('#timer_countdown span,#timer_countdown span.animation').text(minutes + ":" + remainingcount);
            if (minutes == 0 && remainingcount < 10) {
                $('#timer_countdown span.animation').show();
            }
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
        if (time_out_question > 0) {
            $('#quiz_material_popup').modal('show');
            var popup_img = document.getElementById('l4I_popup_image');
            //or however you get a handle to the IMG
            var popimgwidth = popup_img.clientWidth;
            var popimgheight = popup_img.clientHeight;
            if (popimgwidth > popimgheight) {
                $('.img_cont_pop').removeClass('max_height').addClass('max_width');
            } else {
                $('.img_cont_pop').removeClass('max_width').addClass('max_height');
            }
            //$('#quiz_material_popup .modal-body').mCustomScrollbar();
            $('#quiz_material_popup').on('shown.bs.modal', function() {
                setTimeout(function() {
                    $('#quiz_material_popup').modal('hide');
                }, time_out_question);
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
            });
        }
    });
    function autoSubmitAnswer() {
        if ($("#setResponse").val() == 0) {
            if (ansTypeSet == "option_choice_with_response" || ansTypeSet == "select_from_dropdown_option" || ansTypeSet == "single_line_answer" || ansTypeSet == "filling_blank" || ansTypeSet == "option_choice" || ansTypeSet == "image_reorder" || ansTypeSet == "option_reorder" || ansTypeSet == "true_false") {
                var timer = 0;
                var questionID = $("#questionID").val();
                var answerId = 0;
                var answer_order = 0;
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                var form_data = 'questionID=' + questionID + '&answer[0]=' + answerId + '&timer=' + timer + '&answer_order[0]=' + answer_order;
                $('.ajax-loader').show();
                $("#timer_countdown").css('visibility', 'hidden');
                $.ajax({
                    type: 'POST',
                    data: form_data,
                    dataType: 'html',
                    url: "{{ url('/parent/play-level4-intermediate-activity')}}",
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    cache: false,
                    success: function(data) {
                        $('.ajax-loader').hide();
                        var obj = $.parseJSON(data);
                        if (obj.status == 1) {
                            $("#setResponse").val("1");
                            if (obj.answerType == "single_line_answer") {
                                $("#answerRightWrongMsg").text(obj.answerRightWrongMsg + " Correct Answer Is : " + obj.systemCorrectAnswerText + "");
                                if (obj.systemCorrectAnswer == 1) {
                                    $(".response_message_outer").addClass("response_message beta mrTop15");
                                } else {
                                    $(".response_message_outer").addClass("response_message alpha mrTop15");
                                }
                            } else if (obj.answerType === "option_choice_with_response" || obj.answerType === "filling_blank" || obj.answerType === "option_choice" || obj.answerType === "true_false" || obj.answerType === "image_reorder") {

                                $("#answerRightWrongMsg").text(obj.answerRightWrongMsg);
                                if (obj.systemCorrectAnswer == 1) {
                                    $(".response_message_outer").addClass("response_message beta mrTop15");
                                } else {
                                    $(".response_message_outer").addClass("response_message alpha mrTop15");
                                }
                                if(obj.systemCorrectAnswer2){
                                    $.each(obj.systemCorrectAnswer2, function(key, value) {
                                        if (value == 1) {
                                            $('.myClass' + key).addClass("right_answer");
                                        } else {
                                            $('.myClass' + key).addClass("wrong_answer");
                                        }
                                    });
                                }
                                if (obj.answerType === "option_choice") {
                                    if (obj.questionAnswerText !== '') {
                                        var phtml = "<div class='response_info image_type'><div class='image_detail_outer clearfix'><div class='image_detail_outer_img'></div><div class=''>" + obj.questionAnswerText + "</div></div></div>";
                                        $('#showResponseMessage').html(phtml);
                                    }
                                }
                                if (obj.answerType === "option_choice_with_response") {
                                    if (obj.questionAnswerText && obj.questionAnswerText !== '') {
                                        var phtmlImg = '';
                                        if (obj.questionAnswerImage && obj.questionAnswerImage !== '') {
                                            phtmlImg = "<img src=" + obj.questionAnswerImage + " />";
                                        }
                                        var phtml = "<div class='response_info image_type'><div class='image_detail_outer clearfix'><div class='image_detail_outer_img'>" + phtmlImg + "</div><div class='info_body'>" + obj.questionAnswerText + "</div></div></div>";
                                        $('#showResponseMessage').html(phtml);
                                    }
                                }
                                if (obj.answerType == "image_reorder") {
                                    //$("#showResponseMessage").text(obj.answerRightWrongMsg);
                                    if (obj.systemCorrectAnswer == 1) {
                                        $(".response_message_outer").addClass("response_message beta mrTop15");
                                    } else {
                                        $(".response_message_outer").addClass("response_message alpha mrTop15");
                                    }
                                }
                            } else if (obj.answerType == "select_from_dropdown_option") {
                                if (obj.systemCorrectAnswer == 1) {
                                    $(".response_message_outer").addClass("response_message beta mrTop15");
                                    $("#answerRightWrongMsg").text(obj.answerRightWrongMsg);
                                    $(".answer_select_box.special_select").addClass("right_answer");
                                } else {
                                    $("#answerRightWrongMsg").text(obj.answerRightWrongMsg + ", Correct answer is given below");
                                    $(".response_message_outer").addClass("response_message alpha mrTop15");
                                    $("#dropDownTypeSelection option[value='" + obj.systemCorrectOptionOrder + "']").attr("selected", "selected");
                                    $("#dropDownSelection option[value=" + obj.systemCorrectOptionId + "]").attr("selected", "selected");
                                    $(".answer_select_box.special_select").addClass("right_answer");
                                }
                            } else if (obj.answerType == "option_reorder") {
                                $("#answerRightWrongMsg").text(obj.answerRightWrongMsg + " Correct order is given below");
                                if (obj.systemCorrectAnswer == 1) {
                                    $(".response_message_outer").addClass("response_message beta mrTop15");
                                } else {
                                    $(".response_message_outer").addClass("response_message alpha mrTop15");
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
                            $('.saveMe').removeAttr('disabled');
                            $('.saveMe').text('Next');
                            $('.saveMe').removeAttr("onclick");
                            $('.saveMe').attr("onClick", "location.reload(true)");
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

    function saveAnswer() {
        $('.stopOnSubmit iframe').attr("src", jQuery(".stopOnSubmit iframe").attr("src"));
        var isAudio = $("#checkAudio").val();
        if(typeof isAudio !== "undefined"){
            var audioStop = document.getElementById('onOffAudio');
            audioStop.pause();
            $("#onOffAudio").prop('muted',true);
        }
        var validCheck = 0;
        var setSMsg = 0;
        if ($("#singleLineCheck").attr('value') === "yes") {
            setSMsg = 1;
            if ($('[name="answer[0]"]').val().trim() !== '') {
                validCheck = 1;
                $("#timer_countdown").css('visibility', 'hidden');
            }
        } else {
            if ($('[name="' + optionName + '"]:checked').length > 0) {
                validCheck = 1;
                $("#timer_countdown").css('visibility', 'hidden');
            }
        }

        if (validCheck === 1) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var form_data = $("#level4_intermediate_activity_ans").serialize();
            $('.ajax-loader').show();
            $('.saveMe').attr('disabled', 'disabled');
            $.ajax({

                type: 'POST',
                data: form_data,
                //async: false,
                dataType: 'html',
                url: "{{ url('/parent/play-level4-intermediate-activity')}}",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                cache: false,
                success: function(data) {
                    $('.ajax-loader').hide();
                    var obj = $.parseJSON(data);
                    if (obj.status == 1) {
                        $("#setResponse").val("1");
                        if (obj.answerType == "single_line_answer") {
                            $("#answerRightWrongMsg").text(obj.answerRightWrongMsg + " Correct Answer Is : " + obj.systemCorrectAnswerText + "");
                            if (obj.systemCorrectAnswer == 1) {
                                $(".response_message_outer").addClass("response_message beta mrTop15");
                            } else {
                                $(".response_message_outer").addClass("response_message alpha mrTop15");
                            }
                        } else if (obj.answerType === "option_choice_with_response" || obj.answerType === "filling_blank" || obj.answerType === "option_choice" || obj.answerType === "true_false") {
                            $("#answerRightWrongMsg").text(obj.answerRightWrongMsg);
                            if (obj.systemCorrectAnswer == 1) {
                                $(".response_message_outer").addClass("response_message beta mrTop15");
                            } else {
                                $(".response_message_outer").addClass("response_message alpha mrTop15");
                            }
                            $.each(obj.systemCorrectAnswer2, function(key, value) {
                                if (value == 1) {
                                    $('.myClass' + key).addClass("right_answer");
                                } else {
                                    $('.myClass' + key).addClass("wrong_answer");
                                }
                            });
                            if (obj.answerType === "option_choice") {
                                if (obj.questionAnswerText !== '') {
                                    var phtml = "<div class='response_info image_type'><div class='image_detail_outer clearfix'><div class='image_detail_outer_img'></div><div class=''>" + obj.questionAnswerText + "</div></div></div>";
                                    $('#showResponseMessage').html(phtml);
                                }
                            }
                            if (obj.answerType === "option_choice_with_response") {
                                if (obj.questionAnswerText && obj.questionAnswerText !== '') {
                                    var phtmlImg = '';
                                    if (obj.questionAnswerImage && obj.questionAnswerImage !== '') {
                                        phtmlImg = "<img src=" + obj.questionAnswerImage + " />";
                                    }
                                    var phtml = "<div class='response_info image_type'><div class='image_detail_outer clearfix'><div class='image_detail_outer_img'>" + phtmlImg + "</div><div class='info_body'>" + obj.questionAnswerText + "</div></div></div>";
                                    $('#showResponseMessage').html(phtml);
                                }
                            }
                        } else {
                        }
                        $('.saveMe').removeAttr('disabled');
                        $('.saveMe').text('Next');
                        $('.saveMe').removeAttr("onclick");
                        $('.saveMe').attr("onClick", "location.reload()");
                        //setTimeout("location.reload(true);", 2000);
                    } else {
                        $("#showResponseMessage").text(obj.message);
                        var urlSet = obj.redirect;
                        //setTimeout("location.reload(true);", 3000);
                    }
                }
            });
        } else {
            if (setSMsg === 1) {
                window.scrollTo(0, 0);
                if ($("#useForClass").hasClass('r_after_click')) {
                    $("#errorGoneMsg").html('');
                }
                $("#errorGoneMsg").fadeIn();
                $("#errorGoneMsg").append('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please, Fillup the answer</span></div></div></div>');
                setTimeout(function() {
                    $("#errorGoneMsg").fadeOut();
                }, 3000);
            } else {
                window.scrollTo(0, 0);
                if ($("#useForClass").hasClass('r_after_click')) {
                    $("#errorGoneMsg").html('');
                }
                $("#errorGoneMsg").fadeIn();
                $("#errorGoneMsg").append('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please, select at-least one answer</span></div></div></div>');
                setTimeout(function() {
                    $("#errorGoneMsg").fadeOut();
                }, 3000);
            }
        }
    }

    function saveDropDrag() {
        var audio = document.getElementById('audio_1');
        var optionLength = $('#d_d_count').attr('value');
        var countBox = 1;
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
            $("#timer_countdown").css('visibility', 'hidden');
        }

        if (validCheckAll === 1) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var form_data = $("#level4_intermediate_activity_ans").serializeArray();
            form_data.push({name: 'answer[0]', value: arra_ans});
            $('.ajax-loader').show();
            $('.saveMe').attr('disabled', 'disabled');
            $.ajax({
                type : 'POST',
                data : form_data,
                //async: false,
                dataType: 'html',
                url: "{{ url('/parent/play-level4-intermediate-activity')}}",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                cache: false,
                success: function(data) {
                    $('.ajax-loader').hide();
                    var obj = $.parseJSON(data);
                    if (obj.status == 1) {
                        $("#setResponse").val("1");
                        if (obj.answerType == "image_reorder") {
                            $("#answerRightWrongMsg").text(obj.answerRightWrongMsg);
                            if (obj.systemCorrectAnswer == 1) {
                                $(".response_message_outer").addClass("response_message beta mrTop15");
                            } else {
                                $(".response_message_outer").addClass("response_message alpha mrTop15");
                            }
                        } else {
                        }
                        $('.saveMe').removeAttr('disabled');
                        $('.saveMe').text('Next');
                        $('.saveMe').removeAttr("onclick");
                        $('.saveMe').attr("onClick", "location.reload(true)");
                    } else {
                        $("#showResponseMessage").text(obj.message);
                        var urlSet = obj.redirect;
                        //setTimeout("location.reload(true);", 3000);
                    }
                }
            });
        } else {
            window.scrollTo(0, 0);
            if ($("#useForClass").hasClass('r_after_click')) {
                $("#errorGoneMsg").html('');
            }
            $("#errorGoneMsg").fadeIn();
            $("#errorGoneMsg").append('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please fill box</span></div></div></div>');
            setTimeout(function() {
                $("#errorGoneMsg").fadeOut();
            }, 3000);
        }
    }

    function saveDropDown() {
        var validCheckAll = 0;
        var answerValue = $("#dropDownSelection").val();
        var answerTypeValue = $("#dropDownTypeSelection").val();
        if (answerValue > 0 && answerTypeValue != '') {
            validCheckAll = 1;
            $("#timer_countdown").css('visibility', 'hidden');
        } else {
            validCheckAll = 0;
        }
        if (validCheckAll === 1) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var form_data = $("#level4_intermediate_activity_ans").serializeArray();
            $('.ajax-loader').show();
            $('.saveMe').attr('disabled', 'disabled');
            $.ajax({
                type: 'POST',
                data: form_data,
                //async: false,
                dataType: 'html',
                url: "{{ url('/parent/play-level4-intermediate-activity')}}",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                cache: false,
                success: function(data) {
                    $('.ajax-loader').hide();
                    var obj = $.parseJSON(data);
                    if (obj.status == 1) {
                        $("#setResponse").val("1");
                        if (obj.answerType == "select_from_dropdown_option") {
                            if (obj.systemCorrectAnswer == 1) {
                                $(".response_message_outer").addClass("response_message beta mrTop15");
                                $("#answerRightWrongMsg").text(obj.answerRightWrongMsg);
                                $(".answer_select_box.special_select").addClass("right_answer");
                            } else {
                                $("#answerRightWrongMsg").text(obj.answerRightWrongMsg + ", Correct answer is given below");
                                $(".response_message_outer").addClass("response_message alpha mrTop15");
                                $("#dropDownTypeSelection option[value='" + obj.systemCorrectOptionOrder + "']").attr("selected", "selected");
                                $("#dropDownSelection option[value=" + obj.systemCorrectOptionId + "]").attr("selected", "selected");
                                $(".answer_select_box.special_select").addClass("right_answer");
                            }

                        } else {
                            $("#answerRightWrongMsg").text("Invalid answer type");
                        }
                        $('.saveMe').removeAttr('disabled');
                        $('.saveMe').text('Next');
                        $('.saveMe').removeAttr("onclick");
                        $('.saveMe').attr("onClick", "location.reload(true)");
                    } else {
                        $("#showResponseMessage").text(obj.message);
                        var urlSet = obj.redirect;
                        //setTimeout("location.reload(true);", 3000);
                    }
                }
            });
        } else {
            window.scrollTo(0, 0);
            if ($("#useForClass").hasClass('r_after_click')) {
                $("#errorGoneMsg").html('');
            }
            $("#errorGoneMsg").fadeIn();
            $("#errorGoneMsg").append('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Please select order and answer</span></div></div></div>');
            setTimeout(function() {
                $("#errorGoneMsg").fadeOut();
            }, 3000);
        }
    }

    function saveOptionReorder() {
        var countBox = 1;
        var arra_ans = [];
        var validCheckAll = 0;
        $('.ui-state-default').each(function() {
            var id_string = $(this).attr('id');
            arra_ans.push(id_string);
        });
        if (arra_ans.length < 1) {
            validCheckAll = 0;
        } else {
            validCheckAll = 1;
            $("#timer_countdown").css('visibility', 'hidden');
        }
        if (validCheckAll === 1) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var form_data = $("#level4_intermediate_activity_ans").serializeArray();
            form_data.push({name: 'answer[0]', value: arra_ans});
            $('.ajax-loader').show();
            $('.saveMe').attr('disabled', 'disabled');
            $.ajax({
                type: 'POST',
                data: form_data,
                //async: false,
                dataType: 'html',
                url: "{{ url('/parent/play-level4-intermediate-activity')}}",
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                cache: false,
                success: function(data) {
                    $('.ajax-loader').hide();
                    var obj = $.parseJSON(data);
                    if (obj.status == 1) {
                        $("#setResponse").val("1");
                        if (obj.answerType == "option_reorder") {
                            $("#answerRightWrongMsg").text(obj.answerRightWrongMsg + " Correct order is given below");
                            if (obj.systemCorrectAnswer == 1) {
                                $(".response_message_outer").addClass("response_message beta mrTop15");
                            } else {
                                $(".response_message_outer").addClass("response_message alpha mrTop15");
                            }
                            if (obj.systemCorrectOptionOrder) {
                                var htmlLI = '';
                                $.each(obj.systemCorrectOptionOrder, function(key, value) {
                                    htmlLI += '<li class="ui-state-default right" id="' + value['optionId'] + '"><span class="sortable_outer_container"><span class="sortable_container"><span class="drag_me_text">' + value['optionText'] + '</span></span></span></li>';
                                });
                                $("#sortable").html(htmlLI);
                            } else {
                                window.scrollTo(0, 0);
                                if ($("#useForClass").hasClass('r_after_click')) {
                                    $("#errorGoneMsg").html('');
                                }
                                $("#errorGoneMsg").fadeIn();
                                $("#errorGoneMsg").append('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Refresh this page</span></div></div></div>');
                                setTimeout(function() {
                                    $("#errorGoneMsg").fadeOut();
                                }, 3000);
                            }
                        } else {
                            window.scrollTo(0, 0);
                            if ($("#useForClass").hasClass('r_after_click')) {
                                $("#errorGoneMsg").html('');
                            }
                            $("#errorGoneMsg").fadeIn();
                            $("#errorGoneMsg").append('<div class="col-md-8 col-md-offset-2 r_after_click" id="useForClass"><div class="box-body"><div class="alert alert-error danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button><span class="fontWeight">Something went wrong</span></div></div></div>');
                            setTimeout(function() {
                                $("#errorGoneMsg").fadeOut();
                            }, 3000);
                            setTimeout("location.reload(true);", 4000);
                        }
                        $('.saveMe').removeAttr('disabled');
                        $('.saveMe').text('Next');
                        $('.saveMe').removeAttr("onclick");
                        $('.saveMe').attr("onClick", "location.reload(true)");
                    } else {
                        $("#showResponseMessage").text(obj.message);
                        var urlSet = obj.redirect;
                        setTimeout("location.reload(true);", 4000);
                    }
                }
            });
        } else {
            alert("Please, try it again");
        }
    }

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
    if (ansTypeSet == "option_reorder") {
        jQuery(document).ready(function($) {
            $("#sortable").sortable();
            $("#sortable").disableSelection();
            $("#sortable li").on("mousedown", function() {
                $(this).addClass("mouseDown");
            }).on("mouseup", function() {
                $(this).removeClass("mouseDown");
            });
        });
    }
    if (ansTypeSet == "option_reorder_with_step") {
        jQuery(document).ready(function($) {
            $(".drag_drp_step li span.outer").draggable({
                opacity: "0.5",
                helper: "clone",
                containment: "document"
            });
            $(".drag_drp_step li").droppable({
                hoverClass: "ui-state-active",
                drop: function(event, ui) {
                    if ($(this).find('span.outer').length == 0) {
                        ui.draggable.detach().appendTo($(this));
                    }
                }
            });
            $('.answer_submit_btn button').click(function(event) {
                var count = 1;
                var arra_ans = [];
                $('.step_box_dropabble .drp_section_step li').each(function() {
                    if ($(this).find('span.outer').length == 0) {
                        //alert("Please fill step no:" + count);
                        return false;
                    } else {
                        var ans_array = $(this).find('span.outer').attr('id');
                        arra_ans.push(ans_array);
                    }
                    count++;
                });
            });
        });
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
    </script>

<?php } else { ?>
    <script>
        var cst_fix_cst_fix = $(".cst_fix_cst_fix");
        var position_cst_fix = cst_fix_cst_fix.offset();
        $(window).scroll(function(event) {
            var scroll_cst_fix = $(window).scrollTop();
            if (position_cst_fix !== "undefined" && scroll_cst_fix > position_cst_fix.top) {
                $('.cst_fix_cst_fix').addClass('fix_time');
            } else {
                $('.cst_fix_cst_fix').removeClass('fix_time');
            }
        });
    </script>
<?php } ?>
@stop