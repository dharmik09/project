@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.learningstyle')}}
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
                    <h3 class="box-title"><?php echo (isset($learningStyleDetail) && !empty($learningStyleDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.learningstyle')}}</h3>
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
                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                <form id="addLearningStyle" class="form-horizontal" method="post" action="{{ url('/admin/saveLearningStyle') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($learningStyleDetail) && !empty($learningStyleDetail)) ? $learningStyleDetail->id : '0' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <input type="hidden" name="hidden_image" value="<?php echo (isset($learningStyleDetail) && !empty($learningStyleDetail)) ? $learningStyleDetail->ls_image : '' ?>">
                    <div class="box-body">

                    <?php
                    if (old('ls_name'))
                        $ls_name = old('ls_name');
                    elseif ($learningStyleDetail)
                        $ls_name = $learningStyleDetail->ls_name;
                    else
                        $ls_name = '';
                    ?>

                    <div class="form-group">
                        <label for="ls_name" class="col-sm-2 control-label">{{trans('labels.formlblname')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id=ls_name"" name="ls_name" placeholder="{{trans('labels.formlblname')}}" value="{{$ls_name}}" minlength="3" maxlength="50"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ls_image" class="col-sm-2 control-label">{{trans('labels.formlblimage')}}</label>
                        <div class="col-sm-2">
                            <input type="file" id="ls_image" name="ls_image" onchange="readURL(this);"/>
                            <?php
                                if (isset($learningStyleDetail->id) && $learningStyleDetail->id != '0') {
                                $image = ($learningStyleDetail->ls_image != "" && Storage::disk('s3')->exists($uploadLearningStyleThumbPath.$learningStyleDetail->ls_image)) ? Config::get('constant.DEFAULT_AWS').$uploadLearningStyleThumbPath.$learningStyleDetail->ls_image : asset('/backend/images/proteen_logo.png');
                            ?>
                            <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                            <?php } ?>
                        </div>
                    </div>

                    <?php
                        if (old('ls_description'))
                            $ls_description = old('ls_description');
                        elseif ($learningStyleDetail)
                            $ls_description = $learningStyleDetail->ls_description;
                        else
                            $ls_description = '';
                    ?>
                    <div class="form-group">
                        <label for="ls_description" class="col-sm-2 control-label">{{trans('labels.formlbldescription')}}</label>
                            <div class="col-sm-6">
                                <textarea name='ls_description' id='ls_description' rows="3"cols="84" placeholder="{{trans('labels.formlbldescription')}}">{{ $ls_description }}</textarea>
                            </div>
                    </div>

                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($learningStyleDetail)
                        $deleted = $learningStyleDetail->deleted;
                    else
                        $deleted = '';
                    ?>
                    <div class="form-group">
                        <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                        <div class="col-sm-6">
                            <?php $staus = Helpers::status(); ?>
                            <select class="form-control" id="deleted" name="deleted">
                            <?php foreach ($staus as $key => $value) { ?>
                                <option value="{{$key}}" <?php if ($deleted == $key) echo 'selected'; ?>>{{$value}}</option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>

                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/level4LearningStyle') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
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
            ls_name : {
                required : true
            },
            deleted : {
                required : true
            }
        }

        $("#addLearningStyle").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                ls_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        } )
    } );

</script>
@stop
