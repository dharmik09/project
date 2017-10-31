@extends('developer.Master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.level1qualities')}}
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
                    <h3 class="box-title"><?php echo (isset($qualityDetail) && !empty($qualityDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.level1quality')}}</h3>
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

                <form id="addLevel1Quality" class="form-horizontal" method="post" action="{{ url('/developer/savelevel1quality') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($qualityDetail) && !empty($qualityDetail)) ? $qualityDetail->id : '0' ?>">
                    <div class="box-body">

                    <?php
                    if (old('l1qa_name'))
                        $l1qa_name = old('l1qa_name');
                    elseif ($qualityDetail)
                        $l1qa_name = $qualityDetail->l1qa_name;
                    else
                        $l1qa_name = '';
                    ?>
                    <div class="form-group">
                        <label for="l1qa_name" class="col-sm-2 control-label">{{trans('labels.formlblname')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id=l1qa_name"" name="l1qa_name" placeholder="{{trans('labels.formlblname')}}" value="{{$l1qa_name}}" minlength="3" maxlength="50"/>
                        </div>
                    </div>
                        
                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($qualityDetail)
                        $deleted = $qualityDetail->deleted;
                    else
                        $deleted = '';
                    ?>
                    <div class="form-group">
                        <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('developer/level1qualities') }}">{{trans('labels.cancelbtn')}}</a>
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
                l1qa_name : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }

        $("#addLevel1Quality").validate({
            rules : validationRules,
            messages : {
                l1qa_name : {
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

