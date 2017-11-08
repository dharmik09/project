@extends('layouts.admin-master')

@section('content')
<section class="content-header">
    <h1>
        {{trans('labels.parents')}}
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo (isset($parentDetail) && !empty($parentDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.parent')}}</h3>
                </div>
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

                <form id="addParent" class="form-horizontal" method="post" action="{{ url('/admin/save-parent') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($parentDetail) && !empty($parentDetail)) ? $parentDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_password" value="<?php echo (isset($parentDetail) && !empty($parentDetail)) ? $parentDetail->password : '0' ?>">
                    <div class="box-body">
                        <?php
                        if (old('p_first_name'))
                            $p_first_name = old('p_first_name');
                        elseif ($parentDetail)
                            $p_first_name = $parentDetail->p_first_name;
                        else
                            $p_first_name = '';
                        ?>
                        <div class="form-group">
                            <label for="p_first_name" class="col-sm-2 control-label">{{trans('labels.formlblfirst')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="p_first_name" name="p_first_name" placeholder="First Name" value="{{$p_first_name}}" minlength="3" maxlength="30"/>
                            </div>
                        </div>
                        <?php
                        if (old('p_last_name'))
                            $p_last_name = old('p_last_name');
                        elseif ($parentDetail)
                            $p_last_name = $parentDetail->p_last_name;
                        else
                            $p_last_name = '';
                        ?>
                        <div class="form-group">
                            <label for="p_last_name" class="col-sm-2 control-label">{{trans('labels.formlbllast')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="p_last_name" name="p_last_name" placeholder="Last Name" value="{{$p_last_name}}" minlength="3" maxlength="30"/>
                            </div>
                        </div>

                        <?php
                        if (old('p_uniqueid'))
                            $p_uniqueid = old('p_uniqueid');
                        elseif ($parentDetail)
                            $p_uniqueid = $parentDetail->p_uniqueid;
                        else
                            $p_uniqueid = '';
                        $style = '';
                        if ($p_uniqueid != "") {
                            $style = 'style="display:none;"';
                        }
                        ?>
                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlbluniqueid')}}</label>
                            <div class="col-sm-6">
                                <input type="text" readonly="true" class="form-control" id="p_uniqueid" name="p_uniqueid" placeholder="{{trans('labels.formlbluniqueid')}}" value="{{$p_uniqueid}}" minlength="23" maxlength="23"/>
                            </div>
                            <div class="col-sm-2" <?php echo $style; ?>>
                                <a class="btn btn-success" href="#" id="p_uniqueid_generate" name="p_uniqueid_generate" >{{trans('labels.generatebtn')}}</a>
                            </div>
                        </div>

                        <?php
                        if (old('p_address1'))
                            $p_address1 = old('p_address1');
                        elseif ($parentDetail)
                            $p_address1 = $parentDetail->p_address1;
                        else
                            $p_address1 = '';
                        ?>
                        <div class="form-group">
                            <label for="p_address1" class="col-sm-2 control-label">{{trans('labels.formlbladdress1')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="p_address1" name="p_address1" placeholder="Address 1" value="{{$p_address1}}" minlength="3" maxlength="30"/>
                            </div>
                        </div>
                        <?php
                        if (old('p_address2'))
                            $p_address2 = old('p_address2');
                        elseif ($parentDetail)
                            $p_address2 = $parentDetail->p_address2;
                        else
                            $p_address2 = '';
                        ?>
                        <div class="form-group">
                            <label for="p_address2" class="col-sm-2 control-label">{{trans('labels.formlbladdress2')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="p_address2" name="p_address2" placeholder="Address 2" value="{{$p_address2}}" minlength="3" maxlength="30"/>
                            </div>
                        </div>
                        <?php
                        if (old('p_pincode'))
                            $p_pincode = old('p_pincode');
                        elseif ($parentDetail)
                            $p_pincode = $parentDetail->p_pincode;
                        else
                            $p_pincode = '';
                        ?>
                        <div class="form-group">
                            <label for="p_pincode" class="col-sm-2 control-label">{{trans('labels.formlblpincode')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control onlyNumber" id="p_pincode" name="p_pincode" placeholder="Pincode" value="{{$p_pincode}}" minlength="6" maxlength="6"/>
                            </div>
                        </div>
                        <?php
                        if (old('p_country'))
                            $p_country = old('p_country');
                        elseif ($parentDetail)
                            $p_country = $parentDetail->p_country;
                        else
                            $p_country = '';
                        ?>
                        <div class="form-group">
                            <label for="sc_country" class="col-sm-2 control-label">{{trans('labels.formlblcountry')}}</label>
                            <div class="col-sm-6">
                                <?php $countries = Helpers::getCountries(); ?>
                                <select class="form-control" id="p_country" name="p_country" onchange="getDataOfState(this.value)">
                                    <option value="">{{trans('labels.formlblcountry')}}</option>
                                    <?php foreach ($countries as $key => $value) { ?>
                                        <option value="{{$value->id}}" <?php if ($p_country == $value->id) echo "selected"; ?> >{{$value->c_name}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php
                        if (old('p_state'))
                            $p_state = old('p_state');
                        elseif ($parentDetail)
                            $p_state = $parentDetail->p_state;
                        else
                            $p_state = '';
                        ?>
                        <div class="form-group">
                            <label for="p_country" class="col-sm-2 control-label">{{trans('labels.formlblstate')}}</label>
                            <div class="col-sm-6">
                                <select class="form-control" id="p_state" name="p_state" onchange="getDataOfCity(this.value)" onclick="getDataOfCity(this.value)">
                                    <option value="">{{trans('labels.formlblstate')}}</option>
                                    <?php foreach ($states as $key => $state_value){?>
                                        <option value="{{$state_value->id}}" <?php if ($p_state == $state_value->id) echo 'selected'; ?>>{{$state_value->s_name}}</option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <?php
                        if (old('p_city'))
                            $p_city = old('p_city');
                        elseif ($parentDetail)
                            $p_city = $parentDetail->p_city;
                        else
                            $p_city = '';
                        ?>
                        <div class="form-group">
                            <label for="p_city" class="col-sm-2 control-label">{{trans('labels.formlblcity')}}</label>
                            <div class="col-sm-6">
                                <select class="form-control" id="p_city" name="p_city">
                                    <option value="">{{trans('labels.formlblcity')}}</option>
                                    <?php foreach($cities as $key => $city_value) {?>
                                        <option value="{{$city_value->id}}" <?php if ($p_city == $city_value->id) echo 'selected'; ?>>{{$city_value->c_name}}</option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <?php
                        if (old('p_gender'))
                            $p_gender = old('p_gender');
                        elseif ($parentDetail)
                            $p_gender = $parentDetail->p_gender;
                        else
                            $p_gender = '';
                        ?>
                        <div class="form-group">
                            <label for="p_gender" class="col-sm-2 control-label">{{trans('labels.formlblgender')}}</label>
                            <div class="col-sm-6">
                                <?php $gender = Helpers::gender(); ?>
                                <select class="form-control" id="p_gender" name="p_gender">
                                    <option value="">{{trans('labels.formlblselectgender')}}</option>
                                    <?php foreach ($gender as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if ($p_gender == $key) echo 'selected'; ?> >{{$value}}</option>
                                    <?php } ?>

                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="p_photo" class="col-sm-2 control-label">{{trans('labels.formlblphoto')}}</label>
                            <div class="col-sm-2">
                                <input type="file" id="p_photo" name="p_photo" onchange="readURL(this);"/>
                                @if(isset($parentDetail->id) && $parentDetail->id != '0')
                                    <?php
                                        $parent_image = ($parentDetail->p_photo != "") ? Config::get('constant.DEFAULT_AWS').$uploadParentThumbPath.$parentDetail->p_photo : asset('/backend/images/proteen_logo.png');
                                    ?>
                                    <img src="{{$parent_image}}" class="user-image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                @endif
                            </div>
                            <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                        </div>
                        <?php
                        if (old('p_email'))
                            $p_email = old('p_email');
                        elseif ($parentDetail)
                            $p_email = $parentDetail->p_email;
                        else
                            $p_email = '';
                        ?>
                        <div class="form-group">
                            <label for="p_email" class="col-sm-2 control-label">{{trans('labels.formlblemail')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="p_email" name="p_email" placeholder="{{trans('labels.formlblemail')}}" value="{{$p_email}}" minlength="6" maxlength="100"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password" class="col-sm-2 control-label">{{trans('labels.formlblpassword')}}</label>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" id="password" name="password" placeholder="{{trans('labels.formlblpassword')}}" value="" minlength="6" maxlength="20"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password" class="col-sm-2 control-label">{{trans('labels.formlblconfirmpassword')}}</label>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="{{trans('labels.formlblconfirmpassword')}}" value="" minlength="6" maxlength="20"/>
                            </div>
                        </div>
                        <?php
                        if (old('p_teenager_id'))
                            $p_teenager_id = old('p_teenager_id');
                        elseif (isset($tokenDetail) && isset($parentDetail))
                        { ?>
                            @forelse($tokenDetail as $tokenDetails)
                                <?php $teen_unique_id[] = $tokenDetails->t_uniqueid; ?>
                            @empty
                                <?php $teen_unique_id = array(); ?> 
                            @endforelse
                            <?php $finalTeenId = implode(", ", $teen_unique_id);
                        }
                        else
                            $p_teenager_id = '';
                        ?>
                        
                        <div class="form-group">
                            <label for="p_teenager_id" class="col-sm-2 control-label">{{trans('labels.formlblteenagerid')}}</label>
                            <div class="col-sm-6">
                                
                                @if(isset($tokenDetail) && isset($parentDetail))
                                        <input type="text" class="form-control" value="{{$finalTeenId}}" readonly>
                                @else
                                <?php $teenagers = Helpers::getActiveTeenagers(); ?>
                                <select class="form-control" id="p_teenager_id" name="p_teenager_id">
                                    <option value="">{{trans('labels.formlblselectteenagerid')}}</option>
                                    <?php foreach ($teenagers as $key => $value) {
                                        ?>
                                        <option value="{{$value->t_uniqueid}}" >{{$value->t_name}}({{$value->t_uniqueid}})</option>
                                    <?php }
                                    ?>
                                </select>
                                @endif
                            </div>
                        </div>  
                        
                        <?php
                        if (old('p_user_type'))
                            $p_user_type = old('p_user_type');
                        elseif ($parentDetail)
                            $p_user_type = $parentDetail->p_user_type;
                        else
                            $p_user_type = '';
                        ?>
                        <div class="form-group">
                            <label for="p_user_type" class="col-sm-2 control-label">{{trans('labels.formlblusertype')}}</label>
                            <div class="col-sm-6">

                                <select class="form-control" id="p_user_type" name="p_user_type">
                                    <option value="1" <?php if ($p_user_type == 1) echo 'selected'; ?>>parents</option>                                    
                                    <option value="2" <?php if ($p_user_type == 2) echo 'selected'; ?>>Counselor</option>                                    
                                </select>
                            </div>
                        </div>                                                
                        <?php
                        if (old('deleted'))
                            $deleted = old('deleted');
                        elseif ($parentDetail)
                            $deleted = $parentDetail->deleted;
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
                    </div>
                    <div class="box-footer">
                        <button type="submit" id="submit" class="btn btn-primary btn-flat" >{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/parents/1') }}">{{trans('labels.cancelbtn')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@stop

@section('script')
<script type="text/javascript">
    jQuery(document).ready(function () {
        $('.onlyNumber').on('keyup', function () {
            this.value = this.value.replace(/[^0-9]/gi, '');
        });

        var form = $("#addParent");
<?php if (isset($parentDetail->id) && $parentDetail->id != '0') { ?>
            var validationRules = {
                p_first_name: {
                    required: true
                },
                p_last_name: {
                    required: true
                },
                p_address1: {
                    required: true
                },
                p_address2: {
                    required: true
                },
                p_city: {
                    required: true
                },
                p_state: {
                    required: true
                },
                p_country: {
                    required: true
                },
                p_pincode: {
                    required: true
                },
                p_gender: {
                    required: true
                },
                p_email: {
                    required: true,
                    email: true
                },
                confirm_password: {
                    equalTo: '#password'
                },
                p_teenager_id: {
                    required: true,
                },
                deleted: {
                    required: true
                }
            }
<?php } else { ?>
            var validationRules = {
                p_first_name: {
                    required: true
                },
                p_last_name: {
                    required: true
                },
                p_address1: {
                    required: true
                },
                p_address2: {
                    required: true
                },
                p_city: {
                    required: true
                },
                p_state: {
                    required: true
                },
                p_country: {
                    required: true
                },
                p_pincode: {
                    required: true
                },
                p_gender: {
                    required: true
                },
                p_email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true
                },
                confirm_password: {
                    required: true,
                    equalTo: '#password'
                },
                p_teenager_id: {
                    required: true,
                },
                deleted: {
                    required: true
                }
            }
<?php } ?>

        $("#addParent").validate({
            rules: validationRules,
            messages: {
                p_first_name: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                p_last_name: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                p_address1: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                p_address2: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                p_city: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                p_state: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                p_country: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                p_pincode: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                p_email: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                    email: "<?php echo trans('validation.validemail'); ?>"
                },
                password: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                confirm_password: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                    equalTo: "<?php echo trans('validation.passwordnotmatch'); ?>"
                },
                p_gender: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"

                },
                p_teenager_id: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                },
                deleted: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        });
    });
    
    function getDataOfState(countryId)
    {
        $("#p_state").empty();
        $("#p_city").empty();
        $.ajax({
            type: 'GET',
            url: '/get-state/' + countryId,
            dataType: "JSON",
            success: function (JSON) {
                $("#s_state").empty()
                for (var i = 0; i < JSON.length; i++) {
                    $("#p_state").append($("<option></option>").val(JSON[i].id).html(JSON[i].s_name))
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
                $("#p_city").empty();
                for (var i = 0; i < JSON.length; i++) {
                    $("#p_city").append($("<option></option>").val(JSON[i].id).html(JSON[i].c_name));
                }
            }
        });
    }
    $("#p_uniqueid_generate").click(function() {
        $.ajax({
            url: "{{ url('/admin/get-uniqueid') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#p_uniqueid').val(response);
            }
        });
    });
    
</script>

@stop
