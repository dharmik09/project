@if( isset($response['data']) && !empty($response['data']) )
    <div id="basicErrorGoneMsg"></div>
    <div class="quiz_view">
        <div class="clearfix time_noti_view">
            <span class="time_type pull-left">
                <i class="icon-alarm"></i>
                <span class="time-tag intermediate-time-tag">{{ $response['timer'] }}</span>
            </span>
            <span class="help_noti pull-right">
                <span class="pull-right close">
                    <i class="icon-close"></i>
                </span>
            </span>
        </div>
        <form id = "level4_intermediate_activity_ans" action="" role = "form" enctype = "multipart/form-data" method = "POST" autocomplete = "off" autocorrect = "off" autocapitalize = "off" spellcheck = "false">
            <input type = "hidden" name = "_token" value = "{{ csrf_token() }}">
            <input type = "hidden" id = "questionID" name = "questionID" value = "{{$response['data']->activityID}}" >
            <input type ="hidden" id="blackhole" name="timer" />
            <input type ="hidden" id="ajax_answer_type" name="ajax_answer_type" value="{{$response['data']->gt_temlpate_answer_type}}" />
        
            <div class="quiz-que">
                <p class="que"><i class="icon-arrow-simple"></i>{{ $response['data']->l4ia_question_text }}</p>
                <div class="quiz-ans">
                    @if(isset($response['data']->gt_temlpate_answer_type) && ( $response['data']->gt_temlpate_answer_type == "option_choice" || $response['data']->gt_temlpate_answer_type == "true_false" || $response['data']->gt_temlpate_answer_type == "single_line_answer" || $response['data']->gt_temlpate_answer_type == "option_choice_with_response" || $response['data']->gt_temlpate_answer_type == "option_reorder" || $response['data']->gt_temlpate_answer_type == "image_reorder" || $response['data']->gt_temlpate_answer_type == "filling_blank"))
                        @if(isset($response['data']->question_images) && !empty($response['data']->question_images))
                            @foreach($response['data']->question_images as $key=>$image)
                                <div class="question-img">
                                    <img src="{{$image['l4ia_question_image']}}" title="{{isset($image['l4ia_question_imageDescription']) && ($image['l4ia_question_imageDescription'] != '') ? $image['l4ia_question_imageDescription']:'Click to enlarge image'}}" class="pop-me pop_up_me">
                                </div>
                            @endforeach
                        @endif
                    @endif
                    @if(isset($response['data']->l4ia_question_audio) && $response['data']->l4ia_question_audio != '')
                        <div class="quiz-audio">
                            <audio controls id="onOffAudio">                        
                                <source src="{{$response['data']->l4ia_question_audio}}" type="audio/mpeg" id="checkAudio">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    @endif
                    @if(isset($response['data']->l4ia_question_video) && $response['data']->l4ia_question_video != '')
                        <?php 
                            $videoCode = Helpers::youtube_id_from_url($response['data']->l4ia_question_video);
                            $videoCode = ($videoCode != '') ? $videoCode : "ScMzIvxBSi4";
                        ?>
                        <div class="question_image_level_4 stopOnSubmit video-img">
                            <iframe width="560" height="315" src="https://www.youtube.com/embed/{{$videoCode}}?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
                        </div>
                    @endif
                    @if(isset($response['data']->l4ia_question_description) && $response['data']->l4ia_question_description != '')
                        <h3 class="colorWhite">{!! ucfirst($response['data']->l4ia_question_description) !!}</h3>
                    @endif
                    @if(isset($response['data']->totalCorrectOptions) &&  $response['data']->totalCorrectOptions > 1)
                        <span class="colorYellow">(You can select multiple answers for this question)</span>
                    @endif
                    <div class="answer-statement" style="display:none;">
                        <p id="answerRightWrongMsg"></p>
                    </div>
                    <h2 class="colorYellow" id="systemCorrectAnswerText"></h2>
                    <div class="clearfix">
                        <div style="text-align: left;" id="showResponseMessage">
                        </div>
                    </div>
                    <?php
                        $input = '';
                        if ($response['data']->totalCorrectOptions > 1) {
                            $optionType = "checkbox";
                            $optionName = "answer[]";
                        } else {
                            $optionType = "radio";
                            $optionName = "answer[0]";
                        }
                        //$setFlag = 2;
                    ?>
                    <div class="box">
                        @if(isset($response['data']->options) && !empty($response['data']->options))
                         
                        @php( shuffle($response['data']->options) )
                            
                            @foreach($response['data']->options as $keyOption => $option)
                                <label class="{{$optionType}} class{{$option['optionId']}}">
                                    <input type="{{$optionType}}" id="check{{$option['optionId']}}" name="{{$optionName}}" value="{{$option['optionId']}}" class="selectionCheck multiCast"/>
                                    <span class="checker"></span>
                                    <em>{!! $option['optionText'] !!}</em>
                                </label>
                            @endforeach

                        @else
                            <div class='outer_con'>Opps ! No, any options.</div>
                        @endif
                            
                    </div>
                    
                    <div class="clearfix">
                        <a href="#" class="next-que pull-right">
                            <i class="icon-hand"></i>
                        </a>
                    </div>


                </div>
            </div>
        </form>
    </div>
@else
    @if( isset($response['basicCompleted']) && $response['basicCompleted'] == 1 )
        <div class="quiz_view">
            <div class="clearfix time_noti_view">
                <span class="help_noti pull-right">
                    <span class="pull-right close">
                        <i class="icon-close"></i>
                    </span>
                </span>
            </div>
            <div class="cong-block">
                <div class="row">
                    <div class="col-xs-4">
                        <span class="emojis-img"><img  alt="Congratulations" src="{{Storage::url('img/Original-image/icon-4.png')}}"></span>
                        <span class="emojis-img emojis-icon" style="font-size:65px !important"><i class="icon-thumb"></i></span>
                    </div>
                    <div class="col-xs-8">
                        <h2>Congratulations!</h2>
                        <p><strong>{{ ucwords($response['teenagerName']) }} !</strong> You are now a rookie ProTeen {{$response['professionName']}}.</p>
                        <!-- <p><strong><span class="font-blue">Your Score : </span> 2500</strong></p> -->
                    </div>
                </div>
            </div>
            
        </div>
    @else
        <div class="quiz_view">
            <div class="clearfix time_noti_view">
                <span class="help_noti pull-right">
                    <span class="pull-right close">
                        <i class="icon-close"></i>
                    </span>
                </span>
            </div>
            <div class="quiz-que">
                No Questions found!
            </div>
        </div>
    @endif
@endif
<script type="text/javascript">
    var intermediateCount = {{ (isset($response['timer']) && $response['timer'] != "") ? $response['timer'] : 0 }};
    // var optionType = '{{ (isset($optionType) && $optionType != '') ? $optionType : 0 }}';
    // var optionName = '{{ (isset($optionName) && $optionName != '') ? $optionName : "radio" }}';
    // var limitSelect = {{ (isset($response['data']->totalCorrectOptions) && $response['data']->totalCorrectOptions > 1) ? $response['data']->totalCorrectOptions : 1 }};
</script>