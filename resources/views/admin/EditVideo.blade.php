@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.video')}}
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
                     <h3 class="box-title"><?php echo (isset($videoDetail) && !empty($videoDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.video')}}</h3>
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

                <form id="addVideo" class="form-horizontal" method="post" action="{{ url('/admin/saveVideo') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($videoDetail) && !empty($videoDetail)) ? $videoDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_photo" value="<?php echo (isset($videoDetail) && !empty($videoDetail)) ? $videoDetail->v_photo : '' ?>">

                    <div class="box-body">

                        <?php
                        if (old('v_title'))
                            $v_title = old('v_title');
                        elseif ($videoDetail)
                            $v_title = $videoDetail->v_title;
                        else
                            $v_title = '';
                        ?>
                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.headerblheadtitle')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="v_title" name="v_title" placeholder="{{trans('labels.headerblheadtitle')}}" value="{{ $v_title }}" />
                            </div>
                        </div>

                        <?php
                        if (old('v_link'))
                            $v_link = old('v_link');
                        elseif ($videoDetail)
                            $v_link = $videoDetail->v_link;
                        else
                            $v_link = '';
                        ?>
                        <div class="form-group">
                            <label for="v_link" class="col-sm-2 control-label">{{trans('labels.videolink')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="v_link" name="v_link" placeholder="{{trans('labels.videolink')}}" value="{{$v_link}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlblphoto')}}</label>
                            <div class="col-sm-2">
                                <input type="file" id="v_photo" name="v_photo" onchange="readURL(this);"/>
                                <?php
                                if (isset($videoDetail->id) && $videoDetail->id != '0') {
                                    $image = ($videoDetail->v_photo != "" && Storage::disk('s3')->exists($uploadVideoThumbPath.$videoDetail->v_photo)) ? Config::get('constant.DEFAULT_AWS').$uploadVideoThumbPath.$videoDetail->v_photo : asset('/backend/images/proteen_logo.png');
                                    ?>
                                    <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                <?php } ?>
                            </div>
                            <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                        </div>

                        <?php
                        if (old('deleted'))
                            $deleted = old('deleted');
                        elseif ($videoDetail)
                            $deleted = $videoDetail->deleted;
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/video') }}">{{trans('labels.cancelbtn')}}</a>
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
      <?php if (isset($videoDetail->id) && $videoDetail->id != '0') { ?>
          var validationRules = {
                      v_link : {
                          required : true
                      },
                      v_title : {
                          required : true
                      },
                      deleted : {
                          required : true
                      }
                  }
          <?php } else { ?>
            var validationRules = {
                      v_link : {
                          required : true
                      },
                      v_title : {
                          required : true
                      },
                      v_photo : {
                        required : true
                      }
                      deleted : {
                          required : true
                      }
                  }
          <?php }?>

        $("#addVideo").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                v_link : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                v_title: {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                v_photo: {
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

