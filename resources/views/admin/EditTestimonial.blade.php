@extends('layouts.admin-master')

@section('content')
<section class="content-header">
    <h1>
        {{trans('labels.testimonials')}}
    </h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo (isset($testimonial) && !empty($testimonial)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.testimonial')}}</h3>
                </div>
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
                <form id="addTestimonial" class="form-horizontal" method="post" action="{{ url('/admin/saveTestimonial') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($testimonial) && !empty($testimonial)) ? $testimonial->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($testimonial) && !empty($testimonial)) ? $testimonial->t_image : '' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">

                    <?php
                    if (old('t_name'))
                        $t_name = old('t_name');
                    elseif ($testimonial)
                        $t_name = $testimonial->t_name;
                    else
                        $t_name = '';
                    ?>
                    <div class="form-group">
                        <label for="pc_element_name" class="col-sm-2 control-label">{{trans('labels.testimonialname')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="t_name" name="t_name" placeholder="{{trans('labels.testimonialname')}}" value="{{$t_name}}"/>
                        </div>
                    </div>
                    <?php
                    if (old('t_title'))
                        $t_title = old('t_title');
                    elseif ($testimonial)
                        $t_title = $testimonial->t_title;
                    else
                        $t_title = '';
                    ?>
                    <div class="form-group">
                        <label for="t_title" class="col-sm-2 control-label">{{trans('labels.testimonialtitle')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="t_title" name="t_title" placeholder="{{trans('labels.testimonialtitle')}}" value="{{$t_title}}"/>
                        </div>
                    </div>
                    <?php
                        if (old('t_type'))
                            $t_type = old('t_type');
                        elseif ($testimonial)
                            $t_type = $testimonial->t_type;
                        else
                            $t_type = '';
                    ?>
                    <div class="form-group">
                        <label for="t_type" class="col-sm-2 control-label">Type</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="t_type" name="t_type">
                                <option value="testinomials" <?php if ($t_type == "testinomials") echo 'selected'; ?> > Testinomials </option>
                                <option value="management" <?php if ($t_type == "management") echo 'selected'; ?> > Team Management </option>
                                <option value="advisory" <?php if ($t_type == "advisory") echo 'selected'; ?> > Team Advisory </option>
                            </select>
                        </div>
                    </div>
                    <?php
                    if (old('t_image'))
                        $t_image = old('t_image');
                    elseif ($testimonial)
                        $t_image = $testimonial->t_image;
                    else
                        $t_image = '';
                    ?>
                    <div class="form-group">
                        <label for="t_image" class="col-sm-2 control-label">{{trans('labels.testimonialimage')}}</label>
                        <div class="col-sm-2">
                            <input type="file" id="t_image" name="t_image" onchange="readURL(this);"/>
                            <?php
                                if (isset($testimonial->id) && $testimonial->id != '0') {
                                    $image = (isset($testimonial->t_image) && $testimonial->t_image != "" ) ? Storage::url($testimonialOriginalImageUploadPath.$testimonial->t_image) : asset('/backend/images/proteen_logo.png'); ?>
                                    <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                <?php } ?>
                        </div>
                        <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                    </div>
                    <?php
                    if (old('t_description'))
                        $t_description = old('t_description');
                    elseif ($testimonial)
                        $t_description = $testimonial->t_description;
                    else
                        $t_description = '';
                    ?>

                    <div class="form-group">
                        <label for="t_description" class="col-sm-2 control-label">{{trans('labels.testimonialdescription')}}</label>
                        <div class="col-sm-6">
                            <textarea id="t_description" name="t_description" class="form-control">{{$t_description}}</textarea>
                        </div>
                    </div>
                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($testimonial)
                        $deleted = $testimonial->deleted;
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/testimonials') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
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
    CKEDITOR.replace('t_description');
    $('.numeric').on('keyup', function() {
        this.value = this.value.replace(/[^0-9]/gi, '');
    });
    jQuery.validator.addMethod("emptyetbody", function(value, element) {
    var t_description = CKEDITOR.instances['t_description'].getData();
        return t_description != '';
    }, "<?php echo trans('validation.requiredfield')?>");
    jQuery(document).ready(function() {
        <?php if (isset($testimonial->id) && $testimonial->id != '0') { ?>
            var validationRules = {
                t_name : {
                    required : true
                },
                t_title : {
                    required : true
                },
                t_description : {
                    emptyetbody : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } else { ?>
            var validationRules = {
                t_name : {
                    required : true
                },
                t_title : {
                    required : true
                },
                t_image : {
                    required : true
                },
                t_description : {
                    emptyetbody : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } ?>
        $("#addTestimonial").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                t_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                t_title : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                t_image : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                t_description : {
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
