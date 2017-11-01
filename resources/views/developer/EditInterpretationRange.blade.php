@extends('layouts.developer-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.interpretationrange')}}
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
                    <h3 class="box-title"><?php echo (isset($interpretationRangeDetail) && !empty($interpretationRangeDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.interpretationrange')}}</h3>
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

                <form id="addinterpretationRange" class="form-horizontal" method="post" action="{{ url('/developer/saveInterpretationRange') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label"></label>
                            <div class="col-sm-2">
                                {{ trans('labels.formlblrange')}}
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
                        </div>

                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label">Low</label>
                            <input type="hidden" id="ir_text" name="ir_text[]" value="Low"/>
                            <div class="col-sm-1">
                                <input type="number" class="form-control" id="ir_min_score" name="ir_min_score[]" placeholder="{{ trans('labels.formlblmin') }}" value="<?php echo (isset($interpretationRangeDetail) && !empty($interpretationRangeDetail) && isset($interpretationRangeDetail[0]['ir_min_score'])) ? $interpretationRangeDetail[0]['ir_min_score'] : '0' ?>"/>
                            </div>
                            <div class="col-sm-1">
                                <input type="number" class="form-control" id="ir_max_score" name="ir_max_score[]" placeholder="{{ trans('labels.formlblmax') }}" value="<?php echo (isset($interpretationRangeDetail) && !empty($interpretationRangeDetail) && isset($interpretationRangeDetail[0]['ir_max_score'])) ? $interpretationRangeDetail[0]['ir_max_score'] : '0' ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label">Medium</label>
                            <input type="hidden" id="ir_text" name="ir_text[]" value="Medium"/>
                            <div class="col-sm-1">
                                <input type="number" class="form-control" id="ir_min_score" name="ir_min_score[]" placeholder="{{ trans('labels.formlblmin') }}" value="<?php echo (isset($interpretationRangeDetail) && !empty($interpretationRangeDetail) && isset($interpretationRangeDetail[1]['ir_min_score'])) ? $interpretationRangeDetail[1]['ir_min_score'] : '0' ?>"/>
                            </div>
                            <div class="col-sm-1">
                                <input type="number" class="form-control" id="ir_max_score" name="ir_max_score[]" placeholder="{{ trans('labels.formlblmax') }}" value="<?php echo (isset($interpretationRangeDetail) && !empty($interpretationRangeDetail) && isset($interpretationRangeDetail[1]['ir_max_score'])) ? $interpretationRangeDetail[1]['ir_max_score'] : '0' ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label">High</label>
                            <input type="hidden" id="ir_text" name="ir_text[]" value="High"/>
                            <div class="col-sm-1">
                                <input type="number" class="form-control" id="ir_min_score" name="ir_min_score[]" placeholder="{{ trans('labels.formlblmin') }}" value="<?php echo (isset($interpretationRangeDetail) && !empty($interpretationRangeDetail) && isset($interpretationRangeDetail[2]['ir_min_score'])) ? $interpretationRangeDetail[2]['ir_min_score'] : '0' ?>"/>
                            </div>
                            <div class="col-sm-1">
                                <input type="number" class="form-control" id="ir_max_score" name="ir_max_score[]" placeholder="{{ trans('labels.formlblmax') }}" value="<?php echo (isset($interpretationRangeDetail) && !empty($interpretationRangeDetail) && isset($interpretationRangeDetail[2]['ir_max_score'])) ? $interpretationRangeDetail[2]['ir_max_score'] : '0' ?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('developer/interpretationRange') }}">{{trans('labels.cancelbtn')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section><!-- /.content -->

@stop