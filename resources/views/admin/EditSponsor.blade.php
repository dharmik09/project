@extends('layouts.admin-master')

@section('content')
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.sponsors')}}
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
                    <h3 class="box-title"><?php echo (isset($sponsorDetail) && !empty($sponsorDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.sponsor')}}</h3>
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
                <form id="addSponsor" class="form-horizontal" method="post" action="{{ url('/admin/save-sponsor') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($sponsorDetail) && !empty($sponsorDetail)) ? $sponsorDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($sponsorDetail) && !empty($sponsorDetail)) ? $sponsorDetail->sp_logo : '' ?>">
                    <input type="hidden" name="hidden_photo" value="<?php echo (isset($sponsorDetail) && !empty($sponsorDetail)) ? $sponsorDetail->sp_photo : '' ?>">
                    <input type="hidden" name="hidden_password" value="<?php echo (isset($sponsorDetail) && !empty($sponsorDetail)) ? $sponsorDetail->password : '' ?>">
                    <div class="box-body">
                        <?php
                        if (old('sp_company_name'))
                            $sp_company_name = old('sp_company_name');
                        elseif ($sponsorDetail)
                            $sp_company_name = $sponsorDetail->sp_company_name;
                        else
                            $sp_company_name = '';
                        ?>
                       <div class="form-group">
                            <label for="sp_company_name" class="col-sm-2 control-label">{{trans('labels.formlblcompanyname')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id=sp_company_name"" name="sp_company_name" placeholder="{{trans('labels.formlblcompanyname')}}" value="{{$sp_company_name}}" minlength="5" maxlength="50"/>
                            </div>
                        </div>
                        <?php
                        if (old('sp_email'))
                            $sp_email = old('sp_email');
                        elseif ($sponsorDetail)
                            $sp_email = $sponsorDetail->sp_email;
                        else
                            $sp_email = '';
                        ?>
                        <div class="form-group">
                            <label for="sp_email" class="col-sm-2 control-label">{{trans('labels.formlblemail')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="sp_email" name="sp_email" placeholder="{{trans('labels.formlblemail')}}" value="{{$sp_email}}" minlength="5" maxlength="50"/>
                            </div>
                        </div>
                        <?php
                        if (old('sp_admin_name'))
                            $sp_admin_name = old('sp_admin_name');
                        elseif ($sponsorDetail)
                            $sp_admin_name = $sponsorDetail->sp_admin_name;
                        else
                            $sp_admin_name = '';
                        ?>
                        <div class="form-group">
                            <label for="sp_admin_name" class="col-sm-2 control-label">{{trans('labels.formlbladminname')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="sp_admin_name" name="sp_admin_name" placeholder="{{trans('labels.formlbladminname')}}" value="{{$sp_admin_name}}" minlength="5" maxlength="50"/>
                            </div>
                        </div>
                        <?php
                        if (old('sp_uniqueid'))
                            $sp_uniqueid = old('sp_uniqueid');
                        elseif ($sponsorDetail)
                            $sp_uniqueid = $sponsorDetail->sp_uniqueid;
                        else
                            $sp_uniqueid = '';
                        $style = '';
                        if ($sp_uniqueid != "") {
                            $style = 'style="display:none;"';
                        }
                        ?>
                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlbluniqueid')}}</label>
                            <div class="col-sm-6">
                                <input type="text" readonly="true" class="form-control" id="sp_uniqueid" name="sp_uniqueid" placeholder="{{trans('labels.formlbluniqueid')}}" value="{{$sp_uniqueid}}" minlength="23" maxlength="23"/>
                            </div>
                            <div class="col-sm-2" <?php echo $style; ?>>
                                <a class="btn btn-success" href="#" id="sp_uniqueid_generate" name="sp_uniqueid_generate" >{{trans('labels.generatebtn')}}</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-sm-2 control-label">{{trans('labels.formlblpassword')}}</label>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" id="password" name="password" placeholder="{{trans('labels.formlblpassword')}}" value="" minlength="6" maxlength="25"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password" class="col-sm-2 control-label">{{trans('labels.formlblconfirmpassword')}}</label>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="{{trans('labels.formlblconfirmpassword')}}" value="" minlength="6" maxlength="25"/>
                            </div>
                        </div>
                        <?php
                            if (old('sp_address1'))
                            $sp_address1 = old('sp_address1');
                            elseif ($sponsorDetail)
                            $sp_address1 = $sponsorDetail->sp_address1;
                            else
                            $sp_address1 = '';
                        ?>
                        <div class="form-group">
                            <label for="address1" class="col-sm-2 control-label">{{trans('labels.formlbladdress1')}}</label>
                            <div class="col-sm-6">
                                <input type="text" name="sp_address1" class="form-control" maxlength="100" placeholder="Address-1" value="{{ $sp_address1 or ''}}">
                            </div>
                        </div>
                        <?php
                            if (old('sp_address2'))
                                $sp_address2 = old('sp_address2');
                            elseif ($sponsorDetail)
                                $sp_address2 = $sponsorDetail->sp_address2;
                            else
                                $sp_address2 = '';
                        ?>
                        <div class="form-group">
                            <label for="address2" class="col-sm-2 control-label">{{trans('labels.formlbladdress2')}}</label>
                            <div class="col-sm-6">
                                <input type="text" name="sp_address2" class="form-control" maxlength="100" placeholder="Address-2" value="{{ $sp_address2 or ''}}">
                            </div>
                        </div>
                        <?php
                            if (old('sp_pincode'))
                                $sp_pincode = old('sp_pincode');
                            elseif ($sponsorDetail)
                                $sp_pincode = $sponsorDetail->sp_pincode;
                            else
                                $sp_pincode = '';
                        ?>
                            <div class="form-group">
                                <label for="pincode" class="col-sm-2 control-label">{{trans('labels.formlblpincode')}}</label>
                              <div class="col-sm-6">
                                <input type="text" name="sp_pincode" class="form-control onlyNumber" maxlength="6" minlength="6" placeholder="Pincode"  value="{{ $sp_pincode  or ''}}">
                              </div>
                            </div>
                            <!-- country start -->
                            <?php
                            if (old('sp_country'))
                                $sp_country = old('sp_country');
                            elseif ($sponsorDetail)
                                $sp_country = $sponsorDetail->sp_country;
                            else
                                $sp_country = '';
                            ?>
                            <div class="form-group">
                                <label for="sc_country" class="col-sm-2 control-label">{{trans('labels.formlblselectcountry')}}</label>
                                <div class="col-sm-6">
                                    <?php $countries = Helpers::getCountries();?>
                                    <select class="form-control" id="sp_country" name="sp_country" onchange="getDataOfState(this.value)" >
                                        <option value="">{{trans('labels.formlblselectcountry')}}</option>
                                        <?php foreach ($countries as $key => $value) { ?>
                                            <option value="{{$value->id}}" <?php if($sp_country == $value->id) echo 'selected'; ?> >{{$value->c_name}}</option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <!-- state start -->
                            <?php
                            if (old('sp_state')){
                                $sp_state = old('sp_state');
                            $db_state = old('sp_state');}
                            elseif ($sponsorDetail){
                                $sp_state = $sponsorDetail->sp_country;
                            $db_state = $sponsorDetail->sp_state;}
                            else{
                                $sp_state = '';
                                $db_state = '';
                            }
                            ?>
                            <div class="form-group">
                                <label for="sc_country" class="col-sm-2 control-label">{{trans('labels.formlblstate')}}</label>
                                <div class="col-sm-6">
                                    <select class="form-control" id="sp_state" name="sp_state" onchange="getDataOfCity(this.value)" >
                                        <option value="">{{trans('labels.formlblstate')}}</option>
                                        <?php foreach ($states as $key => $state_value){?>
                                            <option value="{{$state_value->id}}" <?php if ($db_state == $state_value->id) echo 'selected'; ?>>{{$state_value->s_name}}</option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <!-- state end -->
                            <!-- city start-->
                            <?php
                            if (old('sp_city')){
                                $sp_city = old('sp_city');
                                $db_city = old('sp_city');}
                            elseif ($sponsorDetail){
                                $sp_city = $sponsorDetail->sp_state;
                                $db_city = $sponsorDetail->sp_city;
                            }
                            else{
                                $sp_city = '';
                                $db_city = '';
                            }
                            ?>
                            <div class="form-group">
                                <label for="sc_city" class="col-sm-2 control-label">{{trans('labels.formlblcity')}}</label>
                                <div class="col-sm-6">
                                    <select class="form-control" id="sp_city" name="sp_city">
                                        <option value="">{{trans('labels.formlblcity')}}</option>
                                        <?php foreach($cities as $key => $city_value) {?>
                                        <option value="{{$city_value->id}}" <?php if ($db_city == $city_value->id) echo 'selected'; ?>>{{$city_value->c_name}}</option>
                                    <?php }?>
                                    </select>
                                </div>
                            </div>
                            <!-- city end -->
                         <?php
                        if (old('sp_credit'))
                            $sp_credit = old('sp_credit');
                        elseif ($sponsorDetail)
                            $sp_credit = $sponsorDetail->sp_credit;
                        else
                            $sp_credit = '';
                        ?>
                        <div class="form-group">
                            <label for="sp_credit" class="col-sm-2 control-label">{{trans('labels.formlblcredit')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control onlyNumber" id="sp_credit" name="sp_credit" placeholder="{{trans('labels.formlblcredit')}}" value="{{$sp_credit}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="sp_logo" class="col-sm-2 control-label">{{trans('labels.formlbllogo')}}</label>
                            <div class="col-sm-2">
                                <input type="file" id="sp_logo" name="sp_logo" onchange="readURL(this);"/>
                                @if(isset($sponsorDetail->id) && $sponsorDetail->id != '0')
                                    <?php
                                        $sp_logo_image = ($sponsorDetail->sp_logo != "" && Storage::disk('s3')->exists($uploadSponsorThumbPath . $sponsorDetail->sp_logo) ) ? Config::get('constant.DEFAULT_AWS') . $uploadSponsorThumbPath . $sponsorDetail->sp_logo : asset('/backend/images/proteen_logo.png');
                                    ?>
                                    <img src="{{$sp_logo_image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>" />
                                @endif
                            </div>
                            <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                        </div>
                        <div class="form-group">
                            <label for="sp_logo" class="col-sm-2 control-label">{{trans('labels.formlblphoto')}}</label>
                            <div class="col-sm-2">
                                <input type="file" id="sp_photo" name="sp_photo" onchange="readURL(this);"/>
                                @if(isset($sponsorDetail->id) && $sponsorDetail->id != '0')
                                    <?php
                                        $sp_photo_image = ($sponsorDetail->sp_photo != "" && Storage::disk('s3')->exists($uploadSponsorPhotoThumbPath . $sponsorDetail->sp_photo) ) ? Config::get('constant.DEFAULT_AWS') . $uploadSponsorPhotoThumbPath . $sponsorDetail->sp_photo : asset('/backend/images/proteen_logo.png');
                                    ?>
                                    <img src="{{$sp_photo_image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>" />
                                @endif
                            </div>
                            <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                        </div>
                        <?php
                            if (old('sp_first_name'))
                                $sp_first_name = old('sp_first_name');
                            elseif ($sponsorDetail)
                                $sp_first_name = $sponsorDetail->sp_first_name;
                            else
                                $first_name = '';
                        ?>
                            <div class="form-group">
                                <label for="sp_credit" class="col-sm-2 control-label">{{trans('labels.formlblfirst')}}</label>
                                <div class="col-sm-6">
                                    <input type="text" name="sp_first_name" class="form-control" maxlength="100" placeholder="First name" value="{{ $sp_first_name or ''}}">
                                </div>
                            </div>
                            </div>
                         <?php
                            if (old('sp_last_name'))
                                $sp_last_name = old('sp_last_name');
                            elseif ($sponsorDetail)
                                $sp_last_name = $sponsorDetail->sp_last_name;
                            else
                                $sp_last_name = '';
                         ?>
                         <div class="form-group">
                            <label for="sp_credit" class="col-sm-2 control-label">{{trans('labels.formlbllast')}}</label>
                                <div class="col-sm-6">
                                    <input type="text" name="sp_last_name" class="form-control" maxlength="100" placeholder="Last name" value="{{ $sp_last_name or ''}}">
                                </div>
                         </div>
                        <?php
                            if (old('sp_title'))
                                $sp_title = old('sp_title');
                            elseif ($sponsorDetail)
                                $sp_title = $sponsorDetail->sp_title;
                            else
                                $sp_title = '';
                        ?>
                        <div class="form-group">
                            <label for="sp_credit" class="col-sm-2 control-label">{{trans('labels.formlbltitle')}}</label>
                                <div class="col-sm-6">
                                    <input type="text" name="sp_title" class="form-control" maxlength="50" placeholder="Title" value="{{ $sp_title or ''}}">
                                </div>
                        </div>
                        <?php
                            if (old('sp_phone'))
                                $sp_phone = old('sp_phone');
                            elseif ($sponsorDetail)
                                $sp_phone = $sponsorDetail->sp_phone;
                            else
                                $phone = '';
                        ?>
                        <div class="form-group">
                            <label for="sp_credit" class="col-sm-2 control-label">{{trans('labels.formlblphone')}}</label>
                                <div class="col-sm-6">
                                    <input type="text" name="sp_phone" class="form-control onlyNumber" placeholder="Mobile Number" minlength="10" maxlength="11" value="{{ $sp_phone or ''}}">
                                </div>
                        </div>
                        <?php
                        if (old('sp_isapproved'))
                            $sp_isapproved = old('sp_isapproved');
                        elseif ($sponsorDetail)
                            $sp_isapproved = $sponsorDetail->sp_isapproved;
                        else
                            $sp_isapproved = '';
                        ?>
                        <div class="form-group">
                            <label for="sp_isapproved" class="col-sm-2 control-label">{{trans('labels.formlblapproved')}}</label>
                            <div class="col-sm-6">
                                <input type="checkbox" value="1" name="sp_isapproved" id="sp_isapproved" <?php if($sp_isapproved){ echo 'checked="cehcked"';} ?>/>
                            </div>
                        </div>

                        <?php
                        if (old('deleted'))
                            $deleted = old('deleted');
                        elseif ($sponsorDetail)
                            $deleted = $sponsorDetail->deleted;
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
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/sponsors') }}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->
@stop
@section('script')
<script type="text/javascript">
    $('.onlyNumber').on('keyup', function() {
        this.value = this.value.replace(/[^0-9]/gi, '');
    });
    jQuery(document).ready(function() {
        <?php if(isset($sponsorDetail->id) && $sponsorDetail->id != '0') { ?>
            var validationRules = {
                sp_company_name : {
                    required : true
                },
                sp_email : {
                    required : true,
                    email : true
                },
                sp_admin_name : {
                    required : true
                },
                confirm_password : {
                    equalTo : '#password'
                },
                sp_address1 : {
                    required : true,
                },
                sp_address2 : {
                    required : true,
                },
                sp_city : {
                    required : true,
                },
                sp_state : {
                    required : true,
                },
                sp_country : {
                    required : true,
                },
                sp_pincode : {
                    required : true,
                },
                sp_credit : {
                    digits : true
                },
                sp_first_name: {
                    required : true,
                },
                sp_last_name : {
                    required : true,
                },
                sp_title : {
                    required : true,
                },
                sp_phone : {
                    required : true,
                },
                deleted : {
                    required : true
                }
            }
        <?php } else { ?>
            var validationRules = {
                sp_company_name : {
                    required : true
                },
                sp_email : {
                    required : true,
                    email : true
                },
                sp_admin_name : {
                    required : true
                },
                password : {
                    required : true
                },
                confirm_password : {
                    required : true,
                    equalTo : '#password'
                },
                sp_address1 : {
                    required : true,
                },
                sp_address2 : {
                    required : true,
                },
                sp_city : {
                    required : true,
                },
                sp_state : {
                    required : true,
                },
                sp_country : {
                    required : true,
                },
                sp_pincode : {
                    required : true,
                },
                sp_credit : {
                    digits : true
                },
                sp_first_name: {
                    required : true,
                },
                sp_last_name : {
                    required : true,
                },
                sp_title : {
                    required : true,
                },
                sp_phone : {
                    required : true,
                },
                deleted : {
                    required : true
                }
            }
        <?php } ?>
        $("#addSponsor").validate({
            rules : validationRules,
            messages : {
                sp_company_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                sp_email : {
                    required : "<?php echo trans('validation.requiredfield'); ?>",
                    email : "<?php echo trans('validation.validemail'); ?>"
                },
                sp_admin_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                password : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                },
                confirm_password : {
                    required : "<?php echo trans('validation.requiredfield'); ?>",
                    equalTo : "<?php echo trans('validation.passwordnotmatch'); ?>"
                },
                sp_address1 : {
                    required : "<?php echo trans('validation.requiredfield'); ?>",
                },
                sp_address2 : {
                    required : "<?php echo trans('validation.requiredfield'); ?>",
                },
                sp_city : {
                    required : "<?php echo trans('validation.requiredfield'); ?>",
                },
                sp_state : {
                    required : "<?php echo trans('validation.requiredfield'); ?>",
                },
                sp_country : {
                    required : "<?php echo trans('validation.requiredfield'); ?>",
                },
                sp_pincode : {
                    required : "<?php echo trans('validation.requiredfield'); ?>",
                },
                sp_credit : {
                    digits : "<?php echo trans('validation.digitsonly'); ?>"
                },
                sp_first_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>",
                },
                sp_last_name : {
                    required : "<?php echo trans('validation.requiredfield'); ?>",
                },
                sp_title : {
                    required : "<?php echo trans('validation.requiredfield'); ?>",
                },
                sp_phone : {
                    required : "<?php echo trans('validation.requiredfield'); ?>",
                },
                deleted : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });
function getDataOfState(countryId)
{
    $("#sp_state").empty();
    $("#sp_city").empty();
    $.ajax({
        type: 'GET',
        url: '/get-state/' + countryId,
        dataType: "JSON",
        success: function(JSON){
            $("#sp_state").empty()
            for(var i=0;i<JSON.length;i++){
                $("#sp_state").append($("<option></option>").val(JSON[i].id).html(JSON[i].s_name))
            }
        }
    });
}
function getDataOfCity(stateId)
{
       $.ajax({
        type: 'GET',
        url: '/get-city/' + stateId,
        dataType: "JSON",
        success: function(JSON){
            $("#sp_city").empty();
            for(var i=0;i<JSON.length;i++){
                $("#sp_city").append($("<option></option>").val(JSON[i].id).html(JSON[i].c_name));
            }
        }
    });
}
$("#sp_uniqueid_generate").click(function() {
    $.ajax({
        url: "{{ url('/admin/get-uniqueid') }}",
        type: 'post',
        data: {
            "_token": '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#sp_uniqueid').val(response);
        }
    });
});
</script>
@stop