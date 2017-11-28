<a class="back_me" href="{{url('parent/level4-play-more')}}/{{$response['profession_id']}}/{{$response['teen_id']}}"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Back</span></a>
@include('teenager/teenagerLevelPointBox')
<?php
if (!empty($response['data'])) {
    ?>
    <div class="question_container">
        <form id = "level4_intermediate_activity_ans" action="" role = "form" enctype = "multipart/form-data" method = "POST" autocomplete = "off" autocorrect = "off" autocapitalize = "off" spellcheck = "false">
            <input type = "hidden" name = "_token" value = "{{ csrf_token() }}">
            <input type = "hidden" id = "questionID" name = "questionID" value = "{{$response['data']->activityID}}" >
            <input type ="hidden" id="blackhole" name="timer" />
            <input type ="hidden" id="ajax_answer_type" name="ajax_answer_type" value="{{$response['data']->gt_temlpate_answer_type}}" />
            <div class="clearfix">
                <div class="col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10">
                    <div class="que"><?php echo $response['data']->l4ia_question_text; ?></div>
                    @if(isset($response['data']->gt_temlpate_answer_type) && ( $response['data']->gt_temlpate_answer_type == "option_choice" || $response['data']->gt_temlpate_answer_type == "true_false" || $response['data']->gt_temlpate_answer_type == "single_line_answer" || $response['data']->gt_temlpate_answer_type == "option_choice_with_response" || $response['data']->gt_temlpate_answer_type == "option_reorder" || $response['data']->gt_temlpate_answer_type == "image_reorder" || $response['data']->gt_temlpate_answer_type == "filling_blank"))

                        @if(isset($response['data']->question_images) && !empty($response['data']->question_images))
                            @if(count($response['data']->question_images) > 1)
                                <div class="scale_it_level_4">
                                    <div class="multi_image_container">
                                        <?php $poCount = 1; ?>
                                        @foreach($response['data']->question_images as $key=>$image)
                                            @if($poCount%2 == 0)
                                                <div class="second_image">
                                                    <img title="{{isset($image['l4ia_question_imageDescription']) && ($image['l4ia_question_imageDescription'] != '') ? $image['l4ia_question_imageDescription']:'Click to enlarge image'}}" style="cursor: pointer;" class="pop_up_me" src="{{ Storage::url($image['l4ia_question_image']) }}" id="option_choice_image"/>
                                                </div>
                                            @else
                                                <div class="first_image">
                                                    <img title="{{isset($image['l4ia_question_imageDescription']) && ($image['l4ia_question_imageDescription'] != '') ? $image['l4ia_question_imageDescription']:'Click to enlarge image'}}" style="cursor: pointer;" class="pop_up_me" src="{{ Storage::url($image['l4ia_question_image']) }}" id="option_choice_image"/>
                                                </div>
                                            @endif
                                        <?php $poCount++ ; ?>    
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                @foreach($response['data']->question_images as $key=>$image)
                                <div class="question_image_level_4">
                                    <img title="{{isset($image['l4ia_question_imageDescription']) && ($image['l4ia_question_imageDescription'] != '') ? $image['l4ia_question_imageDescription']:'Click to enlarge image'}}" style="cursor: pointer;" class="pop_up_me" src="{{ Storage::url($image['l4ia_question_image']) }}" id="option_choice_image"/>
                                </div>
                                @endforeach
                            @endif
                        <br/>                    
                        @endif

                        @if(isset($response['data']->l4ia_question_audio) && $response['data']->l4ia_question_audio != '')

                            <audio controls id="onOffAudio">                        
                                <source src="{{$response['data']->l4ia_question_audio}}" type="audio/mpeg" id="checkAudio" />
                                Your browser does not support the audio element.
                            </audio>
                        @endif

                        @if(isset($response['data']->l4ia_question_video) && $response['data']->l4ia_question_video != '')
                            <?php 
                                $videoCode = Helpers::youtube_id_from_url($response['data']->l4ia_question_video);
                                $videoCode = ($videoCode != '') ? $videoCode : "ScMzIvxBSi4";
                            ?>
                            <div class="question_image_level_4 stopOnSubmit">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/{{$videoCode}}?rel=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
                            </div>
                            <br/>
                        @endif
                    
                        @if(isset($response['data']->l4ia_question_description) && $response['data']->l4ia_question_description != '')
                        <h3 class="colorWhite">{!! ucfirst($response['data']->l4ia_question_description) !!}</h3>
                        @endif
                    @endif
                    
                    @if(isset($response['data']->totalCorrectOptions) &&  $response['data']->totalCorrectOptions > 1)
                    <span class="colorYellow">(You can select multiple answers for this question)</span><br/>
                    @endif
                    <div class="response_message_outer">
                        <p id="answerRightWrongMsg"></p>
                    </div>
                    <h2 class="colorYellow" id="systemCorrectAnswerText"></h2>
                    <div class="clearfix">
                        <div style="text-align: left;" id="showResponseMessage">
                        </div>
                    </div>
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
                                    $optionAsImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH') . "proteen-logo.png";
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
                        $sorImage = Storage::url('frontend/images/sorting.png');
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
                                    $optionAsImage = Config::get('constant.LEVEL4_INTERMEDIATE_ANSWER_ORIGINAL_IMAGE_UPLOAD_PATH') . "proteen-logo.png";
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
                            $input .= "<div class='left_part_question mit0'><img src='" . Storage::url($image['l4ia_question_image']) . "' alt='' class='pop_up_me'></div>";
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
            <div class="clearfix">
                <div class="col-md-offset-1 col-sm-offset-1 col-md-10 col-sm-10 answer_four_option">
    <?php echo $input; ?>
                    @if($response['data']->gt_temlpate_answer_type == "image_reorder")
                    <div class="answer_submit_btn">
                        <button type="button" class="btn primary_btn saveMe" data-dismiss="modal" onclick="saveDropDrag()">Submit</button>
                    </div>
                    @elseif($response['data']->gt_temlpate_answer_type == "select_from_dropdown_option")
                    <div class="answer_submit_btn">
                        <button type="button" class="btn primary_btn saveMe" data-dismiss="modal" onclick="saveDropDown()">Submit</button>
                    </div>
                    @elseif($response['data']->gt_temlpate_answer_type == "option_reorder")
                    <div class="answer_submit_btn">
                        <button type="button" class="btn primary_btn saveMe" id="sortable_submit" onclick="saveOptionReorder()">Submit</button>
                    </div>
                    @else
                    <div class="answer_submit_btn">
                        <button type="button" class="btn primary_btn saveMe" data-dismiss="modal" onclick="saveAnswer()">Submit</button>
                    </div>
                    @endif
                </div>
            </div>
            <span id="d_d_count" value=<?php echo (isset($optionLength) && $optionLength > 0 ) ? $optionLength : 0 ?>></span>
        </form>    
    </div>
    <?php
} else {
    $desc = [];
    if (Auth::guard('parent')->check()) {
        $desc = Helpers::getTemplateNoForParent(Auth::guard('parent')->user()->id, $response['profession_id']);
    }
    $image = Helpers::getTeenagerImageUrl(Auth::guard('parent')->user()->pf_logo, 'original');
    ?>
    <div class="cong_container animation-element in-view">
        <canvas id="canvas">Canvas is not supported in your browser.</canvas>
        <div class="cong cong_hero"><img src="{{Storage::url('frontend/images/jumping.gif')}}" alt=""></div>
        <div class="cong cong_top"><p>Congratulations!</p></div>

        <div class="cong cong_bottom">
            <div class="cong_outer">
                <div class="cong_middle">
                    <p>{!! $desc['msg1'] !!} {!! $desc['msg3'] !!}</p>
                </div>
            </div>
        </div>
        <div class="cong cong_btn" style="margin-top: 30px;">
            <a class="rlink" style="font-size: 20px;font-weight: bold;"  href="https://goo.gl/forms/sOFyvbWqzBH98S2A3" target="_blank">Please give us feedback | Earn 5000 ProCoins&nbsp;&nbsp;<i class="fa fa-external-link" aria-hidden="true"></i></a>
        </div>
        <div class="cong cong_btn">
            <span><a class="btn primary_btn" href="{{url('parent/level4-play-more')}}/{{$response['profession_id']}}/{{$response['teen_id']}}"> Next </a></span>
        </div>
    </div>
    <?php
}
?>
<div class="width_container"></div><div class="booster">
    <div class="skill">
        <div class="outer">
            <div class="inner" data-progress="{{$response['boosterScale']}}%">
                <div></div>
            </div>        
        </div>
    </div>
