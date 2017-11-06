@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.configuration')}}
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
                    <h3 class="box-title"><?php echo (isset($configurationDetail) && !empty($configurationDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.configuration')}}</h3>
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

                <form id="addConfiguration" class="form-horizontal" method="post" action="{{ url('/admin/saveConfiguration') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($configurationDetail) && !empty($configurationDetail)) ? $configurationDetail->id : '0' ?>">
                    <div class="box-body">

            
                        <?php
                        if (old('cfg_key'))
                            $cfg_key = old('cfg_key');
                        elseif ($configurationDetail)
                            $cfg_key = $configurationDetail->cfg_key;
                        else
                            $cfg_key = '';
                                                                        
                        ?>
                       <div class="form-group">
                            <label for="cfg_key" class="col-sm-2 control-label">{{trans('labels.cfg_key')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control"  id="cfg_key" name="cfg_key" placeholder="{{trans('labels.cfg_key')}}" value="{{ $cfg_key or ''}}" >
                            </div>
                        </div>


                        <?php
                        if (old('cfg_value'))
                            $cfg_value = old('cfg_value');
                        elseif ($configurationDetail)
                            $cfg_value = $configurationDetail->cfg_value;
                        else
                            $cfg_value = '';
                        ?>
                        <div class="form-group">
                            <label for="cfg_value" class="col-sm-2 control-label">{{trans('labels.cfg_value')}}</label>
                            <div class="col-sm-6">
                                <input type="cfg_value" class="form-control" id="cfg_value" name="cfg_value" placeholder="{{trans('labels.cfg_value')}}" value="{{$cfg_value or ''}}"/>
                            </div>
                        </div>


                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/configurations') }}">{{trans('labels.cancelbtn')}}</a>
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
        var readonly = '<?php echo isset($configurationDetail) && !empty($configurationDetail) ? 'readonly':''; ?>'
        if(readonly == 'readonly'){
            $('#cfg_key').attr('readonly', true);  
            $('#cfg_key').keypress(function(){
               return false; 
            });
        }
        <?php if(isset($configurationDetail->id) && $configurationDetail->id != '0') { ?>
            var validationRules = {
                cfg_key : {
                    required : true
                },
                cfg_value: {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } else { ?>
            var validationRules = {
                cfg_key : {
                    required : true
                },
                cfg_value : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } ?>

        $("#addConfiguration").validate({
            rules : validationRules,
            messages : {
                cfg_key : {
                    required : "<?php echo trans('validation.cfg_keyrequiredfield'); ?>"
                },
                cfg_value : {
                    required : "<?php echo trans('validation.cfg_valuerequiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })                                    
    });
</script>
@stop


