@extends('layouts.developer-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level4promiseplus')}}
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
                    <h3 class="box-title"><?php echo (isset($promiseplusDetail) && !empty($promiseplusDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.level4promiseplus')}}</h3>
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

                <form id="addPromisePlus" class="form-horizontal" method="post" action="{{ url('/developer/saveLevel4PromisePlus') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="box-body">
                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label"></label>
                           
                            <div class="col-sm-6">
                                {{ trans('labels.formlbldescription')}}
                            </div>
                        </div>
                        

                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label">Look Elsewhere</label>
                            <input type="hidden" id="ps_text" name="ps_text[]" value="Look Elsewhere"/>                           
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ps_description" name="ps_description[]" placeholder="{{ trans('labels.formlbldescription') }}" value="<?php echo (isset($promiseplusDetail) && !empty($promiseplusDetail) && isset($promiseplusDetail[0]['ps_description'])) ? $promiseplusDetail[0]['ps_description'] : '' ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label">Stretch Yourself</label>
                            <input type="hidden" id="ps_text" name="ps_text[]" value="Stretch Yourself"/>                            
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ps_description" name="ps_description[]" placeholder="{{ trans('labels.formlbldescription') }}" value="<?php echo (isset($promiseplusDetail) && !empty($promiseplusDetail) && isset($promiseplusDetail[1]['ps_description'])) ? $promiseplusDetail[1]['ps_description'] : '' ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label">Secondary Choice</label>
                            <input type="hidden" id="ps_text" name="ps_text[]" value="Secondary Choice"/>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ps_description" name="ps_description[]" placeholder="{{ trans('labels.formlbldescription') }}" value="<?php echo (isset($promiseplusDetail) && !empty($promiseplusDetail) && isset($promiseplusDetail[2]['ps_description'])) ? $promiseplusDetail[2]['ps_description'] : '' ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label">Secondary Choice</label>
                            <input type="hidden" id="ps_text" name="ps_text[]" value="Secondary Choice"/>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ps_description" name="ps_description[]" placeholder="{{ trans('labels.formlbldescription') }}" value="<?php echo (isset($promiseplusDetail) && !empty($promiseplusDetail) && isset($promiseplusDetail[3]['ps_description'])) ? $promiseplusDetail[3]['ps_description'] : '' ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label">Possible Choice</label>
                            <input type="hidden" id="ps_text" name="ps_text[]" value="Possible  Choice"/>                            
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ps_description" name="ps_description[]" placeholder="{{ trans('labels.formlbldescription') }}" value="<?php echo (isset($promiseplusDetail) && !empty($promiseplusDetail) && isset($promiseplusDetail[4]['ps_description'])) ? $promiseplusDetail[4]['ps_description'] : '' ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label">Stretch Yourselves</label>
                            <input type="hidden" id="ps_text" name="ps_text[]" value="Stretch Yourselves"/>                            
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ps_description" name="ps_description[]" placeholder="{{ trans('labels.formlbldescription') }}" value="<?php echo (isset($promiseplusDetail) && !empty($promiseplusDetail) && isset($promiseplusDetail[5]['ps_description'])) ? $promiseplusDetail[5]['ps_description'] : '' ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label">Surprise Match</label>
                            <input type="hidden" id="ps_text" name="ps_text[]" value="Surprise Match"/>                            
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ps_description" name="ps_description[]" placeholder="{{ trans('labels.formlbldescription') }}" value="<?php echo (isset($promiseplusDetail) && !empty($promiseplusDetail) && isset($promiseplusDetail[6]['ps_description'])) ? $promiseplusDetail[6]['ps_description'] : '' ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label">Growth Option</label>
                            <input type="hidden" id="ps_text" name="ps_text[]" value="Growth Option"/>                            
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ps_description" name="ps_description[]" placeholder="{{ trans('labels.formlbldescription') }}" value="<?php echo (isset($promiseplusDetail) && !empty($promiseplusDetail) && isset($promiseplusDetail[7]['ps_description'])) ? $promiseplusDetail[7]['ps_description'] : '' ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="mit_name" class="col-sm-2 control-label">Fitting Choice</label>
                            <input type="hidden" id="ps_text" name="ps_text[]" value="Fitting Choice"/>                            
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ps_description" name="ps_description[]" placeholder="{{ trans('labels.formlbldescription') }}" value="<?php echo (isset($promiseplusDetail) && !empty($promiseplusDetail) && isset($promiseplusDetail[8]['ps_description'])) ? $promiseplusDetail[8]['ps_description'] : '' ?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('developer/level4PromisePlus') }}">{{trans('labels.cancelbtn')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section><!-- /.content -->

@stop