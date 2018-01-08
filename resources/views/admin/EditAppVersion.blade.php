@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.appversion')}}
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
                    <h3 class="box-title"><?php echo (isset($data) && !empty($data)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.appversion')}}</h3>
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
                <form id="addAppVersion" class="form-horizontal" method="post" action="{{ url('/admin/saveAppVersion') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($data) && !empty($data)) ? $data->id : '0' ?>">
                    <div class="box-body">
                        <?php
                            if (old('force_update'))
                                $force_update = old('force_update');
                            elseif ($data)
                                $force_update = $data->force_update;
                            else
                                $force_update = '';
                        ?>
                        <div class="form-group">
                            <label for="force_update" class="col-sm-2 control-label">{{trans('labels.appversionforceupdate')}}</label>
                            <div class="col-sm-10">
                                <select class="form-control" id="force_update" name="force_update">
                                    <option value="1" <?php if ($force_update == 1) echo 'selected'; ?>>{{trans('labels.lbltrue')}}</option>
                                    <option value="0" <?php if ($force_update == 0) echo 'selected'; ?>>{{trans('labels.lblfalse')}}</option>
                                </select>
                            </div>
                        </div>

                        <?php
                            if (old('device_type'))
                                $device_type = old('device_type');
                            elseif ($data)
                                $device_type = $data->device_type;
                            else
                                $device_type = '';
                        ?>
                        <div class="form-group">
                            <label for="device_type" class="col-sm-2 control-label">{{trans('labels.appversiondevicetype')}}</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="<?php echo ($device_type == 1) ? trans('labels.formblios') : trans('labels.formblandroid') ?>" disabled />
                                <input type="hidden" name="device_type" value="{{$device_type}}" />
                            </div>
                        </div>

                        <?php
                            if (old('app_version'))
                                $app_version = old('app_version');
                            elseif ($data)
                                $app_version = $data->app_version;
                            else
                                $app_version = '';
                        ?>
                        <div class="form-group">
                            <label for="app_version" class="col-sm-2 control-label">{{trans('labels.appversion')}}</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="app_version" name="app_version" placeholder="{{trans('labels.appversion')}}" value="{{$app_version}}"/>
                            </div>
                        </div>

                        <?php
                            if (old('message'))
                                $message = old('message');
                            elseif ($data)
                                $message = $data->message;
                            else
                                $message = '';
                        ?>
                        <div class="form-group">
                            <label for="message" class="col-sm-2 control-label">{{trans('labels.appversionmessage')}}</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="message" name="message" placeholder="{{trans('labels.appversionmessage')}}" value="{{$message}}"/>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/appVersions') }}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->

@stop

@section('script')
<script type="text/javascript">

    jQuery(document).ready(function() {

        var validationRules = {
            force_update : {
                required : true,
            },
            device_type : {
                required : true,
            },
            message : {
                required : true
            },
            app_version : {
                required : true
            }
        }

        $("#addAppVersion").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                force_update : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                device_type : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                message : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                app_version : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        } )
    } );

</script>
@stop
