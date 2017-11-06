@extends('layouts.admin-master')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.teenagers')}}
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo (isset($teenagerDetail) && !empty($teenagerDetail)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.teenager')}}</h3>
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
                <?php $page = (isset($_GET['page']) && $_GET['page'] > 0 )? "?page=".$_GET['page']."":'';?>
                <form id="addTeenager" class="form-horizontal" method="post" action="{{ url('/admin/save-teenager') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($teenagerDetail) && !empty($teenagerDetail)) ? $teenagerDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_profile" value="<?php echo (isset($teenagerDetail) && !empty($teenagerDetail)) ? $teenagerDetail->t_photo : '' ?>">
                    <input type="hidden" name="hidden_password" value="<?php echo (isset($teenagerDetail) && !empty($teenagerDetail)) ? $teenagerDetail->password : '' ?>">
                    <input type="hidden" name="sid" value="<?php echo (isset($sid) && !empty($sid)) ? $sid : '' ?>">
                    <input type="hidden" name="pageRank" value="<?php echo $page ?>">
                    <div class="box-body">
                        <?php
                        if (old('t_name'))
                            $t_name = old('t_name');
                        else if ($teenagerDetail)
                            $t_name = $teenagerDetail->t_name;
                        else
                            $t_name = '';
                        ?>
                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlblname')}}</label>
                            <div class="col-sm-6">
                                <input type="name" class="form-control" id="t_name" name="t_name" placeholder="{{trans('labels.formlblname')}}" value="{{$t_name}}" minlength="3" maxlength="50"/>
                            </div>
                        </div>

                        <?php
                        if (old('t_nickname'))
                            $t_nickname = old('t_nickname');
                        elseif ($teenagerDetail)
                            $t_nickname = $teenagerDetail->t_nickname;
                        else
                            $t_nickname = '';
                        ?>
                        <div class="form-group">
                            <label for="t_nickname" class="col-sm-2 control-label">{{trans('labels.formlblnickname')}}</label>
                            <div class="col-sm-6">
                                <input type="name" class="form-control" id="t_nickname" name="t_nickname" placeholder="{{trans('labels.formlblnickname')}}" value="{{$t_nickname}}" maxlength="100"/>
                            </div>
                        </div>

                        <?php
                            if (old('t_email'))
                                $t_email = old('t_email');
                            elseif ($teenagerDetail)
                                $t_email = $teenagerDetail->t_email;
                            else
                                $t_email = '';
                        ?>
                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlblemail')}}</label>
                            <div class="col-sm-6">
                                <input type="email" class="form-control" id="t_email" name="t_email" placeholder="{{trans('labels.formlblemail')}}" value="{{ $t_email }}" minlength="5" maxlength="50" />
                            </div>
                        </div>

                        <?php
                            if (old('t_uniqueid'))
                                $t_uniqueid = old('t_uniqueid');
                            elseif ($teenagerDetail)
                                $t_uniqueid = $teenagerDetail->t_uniqueid;
                            else
                                $t_uniqueid = '';
                            $style = '';
                            if ($t_uniqueid != "") {
                                $style = 'style="display:none;"';
                            }
                        ?>
                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlbluniqueid')}}</label>
                            <div class="col-sm-6">
                                <input type="text" readonly="true" class="form-control" id="t_uniqueid" name="t_uniqueid" placeholder="{{trans('labels.formlbluniqueid')}}" value="{{$t_uniqueid}}" minlength="23" maxlength="23"/>
                            </div>
                            <div class="col-sm-2" <?php echo $style; ?>>
                                <a class="btn btn-success" href="#" id="t_uniqueid_generate" name="t_uniqueid_generate" >{{trans('labels.generatebtn')}}</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlblphoto')}}</label>
                            <div class="col-sm-2">
                                <input type="file" id="t_photo" name="t_photo" onchange="readURL(this);"/>
                                <?php
                                if (isset($teenagerDetail->id) && $teenagerDetail->id != '0') {
                                    if (File::exists(public_path($uploadTeenagerThumbPath . $teenagerDetail->t_photo)) && $teenagerDetail->t_photo != '') {
                                        ?><br>
                                        <img src="{{ url($uploadTeenagerThumbPath.$teenagerDetail->t_photo) }}" alt="{{$teenagerDetail->t_photo}}"  height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                    <?php } else { ?>
                                        <img src="{{ asset('/backend/images/proteen_logo.png')}}" class="user-image" alt="Default Image" height="<?php echo Config::get('constant.DEFAULT_IMAGE_HEIGHT'); ?>" width="<?php echo Config::get('constant.DEFAULT_IMAGE_WIDTH'); ?>">
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <label for="image_format" class="col-sm-3 control-label">{{trans('labels.pictureformat')}}</label>
                        </div>

                        <?php
                        if (old('t_school'))
                            $t_school = old('t_school');
                        elseif ($teenagerDetail)
                            $t_school = $teenagerDetail->t_school;
                        else
                            $t_school = '';
                        ?>
                        <div class="form-group">
                            <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblselectschool')}}</label>
                            <div class="col-sm-6">
                                <?php $schools = Helpers::getActiveSchools(); ?>
                                <select class="form-control" id="t_school" name="t_school">
                                    <option value="">{{trans('labels.formlblselectschool')}}</option>
                                    <?php foreach ($schools as $key => $value) { ?>
                                        <option value="{{$value->id}}" <?php if ($t_school == $value->id) echo 'selected'; ?> >{{$value->sc_name}}({{$value->id}})</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlblpassword')}}</label>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" id="password" name="password" placeholder="{{trans('labels.formlblpassword')}}" value=""  maxlength="20" minlength="6" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlblconfirmpassword')}}</label>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="{{trans('labels.formlblconfirmpassword')}}" value=""  maxlength="20" minlength="6" />
                            </div>
                        </div>

                        <?php
                        if (old('t_gender'))
                            $t_gender = old('t_gender');
                        elseif ($teenagerDetail)
                            $t_gender = $teenagerDetail->t_gender;
                        else
                            $t_gender = '';
                        ?>
                        <div class="form-group">
                            <label for="t_gender" class="col-sm-2 control-label">{{trans('labels.formlblgender')}}</label>
                            <div class="col-sm-6">
                                <?php $gender = Helpers::gender(); ?>
                                <select class="form-control" id="t_gender" name="t_gender">
                                    <option value="">{{trans('labels.formlblselectgender')}}</option>
                                    <?php foreach ($gender as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if ($t_gender == $key) echo 'selected'; ?> >{{$value}}</option>
                                    <?php } ?>

                                </select>
                            </div>
                        </div>

                        <?php
                        if (old('t_social_provider'))
                            $t_social_provider = old('t_social_provider');
                        elseif ($teenagerDetail)
                            $t_social_provider = $teenagerDetail->t_social_provider;
                        else
                            $t_social_provider = '';
                        ?>
                        <div class="form-group">
                            <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblsocialmedia')}}</label>
                            <div class="col-sm-6">
                                <?php $socialMedia = ['' => 'Select Social Media', 'Facebook' => 'Facebook', 'Google' => 'Google']; ?>
                                <select class="form-control" id="t_social_provider" name="t_social_provider">
                                    <?php foreach ($socialMedia as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if ($t_social_provider == $value) echo 'selected'; ?> >{{$value}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <?php
                        if (old('t_social_identifier'))
                            $t_social_identifier = old('t_social_identifier');
                        elseif ($teenagerDetail)
                            $t_social_identifier = $teenagerDetail->t_social_identifier;
                        else
                            $t_social_identifier = '';
                        ?>
                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlblsocialidentifier')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="t_social_identifier" name="t_social_identifier" placeholder="{{trans('labels.formlblsocialidentifier')}}" value="{{$t_social_identifier}}" maxlength="25"/>
                            </div>
                        </div>

                        <?php
                        if (old('t_phone'))
                            $t_phone = old('t_phone');
                        elseif ($teenagerDetail)
                            $t_phone = $teenagerDetail->t_phone;
                        else
                            $t_phone = '';
                        ?>
                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlblphone')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="t_phone" name="t_phone" placeholder="{{trans('labels.formlblphone')}}" value="{{$t_phone}}" maxlength="10" />
                            </div>
                        </div>

                        <?php
                        if (old('t_birthdate'))
                            $t_birthdate = old('t_birthdate');
                        elseif ($teenagerDetail)
                            $t_birthdate = date('d/m/Y', strtotime($teenagerDetail->t_birthdate));
                        else
                            $t_birthdate = '';
                        ?>
                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlblbdate')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="t_birthdate" name="t_birthdate" value="{{$t_birthdate}}" /> 
                            </div>
                        </div>

                        <?php
                        if (old('t_country'))
                            $t_country = old('t_country');
                        elseif ($teenagerDetail)
                            $t_country = $teenagerDetail->t_country;
                        else
                            $t_country = '';
                        ?>
                        <div class="form-group">
                            <label for="cat_name" class="col-sm-2 control-label">{{trans('labels.formlblselectcountry')}}</label>
                            <div class="col-sm-6">
                                <?php $countries = Helpers::getCountries(); ?>
                                <select class="form-control" id="t_country" name="t_country">
                                    <option value="">{{trans('labels.formlblselectcountry')}}</option>
                                    <?php foreach ($countries as $key => $value) { ?>
                                        <option value="{{$value->id}}" <?php if ($t_country == $value->id) echo 'selected'; ?> >{{$value->c_name}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <?php
                        if (old('t_pincode'))
                            $t_pincode = old('t_pincode');
                        elseif ($teenagerDetail)
                            $t_pincode = $teenagerDetail->t_pincode;
                        else
                            $t_pincode = '';
                        ?>
                        <div class="form-group">
                            <label for="t_pincode" class="col-sm-2 control-label">{{trans('labels.formlblpincode')}}</label>
                            <div class="col-sm-6">
                                <input type="name" class="form-control" id="t_pincode" name="t_pincode" placeholder="{{trans('labels.formlblpincode')}}" value="{{$t_pincode}}" minlength="6" maxlength="6"/>
                            </div>
                        </div>

                        <?php
                        if (old('t_sponsor_choice'))
                            $t_sponsor_choice = old('t_sponsor_choice');
                        elseif ($teenagerDetail)
                            $t_sponsor_choice = $teenagerDetail->t_sponsor_choice;
                        else
                            $t_sponsor_choice = '';
                        $style = '';
                        if ($t_sponsor_choice != "2") {
                            $style = 'style="display:none;"';
                        }
                        ?>
                        <?php
                        if (isset($t_sponsor_choice) && $t_sponsor_choice != '' && $t_sponsor_choice != 0) {
                            $t_sponsor_choice_default = '';
                        } else {
                            $t_sponsor_choice_default = 'checked="checked"';
                            ;
                        }
                        ?>
                        <div class="form-group" id="sponsor">
                            <label for="t_sponsor_choice" class="col-sm-2 control-label">{{trans('labels.formlblsponsorchoice')}}</label>
                            <div class="col-sm-6">
                                <label class="radio-inline"><input type="radio" name="t_sponsor_choice" id="t_sponsor_choice" value="1" <?php if ($t_sponsor_choice == "1") echo 'checked="checked"'; ?> />{{trans('labels.formblself')}}</label>
                                <label class="radio-inline"><input type="radio" name="t_sponsor_choice" id="t_sponsor_choice" value="2" <?php if ($t_sponsor_choice == "2") echo 'checked="checked"'; ?>/>{{trans('labels.formblsponsor')}}</label>
                                <label class="radio-inline"><input type="radio" name="t_sponsor_choice" id="t_sponsor_choice" value="3" <?php if ($t_sponsor_choice == "3") echo 'checked="checked"'; echo $t_sponsor_choice_default; ?> />{{trans('labels.formblnone')}}</label>
                            </div>
                        </div>

                        <?php 
                        if(isset($teenagerDetail->t_sponsors))
                        {
                            
                                if (old('t_sponsors'))
                                    $t_sponsors = old('t_sponsors');
                                elseif ($teenagerDetail)
                                    $t_sponsors = $teenagerDetail->t_sponsors;
                                else
                                    $t_sponsors = '';
                               
                                if(isset($t_sponsors) && !empty($t_sponsors))
                                {
                                    foreach($t_sponsors as $val)
                                    {
                                        $sponsor_id[] = $val->sponsor_id;
                                    }
                                }
                        }  
                            ?>
                        
                        <div class="form-group" id="sponsor_choice" <?php echo $style; ?>>
                            <label for="sponsor_choice" class="col-sm-2 control-label">{{trans('labels.formlblselectsponsor')}}</label>
                            <div class="col-sm-6">
                                
                                <select class="form-control" id="sponsor_choice" name="selected_sponsor[]" multiple="multiple" required>
                                    <?php
                                    if (isset($sponsorDetail) && !empty($sponsorDetail)) {
                                        foreach ($sponsorDetail as $key => $value) {
                                            ?>
                                            <option value="{{$value->sponsor_id}}"  <?php
                                            if (isset($sponsor_id) && in_array($value->sponsor_id, $sponsor_id)) {
                                                echo 'selected="selected"';
                                            }
                                            ?> >{{$value->sp_company_name}}</option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                </select>
                            </div>
                        </div>

                        <?php
                        if (old('t_isverified'))
                            $t_isverified = old('t_isverified');
                        elseif ($teenagerDetail)
                            $t_isverified = $teenagerDetail->t_isverified;
                        else
                            $t_isverified = '';
                        ?>
                        <div class="form-group">
                            <label for="t_isverified" class="col-sm-2 control-label">{{trans('labels.formlblisverified')}}</label>
                            <div class="col-sm-6">
                                <input type="checkbox" value="1" name="t_isverified" id="t_isverified" <?php
                                if ($t_isverified) {
                                    echo 'checked="cehcked"';
                                }
                                ?>/>
                            </div>
                        </div>

                        <?php
                        if (old('t_device_type'))
                            $t_device_type = old('t_device_type');
                        elseif ($teenagerDetail)
                            $t_device_type = $teenagerDetail->t_device_type;
                        else
                            $t_device_type = '';
                        ?>
                        <div class="form-group">
                            <label for="t_device_type" class="col-sm-2 control-label">{{trans('labels.formlbldevicetype')}}</label>
                            <div class="col-sm-6">
                                <label class="radio-inline"><input type="radio" name="t_device_type" id="t_device_type" value="1" <?php if ($t_device_type == "1") echo 'checked="checked"'; ?>/>{{trans('labels.formblios')}}</label>
                                <label class="radio-inline"><input type="radio" name="t_device_type" id="t_device_type" value="2" <?php if ($t_device_type == "2") echo 'checked="checked"'; ?> />{{trans('labels.formblandroid')}}</label>
                                <label class="radio-inline"><input type="radio" name="t_device_type" id="t_device_type" value="3" <?php if ($t_device_type == "3") echo 'checked="checked"'; ?>/>{{trans('labels.formblweb')}}</label>
                            </div>
                        </div>


                        <?php
                        if (old('deleted'))
                            $deleted = old('deleted');
                        elseif ($teenagerDetail)
                            $deleted = $teenagerDetail->deleted;
                        else
                            $deleted = '';
                        ?>
                        <div class="form-group">
                            <label for="category_type" class="col-sm-2 control-label">{{trans('labels.formlblstatus')}}</label>
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
                        <button type="submit" class="btn btn-primary btn-flat" id="submitTeen">{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/teenagers') }}{{$page}}">{{trans('labels.cancelbtn')}}</a>

                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@stop

@section('script')
<!-- Include Required Prerequisites -->
<script type="text/javascript" src="{{ asset('backend/js/jquery.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery-ui.js') }}"></script>
<script type="text/javascript">
    jQuery.noConflict();
    jQuery("#t_birthdate").datepicker({
        minDate: new Date(1950, 12 - 1, 25),
        maxDate: '-4380',
        yearRange: '1950:2050',
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy',
})
    
    jQuery(document).ready(function() {
        var readonly = '<?php echo isset($teenagerDetail) && !empty($teenagerDetail) ? 'readonly':''; ?>'
        if(readonly == 'readonly'){
            $("#t_email").attr('readonly', true);  
            $("#t_email, #t_birthdate").keypress(function(){
               return false; 
            });
        }
<?php if (isset($teenagerDetail->id) && $teenagerDetail->id != '0') { ?>
    var validationRules = {
                t_name: {
                    required: true
                },
                t_email: {
                    required: {
                        depends: function(element) {
                            return $('#t_email').is(":blank");
                        }
                    },
                    email: true
                },
                t_uniqueid: {
                    required: true
                },
                confirm_password: {
                    equalTo: '#password'
                },
                t_gender: {
                    required: true
                },
                t_phone: {
                    digits: true
                },
                t_birthdate: {
                    required: true
                },
                t_country: {
                    required: true
                },
                t_pincode: {
                    required: true,
                    minlength: 5,
                    maxlength: 6,
                    number: true
                },
                sponsor_choice: {
                    required: true
                },
                deleted: {
                    required: true
                }
            }
<?php } else { ?>
            var validationRules = {
                t_name: {
                    required: true
                },
                t_phone: {
                    minlength: 10,
                    maxlength: 11
                },
                t_uniqueid: {
                    required: true
                },
                t_email: {
                    required: {
                        depends: function(element) {
                            return $('#t_email').is(":blank");
                        }
                    },
                    email: true
                },
                password: {
                    required: true
                },
                confirm_password: {
                    required: true,
                    equalTo: '#password'
                },
                t_gender: {
                    required: true
                },
                t_birthdate: {
                    required: true
                },
                t_country: {
                    required: true
                },
                t_pincode: {
                    required: true,
                    minlength: 5,
                    maxlength: 6
                },
                sponsor_choice: {
                    required: true
                },
                deleted: {
                    required: true
                }
            }
<?php } ?>

        $("#addTeenager").validate({
            rules: validationRules,
            messages: {
                t_name: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                t_email: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                    email: "<?php echo trans('validation.validemail'); ?>"
                },
                t_uniqueid: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                password: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                confirm_password: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                    equalTo: "<?php echo trans('validation.passwordnotmatch'); ?>"
                },
                t_gender: {
                    required: "<?php echo trans('validation.requiredfield') ?>"
                },
                t_phone: {
                    digits: "<?php echo trans('validation.validphoneno'); ?>"
                },
                t_birthdate: {
                    required: "<?php echo trans('validation.birthdaterequiredfield') ?>"
                },
                t_country: {
                    required: "<?php echo trans('validation.requiredfield') ?>"
                },
                t_pincode: {
                    required: "<?php echo trans('validation.requiredfield') ?>"
                },
                sponsor_choice: {
                    required: "<?php echo trans('validation.requiredfield') ?>"
                },
                deleted: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });


    $("#sponsor input:radio").click(function() {

        var sponsor_choice = this.value;
        if (sponsor_choice == '2')
        {
            //$.ajax({
              //  url: "{{ url('/admin/getsponsor') }}",
               // type: 'post',
               // data: {
                  //  "_token": '{{ csrf_token() }}',
                  //  "field_name": 'sponsor_choice'
               // },
                //success: function(response) {
                    $('#sponsor_choice').show();
                  //  $('#sponsor_choice').html(response);
                //}
           // });
        }
        else
        {
            $('#sponsor_choice').hide();
        }
    });
    $("#t_phone").on('keyup', function() {
        this.value = this.value.replace(/[^0-9]/gi, '');
    });
    $("#t_pincode").on('keyup', function() {
        this.value = this.value.replace(/[^0-9]/gi, '');
    });
    $("#t_uniqueid_generate").click(function() {
        $.ajax({
            url: "{{url('/admin/get-uniqueid')}}",
            type: 'POST',
            data: {
                "_token": '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#t_uniqueid').val(response);
            }
        });
    });

</script>
@stop