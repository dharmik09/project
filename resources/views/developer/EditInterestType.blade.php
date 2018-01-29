@extends('layouts.developer-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.interesttypes')}}
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
                    <h3 class="box-title"><?php echo (isset($interestDetail) && !empty($interestDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.interesttype')}}</h3>
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

                <form id="addInterestType" class="form-horizontal" method="post" action="{{ url('/developer/saveInterestType') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($ci_image) && !empty($it_image)) ? $it_image : '' ?>">
                    <input type="hidden" name="id" value="<?php echo (isset($interestDetail) && !empty($interestDetail)) ? $interestDetail->id : '0' ?>">
                    <div class="box-body">

                    <?php
                    if (old('it_name'))
                        $it_name = old('it_name');
                    elseif ($interestDetail)
                        $it_name = $interestDetail->it_name;
                    else
                        $it_name = '';
                    ?>
                    <div class="form-group">
                        <label for="it_name" class="col-sm-2 control-label">{{trans('labels.formlblname')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="it_name" name="it_name" placeholder="{{trans('labels.formlblname')}}" value="{{$it_name}}" minlength="3" maxlength="50"/>
                        </div>
                    </div>
                    <?php
                    if (old('it_slug'))
                        $it_slug = old('it_slug');
                    elseif ($interestDetail)
                        $it_slug = $interestDetail->it_slug;
                    else
                        $it_slug = '';
                    ?>
                    <div class="form-group">
                        <label for="it_slug" class="col-sm-2 control-label">{{trans('labels.formlblslug')}}</label>
                        <div class="col-sm-6">
                            <input type="text" readonly="true" class="form-control" id="it_slug" name="it_slug" placeholder="{{trans('labels.formlblslug')}}" value="{{$it_slug}}" minlength="6" maxlength="50">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="l1ac_points" class="col-sm-2 control-label">{{trans('labels.interestblheadlogo')}}</label>
                        <div class="col-sm-6">
                            <input type="file" id="it_logo" name="it_logo" />
                            <?php  
                                if(isset($interestThumbPath)) {
                                    $image = ($interestDetail->it_logo != "" && Storage::disk('s3')->exists($interestThumbPath.$interestDetail->it_logo)) ? Config::get('constant.DEFAULT_AWS').$interestThumbPath.$interestDetail->it_logo : asset('/backend/images/proteen_logo.png'); ?>        
                                <img src="{{$image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                    if (old('it_description'))
                        $it_description = old('it_description');
                    elseif ($interestDetail)
                        $it_description = $interestDetail->it_description;
                    else
                        $it_description = '';
                    ?>
                    <div class="form-group" id="it_description">
                        <label for="it_description" class="col-sm-2 control-label">{{trans('labels.frmitdescription')}}</label>
                        <div class="col-sm-6">
                            <textarea class="form-control" id="it_description" name="it_description" rows="8">{{$it_description}}</textarea>
                        </div>
                    </div>
                    <?php
                    if (old('it_video'))
                        $it_video = old('it_video');
                    elseif ($interestDetail)
                        $it_video = $interestDetail->it_video;
                    else
                        $it_video = '';
                    ?>
                    <div class="form-group">
                        <label for="it_video" class="col-sm-2 control-label">{{trans('labels.formlblvideo')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="it_video" name="it_video" placeholder="{{trans('labels.formlblvideo')}}" value="{{$it_video}}" minlength="3" maxlength="50"/>
                        </div>
                    </div>
                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($interestDetail)
                        $deleted = $interestDetail->deleted;
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
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('developer/interestType') }}">{{trans('labels.cancelbtn')}}</a>
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
                it_name : {
                    required : true
                },
                it_slug : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }

        $("#addInterestType").validate({
            rules : validationRules,
            messages : {
                it_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                it_slug : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });
</script>
<?php if (empty($interestDetail->it_slug)){ ?>
    <script>
    $('#it_name').keyup(function ()
    {
        var str = $(this).val();
        str = str.replace(/[^a-zA-Z0-9\s]/g, "");
        str = str.toLowerCase();
        str = str.replace(/\s/g, '_');
        $('#it_slug').val("it_"+str);
    });
    </script>
<?php } ?>
@stop

