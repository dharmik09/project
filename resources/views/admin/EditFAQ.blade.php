@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.faq')}}
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
                     <h3 class="box-title"><?php echo (isset($faqDetail) && !empty($faqDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.faq')}}</h3>
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

                <form id="addFAQ" class="form-horizontal" method="post" action="{{ url('/admin/saveFaq') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($faqDetail) && !empty($faqDetail)) ? $faqDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_photo" value="<?php echo (isset($faqDetail) && !empty($faqDetail)) ? $faqDetail->f_photo : '' ?>">

                    <div class="box-body">

                        <?php
                        if (old('f_question_text'))
                            $f_question_text = old('f_question_text');
                        elseif ($faqDetail)
                            $f_question_text = $faqDetail->f_question_text;
                        else
                            $f_question_text = '';
                        ?>
                        <div class="form-group">
                            <label for="f_question_text" class="col-sm-2 control-label">{{trans('labels.questiontext')}}</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="f_question_text" name="f_question_text" placeholder="{{trans('labels.questiontext')}}" value="{{ $f_question_text }}" />
                            </div>
                        </div>

                        <?php
                        if (old('f_que_answer'))
                            $f_que_answer = old('f_que_answer');
                        elseif ($faqDetail)
                            $f_que_answer = $faqDetail->f_que_answer;
                        else
                            $f_que_answer = '';
                        ?>

                        <div class="form-group">
                            <label for="f_que_answer" class="col-sm-2 control-label">{{trans('labels.questionans')}}</label>
                            <div class="col-sm-10">
                                <textarea name="f_que_answer" id="f_que_answer">{{$f_que_answer}}</textarea>
                            </div>
                        </div>

                        <?php
                        if (old('f_que_group'))
                            $f_que_group = old('f_que_group');
                        elseif ($faqDetail)
                            $f_que_group = $faqDetail->f_que_group;
                        else
                            $f_que_group = '';
                        ?>
                        <div class="form-group">
                            <label for="f_que_group" class="col-sm-2 control-label">{{trans('labels.questiongroup')}}</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="f_que_group" name="f_que_group" placeholder="{{trans('labels.questiongroup')}}" value="{{$f_que_group}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlblphoto')}}</label>
                            <div class="col-sm-2">
                                <input type="file" id="f_photo" name="f_photo" onchange="readURL(this);"/>
                                <?php
                                if (isset($faqDetail->id) && $faqDetail->id != '0') {
                                    if (File::exists(public_path($uploadFAQThumbPath . $faqDetail->f_photo)) && $faqDetail->f_photo != '') {
                                        ?><br>
                                        <img src="{{ url($uploadFAQThumbPath.$faqDetail->f_photo) }}" alt="{{$faqDetail->f_photo}}"  height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                    <?php } else { ?>
                                        <img src="{{ asset('/backend/images/proteen_logo.png')}}" class="user-image" alt="Default Image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>

                        <?php
                        if (old('deleted'))
                            $deleted = old('deleted');
                        elseif ($faqDetail)
                            $deleted = $faqDetail->deleted;
                        else
                            $deleted = '';
                        ?>
                        <div class="form-group">
                            <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                            <div class="col-sm-6">
                                <?php $staus = Helpers::status();
                                ?>
                                <select class="form-control" id="deleted" name="deleted">
                                    <?php foreach ($staus as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if($deleted == $key) echo 'selected'; ?> >{{$value}}</option>
                                    <?php } ?>

                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="box-footer">
                        <button type="submit" id="submit" class="btn btn-primary btn-flat" >{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/faq') }}">{{trans('labels.cancelbtn')}}</a>
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
    CKEDITOR.replace( 'f_que_answer' );

    jQuery(document).ready(function() {
        jQuery.validator.addMethod("emptyetbody", function(value, element) {
        var cms_body_data = CKEDITOR.instances['f_que_answer'].getData();

        return cms_body_data != '';
        }, "<?php echo trans('validation.requiredfield')?>");

        var validationRules = {
                  f_question_text : {
                      required : true
                  },
                  f_que_answer : {
                      emptyetbody : true
                  },
                  f_que_group : {
                    required : true
                  },
                  deleted : {
                      required : true
                  }
              }

        $("#addFAQ").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                f_question_text : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                f_que_answer: {
                    emptyetbody : "<?php echo trans('validation.requiredfield'); ?>"
                },
                f_que_group: {
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

