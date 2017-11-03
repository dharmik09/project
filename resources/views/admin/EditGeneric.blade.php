@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.generic')}}
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
                    <h3 class="box-title"><?php echo (isset($genericDetail) && !empty($genericDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.generic')}}</h3>
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

                <form id="addGeneric" class="form-horizontal" method="post" action="{{ url('/admin/saveGeneric') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($genericDetail) && !empty($genericDetail)) ? $genericDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($genericDetail) && !empty($genericDetail)) ? $genericDetail->ga_image : '' ?>">
                    <div class="box-body">

                        <?php
                        if (old('ga_name'))
                            $ga_name = old('ga_name');
                        elseif ($genericDetail)
                            $ga_name = $genericDetail->ga_name;
                        else
                            $ga_name = '';
                        ?>
                       <div class="form-group">
                            <label for="ga_name" class="col-sm-2 control-label">{{trans('labels.genericname')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ga_name" name="ga_name" placeholder="{{trans('labels.genericname')}}" value="{{$ga_name}}" minlength="3" maxlength="50"/>
                            </div>
                        </div>
                        
                       <div class="form-group">
                            <label for="ga_image" class="col-sm-2 control-label">{{trans('labels.genericimage')}}</label>
                            <div class="col-sm-2">
                                <input type="file" id="ga_image" name="ga_image" onchange="readURL(this);"/>
                                <?php
                                    if(isset($genericDetail->id) && $genericDetail->id != '0'){
                                        if(File::exists(public_path($genericThumbImagePath.$genericDetail->ga_image)) && $genericDetail->ga_image !='') { ?><br>
                                            <img src="{{ url($genericThumbImagePath.$genericDetail->ga_image) }}" alt="{{$genericDetail->ga_image}}" >
                                        <?php }else{ ?>
                                        <img src="{{ asset('/backend/images/proteen_logo.png')}}" class="user-image" alt="Default Image" height="<?php echo Config::get('constant.SCHOOL_THUMB_IMAGE_HEIGHT');?>" width="<?php echo Config::get('constant.SCHOOL_THUMB_IMAGE_WIDTH');?>">
                                <?php   }
                                    }
                                ?>
                            </div>
                            <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                        </div>

                        <?php
                        if (old('ga_start_date'))
                            $ga_start_date = old('ga_start_date');
                        elseif ($genericDetail)
                            $ga_start_date = date('d/m/Y', strtotime($genericDetail->ga_start_date));
                        else
                            $ga_start_date = '';
                        ?>
                        <div class="form-group">
                            <label for="ga_start_date" class="col-sm-2 control-label">{{trans('labels.genericstartdate')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ga_start_date" name="ga_start_date" value="{{$ga_start_date}}" />
                            </div>
                        </div>

                        <?php
                        if (old('ga_end_date'))
                            $ga_end_date = old('ga_end_date');
                        elseif ($genericDetail)
                            $ga_end_date = date('d/m/Y', strtotime($genericDetail->ga_end_date));
                        else
                            $ga_end_date = '';
                        ?>
                        <div class="form-group">
                            <label for="ga_end_date" class="col-sm-2 control-label">{{trans('labels.genericenddate')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ga_end_date" name="ga_end_date" value="{{$ga_end_date}}" />
                            </div>
                        </div>

                        
                        <?php
                        if (old('deleted'))
                            $deleted = old('deleted');
                        elseif ($genericDetail)
                            $deleted = $genericDetail->deleted;
                        else
                            $deleted = '';
                        ?>
                        <div class="form-group">
                            <label for="deleted" class="col-sm-2 control-label">{{trans('labels.genericstatus')}}</label>
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/genericAds') }}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->

@stop

@section('script')
<script type="text/javascript" src="{{ asset('backend/js/jquery.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery-ui.js') }}"></script>
<script type="text/javascript">
    jQuery("#ga_start_date").datepicker({
        //minDate: -6935, maxDate: -4380,
        //yearRange: "-18:-13",
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        defaultDate: null
    }).on('change', function() {
        $(this).valid();
    });
    jQuery("#ga_end_date").datepicker({
        //minDate: -6935, maxDate: -4380,
        //yearRange: "-18:-13",
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        defaultDate: null
    }).on('change', function() {
        $(this).valid();
    });
    jQuery(document).ready(function() {
        <?php if(isset($genericDetail->id) && $genericDetail->id != '0') { ?>
            var validationRules = {
                ga_name : {
                    required : true
                }
            }
        <?php } else { ?>
            var validationRules = {
                ga_name : {
                    required : true
                },
                ga_image : {
                    required : true
                },
                ga_start_date : {
                    required : true
                },
                ga_end_date : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } ?>

        $("#addGeneric").validate({
            rules : validationRules,
            messages : {
                ga_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                ga_image : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                ga_start_date : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                ga_end_date : {
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


