@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.professiontag')}}
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
                    <h3 class="box-title"><?php echo (isset($tags) && !empty($tags)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.professiontag')}}</h3>
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
                <form id="addProfessionTag" class="form-horizontal" method="post" action="{{ url('/admin/saveProfessionTag') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($tags) && !empty($tags)) ? $tags->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($tags) && !empty($tags)) ? $tags->pt_image : '' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">

                    <?php
                    if (old('pt_name'))
                        $pt_name = old('pt_name');
                    elseif ($tags)
                        $pt_name = $tags->pt_name;
                    else
                        $pt_name = '';
                    ?>
                    <div class="form-group">
                        <label for="pt_name" class="col-sm-2 control-label">{{trans('labels.professiontagname')}}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="pt_name" name="pt_name" placeholder="{{trans('labels.professiontagname')}}" value="{{$pt_name}}"/>
                        </div>
                    </div>
                    <?php
                    if (old('pt_image'))
                        $pt_image = old('pt_image');
                    elseif ($tags)
                        $pt_image = $tags->pt_image;
                    else
                        $pt_image = '';
                    ?>
                    <div class="form-group">
                        <label for="pt_image" class="col-sm-2 control-label">{{trans('labels.professiontagimage')}}</label>
                        <div class="col-sm-2">
                            <input type="file" id="pt_image" name="pt_image" onchange="readURL(this);"/>
                            <?php
                                if (isset($tags->id) && $tags->id != '0') {
                                    $image = (isset($tags->pt_image) && $tags->pt_image != "" ) ? Storage::url($tagThumbImageUploadPath.$tags->pt_image) : asset('/backend/images/proteen_logo.png'); ?>
                                    <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                <?php } ?>
                        </div>
                        <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                    </div>
                    <?php
                    if (old('pt_description'))
                        $pt_description = old('pt_description');
                    elseif ($tags)
                        $pt_description = $tags->pt_description;
                    else
                        $pt_description = '';
                    ?>
                    <div class="form-group">
                        <label for="pt_description" class="col-sm-2 control-label">{{trans('labels.professiontagdescription')}}</label>
                        <div class="col-sm-10">
                            <textarea id="pt_description" name="pt_description">{{$pt_description}}</textarea>
                        </div>
                    </div>
                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($tags)
                        $deleted = $tags->deleted;
                    else
                        $deleted = '';
                    ?>
                    <div class="form-group">
                        <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                        <div class="col-sm-10">
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/professionTags') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
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
    CKEDITOR.replace('pt_description');
    $( document ).ready(function( $ ) {
        jQuery.validator.addMethod("emptyetbody", function(value, element) {
            var et_body_data = CKEDITOR.instances['pt_description'].getData();
            return et_body_data != '';
        }, "<?php echo trans('validation.newsdescriptionrequired')?>");
    });
    jQuery(document).ready(function() {
        <?php if (isset($tags->id) && $tags->id != '0') { ?>
            var validationRules = {
                pt_name : {
                    required : true,
                },
                pt_description : {
                    emptyetbody : true,
                },
                deleted : {
                    required : true
                }
            }
        <?php } else { ?>
            var validationRules = {
                pt_name : {
                    required : true,
                },
                pt_description : {
                    emptyetbody : true,
                },
                pt_image : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } ?>
        $("#addProfessionTag").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                pt_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                pt_image : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                pt_description : {
                    emptyetbody : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        } )
    } );

</script>
@stop