</div>
<div id="opption_zoom_image" class="modal fade hint_image_modal_show" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
            <img src="" alt="">
        </div>
    </div>
</div>
<?php
if (isset($response['data']->gt_temlpate_answer_type) && ($response['data']->l4ia_question_popup_image != '' || $response['data']->l4ia_question_popup_description != '')) {
    $setPopupTime = $response['data']->l4ia_extra_question_time;
    ?> 
    <div id="quiz_material_popup" class="modal fade quiz_pre_material" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="time_out_cst"></div>
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                <div class="modal-body popup_image_and_desc">   
                    @if(isset($response['data']->l4ia_question_popup_description) && $response['data']->l4ia_question_popup_description != '')                
                    <div class="pre_material_img">
                        {!! $response['data']->l4ia_question_popup_description !!}
                    </div>
                    @endif  
                    @if(isset($response['data']->l4ia_question_popup_image) && $response['data']->l4ia_question_popup_image != '')                
                    <div class="img_cont_pop l4i_popup_image"><img src="{{$response['data']->l4ia_question_popup_image}}" id="l4I_popup_image"/></div>                                      
                    @endif                      
                </div>                                           
            </div>
        </div>
    </div>
    <?php
} else {
    $setPopupTime = 0;
}
?>

<script>

                            var ansTypeSet = "<?php echo (isset($response['data']->gt_temlpate_answer_type)) ? $response['data']->gt_temlpate_answer_type : 0 ?>";
                            var timeCount = <?php echo (isset($response['timer'])) ? $response['timer'] : 0 ?>;
                            var setPopupTime = <?php echo (isset($setPopupTime)) ? $setPopupTime : 0 ?>;
                            var optionType = "<?php echo isset($optionType) ? $optionType : "radio" ?>";
                            var optionName = "<?php echo isset($optionName) ? $optionName : 'answerID[0]' ?>";
                            var limitSelect = <?php echo (isset($response['data']->totalCorrectOptions) && $response['data']->totalCorrectOptions > 1) ? $response['data']->totalCorrectOptions : 1 ?>;
                            
</script>