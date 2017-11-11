@extends('layouts.admin-master')

@section('content')

<section class="content-header">
    <h1>
        {{trans('labels.coupons')}}
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
                    <h3 class="box-title"><?php echo (isset($couponDetail) && !empty($couponDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.coupon')}}</h3>
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

                <form id="addCoupon" class="form-horizontal" method="post" action="{{ url('/admin/save-coupon') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($couponDetail) && !empty($couponDetail)) ? $couponDetail->id : '0' ?>">
                     <input type="hidden" name="hidden_logo" value="<?php echo (isset($couponDetail) && !empty($couponDetail)) ? $couponDetail->cp_image : '' ?>">
                    <div class="box-body">
                    <?php
                    if (old('cp_code'))
                        $cp_code = old('cp_code');
                    elseif ($couponDetail)
                        $cp_code = $couponDetail->cp_code;
                    else
                        $cp_code = '';
                    ?>
                    <div class="form-group">
                        <label for="cp_code" class="col-sm-2 control-label">{{trans('labels.formlblcode')}}</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="cp_code" name="cp_code" placeholder="{{trans('labels.formlblcode')}}" value="{{$cp_code}}" minlength="5" maxlength="50"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cp_image" class="col-sm-2 control-label">{{trans('labels.formlbllogo')}}</label>
                        <div class="col-sm-2">
                            <input type="file" id="cp_image" name="cp_image" onchange="readURL(this);"/>
                            @if(isset($couponDetail->id) && $couponDetail->id != '0')
                                <?php
                                    $image_data = ($couponDetail->cp_image != "" && Storage::disk('s3')->exists($uploadCouponThumbPath.$couponDetail->cp_image) ) ? Config::get('constant.DEFAULT_AWS').$uploadCouponThumbPath.$couponDetail->cp_image : asset('/backend/images/proteen_logo.png');
                                ?>
                                <img src="{{$image_data}}" class="user-image" height="<?php echo Config::get('constant.COUPON_THUMB_IMAGE_HEIGHT');?>" width="<?php echo Config::get('constant.COUPON_THUMB_IMAGE_WIDTH');?>">
                            @endif
                        </div>
                        <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                    </div>
                    <?php
                    if (old('cp_sponsor'))
                        $cp_sponsor = old('cp_sponsor');
                    elseif ($couponDetail)
                        $cp_sponsor = $couponDetail->cp_sponsor;
                    else
                        $cp_sponsor = '';
                    ?>
                    <div class="form-group">
                        <label for="cp_sponsor" class="col-sm-2 control-label">{{trans('labels.formlblsponsor')}}</label>
                        <div class="col-sm-6">
                            <?php $sponsors = Helpers::getActiveSponsors();?>
                            <select class="form-control" id="cp_sponsor" name="cp_sponsor">
                                    <option value="">{{trans('labels.formlblselectsponsor')}}</option>
                                     <?php foreach ($sponsors as $key => $value) {
                                        ?>
                                            <option value="{{$value->id}}" <?php if($cp_sponsor == $value->id) echo 'selected'; ?>>{{$value->sp_company_name}}</option>
                                        <?php
                                        }
                                    ?>
                            </select>
                        </div>
                    </div>
                    <?php
                    if (old('cp_validfrom'))
                        $cp_validfrom = old('cp_validfrom');
                    elseif ($couponDetail)
                        $cp_validfrom = date('d/m/Y', strtotime($couponDetail->cp_validfrom));
                    else
                        $cp_validfrom = '';
                    ?>
                    <div class="form-group">
                            <label for="cp_validfrom" class="col-sm-2 control-label">{{trans('labels.formlblvalidfrom')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="cp_validfrom" name="cp_validfrom" placeholder="{{trans('labels.formlblvalidfrom')}}" value="{{$cp_validfrom}}" minlength="10" maxlength="10"/>
                            </div>
                    </div>
                    <?php
                    if (old('cp_validto'))
                        $cp_validto = old('cp_validto');
                    elseif ($couponDetail)
                        $cp_validto = date('d/m/Y', strtotime($couponDetail->cp_validto));
                    else
                        $cp_validto = '';
                    ?>
                    <div class="form-group">
                            <label for="cp_validto" class="col-sm-2 control-label">{{trans('labels.formlblvalidto')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="cp_validto" name="cp_validto" placeholder="{{trans('labels.formlblvalidto')}}" value="{{$cp_validto}}" minlength="10" maxlength="10"/>
                            </div>
                    </div>
                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($couponDetail)
                        $deleted = $couponDetail->deleted;
                    else
                        $deleted = '';
                    ?>
                    <div class="form-group">
                            <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                            <div class="col-sm-6">
                                <?php $staus = Helpers::status(); ?>
                                <select class="form-control" id="deleted" name="deleted">
                                    <?php foreach ($staus as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if($deleted == $key) echo 'selected'; ?>>{{$value}}</option>
                                <?php } ?>
                                </select>
                            </div>
                    </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" id="submit" class="btn btn-primary btn-flat" >{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/coupons') }}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->

@stop

@section('script')
<script src="{{ asset('backend/plugins/datepicker/moment-with-locales.js') }}"></script>
<script src="{{ asset('backend/plugins/datepicker/bootstrap-datetimepicker.js') }}"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        <?php if(isset($couponDetail->id) && $couponDetail->id != '0') { ?>
            var validationRules = {                
                cp_code : {
                    required : true,
                },
                cp_sponsor : {
                    required : true
                },
                cp_validfrom : {
                    required : true
                },
                cp_validto : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } else { ?>
            var validationRules = {                
                cp_code : {
                    required : true,
                },
                cp_sponsor : {
                    required : true
                },
                cp_validfrom : {
                    required : true
                },
                cp_validto : {
                    required : true
                },
                deleted : {
                    required : true
                }
            }
        <?php } ?>

        $("#addCoupon").validate({
            rules : validationRules,
            messages : {
                cp_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                cp_code : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                cp_sponsor : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                cp_validfrom : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"                    
                },
                cp_validto : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });

    var CurrentDate = new Date();
    $('#cp_validfrom').datetimepicker({
        format: 'DD/MM/YYYY',
        maxDate: CurrentDate
    });

    var CurrentDate = new Date();
    $('#cp_validto').datetimepicker({
        format: 'DD/MM/YYYY'
    });

</script>
@stop