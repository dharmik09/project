@extends('layouts.admin-master')

@section('content')

<link rel="stylesheet" href="{{ URL::asset('backend/css/slider.css') }}">
<script src="{{ URL::asset('backend/js/bootstrap-slider.js') }}"></script> 

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.traits')}}
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
                    <h3 class="box-title"><?php echo (isset($level1traits) && !empty($level1traits)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.level1traits')}}</h3>
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
                    if($traitsDetail)
                    {
                        foreach($traitsDetail as $value)
                        {
                            $id = $value->id;
                            $tqq_text = $value->tqq_text;
                            $tqq_points = $value->tqq_points;
                            $tqq_image = $value->tqq_image;
                            $date = date('d/m/Y', strtotime($value->tqq_active_date));
                            $deleted = $value->deleted;
                            $tqo_option = $value->tqo_option;
                        }
                    }
                    else
                    {
                        $id = '';
                        $tqq_text = '';
                        $tqq_points = '';
                        $tqq_image = '';
                        $date = '';
                        $deleted = '';
                        $tqo_option = '';
                        $l1op_fraction = '';
                    }
                    $option = explode("," , $tqo_option);
                ?>
                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                <form id="addLevel1Traits" class="form-horizontal" method="post" action="{{ url('/admin/saveLevel1Traits') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($id) && !empty($id)) ? $id : ''?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($tqq_image) && !empty($tqq_image)) ? $tqq_image : '' ?>">
                    <input type="hidden" name="hidden_points" value="<?php echo (isset($tqq_points) && !empty($tqq_points)) ? $tqq_points : '' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">

                    <div class="box-body">

                    <div class="form-group">
                        <label for="tqq_text" class="col-sm-2 control-label">{{trans('labels.formlbltext')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="tqq_text" name="tqq_text" placeholder="{{trans('labels.formlbltext')}}" value="{{$tqq_text}}" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tqq_points" class="col-sm-2 control-label">{{trans('labels.formlblpoint')}}</label>
                        <div class="col-sm-6">
                            <input id="ex1" data-slider-id='ex1Slider' type="text" data-slider-min="0" data-slider-max="200" data-slider-step="5" data-slider-value="{{$tqq_points}}"  name="tqq_points" class="boot_slider"/>
                            <span class="badge bg-green" id="label_point" name="label_point"> {{$tqq_points }} </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tqq_image" class="col-sm-2 control-label">{{trans('labels.formlblimage')}}</label>
                        <div class="col-sm-6">
                            <input type="file" id="tqq_image" name="tqq_image" onchange="readURL(this);"/>
                            <?php
                                if(isset($id) && $id != '0'){
                                    if(File::exists(public_path($uploadLevel1TraitsThumbPath.$tqq_image)) && $tqq_image !='') { ?> <br>
                                        <img src="{{ url($uploadLevel1TraitsThumbPath.$tqq_image) }}" alt="{{$tqq_image}}" >
                                    <?php }
                                }
                            ?>
                        </div>
                    </div>

                    <div id="addoption" class="addoption">
                        <div class="form-group">
                            <label for="tqo_option" class="col-sm-2 control-label">{{trans('labels.formlbloptions')}}</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="tqo_option" name="tqo_option[]" placeholder="{{trans('labels.formlbloption')}}" value="{{$option[0]}}" />
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-5">
                                <input type="text" class="form-control" id="tqo_option" name="tqo_option[]" placeholder="{{trans('labels.formlbloption')}}" value="<?php if($option[0]) echo $option[1]; ?>" />
                            </div>
                        </div>

                        <?php
                            for($i=2 ; $i< (count($option)) ; $i++)
                            {
                        ?>
                            <div class="form-group" id="click">
                                <div class="col-sm-2"></div>
                                <div class="col-sm-5">
                                   <input type="text" class="form-control" id="tqo_option" name="tqo_option[]" placeholder="{{trans('labels.formlbloption')}}" value="{{$option[$i]}}" />
                                </div>
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
                           <input type="text" class="form-control" id="tqq_active_date" name="tqq_active_date" value="{{$date}}" />
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
        
        var data = $(':input[id="tqo_option"]').length;
        var wrapper = $("#addoption");
         $('#add').click(function(e)
         {
            e.preventDefault();
                
            var option = '<div class="form-group" id="click">'+
                          '<div class="col-sm-2"></div>'+
                          '<div class="col-sm-5">'+
                               '<input type="text" class="form-control" id="tqo_option" name="tqo_option[]" placeholder="{{trans("labels.formlbloption")}}" value="" />' +
                          '</div>'+
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
                tqq_text : {
                    required : true
                },
                tqq_points : {
                    required : true
                },
                'tqo_option[]' : {
                    required : true
                },
                tqq_active_date : {
                    required : true
                },
                deleted : {
                    required : true
                }

        }


        $validate("#addLevel1Traits").validate({
            rules : validationRules,
            messages : {
                tqq_text : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                tqq_points : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                'tqo_option[]' : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                tqq_active_date : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }

            }
        }),
        
        $("#tqq_active_date").datepicker({
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