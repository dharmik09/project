@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Hint
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
                    <h3 class="box-title"><?php echo (isset($basketDetail) && !empty($basketDetail)) ? trans('labels.edit') : trans('labels.add') ?> Hint</h3>
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
                <form id="addhint" class="form-horizontal" method="post" action="{{ url('/admin/saveHint') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($hintData) && !empty($hintData)) ? $hintData->id : '0' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                  
                    <div class="box-body">

                    <div class="form-group">
                        <?php
                        if (old('applied_level'))
                            $applied_level = old('applied_level');
                        elseif ($hintData)
                            $applied_level = $hintData->applied_level;
                        else
                            $applied_level = '';
                        ?>
                        <label for="b_name" class="col-sm-2 control-label">Select Page</label>
                        <?php
                            unset($systemLevels[2]);
                            unset($systemLevels[3]);
                        ?>
                        <div class="col-sm-10">
                            <select class="form-control" id="level" name="level" onchange="getActivitiesForLevel();">
                                <option value="">Select</option>
                                @foreach($systemLevels as $key=>$level)
                                    <option value="{{$level->sl_name}}" <?php if($applied_level == $level->sl_name) echo 'selected'; ?>>{{$level->sl_name}}</option>                               
                                @endforeach
                                <option value="icon-selection" <?php if($applied_level == 'icon-selection') echo 'selected'; ?> >Icon Selection(Level1 Part B)</option>                               
                                <option value="basket" <?php if($applied_level == 'basket') echo 'selected'; ?>>Level 3 Industries</option>                               
                                <option value="basket-profession" <?php if($applied_level == 'basket-profession') echo 'selected'; ?>>Level 3 Professions</option>                               
                                <option value="profession-detail" <?php if($applied_level == 'profession-detail') echo 'selected'; ?>>Level 3 Profession Detail</option>                                                               
                                <option value="level4-inclination" <?php if($applied_level == 'level4-inclination') echo 'selected'; ?>>Level 4 Inclination</option>                                                               
                                <option value="level4-explore" <?php if($applied_level == 'level4-explore') echo 'selected'; ?>>Level 4 Explore</option>                                                               
                            </select>
                        </div>
                    </div>
                    
                    <?php
                    if (old('hint_type'))
                        $hint_type = old('hint_type');
                    elseif ($hintData)
                        $hint_type = $hintData->hint_type;
                    else
                        $hint_type = '';
                    ?>
                        
                        
                    <div class="form-group" id="hint_type">
                        <label for="b_name" class="col-sm-2 control-label">Hint Type</label>
                        <div class="col-sm-10">
                            <label class="radio-inline"><input type="radio" name="hint_type" id="global_hint" value="1" <?php if($hint_type == 1) echo 'checked="checked"'; ?> />Global Hint For <span id="selected_level"></span></label>
                            <label class="radio-inline"><input type="radio" name="hint_type" id="individual_hint" value="2" <?php if($hint_type == 2) echo 'checked="checked"'; ?> />Individual Hint</label>
                        </div>
                        
                    </div>    
                    <div class="form-group">
                        <label for="b_name" class="col-sm-2 control-label"></label>
                        <div class="col-sm-3" id="hint_type_error_msg"></div>
                    </div>
    
                    <div class="form-group" id="sub_activity" style="display: none;">
                        <label for="b_name" class="col-sm-2 control-label">Choose Activity</label>
                        <div class="col-sm-10" id="ajax-data">                            
                        </div>
                    </div>
        
                    
                    <div class="form-group">
                        <?php
                        if (old('hint_text'))
                            $hint_text = old('hint_text');
                        elseif ($hintData)
                            $hint_text = $hintData->hint_text;
                        else
                            $hint_text = '';
                        ?>
                        <label for="b_name" class="col-sm-2 control-label">Hint Text</label>
                        <div class="col-sm-10">
                            <textarea name="hint_text" id="hint_text" placeholder="{{trans('labels.formlblname')}}">{{(isset($hint_text) && !empty($hint_text)) ? $hint_text : ''}}</textarea>
                        </div>
                    </div>
                        
                    <?php
                    if (old('hint_image'))
                        $hint_image = old('hint_image');
                    elseif ($hintData)
                        $hint_image = $hintData->hint_image;
                    else
                        $hint_image = '';
                    ?>    
              
                    <div class="form-group">
                        <label for="hint_image" class="col-sm-2 control-label">Hint Image</label>
                        <div class="col-sm-6">
                            <!--<input type="file" id="hint_image" name="hint_image" />-->
                            <select id="hint_image" name="hint_image" class="chosen-select">
                                @foreach($gifImagesName as $key=>$name)
                                <?php $image =  asset($hintOriginalImageUploadPath.$name); ?>
                                <option value="{{$name}}" <?php if($hint_image == $name) echo 'selected'; ?> >{{$name}}</option>  
                                @endforeach
                            </select>
                        </div>
                   </div>
                        
              
                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($hintData)
                        $deleted = $hintData->deleted;
                    else
                        $deleted = '';
                    ?>
                        
                    <div class="form-group">
                        <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                        <div class="col-sm-6">
                            <?php $staus = Helpers::status(); ?>
                            <select class="form-control" id="deleted" name="deleted">
                            <?php foreach ($staus as $key => $value) { ?>
                                 <option value="{{$key}}" <?php if($deleted == $key) echo 'selected'; ?> >{{$value}}</option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>

                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/listHint') }}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->

@stop

@section('script')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
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
    
    jQuery(document).ready(function() {
        CKEDITOR.replace( 'hint_text' );
        
        var editlevel = "<?php echo (isset($hintData) && !empty($hintData)) ? $hintData->applied_level : '' ?>";
        if(editlevel != ''){
            var data_id = "<?php echo (isset($hintData) && !empty($hintData)) ? $hintData->data_id : '' ?>";
            getLevelActivity(editlevel,data_id);
        }
        
            jQuery.validator.addMethod("emptyetbody", function(value, element) {
            var et_body_data = CKEDITOR.instances['hint_text'].getData();

            return et_body_data != '';
        }, "<?php echo trans('validation.requiredfield')?>");

            var validationRules = {
                level : {
                    required : true
                },
                hint_type : {
                    required : true
                },
                hint_text : {
                    emptyetbody : true
                }
            }

        $("#addhint").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                level : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                hint_type : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                hint_text : {
                    emptyetbody : "<?php echo trans('validation.requiredfield'); ?>"
                }
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "hint_type") {
                    error.appendTo("#hint_type_error_msg");
                } else {
                    error.insertAfter(element)
                }
            }
        })
        
        $("#hint_type input:radio").click(function() {

        var hint_type = this.value;
        var selectedLevel = $('select[name=level]').val(); 
        if (hint_type == '2' && (selectedLevel=='Level1' || selectedLevel=='Level2' || selectedLevel=='profession-detail'))
        {
            getLevelActivity(selectedLevel,data_id);
        }
        else
        {
            $('#sub_activity').hide();
        }
    });
                    
    });
    
    function getActivitiesForLevel()
    {
        $('input[name="hint_type"]').prop('checked', false);
        $('#selected_level').html($("#level option:selected").text());          
    }
    
    function getLevelActivity(selectedLevel,data_id)
    {
        $.ajax({
                url: "{{ url('/admin/getLevelActivity') }}",
                type: 'post',
                data: {
                    "_token": '{{ csrf_token() }}',
                    "field_name": selectedLevel,
                    "data_id": data_id
                },
                success: function(response) {
                    $('#sub_activity').show();
                    $('#ajax-data').html(response);
                    $('#data_id').chosen({ width: "100%" });
                }
        });
        $('#sub_activity').show();
    }
</script>
@stop
