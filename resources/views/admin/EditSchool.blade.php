@extends('layouts.admin-master')

@section('content')
<section class="content-header">
    <h1>
        {{trans('labels.schools')}}
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
                    <h3 class="box-title"><?php echo (isset($schoolDetail) && !empty($schoolDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.school')}}</h3>
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
                <form id="addSchool" class="form-horizontal" method="post" action="{{ url('/admin/save-school') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($schoolDetail) && !empty($schoolDetail)) ? $schoolDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($schoolDetail) && !empty($schoolDetail)) ? $schoolDetail->sc_logo : '' ?>">
                    <input type="hidden" name="hidden_password" value="<?php echo (isset($schoolDetail) && !empty($schoolDetail)) ? $schoolDetail->password : '' ?>">
                    <div class="box-body">
                        <?php
                        if (old('sc_name'))
                            $sc_name = old('sc_name');
                        elseif ($schoolDetail)
                            $sc_name = $schoolDetail->sc_name;
                        else
                            $sc_name = '';
                        ?>
                        <div class="form-group">
                            <label for="sp_company_name" class="col-sm-2 control-label">{{trans('labels.formlblschoolname')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id=sc_name" name="sc_name" placeholder="{{trans('labels.formlblschoolname')}}" value="{{$sc_name}}" minlength="5" maxlength="255"/>
                            </div>
                        </div>
                        <?php
                        if (old('sc_email'))
                            $sc_email = old('sc_email');
                        elseif ($schoolDetail)
                            $sc_email = $schoolDetail->sc_email;
                        else
                            $sc_email = '';
                        ?>
                        <div class="form-group">
                            <label for="sp_email" class="col-sm-2 control-label">{{trans('labels.formlblemail')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="sc_email" name="sc_email" placeholder="{{trans('labels.formlblemail')}}" value="{{$sc_email}}" minlength="5" maxlength="50"/>
                            </div>
                        </div>
                        <?php
                        if (old('sc_uniqueid'))
                            $sc_uniqueid = old('sc_uniqueid');
                        elseif ($schoolDetail)
                            $sc_uniqueid = $schoolDetail->sc_uniqueid;
                        else
                            $sc_uniqueid = '';
                        $style = '';
                        if ($sc_uniqueid != "") {
                            $style = 'style="display:none;"';
                        }
                        ?>
                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlbluniqueid')}}</label>
                            <div class="col-sm-6">
                                <input type="text" readonly="true" class="form-control" id="sc_uniqueid" name="sc_uniqueid" placeholder="{{trans('labels.formlbluniqueid')}}" value="{{$sc_uniqueid}}" minlength="23" maxlength="23"/>
                            </div>
                            <div class="col-sm-2" <?php echo $style; ?>>
                                <a class="btn btn-success" href="#" id="sc_uniqueid_generate" name="sc_uniqueid_generate" >{{trans('labels.generatebtn')}}</a>
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
                        if (old('sc_address1'))
                            $sc_address1 = old('sc_address1');
                        elseif ($schoolDetail)
                            $sc_address1 = $schoolDetail->sc_address1;
                        else
                            $sc_address1 = '';
                        ?>
                        <div class="form-group">
                            <label for="address1" class="col-sm-2 control-label">{{trans('labels.formlbladdress1')}}</label>
                            <div class="col-sm-6">
                                <input type="text" name="sc_address1" class="form-control" maxlength="100" placeholder="Address-1" value="{{ $sc_address1 or ''}}">
                            </div>
                        </div>
                        <?php
                        if (old('sc_address2'))
                            $sc_address2 = old('sc_address2');
                        elseif ($schoolDetail)
                            $sc_address2 = $schoolDetail->sc_address2;
                        else
                            $sc_address2 = '';
                        ?>
                        <div class="form-group">
                            <label for="address2" class="col-sm-2 control-label">{{trans('labels.formlbladdress2')}}</label>
                            <div class="col-sm-6">
                                <input type="text" name="sc_address2" class="form-control" maxlength="100" placeholder="Address-2" value="{{ $sc_address2 or ''}}">
                            </div>
                        </div>
                        <?php
                        if (old('sc_pincode'))
                            $sc_pincode = old('sc_pincode');
                        elseif ($schoolDetail)
                            $sc_pincode = $schoolDetail->sc_pincode;
                        else
                            $sc_pincode = '';
                        ?>
                        <div class="form-group">
                            <label for="pincode" class="col-sm-2 control-label">{{trans('labels.formlblpincode')}}</label>
                            <div class="col-sm-6">
                                <input type="text" name="sc_pincode" class="form-control onlyNumber" maxlength="6" minlength="6" placeholder="Pincode"  value="{{ $sc_pincode  or ''}}">
                            </div>
                        </div>
                        <?php
                        if (old('sc_country'))
                            $sc_country = old('sc_country');
                        elseif ($schoolDetail)
                            $sc_country = $schoolDetail->sc_country;
                        else
                            $sc_country = '';
                        ?>
                        <!-- country start -->
                        <div class="form-group">
                            <label for="sc_country" class="col-sm-2 control-label">{{trans('labels.formlblcountry')}}</label>
                            <div class="col-sm-6">
                                <?php $countries = Helpers::getCountries(); ?>
                                <select class="form-control" id="sc_country" name="sc_country" onchange="getDataOfState(this.value)" >
                                    <option value="">{{trans('labels.formlblcountry')}}</option>
                                    <?php foreach ($countries as $key => $value) { ?>
                                        <option value="{{$value->id}}" <?php if ($sc_country == $value->id) echo 'selected'; ?> >{{$value->c_name}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <!-- country end -->
                        <?php
                        if (old('sc_state'))
                            $sc_state = old('sc_state');
                        elseif ($schoolDetail)
                            $sc_state = $schoolDetail->sc_state;
                        else
                            $sc_state = '';
                        ?>
                        <!-- state start -->
                        <div class="form-group">
                            <label for="sc_country" class="col-sm-2 control-label">{{trans('labels.formlblstate')}}</label>
                            <div class="col-sm-6">
                                <select class="form-control" id="sp_state" name="sc_state" onchange="getDataOfCity(this.value)">
                                    <option value="">{{trans('labels.formlblstate')}}</option>
                                    <?php foreach ($states as $key => $state_value){?>
                                        <option value="{{$state_value->id}}" <?php if ($sc_state == $state_value->id) echo 'selected'; ?>>{{$state_value->s_name}}</option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <!-- state end -->
                        <!-- city start -->
                        <?php
                        if (old('sc_city'))
                            $sc_city = old('sc_city');
                        elseif ($schoolDetail)
                            $sc_city = $schoolDetail->sc_city;
                        else
                            $sc_city = '';
                        ?>
                        <div class="form-group">
                            <label for="sc_country" class="col-sm-2 control-label">{{trans('labels.formlblcity')}}</label>
                            <div class="col-sm-6">
                                <select class="form-control" id="sc_city" name="sc_city">
                                    <option value="">{{trans('labels.formlblcity')}}</option>
                                    <?php foreach($cities as $key => $city_value) {?>
                                        <option value="{{$city_value->id}}" <?php if ($sc_city == $city_value->id) echo 'selected'; ?>>{{$city_value->c_name}}</option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <!-- city end -->
                        <div class="form-group">
                            <label for="sp_logo" class="col-sm-2 control-label">{{trans('labels.formlbllogo')}}</label>
                            <div class="col-sm-2">
                                <input type="file" id="sp_logo" name="sc_logo" onchange="readURL(this);"/>
                                @if(isset($schoolDetail->id) && $schoolDetail->id != '0')
                                    <?php
                                        $school_logo = ($schoolDetail->sc_logo != "" && Storage::disk('s3')->exists($uploadSchoolThumbPath . $schoolDetail->sc_logo) ) ? Config::get('constant.DEFAULT_AWS').$uploadSchoolThumbPath . $schoolDetail->sc_logo : asset('/backend/images/proteen_logo.png');
                                    ?>
                                    <img src="{{ $school_logo }}" class="user-image" alt="Default Image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                @endif
                            </div>
                            <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                        </div>
                        <div class="form-group">
                            <label for="sp_logo" class="col-sm-2 control-label">{{trans('labels.formlblphoto')}}</label>
                            <div class="col-sm-2">
                                <input type="file" id="sc_photo" name="sc_photo" onchange="readURL(this);"/>
                                @if(isset($schoolDetail->id) && $schoolDetail->id != '0')
                                    <?php
                                        $school_photo = ($schoolDetail->sc_photo != "" && Storage::disk('s3')->exists($uploadSchoolPhotoThumbPath . $schoolDetail->sc_photo)) ? Config::get('constant.DEFAULT_AWS').$uploadSchoolPhotoThumbPath . $schoolDetail->sc_photo : asset('/backend/images/proteen_logo.png');
                                    ?>
                                    <img src="{{ $school_photo }}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                @endif
                            </div>
                            <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                        </div>
                        <?php
                        if (old('sc_first_name'))
                            $sc_first_name = old('sc_first_name');
                        elseif ($schoolDetail)
                            $sc_first_name = $schoolDetail->sc_first_name;
                        else
                            $sc_first_name = '';
                        ?>
                        <div class="form-group">
                            <label for="sp_credit" class="col-sm-2 control-label">{{trans('labels.formlblfirst')}}</label>
                            <div class="col-sm-6">
                                <input type="text" name="sc_first_name" class="form-control" maxlength="100" placeholder="First name" value="{{ $sc_first_name or ''}}">
                            </div>
                        </div>
                    </div>
                    <?php
                    if (old('sc_last_name'))
                        $sc_last_name = old('sc_last_name');
                    elseif ($schoolDetail)
                        $sc_last_name = $schoolDetail->sc_last_name;
                    else
                        $sc_last_name = '';
                    ?>
                    <div class="form-group">
                        <label for="sp_credit" class="col-sm-2 control-label">{{trans('labels.formlbllast')}}</label>
                        <div class="col-sm-6">
                            <input type="text" name="sc_last_name" class="form-control" maxlength="100" placeholder="Last name" value="{{ $sc_last_name or ''}}">
                        </div>
                    </div>
                    <?php
                    if (old('sc_title'))
                        $sc_title = old('sc_title');
                    elseif ($schoolDetail)
                        $sc_title = $schoolDetail->sc_title;
                    else
                        $sc_title = '';
                    ?>
                    <div class="form-group">
                        <label for="sp_credit" class="col-sm-2 control-label">{{trans('labels.formlbltitle')}}</label>
                        <div class="col-sm-6">
                            <input type="text" name="sc_title" class="form-control" maxlength="50" placeholder="Title" value="{{ $sc_title or ''}}">
                        </div>
                    </div>
                    <?php
                    if (old('sc_phone'))
                        $sc_phone = old('sc_phone');
                    elseif ($schoolDetail)
                        $sc_phone = $schoolDetail->sc_phone;
                    else
                        $sc_phone = '';
                    ?>
                    <div class="form-group">
                        <label for="sp_credit" class="col-sm-2 control-label">{{trans('labels.formlblphone')}}</label>
                        <div class="col-sm-6">
                            <input type="text" name="sc_phone" class="form-control onlyNumber" placeholder="Mobile Number" minlenght="10" maxlength="11" value="{{ $sc_phone or ''}}">
                        </div>
                    </div>
                    <?php
                    if (old('deleted'))
                        $deleted = old('deleted');
                    elseif ($schoolDetail)
                        $deleted = $schoolDetail->deleted;
                    else
                        $deleted = '';
                    ?>
                    <div class="form-group">
                        <label for="deleted" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
                        <div class="col-sm-6">
                                <?php $staus = Helpers::status(); ?>
                            <select class="form-control" id="deleted" name="deleted">
<?php foreach ($staus as $key => $value) { ?>
                                    <option value="{{$key}}" <?php if ($deleted == $key) echo 'selected'; ?> >{{$value}}</option>
<?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary btn-flat">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/schools') }}">{{trans('labels.cancelbtn')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@stop

@section('script')

<script type="text/javascript">
    $('.onlyNumber').on('keyup', function () {
        this.value = this.value.replace(/[^0-9]/gi, '');
    });
    jQuery(document).ready(function () {
        
<?php if (isset($schoolDetail->id) && $schoolDetail->id != '0') { ?>
            var validationRules = {
                sc_name: {
                    required: true
                },
                sc_email: {
                    required: true,
                    email: true
                },
                sc_address1: {
                    required: true
                },
                sc_address2: {
                    required: true
                },
                sc_pincode: {
                    required: true,
                    digits: true
                },
                sc_city: {
                    required: true
                },
                sc_state: {
                    required: true
                },
                sc_country: {
                    required: true
                },
                confirm_password: {
                    equalTo: '#password'
                },
                sc_phone: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 11
                },
                sc_first_name: {
                    required: true
                },
                sc_last_name: {
                    required: true
                },
                sc_title: {
                    required: true
                },
                deleted: {
                    required: true
                }
            }
<?php } else { ?>
            var validationRules = {
                sc_name: {
                    required: true
                },
                sc_email: {
                    required: true,
                    email: true
                },
                sc_address1: {
                    required: true
                },
                sc_address2: {
                    required: true
                },
                sc_pincode: {
                    required: true,
                    digits: true
                },
                sc_city: {
                    required: true
                },
                sc_state: {
                    required: true
                },
                sc_country: {
                    required: true
                },
                password: {
                    required: true
                },
                confirm_password: {
                    required: true,
                    equalTo: '#password'
                },
                sc_phone: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 11
                },
                sc_first_name: {
                    required: true
                },
                sc_last_name: {
                    required: true
                },
                sc_title: {
                    required: true
                },
                deleted: {
                    required: true
                }
            }
<?php } ?>

        $("#addSchool").validate({
            rules: validationRules,
            messages: {
                sc_name: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                sc_email: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                    email: "<?php echo trans('validation.validemail'); ?>"
                },
                sc_address1: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                },
                sc_address2: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                },
                sc_city: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                },
                sc_state: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                },
                sc_country: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                },
                sc_pincode: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                },
                password: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                confirm_password: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                    equalTo: "<?php echo trans('validation.passwordnotmatch'); ?>"
                },
                sc_phone: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                    digits: "<?php echo trans('validation.digitsonly'); ?>"
                },
                sc_first_name: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                },
                sc_last_name: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                },
                sc_title: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                },
                deleted: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });
    function getDataOfState(countryId)
    {
        $("#sp_state").empty();
        $("#sc_city").empty();
        $.ajax({
            type: 'GET',
            url: '/get-state/' + countryId,
            dataType: "JSON",
            success: function (JSON) {
                $("#sp_state").empty()
                for (var i = 0; i < JSON.length; i++) {
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
            success: function (JSON) {
                $("#sc_city").empty();
                for (var i = 0; i < JSON.length; i++) {
                    $("#sc_city").append($("<option></option>").val(JSON[i].id).html(JSON[i].c_name));
                }
            }
        });
    }
    $("#sc_uniqueid_generate").click(function() {
        $.ajax({
            url: "{{ url('/admin/get-uniqueid') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#sc_uniqueid').val(response);
            }
        });
    });
</script>
@stop


