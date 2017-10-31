@extends('developer.Master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.personalitytypescale')}}
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
                    <h3 class="box-title"><?php echo (isset($personalityDetail) && !empty($personalityDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.personalitytypescale')}}</h3>
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
              
                <form id="addPersonalityTypeScale" class="form-horizontal" method="post" action="{{ url('/developer/savepersonalitytypescale') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                    <div class="box-body">
                    <div class="form-group">
                        <label for="mit_name" class="col-sm-2 control-label"></label>
                        
                        <div class="col-sm-2">
                            {{ trans('labels.formlbllowrange')}}
                        </div>
                        
                        <div class="col-sm-2">
                            {{ trans('labels.formlblmoderaterange')}}
                        </div>
                        
                        <div class="col-sm-2">
                            {{ trans('labels.formlblhighrange')}}
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
                   <?php $personality = Helpers::getActivePersonality(); $row=0; 
                    foreach ($personality as $key => $value) { ?>
                        <input type="hidden" name="id[]" value="<?php echo (isset($personalityDetail) && !empty($personalityDetail) && isset($personalityDetail[$row]->id)) ? $personalityDetail[$row]->id : '0' ?>">
                    <div class="form-group">
                        <label for="pts_name" class="col-sm-2 control-label"> <?php echo $value->pt_name; ?></label>
                        <input type="hidden" name="pts_name[]" value="<?php echo $value->id; ?>" /> 
                        
                        <div class="col-sm-1">
                            <input type="number" class="form-control" id="pts_low_min_score" name="pts_low_min_score[]" placeholder="{{ trans('labels.formlblmin') }}" value="<?php echo (isset($personalityDetail) && !empty($personalityDetail) && isset($personalityDetail[$row]->pts_high_min_score)) ? $personalityDetail[$row]->pts_low_min_score : '0' ?>"/>
                        </div>
                        <div class="col-sm-1">
                            <input type="number" class="form-control" id="pts_low_max_score" name="pts_low_max_score[]" placeholder="{{ trans('labels.formlblmax') }}" value="<?php echo (isset($personalityDetail) && !empty($personalityDetail) && isset($personalityDetail[$row]->pts_high_min_score)) ? $personalityDetail[$row]->pts_low_max_score : '0' ?>"/>
                        </div>
                        
                        
                        <div class="col-sm-1">
                            <input type="number" class="form-control" id="pts_moderate_min_score" name="pts_moderate_min_score[]" placeholder="{{ trans('labels.formlblmin') }}" value="<?php echo (isset($personalityDetail) && !empty($personalityDetail) && isset($personalityDetail[$row]->pts_high_min_score)) ? $personalityDetail[$row]->pts_moderate_min_score : '0' ?>"/>
                        </div>
                        <div class="col-sm-1">
                            <input type="number" class="form-control" id="pts_moderate_max_score" name="pts_moderate_max_score[]" placeholder="{{ trans('labels.formlblmax') }}" value="<?php echo (isset($personalityDetail) && !empty($personalityDetail) && isset($personalityDetail[$row]->pts_high_min_score)) ? $personalityDetail[$row]->pts_moderate_max_score : '0' ?>"/>
                        </div>
                        
                        
                        <div class="col-sm-1">
                            <input type="number" class="form-control" id="pts_high_min_score" name="pts_high_min_score[]" placeholder="{{ trans('labels.formlblmin') }}" value="<?php echo (isset($personalityDetail) && !empty($personalityDetail) && isset($personalityDetail[$row]->pts_high_min_score)) ? $personalityDetail[$row]->pts_high_min_score : '0' ?>"/>
                        </div>
                        <div class="col-sm-1">
                            <input type="number" class="form-control" id="pts_high_max_score" name="pts_high_max_score[]" placeholder="{{ trans('labels.formlblmax') }}" value="<?php echo (isset($personalityDetail) && !empty($personalityDetail) && isset($personalityDetail[$row]->pts_high_min_score)) ? $personalityDetail[$row]->pts_high_max_score : '0' ?>"/>
                        </div>
                        
                    </div>
                    <?php $row++; } ?>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('developer/personalitytypescale') }}">{{trans('labels.cancelbtn')}}</a>
                    </div>
                </form>
            </div>  
        </div>
    </div>
</section><!-- /.content -->

@stop



