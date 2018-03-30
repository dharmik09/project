@if( isset($response['data']) && !empty($response['data']) )
    <div id="intermediateErrorGoneMsg" class="intermediateErrorGoneMsg"></div>
    <div class="quiz_view">
        <div class="loading-screen loading-wrapper-sub intermediate-question-loader" style="display:none;">
            <div class="loading-content"></div>
        </div>
        
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
        <form id = "level4_intermediate_activity_ans" action="{{ url('/teenager/save-intermediate-level-activity') }}" onkeypress="return event.keyCode != 13;" role = "form" enctype = "multipart/form-data" method = "POST" autocomplete = "off" autocorrect = "off" autocapitalize = "off" spellcheck = "false">
            <input type = "hidden" name = "_token" value = "{{ csrf_token() }}">
            <input type = "hidden" id = "questionID" name = "questionID" value = "{{$response['data']->activityID}}" >
            <input type ="hidden" id="blackholeIntermediate" name="timer" />
            <input type ="hidden" id="ajax_answer_type" name="ajax_answer_type" value="{{$response['data']->gt_temlpate_answer_type}}" />
        
            <div class="quiz-que">
                <p class="que"><i class="icon-arrow-simple"></i>{!! $response['data']->l4ia_question_text !!}</p>
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
                        <div class="colorWhite">{!! ucfirst($response['data']->l4ia_question_description) !!}</div>
                    @endif
                    @if(isset($response['data']->totalCorrectOptions) &&  $response['data']->totalCorrectOptions > 1)
                        <p class="multiple-select">(You can select multiple answers for this question)</p>
                    @endif
                    <div class="answer-statement response_message_outer">
                        <p id="answerRightWrongMsg"></p>
                    </div>
                    <h2 class="colorYellow" id="systemCorrectAnswerText"></h2>
                    <div class="clearfix answer-statement">
                        <p id="showResponseMessage"></p>
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
                    ?>
                    <div class="box optionSelectionIntermediate form-group quiz-text">
                        @php($correctOptionLength = strlen($response['data']->correctOption))
                        <span id='singleLineCheck' value='yes'></span>
                        @if (isset($correctOptionLength) && $correctOptionLength > 0)
                            @php($x = 0)
                            <div class='block-grp fill_in_blank'>
                                <input type='text' class="form-control" id='single_line_answer_box' name='answer[{{$x}}]' maxlength='{{$correctOptionLength}}' size='{{$correctOptionLength}}' />
                            </div>
                        @else
                            <div class='block-grp fill_in_blank'>
                                <span>Something went wrong with this question!</span>
                            </div>
                        @endif
                    </div>
                    <div class="text-center next-intermediate" style="display: none;">
                        <br/>
                        <span class="btn-play btn-play-intermediate" style="display:none;"><img src="{{Storage::url('img/loading.gif')}}"></span>
                        <button class="btn btn-primary btn-next btn-intermediate" type="button" title="Next" onClick="getNextIntermediateQuestion({{$response['data']->l4ia_question_template}});">Next</button>
                    </div>
                    <div class="clearfix text-center">
                        <a href="javascript:void(0);" class="next-que saveIntMe" onClick="saveIntermediateAnswer();">
                            <i class="icon-hand"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @if( isset($response['data']->gt_temlpate_answer_type) && ( $response['data']->l4ia_question_popup_image != '' || $response['data']->l4ia_question_popup_description != '') )
        <?php $setPopupTime = $response['data']->l4ia_extra_question_time; ?>
        <div id="quiz_material_popup" class="modal fade quiz_pre_material " role="dialog" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content modal_content_fill">
                    <div class="time_out_cst"></div>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><i class="icon-close"></i></button>
                    </div>
                    <div class="modal-body popup_image_and_desc">   
                        @if(isset($response['data']->l4ia_question_popup_description) && $response['data']->l4ia_question_popup_description != '')                
                        <div class="pre_material_img">
                            {!! $response['data']->l4ia_question_popup_description !!}
                        </div>
                        @endif  
                        @if(isset($response['data']->l4ia_question_popup_image) && $response['data']->l4ia_question_popup_image != '')                
                            <div class="img_cont_pop l4i_popup_image video-img">
                                <img src="{{$response['data']->l4ia_question_popup_image}}" id="l4I_popup_image"/>
                            </div>
                        @endif                      
                    </div>                                           
                </div>
            </div>
        </div>
    @else
        <?php $setPopupTime = 0; ?>
    @endif
    <script type="text/javascript">
        var intermediateCount = {{ (isset($response['timer']) && $response['timer'] != "") ? $response['timer'] : 0 }};
        var ansTypeSet = "{{ (isset($response['data']->gt_temlpate_answer_type)) ? $response['data']->gt_temlpate_answer_type : 0 }}";
        var setPopupTime = {{ (isset($setPopupTime)) ? $setPopupTime : 0 }};
        var optionType = "{{ isset($optionType) ? $optionType : 'radio' }}";
        var optionName = "{{ isset($optionName) ? $optionName : 'answerID[0]' }}";
        var limitSelect = {{ (isset($response['data']->totalCorrectOptions) && $response['data']->totalCorrectOptions > 1) ? $response['data']->totalCorrectOptions : 1 }};
    </script>
@else
    @if( isset($response['intermediateCompleted']) && $response['intermediateCompleted'] == 1 )
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
