@extends('layouts.admin-master')

@section('content')
<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Question Concept
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
                    <h3 class="box-title"><?php echo (isset($level4TemplateDetail) && !empty($level4TemplateDetail)) ? trans('labels.edit') : trans('labels.add') ?> Question Concept</h3>
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
                <form id="addGamificationTemplate" class="form-horizontal" method="post" action="{{ url('/admin/saveGamificationTemplate') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($level4TemplateDetail) && !empty($level4TemplateDetail)) ? $level4TemplateDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($level4TemplateDetail) && !empty($level4TemplateDetail)) ? $level4TemplateDetail->gt_template_image : '' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">
                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Select Profession</label>
                        <div class="col-sm-9">
                            <?php $professionId = (isset($level4TemplateDetail) && !empty($level4TemplateDetail)) ? $level4TemplateDetail->gt_profession_id : '' ?>                            
                            <select class="form-control chosen-select" id="question_profession" name="question_profession">
                                <option value="">Select</option>
                                @if(isset($allActiveProfessions)&&!empty($allActiveProfessions))
                                    @foreach($allActiveProfessions as $key=>$profession)
                                    <option value="{{$profession->id}}" <?php if($professionId == $profession->id) echo 'selected'; ?>>{{$profession->pf_name}}</option> 
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>    
                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Concept Title</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="template_title" name="template_title" value="{{(isset($level4TemplateDetail) && !empty($level4TemplateDetail)) ? $level4TemplateDetail->gt_template_title : ''}}" minlength="6" maxlength="255">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="gt_template_image" class="col-sm-2 control-label">Concept Image</label>
                        <div class="col-sm-2">
                            <input type="file" id="gt_template_image" name="gt_template_image"  onchange="readURL(this);"/> 
                            @if(isset($level4TemplateDetail) && !empty($level4TemplateDetail))
                                <?php 
                                    $image_data = ($level4TemplateDetail->gt_template_image != "" && Storage::disk('s3')->exists($conceptOriginalImageUploadPath.$level4TemplateDetail->gt_template_image) ) ? Config::get('constant.DEFAULT_AWS').$conceptOriginalImageUploadPath.$level4TemplateDetail->gt_template_image : asset($conceptOriginalImageUploadPath.'proteen-logo.png');
                                ?>
                                <img src="{{$image_data}}" width="150px" height="150px" />
                            @endif
                        </div>
                        <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                    </div>    
                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Concept Description</label>
                        <div class="col-sm-10">
                            <textarea name="template_description" id="template_description">{{(isset($level4TemplateDetail) && !empty($level4TemplateDetail)) ? $level4TemplateDetail->gt_template_descritpion : ''}}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="gt_template_descritpion_popup_imge" class="col-sm-2 control-label">Concept Description PopUp Image</label>
                        <div class="col-sm-6">
                            <input type="file" id="gt_template_descritpion_popup_imge" name="gt_template_descritpion_popup_imge"  onchange="readURL(this);"/> 
                            @if(isset($level4TemplateDetail) && !empty($level4TemplateDetail))
                                <?php 
                                    $image_data_path = ($level4TemplateDetail->gt_template_descritpion_popup_imge != "" && Storage::disk('s3')->exists($conceptOriginalImageUploadPath.$level4TemplateDetail->gt_template_descritpion_popup_imge) ) ? Config::get('constant.DEFAULT_AWS').$conceptOriginalImageUploadPath.$level4TemplateDetail->gt_template_descritpion_popup_imge : asset($conceptOriginalImageUploadPath.'proteen-logo.png');
                                ?>
                                <img src="{{$image_data_path}}" width="150px" height="150px" />
                            @endif
                        </div>
                    </div>    
                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Select Answer Type</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="template_answer_type" name="template_answer_type">
                                <?php $answer_type = (isset($level4TemplateDetail) && !empty($level4TemplateDetail)) ? $level4TemplateDetail->gt_template_id : '' ?>
                                <option value="">Select</option>
                                @if(isset($leve4TemplateAnswrTypes) && !empty($leve4TemplateAnswrTypes))  
                                    @foreach($leve4TemplateAnswrTypes as $key=>$data)
                                        <option value="{{$data->tat_value}}##{{$data->id}}" <?php if($data->id == $answer_type) echo 'selected'; ?>>{{$data->tat_type}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <a hre="javascript:void(0)" onclick="PreviewAnswerBox()" style="cursor:pointer;">Click to view preview</a>
                        </div>
                    </div>
                    <?php
                    if (old('gt_coins'))
                        $gt_coins = old('gt_coins');
                    elseif ($level4TemplateDetail)
                        $gt_coins = $level4TemplateDetail->gt_coins;
                    else
                        $gt_coins = '';
                    ?>
                    <div class="form-group">
                        <label for="gt_coins" class="col-sm-2 control-label">{{trans('labels.formlblcoins')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control numeric" id="gt_coins" name="gt_coins" placeholder="{{trans('labels.formlblcoins')}}" value="{{$gt_coins}}"/>
                        </div>
                    </div>
                    <?php
                    if (old('gt_valid_upto'))
                        $gt_valid_upto = old('gt_valid_upto');
                    elseif ($level4TemplateDetail)
                        $gt_valid_upto = $level4TemplateDetail->gt_valid_upto;
                    else
                        $gt_valid_upto = '';
                    ?>
                    <div class="form-group">
                        <label for="gt_valid_upto" class="col-sm-2 control-label">{{trans('labels.formlblvalidupto')}}</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control numeric" id="gt_valid_upto" name="gt_valid_upto" placeholder="{{trans('labels.formlblvalidupto')}}" value="{{$gt_valid_upto}}"/>
                        </div>
                        <label for="c_valid_for" class="col-sm-1 control-label">{{trans('labels.formlbldays')}}</label>
                    </div>
                    <div class="form-group">
                        <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                        <div class="col-sm-6">
                            <?php $staus = Helpers::status(); $deleted = (isset($level4TemplateDetail) && !empty($level4TemplateDetail)) ? $level4TemplateDetail->deleted : '' ?>
                            <select class="form-control" id="deleted" name="deleted">
                                <?php foreach ($staus as $key => $value) { ?>
                                    <option value="{{$key}}" <?php if($deleted == $key) echo 'selected'; ?>>{{$value}}</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/listGamificationTemplate') }}{{$page}}"> {{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="model_title"></h4>
        </div>
        <div class="modal-body">
            <p><img id="answer_image" src=""/></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>      
    </div>
</div>
@stop
@section('script')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
    $('.numeric').on('keyup', function() {
        this.value = this.value.replace(/[^0-9]/gi, '');
    });
    jQuery(document).ready(function() {

    jQuery.validator.addMethod("emptyetbody", function(value, element) {
    var et_body_data = CKEDITOR.instances['template_description'].getData();

    return et_body_data != '';
}, "<?php echo trans('validation.requiredfield')?>");

            var validationRules = {
                question_profession: {
                    required : true
                },
                template_title : {
                    required : true
                },
                template_answer_type : {
                    required : true
                },
                template_description: {
                    emptyetbody : true
                },
                gt_coins: {
                    required : true
                },
                deleted : {
                    required : true
                }
            }

        $("#addGamificationTemplate").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                question_profession : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                template_title : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                template_answer_type : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                template_description : {
                    emptyetbody : "<?php echo trans('validation.requiredfield'); ?>"
                },
                gt_coins : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });
    CKEDITOR.replace( 'template_description' );
    function PreviewAnswerBox()
    {
       var answerType = $('#template_answer_type option:selected').html()
       if(answerType)
       {
           var imagepath = '<?php echo asset('backend/images/answertype/') ?>';
           switch(answerType) {
                case 'Template 1':
                    imagename = 'template_1.PNG';
                    break;
                case 'Template 2':
                    imagename = 'template_2.PNG';
                    break;
                case 'Template 3':
                    imagename = 'template_3.PNG';
                    break;
                case 'Template 4':
                    imagename = 'template_4.PNG';
                    break;
                case 'Template 5':
                    imagename = 'template_5.PNG';
                    break;
                case 'Template 6':
                    imagename = 'template_6.PNG';
                    break;
                case 'Template 7':
                    imagename = 'template_7.PNG';
                    break;
                case 'Template 8':
                    imagename = 'template_8.PNG';
                    break;
                case 'Template 9':
                    imagename = 'template_9.PNG';
                    break;
                case 'Template 10':
                    imagename = 'template_10.PNG';
                    break;
                case 'Template 11':
                    imagename = 'template_11.PNG';
                    break;
                case 'Template 12':
                    imagename = 'template_12.PNG';
                    break;
                case 'Template 13':
                    imagename = 'template_13.PNG';
                    break;
                case 'Template 14':
                    imagename = 'template_14.PNG';
                    break;    
                default:
                    imagename = 'true_false.PNG';
            }            
           $('#model_title').html("This answer template suitable for such type of questions...");
           $('#answer_image').attr("src",imagepath+'/'+imagename);           
           $('#myModal').modal('show');
       }              
    }
    
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
</script>
@stop