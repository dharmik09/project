@extends('layouts.developer-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.systemlevels')}}
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
                    <h3 class="box-title"><?php echo (isset($systemlevelDetail) && !empty($systemlevelDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.systemlevel')}}</h3>
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

                <form id="addSystemLevel" class="form-horizontal" method="post" action="{{ url('/developer/saveSystemLevel') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($systemlevelDetail) && !empty($systemlevelDetail)) ? $systemlevelDetail->id : '0' ?>">
                    <div class="box-body">

                    <?php
                    if (old('sl_name'))
                        $sl_name = old('sl_name');
                    elseif ($systemlevelDetail)
                        $sl_name = $systemlevelDetail->sl_name;
                    else
                        $sl_name = '';
                    ?>
                    <div class="form-group">
                        <label for="sl_name" class="col-sm-2 control-label">{{trans('labels.formlblname')}}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id=sl_name"" name="sl_name" placeholder="{{trans('labels.formlblname')}}" value="{{$sl_name}}" minlength="3" maxlength="50"/>
                        </div>
                    </div>

                    <?php
                    if (old('sl_info'))
                        $sl_info = old('sl_info');
                    elseif ($systemlevelDetail)
                        $sl_info = $systemlevelDetail->sl_info;
                    else
                        $sl_info = '';
                    ?>
                    <div class="form-group">
                        <label for="sl_info" class="col-sm-2 control-label">{{trans('labels.formlblsystemlevelinfo')}}</label>
                        <div class="col-sm-10">
                            <textarea name="sl_info" id="sl_info" >{{$sl_info}}</textarea>
                        </div>
                    </div>

                     <?php
                     if (old('sl_boosters'))
                        $sl_boosters = old('sl_boosters');
                     elseif ($systemlevelDetail)
                        $sl_boosters = $systemlevelDetail->sl_boosters;
                     else
                        $sl_boosters = '';
                     ?>
                     <div class="form-group">
                        <label for="sl_boosters" class="col-sm-2 control-label">{{trans('labels.formlblbooster')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="sl_boosters" name="sl_boosters" placeholder="{{trans('labels.formlblbooster')}}" value="{{$sl_boosters}}"/>
                        </div>
                     </div>

                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($systemlevelDetail)
                        $deleted = $systemlevelDetail->deleted;
                    else
                        $deleted = '';
                    ?>
                    <div class="form-group">
                        <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                        <div class="col-sm-6">
                        <?php $staus = Helpers::status(); //array('1' => 'Active', '2' => 'In active'); ?>
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('developer/systemLevel') }}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->

@stop

@section('script')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
    CKEDITOR.replace( 'sl_info' );

    jQuery(document).ready(function() {

    jQuery.validator.addMethod("emptyetbody", function(value, element) {
    var data = CKEDITOR.instances['sl_info'].getData();

     return data != '';
    }, "<?php echo trans('validation.requiredfield')?>");

            var validationRules = {
                sl_name : {
                    required : true
                },
                sl_boosters : {
                    digits : true
                },
                sl_info : {
                    emptyetbody : true
                },
                deleted : {
                    required : true
                }
            }

        $("#addSystemLevel").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                sl_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                sl_boosters : {
                    digits : "<?php echo trans('validation.digitsonly'); ?>"
                },
                sl_info : {
                    emptyetbody : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });
</script>
@stop

