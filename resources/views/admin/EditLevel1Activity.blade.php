@extends('layouts.admin-master')

@section('content')

<link rel="stylesheet" href="{{ URL::asset('backend/css/slider.css') }}">
<script src="{{ URL::asset('backend/js/bootstrap-slider.js') }}"></script> 

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level1activities')}}
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
                    <h3 class="box-title"><?php echo (isset($activityDetail) && !empty($activityDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.level1activity')}}</h3>
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
                    if($activityDetail)
                    {
                        foreach($activityDetail as $value)
                        {
                            $id = $value->id;
                            $l1ac_text = $value->l1ac_text;
                            $l1ac_points = $value->l1ac_points;
                            $l1ac_image = $value->l1ac_image;
                            $date = date('d/m/Y', strtotime($value->l1ac_active_date));
                            $deleted = $value->deleted;
                            $l1op_option = $value->l1op_option;
                            $l1op_fraction = $value->l1op_fraction;
                        }
                    }
                    else
                    {
                        $id = '';
                        $l1ac_text = '';
                        $l1ac_points = '';
                        $l1ac_image = '';
                        $date = '';
                        $deleted = '';
                        $l1op_option = '';
                        $l1op_fraction = '';
                    }
                    $fraction = explode("," , $l1op_fraction);
                    $option = explode("," , $l1op_option);
                ?>
                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                <form id="addLevel1Activity" class="form-horizontal" method="post" action="{{ url('/admin/saveLevel1Activity') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($id) && !empty($id)) ? $id : '0'?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($l1ac_image) && !empty($l1ac_image)) ? $l1ac_image : '' ?>">
                    <input type="hidden" name="hidden_points" value="<?php echo (isset($l1ac_points) && !empty($l1ac_points)) ? $l1ac_points : '' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">

                    <div class="box-body">

                    <div class="form-group">
                        <label for="l1ac_text" class="col-sm-2 control-label">{{trans('labels.formlbltext')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="l1ac_text" name="l1ac_text" placeholder="{{trans('labels.formlbltext')}}" value="{{$l1ac_text}}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="l1ac_points" class="col-sm-2 control-label">{{trans('labels.formlblpoint')}}</label>
                        <div class="col-sm-6">
                            <input id="ex1" data-slider-id='ex1Slider' type="text" data-slider-min="0" data-slider-max="200" data-slider-step="5" data-slider-value="{{$l1ac_points}}"  name="l1ac_points" class="boot_slider"/>
                            <span class="badge bg-green" id="label_point" name="label_point"> {{$l1ac_points }} </span>
                        </div>
                    </div>

                    <div class="form-group">
                            <label for="l1ac_image" class="col-sm-2 control-label">{{trans('labels.formlblimage')}}</label>
                            <div class="col-sm-6">
                                <input type="file" id="l1ac_image" name="l1ac_image" onchange="readURL(this);"/>
                                <?php
                                    if(isset($id) && $id != '0'){
                                        if(File::exists(public_path($uploadLevel1ActivityThumbPath.$l1ac_image)) && $l1ac_image !='') { ?> <br>
                                            <img src="{{ url($uploadLevel1ActivityThumbPath.$l1ac_image) }}" alt="{{$l1ac_image}}" >
                                        <?php }
                                    }
                                ?>
                            </div>
                        </div>

                    <div id="addoption" class="addoption">
                      <div class="form-group">
                          <label for="l1op_option" class="col-sm-2 control-label">{{trans('labels.formlbloptions')}}</label>
                          <div class="col-sm-5">
                               <input type="text" class="form-control" id="l1op_option" name="l1op_option[]" placeholder="{{trans('labels.formlbloption')}}" value="{{$option[0]}}" />
                          </div>
                          <!--<div class="col-sm-3">
                                <label class="radio-inline"><input type="radio" name="l1op_fraction" id="l1op_fraction" value="0" <?php if($fraction[0]) echo 'checked="checked"';?> />{{trans('labels.formblfraction')}}</label>
                          </div>-->
                      </div>

                      <div class="form-group">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-5">
                               <input type="text" class="form-control" id="l1op_option" name="l1op_option[]" placeholder="{{trans('labels.formlbloption')}}" value="<?php if($option[0]) echo $option[1]; ?>" />
                          </div>
                          <!--<div class="col-sm-1">
                                <label class="radio-inline"><input type="radio" name="l1op_fraction" id="l1op_fraction" value="1" <?php if($fraction[0]){ } else {if($fraction[0] == "0" && $fraction[1] == "1") {echo 'checked="checked"';}}?>/>{{trans('labels.formblfraction')}}</label>
                          </div>-->
                        </div>

                      <?php
                        for($i=2 ; $i< (count($option)) ; $i++)
                        {
                          ?>
                        <div class="form-group" id="click">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-5">
                               <input type="text" class="form-control" id="l1op_option" name="l1op_option[]" placeholder="{{trans('labels.formlbloption')}}" value="{{$option[$i]}}" />
                            </div>
                            <!--<div class="col-sm-1">
                                <label class="radio-inline"><input type="radio" name="l1op_fraction" id="l1op_fraction" value="{{$i}}" <?php if($fraction[$i] == "1")  echo 'checked="checked"'; ?> />{{trans('labels.formblfraction')}}</label>
                            </div>-->
                            <div class="col-sm-1">
                                    <a href="#" class="btn btn-danger fa remove" name="remove" id="remove">
                                        <span class="glyphicon glyphicon-minus"></span>
                                    </a>
                            </div>
                        </div>
                          <?php
                        }
                      ?>

                    </div>

                    <div class="form-group">
                        <label  class="col-sm-2 control-label"></label>
                        <div class="col-sm-2">
                             <a href="#" class="btn btn-success fa" name="add" id="add">
                                <span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </div>
                    </div>

                    
                    <div class="form-group">
                        <label for="queston_termination_date" class="col-sm-2 control-label">{{trans('labels.question_expiry_date')}}</label>
                        <div class="col-sm-6">
                           <input type="text" class="form-control" id="l1ac_active_date" name="l1ac_active_date" value="{{$date}}" />
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/level1Activity') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->


@stop

<script type = "text/javascript" src = "//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js" ></script>
<script type="text/javascript" src="{{ asset('backend/js/jquery.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery-ui.js') }}"></script>

@section('script')

<script type="text/javascript">
    
    jQuery(document).ready(function()
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
        
        var data = $(':input[id="l1op_option"]').length;
        var wrapper = $("#addoption");
         $('#add').click(function(e)
         {
            e.preventDefault();
                
            var option = '<div class="form-group" id="click">'+
                          '<div class="col-sm-2"></div>'+
                          '<div class="col-sm-5">'+
                               '<input type="text" class="form-control" id="l1op_option" name="l1op_option[]" placeholder="{{trans("labels.formlbloption")}}" value="" />' +
                          '</div>'+
                          /*'<div class="col-sm-1">'+
                                '<label class="radio-inline"><input type="radio" name="l1op_fraction" id="l1op_fraction" value="'+data+'" />{{trans("labels.formblfraction")}}</label>'+
                          '</div>'+*/
                          '<div class="col-sm-1">'+
                                    '<a href="#" class="btn btn-danger fa remove" name="remove" id="remove">'+
                                        '<span class="glyphicon glyphicon-minus"></span>'+
                                   '</a>'+
                            '</div>'+
                      '</div>';
            $(wrapper).append(option);
            data++;
         });

         $(wrapper).on("click",".remove", function(){
                 $(this).parents('#click').remove();
        });

        var validationRules =
        {
                l1ac_text : {
                    required : true
                },
                l1ac_points : {
                    required : true
                },
                'l1op_option[]' : {
                    required : true
                },
                l1ac_active_date : {
                    required : true
                },
                deleted : {
                    required : true
                }

        }


        $validate("#addLevel1Activity").validate({
            rules : validationRules,
            messages : {
                l1ac_text : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                l1ac_points : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                'l1op_option[]' : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                l1ac_active_date : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }

            }
        }),
        
        $("#l1ac_active_date").datepicker({
            minDate: 'today',
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd/mm/yy',
            defaultDate: null
        }).on('change', function() {
            $(this).valid();
        })

    });
</script>
@stop