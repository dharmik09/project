@extends('developer.Master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.apptitudetypescale')}}
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
                    <h3 class="box-title"><?php echo (isset($apptitudeScaleDetail) && !empty($apptitudeScaleDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.apptitudetypescale')}}</h3>
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

                <form id="addApptitudeTypeScale" class="form-horizontal" method="post" action="{{ url('/developer/saveapptitudetypescale') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="">
                    <div class="box-body">
                     <div class="form-group">
                        <label for="mit_name" class="col-sm-2 control-label"></label>
                        <div class="col-sm-2">
                            <h4>{{ trans('labels.formlbllowrange')}}</h4>
                        </div>
                        
                        
                        <div class="col-sm-2">
                           <h4> {{ trans('labels.formlblmoderaterange')}}</h4>
                        </div>
                        
                        <div class="col-sm-2">
                            <h4>{{ trans('labels.formlblhighrange')}}</h4>
                        </div>
                    </div>
                        
                        <div class="form-group">
                        <label for="mit_name" class="col-sm-2 control-label"></label>
                        <div class="col-sm-1">
                            Min
                        </div>
                        <div class="col-sm-1">
                            Max
                        </div>
                        <div class="col-sm-1">
                            Min
                        </div>
                        <div class="col-sm-1">
                            Max
                        </div>
                        <div class="col-sm-1">
                            Min
                        </div>
                        <div class="col-sm-1">
                            Max
                        </div>
                        
                    </div>
                        
                   <?php $apptitude = Helpers::getActiveApptitude(); $row=0;
                    foreach ($apptitude as $key => $value) { ?>
                    <input type="hidden" name="id[]" value="<?php echo (isset($apptitudeScaleDetail) && !empty($apptitudeScaleDetail) && isset($apptitudeScaleDetail[$row]->id)) ? $apptitudeScaleDetail[$row]->id : '0' ?>">

                    <input type="hidden" name="ats_apptitude_type_id[]" id="ats_apptitude_type_id" value="{{$value->id}}">

                    <div class="form-group">
                        <label for="mit_name" class="col-sm-2 control-label"> <?php echo $value->apt_name; ?></label>
                        
                        <div class="col-sm-1">
                            <input type="number" class="form-control" id="ats_low_min_score" name="ats_low_min_score[]" placeholder="" value="<?php echo (isset($apptitudeScaleDetail) && !empty($apptitudeScaleDetail) && isset($apptitudeScaleDetail[$row]->ats_low_min_score)) ? $apptitudeScaleDetail[$row]->ats_low_min_score : '0' ?>"/>
                        </div>
                        <div class="col-sm-1">
                            <input type="number" class="form-control" id="ats_low_max_score" name="ats_low_max_score[]" placeholder="" value="<?php echo (isset($apptitudeScaleDetail) && !empty($apptitudeScaleDetail) && isset($apptitudeScaleDetail[$row]->ats_low_max_score)) ? $apptitudeScaleDetail[$row]->ats_low_max_score : '0' ?>"/>
                        </div>
                        
                        <div class="col-sm-1">
                            <input type="number" class="form-control" id="ats_moderate_min_score" name="ats_moderate_min_score[]" placeholder="" value="<?php echo (isset($apptitudeScaleDetail) && !empty($apptitudeScaleDetail) && isset($apptitudeScaleDetail[$row]->ats_moderate_min_score)) ? $apptitudeScaleDetail[$row]->ats_moderate_min_score : '0' ?>"/>
                        </div>
                        <div class="col-sm-1">
                            <input type="number" class="form-control" id="ats_moderate_max_score" name="ats_moderate_max_score[]" placeholder="" value="<?php echo (isset($apptitudeScaleDetail) && !empty($apptitudeScaleDetail) && isset($apptitudeScaleDetail[$row]->ats_moderate_max_score)) ? $apptitudeScaleDetail[$row]->ats_moderate_max_score : '0' ?>"/>
                        </div>
                        
                        <div class="col-sm-1">
                            <input type="number" class="form-control" id="ats_high_min_score" name="ats_high_min_score[]" placeholder="" value="<?php echo (isset($apptitudeScaleDetail) && !empty($apptitudeScaleDetail) && isset($apptitudeScaleDetail[$row]->ats_high_min_score)) ? $apptitudeScaleDetail[$row]->ats_high_min_score : '0' ?>"/>
                        </div>
                        <div class="col-sm-1">
                            <input type="number" class="form-control" id="ats_high_max_score" name="ats_high_max_score[]" placeholder="" value="<?php echo (isset($apptitudeScaleDetail) && !empty($apptitudeScaleDetail) && isset($apptitudeScaleDetail[$row]->ats_high_max_score)) ? $apptitudeScaleDetail[$row]->ats_high_max_score : '0' ?>"/>
                        </div>

                    </div>
                    <?php $row++; } ?>

                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('developer/apptitudetypescale') }}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->

@stop

