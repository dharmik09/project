@extends('layouts.developer-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.multipleintelligencetypes')}}
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
                    <h3 class="box-title"><?php echo (isset($multipleintelligenceDetail) && !empty($multipleintelligenceDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.multipleintelligencetype')}}</h3>
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

                <form id="addMultipleIntelligenceType" class="form-horizontal" method="post" action="{{ url('/developer/saveMultipleintelligenceType') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($multipleintelligenceDetail) && !empty($multipleintelligenceDetail)) ? $multipleintelligenceDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($ci_image) && !empty($mi_image)) ? $mi_image : '' ?>">
                    <div class="box-body">

                    <?php
                    if (old('mit_name'))
                        $mit_name = old('mit_name');
                    elseif ($multipleintelligenceDetail)
                        $mit_name = $multipleintelligenceDetail->mit_name;
                    else
                        $mit_name = '';
                    ?>
                    <div class="form-group">
                        <label for="mit_name" class="col-sm-2 control-label">{{trans('labels.formlblname')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id=mit_name"" name="mit_name" placeholder="{{trans('labels.formlblname')}}" value="{{$mit_name}}" minlength="3" maxlength="50"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="l1ac_points" class="col-sm-2 control-label">{{trans('labels.multipleintelligenceblheadlogo')}}</label>
                        <div class="col-sm-6">
                            <input type="file" id="mit_logo" name="mit_logo" />
                            <?php  
                                if(isset($miThumbPath)) { 
                                    $image = ($multipleintelligenceDetail->mit_logo != "" && Storage::disk('s3')->exists($miThumbPath.$multipleintelligenceDetail->mit_logo)) ? Config::get('constant.DEFAULT_AWS').$miThumbPath.$multipleintelligenceDetail->mit_logo : asset('/backend/images/proteen_logo.png'); ?>
                                    <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                            <?php } ?>
                        </div>
                    </div>

                    <?php
                          if (old('mi_video'))
                              $mi_video = old('mi_video');
                          elseif ($multipleintelligenceDetail)
                              $mi_video = $multipleintelligenceDetail->mi_video;
                          else
                              $mi_video = '';
                         ?>
                        <div class="form-group" id="mi_video">
                            <label for="mi_video" class="col-sm-2 control-label">{{trans('labels.multipleintelligencevideo')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="mit_video" name="mi_video" onblur="validateUrl();" placeholder="{{trans('labels.formlblyoutube')}}" value="{{$mi_video}}" />
                            </div>
                        </div>
                        
                         <?php
                          if (old('mi_information'))
                              $mi_information = old('mi_information');
                          elseif ($multipleintelligenceDetail)
                              $mi_information = $multipleintelligenceDetail->mi_information;
                          else
                              $mi_information = '';
                         ?>
                        <div class="form-group" id="mi_information">
                            <label for="mi_information" class="col-sm-2 control-label">{{trans('labels.frmdeveloperinformation')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="mi_information" name="mit_information" placeholder="{{trans('labels.frmwritedeveloperinformation')}}" value="{{$mi_information}}" />
                            </div>
                        </div>


                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($multipleintelligenceDetail)
                        $deleted = $multipleintelligenceDetail->deleted;
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('developer/multipleintelligenceType') }}">{{trans('labels.cancelbtn')}}</a>
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
                mit_name : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }

        $("#addMultipleIntelligenceType").validate({
            rules : validationRules,
            messages : {
                mit_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        });
    });
    
    function validateUrl()
    {            
      $('#submit').prop("disabled", false);
        var url = $('#mit_video').val();
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
@stop

