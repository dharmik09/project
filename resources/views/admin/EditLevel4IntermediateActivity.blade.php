@extends('layouts.admin-master')

@section('content')

<link rel="stylesheet" href="{{ URL::asset('backend/css/slider.css') }}">
<script src="{{ URL::asset('backend/js/bootstrap-slider.js') }}"></script>    

<section class="content-header">
    <h1>
        Level4 Intermediate Activity
    </h1>
</section>

<!-- Main content -->
<section class="content">

    <div class="row">
        <!-- right column -->
        <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo (isset($level4IntermediateActivityDetail) && !empty($level4IntermediateActivityDetail)) ? trans('labels.edit') : trans('labels.add') ?>Level4 Intermediate Activity</h3>
                </div><!-- /.box-header -->
                @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>{{trans('validation.whoops')}}</strong>{{trans('validation.someproblems')}}<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                <form id="intermediateActivity" class="form-horizontal" method="post" action="{{ url('/admin/savelevel4Intermediateactivity') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($level4IntermediateActivityDetail) && !empty($level4IntermediateActivityDetail)) ? $level4IntermediateActivityDetail->id : '0' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">

                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Select Profession</label>
                        <div class="col-sm-9">
                            <?php
                                if(isset($level4IntermediateActivityDetail) && !empty($level4IntermediateActivityDetail))
                                {
                                    $professionId = $level4IntermediateActivityDetail->l4ia_profession_id;
                                    $questionTime = $level4IntermediateActivityDetail->l4ia_question_time;
                                    $questionPoint = $level4IntermediateActivityDetail->l4ia_question_point;
                                }
                                elseif(isset($lastAddedL4IActivity) && !empty($lastAddedL4IActivity))
                                {
                                    $professionId = $lastAddedL4IActivity->l4ia_profession_id;
                                    $questionTime = $lastAddedL4IActivity->l4ia_question_time;
                                    $questionPoint = $lastAddedL4IActivity->l4ia_question_point;
                                }else{
                                    $professionId = '';
                                    $questionTime = '';
                                    $questionPoint = '';
                                }
                            ?>
                            <select class="form-control chosen-select" id="question_profession" name="question_profession">
                                <option value="">Select</option>
                                @if(isset($allActiveProfessions)&&!empty($allActiveProfessions))
                                    @foreach($allActiveProfessions as $key=>$profession)
                                    <option value="{{$profession->id}}" <?php if($professionId == $profession->id) echo 'selected'; ?> >{{$profession->pf_name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="hidden_points" value="{{$questionPoint or '25'}}">

                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Question Text</label>
                        <div class="col-sm-9">
                            <textarea name="question_text" rows="5" class="form-control" id="question_text">{{$level4IntermediateActivityDetail->l4ia_question_text or ''}}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Question Time</label>
                        <div class="col-sm-3">
                            <input type="number" value="{{$questionTime or '60'}}" name="quetion_time" class="form-control" id="quetion_time">(Second...)
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Question Point</label>
                        <div class="col-sm-6">
                            <input id="ex1" data-slider-id='ex1Slider' type="text" data-slider-min="0" data-slider-max="200" data-slider-step="5" data-slider-value="{{$questionPoint or '25'}}"  name="quetion_point" class="boot_slider"/>
                            <span class="badge bg-green" id="label_point"> {{$questionPoint or '25'}} </span>
<!--                            <input type="number" value="{{$level4IntermediateActivityDetail->l4ia_question_point or '50'}}" name="quetion_point" class="form-control" id="quetion_point">-->
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Question Description</label>
                        <div class="col-sm-10">
                            <textarea name="question_description" id="question_description">{{$level4IntermediateActivityDetail->l4ia_question_description or ''}}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Question Answer Description</label>
                        <div class="col-sm-10">
                            <textarea name="question_answer_description" id="question_answer_description">{{$level4IntermediateActivityDetail->l4ia_question_answer_description or ''}}</textarea>
                        </div>
                    </div>

                    <!--For edit time don't show media as they are managed separately-->
                    @if(empty($level4IntermediateActivityDetail))
                    @for ($i = 0; $i < 2; $i++)
                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Question Image {{$i+1}}</label>
                        <div class="col-sm-3">
                            <input type="file" id="question_image_{{$i}}" name="question_image[{{$i}}]" onchange="readURL(this);"/>
                        </div>
                        <div class="col-sm-4">
                            <textarea id="question_image_description" placeholder="Enter Description..." class="form-control" name="question_image_description[{{$i}}]"></textarea>
                        </div>
                    </div>
                    @endfor


                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Question YouTube Video URL</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="question_video" name="question_video"/>
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Question PopUp Image</label>
                        <div class="col-sm-3">
                            <input type="file" id="question_popup_image" name="question_popup_image" onchange="readURL(this);"/>
                            <?php
                                if(isset($level4IntermediateActivityDetail->id) && $level4IntermediateActivityDetail->id != '0'){
                                    if(File::exists(public_path($intermediateQuestionOriginalImageUploadPath.$level4IntermediateActivityDetail->l4ia_question_popup_image)) && $level4IntermediateActivityDetail->l4ia_question_popup_image != '') { ?><br>
                                    <img src="{{ url($intermediateQuestionOriginalImageUploadPath.$level4IntermediateActivityDetail->l4ia_question_popup_image) }}" alt="{{$level4IntermediateActivityDetail->l4ia_question_popup_image}}" width="100px" height="100px">
                                        &nbsp;&nbsp;<a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteAudioPopupImage') }}/{{$level4IntermediateActivityDetail->id}}/{{$level4IntermediateActivityDetail->l4ia_question_popup_image}}/1" title="Delete image"><i class="fa fa-times" aria-hidden="true"></i></a>
                                    <?php } ?>

                                <?php
                                }
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Question PopUp Description</label>
                        <div class="col-sm-10">
                            <textarea name="l4ia_question_popup_description" id="l4ia_question_popup_description">{{$level4IntermediateActivityDetail->	l4ia_question_popup_description or ''}}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Question Audio Upload</label>
                        <div class="col-sm-3">
                            <input type="file" id="question_audio" name="question_audio"/>
                            <?php
                                if(isset($level4IntermediateActivityDetail->id) && $level4IntermediateActivityDetail->id != '0'){
                                    if(File::exists(public_path($intermediateQuestionOriginalImageUploadPath.$level4IntermediateActivityDetail->l4ia_question_audio)) && $level4IntermediateActivityDetail->l4ia_question_audio != '') { ?><br>
                                    <span><?php echo $level4IntermediateActivityDetail->l4ia_question_audio;?></span>
                                    &nbsp;&nbsp;<a onclick="return confirm('<?php echo trans('labels.confirmdelete'); ?>')" href="{{ url('/admin/deleteAudioPopupImage') }}/{{$level4IntermediateActivityDetail->id}}/{{$level4IntermediateActivityDetail->l4ia_question_audio}}/2" title="Delete audio file"><i class="fa fa-times" aria-hidden="true"></i></a>
                                <?php } }?>
                        </div>
                    </div>


                    <input type="hidden" name="edit_template_id" value="{{(isset($level4IntermediateActivityDetail) && !empty($level4IntermediateActivityDetail)) ? $level4IntermediateActivityDetail->l4ia_question_template : '0'}}">
                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Select Question Concept</label>
                        <div class="col-sm-6">
                            <?php
                                 if(isset($level4IntermediateActivityDetail) && !empty($level4IntermediateActivityDetail)){
                                     $templateId = $level4IntermediateActivityDetail->l4ia_question_template;
                                     $isShowLastConcept = false;
                                     $matrixRowColumn = unserialize($level4IntermediateActivityDetail->l4ia_options_metrix);
                                     $row = $matrixRowColumn['row'];
                                     $column = $matrixRowColumn['column'];
                                 }
                                 elseif(isset($lastAddedL4IActivity) && !empty($lastAddedL4IActivity))
                                 {
                                     $templateId = $lastAddedL4IActivity->l4ia_question_template;
                                     $isShowLastConcept = true;
                                     $row = 1;
                                     $column = 1;
                                 }else{
                                     $templateId = '';
                                     $isShowLastConcept = false;
                                     $row = 1;
                                     $column = 1;
                                 }

                            ?>

                            <?php $disabled = (isset($level4IntermediateActivityDetail) && !empty($level4IntermediateActivityDetail)) ? 'disabled' : '' ?>

                            <select class="form-control" id="gamification_template" {{$disabled}} name="gamification_template" onchange="getTemplateAnswerBox()">
                                <option value="">Select</option>
                                @if(isset($gamificationTemplate)&&!empty($gamificationTemplate))
                                    @foreach($gamificationTemplate as $key=>$val)
                                    <option value="{{$val->id}}" data-capacity="{{$val->gt_temlpate_answer_type}}" <?php if($templateId == $val->id) echo 'selected'; ?> >{{$val->gt_template_title}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    @if(isset($level4IntermediateActivityDetail) && !empty($level4IntermediateActivityDetail))
                        <div class="form-group">
                            <label for="l2ac_text" class="col-sm-2 control-label"></label>
                            <div class="col-sm-6">
                                <a href="{{ url('/admin/manageIntermediateActivityAnswer') }}/{{$level4IntermediateActivityDetail->id}}" title="Manage answer">Manage Answer &nbsp;&nbsp;&nbsp;&nbsp;</a>
                                <a href="{{ url('/admin/manageIntermediateActivityMedia') }}/{{$level4IntermediateActivityDetail->id}}" title="Manage Question Media">Manage Media &nbsp;&nbsp;&nbsp;&nbsp;</a>
                            </div>
                        </div>
                    @endif

                    <div id="option_matrix" style="display: none;">
                        <div class="form-group">
                            <label for="category_type" class="col-sm-2 control-label">Matrix</label>
                            <div class="col-sm-2">
                                <select class="form-control" name="grid_column">
                                    @for($i=1;$i<5;$i++)
                                    <option value="{{$i}}" <?php if($column == $i){echo "selected='selected'";}?>>{{$i}}</option>
                                    @endfor
                                </select>
                                Column
                            </div>

                            <div class="col-sm-2">
                                <select class="form-control" name="grid_row">
                                    @for($i=1;$i<5;$i++)
                                    <option value="{{$i}}" <?php if($row == $i){echo "selected='selected'";}?>>{{$i}}</option>
                                    @endfor
                                </select>
                                Row
                            </div>
                        </div>
                    </div>
                    <div id="answer_box">

                    </div>



                    <div class="form-group" id="shuffle_option_checkbox" style="display: none;">
                        <label for="category_type" class="col-sm-2 control-label">Shuffle Options while display</label>
                        <div class="col-sm-3">
                            <input type="checkbox" name="shuffle_options" value="1" title="Check if you want to shuffle the options at display time" style="cursor:pointer;">
                        </div>
                    </div>

                    <div class="form-group" id="multiple_attempt_checkbox" style="display: none;">
                        <label for="category_type" class="col-sm-2 control-label">Multiple attempt allowed</label>
                        <div class="col-sm-3">
                            <input type="checkbox" name="multiple_attempt" value="1" title="Check if you want to allow multiple attempt for this question" style="cursor:pointer;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="category_type" class="col-sm-2 control-label">Right Question Message</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="right_question_message" id="right_question_message" value="{{$level4IntermediateActivityDetail->l4ia_question_right_message or 'Congratulations! That\'s the right answer!'}}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="category_type" class="col-sm-2 control-label">Wrong Question Message</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="wrong_question_message" id="wrong_question_message" value="{{$level4IntermediateActivityDetail->l4ia_question_wrong_message or 'Oh no! That\'s not the right answer'}}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                        <div class="col-sm-6">
                            <?php $staus = Helpers::status(); $deleted = (isset($level4IntermediateActivityDetail) && !empty($level4IntermediateActivityDetail)) ? $level4IntermediateActivityDetail->deleted : '' ?>

                            <select class="form-control" id="deleted" name="deleted">
                                <?php foreach ($staus as $key => $value) { ?>
                                    <option value="{{$key}}" <?php if($deleted == $key) echo 'selected'; ?>>{{$value}}</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="questionTemplateAnsType" id="questionTemplateAnsType" />
                    </div>
                    <div class="box-footer">
                        <button type="submit" id="save" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/listLevel4IntermediateActivity') }}{{$page}}"> {{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->


@stop
 <script type = "text/javascript" src = "//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js" ></script>
@section('script')

<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('backend/js/jquery-ui.js') }}"></script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        var isShowLastConcept = '<?php echo $isShowLastConcept?>';
        if(isShowLastConcept){
            getTemplateAnswerBox();
        }
        var selectedAnswerType = $('#gamification_template option:selected').attr('data-capacity');
        if(selectedAnswerType == 'image_reorder'){
            $('#option_matrix').show();
         }else{
            $('#option_matrix').hide();
         }

        $validate=jQuery.noConflict();
        $('#ex1').slider({
            formatter: function(value)
            {
               return 'Current value: ' + value;
            },

        });
        var originalVal;

        $('#ex1').slider().on('slideStart', function(ev){
            originalVal = $('#ex1').data('slider').getValue();
        });

        $('#ex1').slider().on('slideStop', function(ev){
            var newVal = $('#ex1').data('slider').getValue();
            if(originalVal != newVal) {
                $('#label_point').text(newVal);
            }
        });

        $('.slider-track').click(function(){
            var sliderval = $('.tooltip-inner').html();
            $("#dSAnalysisSliderValue").text(sliderval);
         });

        var validationRules = {
                gamification_template : {
                    required : true
                },
                question_profession : {
                    required : true
                },
                question_text : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }

        $validate("#intermediateActivity").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                gamification_template : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                question_profession : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                question_text : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        });


    });

    CKEDITOR.replace( 'question_description', {
        toolbar: [
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline'] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-'] },
            '/',
            { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'others', items: [ '-' ] },
        ]
    });

    CKEDITOR.replace( 'question_answer_description', {
        toolbar: [
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline'] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-'] },
            '/',
            { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'others', items: [ '-' ] },
        ]
    });

    CKEDITOR.replace( 'l4ia_question_popup_description', {
        toolbar: [
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline'] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-'] },
            '/',
            { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'others', items: [ '-' ] },
        ]
    });


    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }


    function getTemplateAnswerBox()
    {
       var selectedTemplate = $('#gamification_template option:selected').attr('data-capacity');
       $("#questionTemplateAnsType").val(selectedTemplate);
        $.ajax({
            url: "{{ url('/admin/getGamificationTemplateAnswerBox') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "template": selectedTemplate
            },
            success: function(response) {

               $('#answer_box').html(response);
               if(selectedTemplate == 'option_choice' || selectedTemplate == 'option_reorder' || selectedTemplate == 'option_choice_with_response' || selectedTemplate == 'image_reorder' || selectedTemplate == 'filling_blank')
               {
                   $('#shuffle_option_checkbox').show();
               }else{
                   $('#shuffle_option_checkbox').hide();
               }
               if(selectedTemplate == 'image_reorder'){
                  $('#option_matrix').show();
               }else{
                  $('#option_matrix').hide();
               }

            }
        });
        $( "#save" ).click(function() {
            if($(".l4optiontype").length){
            if(!$('input.l4optiontype').is(':checked'))
            {
                alert("Please check atleast one checkbox for correct answer");
                return false;
            }
            else
            {
            }
            }
        });

    }
</script>
@stop