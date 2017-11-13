@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        {{trans('labels.viewsponsoractivityform')}}
    </h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-body">

                    <?php
                    if (isset($sponsorsActivities) && !empty($sponsorsActivities)) {
                        if (isset($sponsorsActivities->sa_type) && !empty($sponsorsActivities->sa_type)) {
                            $type = Helpers::getActiveSponsorActivity($sponsorsActivities->sa_type);
                            ?>
                            <div class="form-group clearfix">
                                <label for="sa_type" class="col-sm-6 control-label">{{trans('labels.viewsponsoractivitytype')}}</label>
                                <div class="col-sm-4">
                                    {{$type->cfg_key}}
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                        if (isset($sponsorsActivities->sa_name) && !empty($sponsorsActivities->sa_name)) {
                            ?>
                            <div class="form-group clearfix">
                                <label for="sa_name" class="col-sm-6 control-label">{{trans('labels.viewsponsoractivityname')}}</label>
                                <div class="col-sm-6">
                                    {{$sponsorsActivities->sa_name}}
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                        if (isset($sponsorsActivities->sa_apply_level) && !empty($sponsorsActivities->sa_apply_level)) {
                            $level = Helpers::getActiveSponsorActivityLevel($sponsorsActivities->sa_apply_level);
                            ?>
                            <div class="form-group clearfix">
                                <label for="sa_apply_level" class="col-sm-6 control-label">{{trans('labels.viewsponsoractivitylevel')}}</label>
                                <div class="col-sm-6">
                                    {{$level->sl_name}}
                                </div>
                            </div>
                        <?php } ?>


                        <?php if ($sponsorsActivities->sa_location != 0 && !empty($sponsorsActivities->sa_location) && isset($sponsorsActivities->sa_location)) {
                            ?>
                            <div class="form-group clearfix">
                                <label for="sa_location" class="col-sm-6 control-label">{{trans('labels.viewsponsoractivitylocation')}}</label>
                                <div class="col-sm-6">
                                    {{$sponsorsActivities->sa_location}}
                                </div>
                            </div>
                        <?php } ?>


                        <div class="form-group clearfix">
                            <label for="sa_image" class="col-sm-6 control-label">{{trans('labels.formlblphoto')}}</label>
                            <div class="col-sm-6">
                                @if(isset($sponsorsActivities->id) && $sponsorsActivities->id != '0')
                                    <?php 
                                        $image_url = ($sponsorsActivities->sa_image != "" && Storage::disk('s3')->exists($sponsorActivityThumbImageUploadPath . $sponsorsActivities->sa_image) ) ? Config::get('constant.DEFAULT_AWS').$sponsorActivityThumbImageUploadPath.$sponsorsActivities->sa_image : asset('/backend/images/proteen_logo.png');
                                    ?>
                                    <img src="{{$image_url}}" class="user-image" height="<?php echo Config::get('constant.SA_THUMB_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.SA_THUMB_IMAGE_WIDTH'); ?>">
                                @endif
                            </div>
                        </div>

                        <?php
                        if ($sponsorsActivities->sa_credit_used != 0 && !empty($sponsorsActivities->sa_credit_used) && isset($sponsorsActivities->sa_credit_used)) {
                            ?>
                            <div class="form-group clearfix">
                                <label for="sa_credit_used" class="col-sm-6 control-label">{{trans('labels.viewsponsoractivitycredit')}}</label>
                                <div class="col-sm-6">
                                    {{$sponsorsActivities->sa_credit_used}}
                                </div>
                            </div>
                        <?php } ?>


                        <?php
                        if ($sponsorsActivities->sa_start_date != 0 && !empty($sponsorsActivities->sa_start_date) && isset($sponsorsActivities->sa_start_date)) {
                            ?>
                            <div class="form-group clearfix">
                                <label for="sa_start_date" class="col-sm-6 control-label">{{trans('labels.viewactivitystartdate')}}</label>
                                <div class="col-sm-6">
                                    {{$sponsorsActivities->sa_start_date}}
                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($sponsorsActivities->sa_end_date != 0 && !empty($sponsorsActivities->sa_end_date) && isset($sponsorsActivities->sa_end_date)) {
                            ?>
                            <div class="form-group clearfix">
                                <label for="sa_end_date" class="col-sm-6 control-label">{{trans('labels.viewactivityenddate')}}</label>
                                <div class="col-sm-6">
                                    {{$sponsorsActivities->sa_end_date}}
                                </div>
                            </div>


                        <?php
                        }
                    } else {
                        echo "No Records Found..";
                    }
                    ?>
                    <div class="box-footer">
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/sponsor-activity') }}/{{$sponsorsActivities->id}}">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@stop