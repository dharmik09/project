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

                        switch ($response['data']->gt_temlpate_answer_type) {
                            case "filling_blank":
                                $setFlag = 2;
                                if (isset($response['data']->options) && !empty($response['data']->options)) {
                                    $input .= "<div class='outer_con'>";
                                    foreach ($response['data']->options as $keyOption => $option) {
                                        if ($setFlag % 2 == 0) {
                                            $input .= "<div class='middle_con'>";
                                        }
                                        $input .= "<div class='inner_con myClass" . $option['optionId'] . "'>";
                                        $input .= "<input type='radio' id='radio$keyOption' name='answer[0]' value=" . $option['optionId'] . " >";
                                        $input .= "<label for='radio$keyOption'>" . $option['optionText'] . "</label>";
                                        $input .= "</div>";
                                        $setFlag++;
                                        if ($setFlag != 2 && $setFlag % 2 == 0) {
                                            $input .= "</div>";
                                        }
                                    }
                                    $input .= "</div>";
                                } else {
                                    $input .= "<div class='outer_con'>Opps ! No, any options.</div>";
                                }
                                break;
                            case "true_false":
                            case "option_choice_with_response":
                            case "option_choice":
                                $setFlag = 2;
                                if (isset($response['data']->options) && !empty($response['data']->options)) {
                                    $input .= "<div class='outer_con'>";
                                    shuffle($response['data']->options);
                                    foreach ($response['data']->options as $keyOption => $option) {
                                        if ($option['optionText'] == '') {
                                            if ($option['optionAsImage'] != '') {
                                                $optionAsImage = $option['optionAsImage'];
                                            } else {
                                                $optionAsImage = asset(Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                                            }
                                            $option['optionText'] = "<span class='zoom_me'><i class='fa fa-search-plus' aria-hidden='true'></i></span><img src='$optionAsImage'/>";
                                        } else {
                                            $option['optionText'] = $option['optionText'];
                                        }
                                        if ($setFlag % 2 == 0) {
                                            $input .= "<div class='middle_con'>";
                                        }
                                        $extraSpan = '';
                                        if ($option['optionImageText'] != '') {
                                            $extraSpan = "<span class='img_disc'>" . $option['optionImageText'] . "</span>";
                                        }
                                        $input .= "<div class='inner_con myClass" . $option['optionId'] . "'>";
                                        $input .= "<input class='multiCast' type='$optionType' id='radio$keyOption' name='$optionName' value=" . $option['optionId'] . " >";
                                        $input .= "<label for='radio$keyOption'>" . $option['optionText'] . $extraSpan . "</label>";
                                        $input .= "</div>";
                                        $setFlag++;
                                        if ($setFlag != 2 && $setFlag % 2 == 0) {
                                            $input .= "</div>";
                                        }
                                    }
                                    $input .= "</div>";
                                } else {
                                    $input .= "<div class='outer_con'>Opps ! No, any options.</div>";
                                }
                                break;
                            case "option_reorder":
                                if (isset($response['data']->options) && !empty($response['data']->options)) {
                                    $LHSText = array();
                                    $LHSImages = array();

                                    foreach ($response['data']->options as $keyOption => $option) {
                                        if ($option['optionImageText'] != '') {
                                            $LHSText[] = $option['optionImageText'];
                                        }
                                    }
                                    foreach ($response['data']->options as $keyOptionImage => $optionImage) {
                                        if ($optionImage['optionAsImage'] != '') {
                                            $LHSImages[] = $optionImage['optionAsImage'];
                                        }
                                    }
                                    if (isset($LHSText) && !empty($LHSText)) {
                                        $input .= "<div class='col-md-5 col-sm-4  connectify image_h'>";
                                        $input .= "<ul id='sortable_1' class='reorder_question_type fixed_box'>";
                                        foreach ($LHSText as $key => $LHSoption) {
                                            $input .= "<li class=''><span class='sortable_outer_container'><span class='sortable_container'>" . $LHSoption . "</span></span></li>";
                                        }
                                        $input .= "</ul>";
                                        $input .= "</div>";
                                    }

                                    if (isset($LHSImages) && !empty($LHSImages)) {
                                        $input .= "<div class='col-md-4 col-sm-5 image_h connectify'>";
                                        $input .= "<ul id='sortable_1' class='reorder_question_type fixed_box'>";
                                        foreach ($LHSImages as $key1 => $LHSoptionImage) {
                                            $input .= "<li class=''><span class='sortable_outer_container'><span class='sortable_container'><img src='" . $LHSoptionImage . "' alt='' height='100' class='pop_up_me'/></span></span></li>";
                                        }
                                        $input .= "</ul>";
                                        $input .= "</div>";
                                    }

                                    if (empty($LHSText) && empty($LHSImages)) {
                                        $RHSClass = 'col-md-12';
                                    } else {
                                        $RHSClass = 'col-md-7 image_h connectify';
                                    }

                                    $input .= "<div class='" . $RHSClass . " col-sm-7'>";
                                    $input .= "<ul id='sortable' class='reorder_question_type'>";
                                    shuffle($response['data']->options);
                                    $sorImage = asset('frontend/images/sorting.png');
                                    foreach ($response['data']->options as $keyOption => $option) {
                                        $input .= "<li class='ui-state-default' id='" . $option['optionId'] . "'><span class='sortable_outer_container'><span class='sortable_container'><span class='drag_me_text'>" . $option['optionText'] . "</span></span></span><span class='drag_me'><img src='" . $sorImage . "' alt=''></span></li>";
                                    }
                                    $input .= "</ul>";
                                    $input .= "</div>";
                                } else {
                                    $input .= "<div class='outer_con'>Opps ! No, any options.</div>";
                                }
                                break;
                            case "image_reorder":
                                if (isset($response['data']->options) && !empty($response['data']->options)) {
                                    if (isset($response['data']->l4ia_options_metrix) && $response['data']->l4ia_options_metrix != '') {
                                        $columns = unserialize($response['data']->l4ia_options_metrix);
                                        $noOfColumn = $columns['column'];
                                    } else {
                                        $noOfColumn = 4;
                                    }

                                    $input .= "<ul class='drag_drp drg_section clearfix' data-col=" . $noOfColumn . "><span class='title_drg_drp'>Drag from here</span>";
                                    $input2 = "<ul class='drag_drp drp_section clearfix'><span class='title_drg_drp'>Drop here</span>";
                                    $optionLength = 0;
                                    shuffle($response['data']->options);
                                    foreach ($response['data']->options as $keyOption => $option) {
                                        $optionLength++;
                                        $input2 .= "<li></li>";
                                        if ($option['optionText'] == '') {
                                            if ($option['optionAsImage'] != '') {
                                                $optionAsImage = $option['optionAsImage'];
                                            } else {
                                                $optionAsImage = asset(Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH') . "proteen-logo.png");
                                            }
                                            $option['optionText'] = "<img src='$optionAsImage' data-imageid='" . $option['optionId'] . "' class='pop_up_me' />";
                                        } else {
                                            $option['optionText'] = $option['optionText'];
                                        }
                                        $input .= "<li><span>";
                                        //$input .= "<input type='checkbox' id='radio$keyOption' name='answer[]' value=" . $option['optionId'] . " >";
                                        $input .= $option['optionText'] . "</label>";
                                        $input .= "</span></li>";
                                    }
                                    $input2 .= "</ul>";
                                    $input .= "</ul>";
                                    $input = $input . $input2;
                                } else {
                                    $input .= "<div class='outer_con'>Opps ! No, any options.</div>";
                                }
                                break;
                            case "group_selection":
                                break;
                            case "select_from_dropdown_option":
                                if(isset($response['data']->l4ia_question_audio) && $response['data']->l4ia_question_audio != '')
                                {
                                    $input .= "<audio controls><source src='".$response['data']->l4ia_question_audio."' type='audio/mpeg'></audio>";                                                                           
                                }   
                                if(isset($response['data']->l4ia_question_description) && $response['data']->l4ia_question_description != '')
                                {
                                    $input .= "<div class='col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10' style='margin-bottom:15px;'>".ucfirst($response['data']->l4ia_question_description)."</div>";
                                }
                                if (isset($response['data']->question_images) && !empty($response['data']->question_images)) {
                                    foreach ($response['data']->question_images as $key => $image) {
                                        $input .= "<div class='left_part_question mit0'><img src='" . $image['l4ia_question_image'] . "' alt='' class='pop_up_me'></div>";
                                    }
                                }
                                if (isset($response['data']->options) && !empty($response['data']->options)) {
                                    $input .= "<div class='right_part_answer identify_part_que'>";
                                    $inputCollect = $inputCollect2 = '';
                                    $orderArrayCollect = [];
                                    shuffle($response['data']->options);
                                    foreach ($response['data']->options as $keyOption => $option) {
                                        $orderArrayCollect[] = $option['correctOrder'];
                                        $inputCollect .= '<option value="' . $option['optionId'] . '">' . $option['optionText'] . '</option>';
                                    }
                                    sort($orderArrayCollect);
                                    if (!empty($orderArrayCollect) && count($orderArrayCollect) > 0) {
                                        foreach ($orderArrayCollect as $optionOrder) {
                                            $inputCollect2 .= '<option value="' . $optionOrder . '">' . $optionOrder . '</option>';
                                        }
                                    }
                                    $input .= "<div class='answer_select_box special_select'>";
                                    $input .= "<select name='answer_order[0]' id='dropDownTypeSelection'>";
                                    $input .= $inputCollect2;
                                    $input .= "</select>";
                                    $input .= "<span class='drop_arrow'><i class='fa fa-chevron-down'></i></span>";
                                    $input .= "</div>";
                                    $input .= "<div class='answer_select_box special_select'>";
                                    $input .= "<select name='answer[0]' id='dropDownSelection'>";
                                    $input .= $inputCollect;
                                    $input .= "</select>";
                                    $input .= "<span class='drop_arrow'><i class='fa fa-chevron-down'></i></span>";
                                    $input .= "</div>";
                                    $input .= "</div>";
                                } else {
                                    $input .= "<div class='outer_con'>Opps ! No, any options.</div>";
                                }
                                break;
                            case "option_reorder_with_step":

                                break;
                            case "single_line_answer":
                                //$correctOptionLength = strlen(str_replace(' ', '', $response['data']->correctOption));
                                $correctOptionLength = strlen($response['data']->correctOption);
                                $input .= "<span id='singleLineCheck' value='yes' /></span>";
                                if (isset($correctOptionLength) && $correctOptionLength > 0) {
                                    $x = 0;
                                    $input .= "<div class='fill_in_blank'>";
                                    $input .= "<input type='text' id='single_line_answer_box' name='answer[$x]' maxlength='$correctOptionLength' size='$correctOptionLength'/>";
                                    $input .= "</div>";                        
                                }
                                break;
                            default : "something went wrong. Please, Go back to the level";
                        }
                    ?>

                    <div class="radio">
                        <label>
                            <input type="radio" name="gender">
                            <span class="checker"></span>
                            <em>Lorem ipsum dolor sit amet</em>
                        </label>
                        <label>
                            <input type="radio" name="gender">
                            <span class="checker"></span>
                            <em>Lorem ipsum dolor sit amet</em>
                        </label>
                        <label>
                            <input type="radio" name="gender">
                            <span class="checker"></span>
                            <em>Lorem ipsum dolor sit amet</em>
                        </label>
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
    <script type="text/javascript">
        var intermediateCount = {{ (isset($response['timer']) && $response['timer'] != "") ? $response['timer'] : 0 }};
        // var optionType = '{{ (isset($optionType) && $optionType != '') ? $optionType : 0 }}';
        // var optionName = '{{ (isset($optionName) && $optionName != '') ? $optionName : "radio" }}';
        // var limitSelect = {{ (isset($response['data']->totalCorrectOptions) && $response['data']->totalCorrectOptions > 1) ? $response['data']->totalCorrectOptions : 1 }};
    </script>
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
