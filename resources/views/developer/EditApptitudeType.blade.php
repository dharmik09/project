@extends('layouts.developer-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.apptitudetypes')}}
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
                    <h3 class="box-title"><?php echo (isset($apptitudeDetail) && !empty($apptitudeDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.apptitudetype')}}</h3>
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

                <form id="addApptitudeType" class="form-horizontal" method="post" action="{{ url('/developer/saveApptitudeType') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($apptitudeDetail) && !empty($apptitudeDetail)) ? $apptitudeDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($ci_image) && !empty($apt_image)) ? $apt_image : '' ?>">
                    <div class="box-body">

                    <?php
                    if (old('apt_name'))
                        $apt_name = old('apt_name');
                    elseif ($apptitudeDetail)
                        $apt_name = $apptitudeDetail->apt_name;
                    else
                        $apt_name = '';
                    ?>
                    <div class="form-group">
                        <label for="apt_name" class="col-sm-2 control-label">{{trans('labels.formlblname')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="apt_name" name="apt_name" placeholder="{{trans('labels.formlblname')}}" value="{{$apt_name}}" minlength="3" maxlength="50"/>
                        </div>
                    </div>
                    <?php
                    if (old('apt_slug'))
                        $apt_slug = old('apt_slug');
                    elseif ($apptitudeDetail)
                        $apt_slug = $apptitudeDetail->apt_slug;
                    else
                        $apt_slug = '';
                    ?>
                    <div class="form-group">
                        <label for="apt_slug" class="col-sm-2 control-label">{{trans('labels.formlblslug')}}</label>
                        <div class="col-sm-6">
                            <input type="text" readonly="true" class="form-control" id="apt_slug" name="apt_slug" placeholder="{{trans('labels.formlblslug')}}" value="{{$apt_slug}}" minlength="6" maxlength="50">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="l1ac_points" class="col-sm-2 control-label">{{trans('labels.apptitudeblheadlogo')}}</label>
                        <div class="col-sm-6">
                            <input type="file" id="apt_logo" name="apt_logo" />
                            <?php  
                                if(isset($apptitudeThumbPath)){ 
                                    $image = ($apptitudeDetail->apt_logo != "" && Storage::disk('s3')->exists($apptitudeThumbPath.$apptitudeDetail->apt_logo)) ? Config::get('constant.DEFAULT_AWS').$apptitudeThumbPath.$apptitudeDetail->apt_logo : asset('/backend/images/proteen_logo.png');  
                                ?>
                                <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                            <?php } ?>
                        </div>
                    </div>

                    <?php
                          if (old('apt_video'))
                              $apt_video = old('apt_video');
                          elseif ($apptitudeDetail)
                              $apt_video = $apptitudeDetail->apt_video;
                          else
                              $apt_video = '';
                         ?>
                        <div class="form-group" id="ap_video">
                            <label for="ap_video" class="col-sm-2 control-label">{{trans('labels.multipleintelligencevideo')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="apt_video" name="ap_video" onblur="validateUrl();" placeholder="{{trans('labels.formlblyoutube')}}" value="{{$apt_video}}" />
                            </div>
                        </div>
                        
                         <?php
                          if (old('ap_information'))
                              $ap_information = old('ap_information');
                          elseif ($apptitudeDetail)
                              $ap_information = $apptitudeDetail->ap_information;
                          else
                              $ap_information = '';
                         ?>
                        <div class="form-group" id="ap_information">
                            <label for="ap_information" class="col-sm-2 control-label">{{trans('labels.frmdeveloperinformation')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="ap_information" name="apt_information" placeholder="{{trans('labels.frmwritedeveloperinformation')}}" value="{{$ap_information}}" />
                            </div>
                        </div>

                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($apptitudeDetail)
                        $deleted = $apptitudeDetail->deleted;
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
                        <button id="submit" type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('developer/apptitudeType') }}">{{trans('labels.cancelbtn')}}</a>
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
                apt_name : {
                    required : true
                },
                apt_slug : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }

        $("#addApptitudeType").validate({
            rules : validationRules,
            messages : {
                apt_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                apt_slug : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });
    
    function validateUrl()
    {        
        $('#submit').prop("disabled", false);
        var url = $('#apt_video').val();
        if (url != undefined || url != '') {        
            var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
            var match = url.match(regExp);
            if (match && match[2].length == 11) {
                // Do anything for being valid
                // if need to change the url to embed url then use below line            
                //$('#videoObject').attr('src', 'https://www.youtube.com/embed/' + match[2] + '?autoplay=1&enablejsapi=1');
            } else {
                alert("Youtube Video Url is not valid..");
                $('#submit').attr('disabled', true);
                
                // Do anything for not being valid
            }
        }              
    }
</script>
<?php if (empty($apptitudeDetail->apt_slug)){ ?>
    <script>
    $('#apt_name').keyup(function ()
    {
        var str = $(this).val();
        str = str.replace(/[^a-zA-Z0-9\s]/g, "");
        str = str.toLowerCase();
        str = str.replace(/\s/g, '-');
        $('#apt_slug').val(str);
    });
    </script>
<?php } ?>
@stop

