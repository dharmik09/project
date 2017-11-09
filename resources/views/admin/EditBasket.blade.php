@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.baskets')}}
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
                    <h3 class="box-title"><?php echo (isset($basketDetail) && !empty($basketDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.basket')}}</h3>
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
                <form id="addBasket" class="form-horizontal" method="post" action="{{ url('/admin/saveBasket') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($basketDetail) && !empty($basketDetail)) ? $basketDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($basketDetail) && !empty($basketDetail)) ? $basketDetail->b_logo : '' ?>">
                    <input type="hidden" name="hidden_video" value="<?php echo (isset($basketDetail) && !empty($basketDetail)) ? $basketDetail->b_video : '' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">

                    <?php
                    if (old('b_name'))
                        $b_name = old('b_name');
                    elseif ($basketDetail)
                        $b_name = $basketDetail->b_name;
                    else
                        $b_name = '';
                    ?>
                    <div class="form-group">
                        <label for="b_name" class="col-sm-2 control-label">{{trans('labels.formlblname')}}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id=b_name"" name="b_name" placeholder="{{trans('labels.formlblname')}}" value="{{$b_name}}" minlength="3" maxlength="50"/>
                        </div>
                    </div>

                    <?php
                    if (old('b_intro'))
                        $b_intro = old('b_intro');
                    elseif ($basketDetail)
                        $b_intro = $basketDetail->b_intro;
                    else
                        $b_intro = '';
                    ?>
                        <div class="form-group" style="display:none;">
                        <label for="b_intro" class="col-sm-2 control-label">{{trans('labels.formlblbasketintro')}}</label>
                        <div class="col-sm-10">
                            <textarea name="b_intro" id="b_intro" >{{$b_intro}}</textarea>
                        </div>
                    </div>

                    <?php
                    if (old('b_logo'))
                        $b_logo = old('b_logo');
                    elseif ($basketDetail)
                        $b_logo = $basketDetail->b_logo;
                    else
                        $b_logo = '';
                    ?>
                    <div class="form-group">
                        <label for="b_logo" class="col-sm-2 control-label">{{trans('labels.formlbllogo')}}</label>
                        <div class="col-sm-2">
                            <input type="file" id="b_logo" name="b_logo" onchange="readURL(this);"/>
                            <?php
                                if(isset($basketDetail->id) && $basketDetail->id != '0') {
                                    $image = ($basketDetail->b_logo != "" && Storage::disk('s3')->exists($uploadBasketThumbPath.$basketDetail->b_logo)) ? Config::get('constant.DEFAULT_AWS').$uploadBasketThumbPath.$basketDetail->b_logo : asset('/backend/images/proteen_logo.png'); ?>
                                    <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                <?php } ?>
                        </div>
                        <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                    </div>

                    <?php
                        if (old('b_video_type'))
                            $b_video_type = old('b_video_type');
                        elseif ($basketDetail)
                            $b_video_type = $basketDetail->b_video_type;
                        else
                            $b_video_type = '';
                        $style = '';
                        $styleYoutube = '';
                        $styleVimeo = '';
                        if($b_video_type != "1")
                        {
                            $style = 'style="display:none;"';
                        }
                        if($b_video_type != "2")
                        {
                            $styleYoutube = 'style="display:none;"';
                        }
                        if($b_video_type != "3")
                        {
                            $styleVimeo = 'style="display:none;"';
                        }

                        ?>
                        <div class="form-group" id="video">
                            <label for="b_video_type" class="col-sm-2 control-label">{{trans('labels.formlblvideo')}}</label>
                            <div class="col-sm-6">
                                  <label class="radio-inline"><input type="radio" name="b_video_type" id="pf_video_type" value="2" <?php if($b_video_type == "2")  echo 'checked="checked"'; ?>/>{{trans('labels.formblyoutube')}}</label>
                            </div>
                        </div>

                        <?php
                          if (old('b_video'))
                              $b_video = old('b_video');
                          elseif ($basketDetail)
                              $b_video = $basketDetail->b_video;
                          else
                              $b_video = '';
                         ?>
                        <div class="form-group" id="youtube" <?php echo $styleYoutube;?>>
                            <label for="youtube" class="col-sm-2 control-label"></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="youtube" name="youtube" placeholder="{{trans('labels.formlblyoutube')}}" value="<?php if($b_video_type == '2') {?>{{$b_video}} <?php }?>" />
                                <?php
                                                         
                                if(isset($basketDetail->id) && $basketDetail->id != '0' && $basketDetail->b_video != ''){
                                    $videoCode = Helpers::youtube_id_from_url($basketDetail->b_video); 
                                    
                                     if(($b_video_type == '2')) {?><br>
                                        <iframe  id="b_video" width="500" height="300"
                                      	     src="https://www.youtube.com/embed/{{$videoCode}}" >
                                      	</iframe >
                        
                                    <?php }
                                    }
                            ?>
                            </div>
                        </div>

                        
                    <div class="form-group">
                        <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                        <div class="col-sm-6">
                            <?php $staus = Helpers::status(); ?>
                            <select class="form-control" id="deleted" name="deleted">
                            <?php foreach ($staus as $key => $value) { ?>
                                <option value="{{$key}}">{{$value}}</option>
                            <?php } ?>
                            </select>
                        </div>
                    </div>

                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/baskets') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>
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
    CKEDITOR.replace( 'b_intro' );
    jQuery(document).ready(function() {

    jQuery.validator.addMethod("emptyetbody", function(value, element) {
    var data = CKEDITOR.instances['b_intro'].getData();

     return data != '';
    }, "<?php echo trans('validation.requiredfield')?>");

            var validationRules = {
                b_name : {
                    required : true
                },                
                deleted : {
                    required : true
                }
            }

        $("#addBasket").validate({
            ignore : "",
            rules : validationRules,
            messages : {
                b_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },                
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });

    $("#video input:radio").click(function() {

            var b_video = this.value;
            if (b_video == '1')
            {
                $('#normal').show();
                $('#youtube').hide();
                $('#vimeo').hide();
            }
            else if (b_video == '2' )
            {
                $('#youtube').show();
                $('#normal').hide();
                $('#vimeo').hide();
            }
            else if (b_video == '3' )
            {
                $('#vimeo').show();
                $('#normal').hide();
                $('#youtube').hide();
            }
            else
            {
                $('#normal').hide();
                $('#youtube').hide();
                $('#vimeo').hide();
            }
    });

</script>
@stop
