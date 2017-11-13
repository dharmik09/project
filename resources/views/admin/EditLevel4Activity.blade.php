@extends('layouts.admin-master')

@section('content')


<link rel="stylesheet" href="{{ URL::asset('backend/css/slider.css') }}">
<script src="{{ URL::asset('backend/js/bootstrap-slider.js') }}"></script>


<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level4activity')}}
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
                    <h3 class="box-title"><?php echo (isset($activity4Detail) && !empty($activity4Detail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.level4activity')}}</h3>
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

                <?php
                    if($activity4Detail)
                    {
                       foreach($activity4Detail as $value)
                        {
                            $id = $value->id;
                            $question_text = $value->question_text;
                            $points = $value->points;
                            $deleted = $value->deleted;
                            $options_text = $value->options_text;
                            $options = explode("#" , $options_text);
                            $correct_option = $value->correct_option;
                            $correct_option = explode("," , $correct_option);
                            $profession_id = $value->profession_id;
                            $question_type = $value->type;
                        }
                    }
                    else
                    {

                        $id = '';
                        $question_text = '';
                        $points = '';
                        $deleted = '';
                        $options_text = '';

                        $options=array();
                        $options[0]='';
                        $options[1]='';

                        $correct_option =array();
                        $correct_option[0]='';
                        $correct_option[1]='';
                        $profession_id = '';
                        $question_type = '';
                      }
                ?>
                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                <form id="addLevel4Activity" class="form-horizontal" method="post" action="{{ url('/admin/saveLevel4Activity') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($id) && !empty($id)) ? $id : '0'?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <input type="hidden" name="hidden_points" value="<?php echo (isset($points) && !empty($points)) ? $points : '0'?>">
                    <div class="box-body">
                        
                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">Select Profession</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="profession_id" name="profession_id">
                                <option>Select Profession</option>
                                @if(isset($allActiveProfessions) && !empty($allActiveProfessions))
                                    @foreach($allActiveProfessions as $key=>$val)
                                    <option value="{{$val->id}}" <?php if($profession_id == $val->id) echo 'selected'; ?>>{{$val->pf_name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>    

                    <div class="form-group">
                        <label for="l2ac_text" class="col-sm-2 control-label">{{trans('labels.formlbltext')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="question_text" name="question_text" placeholder="{{trans('labels.formlbltext')}}" value="{{$question_text}}" />
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="points" class="col-sm-2 control-label">{{trans('labels.formlblpoint')}}</label>
                        <div class="col-sm-6">
                           <input id="ex1" data-slider-id='ex1Slider' type="text" data-slider-min="0" data-slider-max="200" data-slider-step="5" data-slider-value="{{$points}}"  name="points" class="boot_slider"/>
                           <span class="badge bg-green" id="label_point"> {{$points}} </span>
                        </div>
                    </div>



                    <div id="addoption" class="addoption">
                      <div class="form-group">
                          <label for="options_text" class="col-sm-2 control-label">{{trans('labels.formlbloptions')}}</label>
                          <div class="col-sm-5">
                               <input type="text" class="form-control" id="options_text" name="options_text[]" placeholder="{{trans('labels.formlbloption')}}" value="{{$options[0]}}" />
                          </div>
                          <div class="col-sm-1">
                               <input type="checkbox" name="correct_option[]" value="0" <?php if(isset($correct_option[0]) && $correct_option[0]==1){ ?> checked="checked" <?php } ?> />
                               {{trans('labels.formblcorrectoption')}}
                          </div>

                      </div>

                      <div class="form-group">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-5">
                               <input type="text" class="form-control" id="options_text" name="options_text[]" placeholder="{{trans('labels.formlbloption')}}" value="<?php if(isset($options[0])){ echo isset($options[1])?$options[1]:'';} ?>" />
                            </div>

                            <div class="col-sm-1">
                               <input type="checkbox" name="correct_option[]" value="1" <?php if(isset($correct_option[1]) && $correct_option[1]==1){ ?> checked="checked" <?php } ?> />
                               {{trans('labels.formblcorrectoption')}}
                          </div>

                            <input type="hidden" name="countRadio" value="2" id="countRadio"/>
                        </div>

                      <?php
                        for($i=2 ; $i< (count($options)) ; $i++)
                        {
                       ?>
                        <div class="form-group" id='delete_action_<?php echo $i; ?>'>
                            <div class="col-sm-2"></div>
                            <div class="col-sm-5">
                               <input type="text" class="form-control" id="options_text" name="options_text[]" placeholder="{{trans('labels.formlbloption')}}" value="{{$options[$i]}}" />
                            </div>
                             <div class="col-sm-1">
                               <input type="checkbox" name="correct_option[]" value="<?php echo $i; ?>" <?php if(isset($correct_option[$i]) && $correct_option[$i]==1){ ?> checked="checked" <?php } ?>/>
                               {{trans('labels.formblcorrectoption')}}
                            </div>
                            <div class="col-sm-1" onclick="delete_action('<?php echo $i; ?>');">
                            <a href="#" class="btn btn-success " name="minus">
                                <span class="glyphicon glyphicon-minus"> </span>
                            </a>
                            </div>
                            <input type="hidden" name="countRow" value="<?php echo count($options); ?>" id="countRaw" />
                        </div>

                          <?php

                        }
                      ?>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-2 control-label"></label>
                        <div class="col-sm-2">
                            <a href="#" class="btn btn-success " name="add" id="add">
                                <span class="glyphicon glyphicon-plus"> </span>
                            </a>
                        </div>
                    </div>
                        
                    <?php
                        if (isset($question_type) && $question_type != '') {
                            $question_type_default = '';
                        } else {
                            $question_type_default = 'checked="checked"';                           
                        }                       
                    ?>    
                    <div class="form-group">
                        <label for="category_type" class="col-sm-2 control-label">Question Type</label>
                        <div class="col-sm-6">
                            <label class="radio-inline">
                                <input name="question_type" id="question_type" value="1" type="radio" <?php if($question_type == '1') echo 'checked="checked"'; ?> >True/False
                            </label>
                            <label class="radio-inline">
                                <input name="question_type" id="question_type" value="0" type="radio" <?php if(isset($question_type) && $question_type == '0') echo 'checked="checked"'; echo $question_type_default; ?> >Multi-Choice
                            </label> 
                        </div>
                    </div>  
                        
                    <div class="form-group">
                        <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                        <div class="col-sm-6">
                            <?php $staus = Helpers::status(); ?>
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/level4Activity') }}{{$page}}"> {{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->


@stop
 <script type = "text/javascript" src = "//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js" ></script>
@section('script')

<script type="text/javascript">

   function delete_action(no)
     {
            $('#delete_action_'+no).remove();
            return false;
       }
    $(document).ready(function()
    {
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

        $('#add').click(function()
         {
            var countRadio=$('#countRadio').val();
            var countRaw=$('#countRaw').val();
            var divid='delete_action_'+countRaw;

            var option = '<div class="form-group" id="'+divid+'">'+
                          '<div class="col-sm-2"></div>'+
                          '<div class="col-sm-5">' +
                               '<input type="text" class="form-control" id="options_text" name="options_text[]" placeholder="{{trans("labels.formlbloption")}}" value="" />' +
                          '</div>'+
                          '<div class="col-sm-1">'+
                               '<input type="checkbox" name="correct_option[]" value="'+countRadio+'">' +
                               '<?php  echo trans('labels.formblcorrectoption'); ?>'+
                          '</div>'+
                          '<div class="col-sm-1" onclick="delete_action('+countRaw+');">'+
                            '<a href="#" class="btn btn-success " name="minus">'+
                                '<span class="glyphicon glyphicon-minus"> </span>'+
                            '</a>'+
                            '</div>'+
                      '</div>';

            $('#addoption').append(option);

            countRadio=parseInt(countRadio)+1;
            $('#countRadio').val(countRadio);
            countRaw=parseInt(countRaw)+1;
            $('#countRaw').val(countRaw);

         });


        var validationRules =
        {
                question_text : {
                    required : true
                },
                points : {
                    required : true
                },
                'options_text[]' : {
                    required : true
                },
                deleted : {
                    required : true
                }

        }


        $validate("#addLevel4Activity").validate({
            rules : validationRules,
            messages : {
                question_text : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                points : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                'options_text[]' : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }

            }
        })

    });
</script>
@stop