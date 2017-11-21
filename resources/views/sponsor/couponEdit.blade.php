@extends('layouts.sponsor-master')

@section('content')


<div class="centerlize">
    <div class="container">
        <div class="detail_container container_padd clearfix">

            <div class="col-xs-12">
                @if (count($errors) > 0)
                <div class="alert alert-danger danger alert-dismissable danger">
                    <strong>{{trans('validation.whoops')}}</strong> {{trans('validation.someproblems')}}<br><br> 
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if ($message = Session::get('error'))
                <div class="row">
                    <div class="col-md-8 col-md-offset-2 invalid_pass_error">
                        <div class="box-body">
                            <div class="alert alert-error alert-dismissable danger">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                                <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                                {{ $message }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-md-offset-1 col-md-10 col-sm-12 padd_none">
                <form name="editCoupon" id="editCoupon" method="post" class="sponsor_account_form" action="{{ url('/sponsor/save-coupon') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <h1><span class="title_border" style="margin-bottom: 30px;">Coupon</span></h1>
                    <input type="hidden" name="id" value="<?php echo (isset($coupon) && !empty($coupon)) ? $coupon->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($coupon) && !empty($coupon)) ? $coupon->cp_image : '' ?>">                    
                    <?php
                    if (old('cp_code'))
                        $couponCode = old('cp_code');
                    elseif ($coupon)
                        $couponCode = $coupon->cp_code;
                    else
                        $couponCode = '';
                    ?>
                    <div class="clearfix">
                        <div class="col-md-1 col-sm-2 input_title"><span>Code</span></div>
                        <div class="col-md-5 col-sm-4">
                            <div class="mandatory">*</div>
                            <input type="text" name="cp_code" id="cp_code" class="cst_input_primary" placeholder="Name" value="{{$couponCode}}">
                        </div>
                        
                        
                    </div>

                    
                    <div class="clearfix">                       
                        <?php
                        if (old('cp_description'))
                            $description = old('cp_description');
                        elseif ($coupon)
                            $description = $coupon->cp_description;
                        else
                            $description = '';
                        ?>
                        <div class="col-md-1 col-sm-2 input_title"><span>Description</span></div>
                        <div class="col-md-5 col-sm-4 input_icon">
                            <textarea id="cp_description" name="cp_description"  class="cst_input_primary" placeholder="Please enter description">{{$description}}</textarea> 
                        </div>
                        <div class="col-md-1 col-sm-2 input_title"></div>
                        <div class="col-md-5 col-sm-4 u_image">
                            <div class="user_detail">
                                <div class="pull-right">
                                    <div class="user_image">
                                        <div class="upload_image">                                            
                                            <?php
                                            $saImage = isset($coupon->cp_image) ? $coupon->cp_image : '';
                                            if (isset($saImage) && $saImage != '') {
                                                $image = $couponOriginalImagePath . $coupon->cp_image;
                                            } else {
                                                $image = $couponOriginalImagePath . 'proteen-logo.png';
                                            }
                                            ?>                                                       
                                            <input type='file' name="cp_image" onchange="readURL(this);" class="profilePhoto" accept=".png, .jpg, .jpeg, .bmp"/>
                                            <div class="placeholder_image update_profile">
                                                <span>
                                                    <?php if (!empty($image)) { ?>
                                                        <img src="{{Storage::url($image)}}"/>
                                                    <?php } else { ?>
                                                        <span> <p>Upload Coupon Image</p> </span>
                                                    <?php } ?>
                                                </span>
                                            </div>                                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>

                    <?php
                    if (old('cp_validto'))
                        $coupon_end_date = old('cp_validto');
                    elseif ($coupon)
                        $coupon_end_date = date('d/m/Y', strtotime($coupon->cp_validto));
                    else
                        $coupon_end_date = '';
                    ?>
                    <div class="clearfix">                        
                        <?php
                        if (old('cp_limit'))
                            $coupon_limit = old('cp_limit');
                        elseif ($coupon)
                            $coupon_limit = $coupon->cp_limit;
                        else
                            $coupon_limit = '';
                        ?>
                        <div class="clearfix">

                            <div class="col-md-1 col-sm-2 input_title"><span>Limit</span></div>
                            <div class="col-md-5 col-sm-4">
                                <div class="mandatory">*</div>
                                <input type="text" name="cp_limit" id="cp_limit" class="cst_input_primary" placeholder="Please enter limit" value="{{$coupon_limit}}">
                            </div>
                            <div class="col-md-1 col-sm-2 input_title"><span></span></div>
                            <div class="col-md-5 col-sm-4"  style="text-align: right;">
                                <div class="mandatory"></div>
                                <span>(The size of the image must be 255 x 150 pixels.)</span>
                            </div>
                            
                        </div>
                    </div>
                    <div class="clearfix">
                        <?php
                        if (old('cp_credit_used'))
                            $cp_credit_used = old('cp_credit_used');
                        elseif ($coupon)
                            $cp_credit_used = $coupon->cp_credit_used;
                        else
                            $cp_credit_used = '';
                        ?>

                        <div class="col-md-1 col-sm-2 input_title"><span class="special">Credit Deducted</span></div>
                        <div class="col-md-5 col-sm-4">                            
                            <div class="mandatory">*</div>
                            <input type="text" name="cp_credit_used" id="cp_credit_used" class="cst_input_primary" placeholder="" readonly value="{{$cp_credit_used}}">
                        </div>
                                                
                        <?php
                        if (old('cp_validfrom'))
                            $coupon_start_date = old('cp_validfrom');
                        elseif ($coupon)
                            $coupon_start_date = date('d/m/Y', strtotime($coupon->cp_validfrom));
                        else
                            $coupon_start_date = '';
                        ?>
                        <div class="col-md-1 col-sm-2 input_title"><span>Start Date</span></div>
                        <div class="col-md-5 col-sm-4 input_icon date_picker">                            
                            <div class="mandatory">*</div>
                            <input type="text" id="cp_validfrom" name="cp_validfrom" readonly="readonly"  class="cst_input_primary datepicker" placeholder="Please enter start Date" value="{{$coupon_start_date}}">
                        </div>
                    </div>
                    <div class="clearfix">                        
                        <div class="col-md-1 col-sm-2 input_title"><span>Status</span></div>
                        <div class="col-md-5 col-sm-4">
                            <div class="mandatory">*</div>
                            <div class="select-style">
                                <?php $status = Helpers::status(); ?>
                                <select name="status" id="status">
                                    <?php foreach ($status as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if($coupon->deleted == $key) {echo "selected";} ?> >{{$value}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="clearfix start_end_date right">
                            <div class="col-md-2 col-sm-4 input_title"><span>End Date</span></div>
                            <div class="col-md-10 col-sm-8 input_icon date_picker">
                                <div class="mandatory">*</div>
                                <input type="text" id="cp_validto" name="cp_validto" readonly="readonly" class="cst_input_primary datepicker" placeholder="Please enter end Date" value="{{$coupon_end_date}}">
                            </div>
                        </div>
                        
                    </div>
                    <div class="button_container">
                        <div class="clearfix">
                            <input id="submit" type="submit" class="btn primary_btn" value="Submit">
                            <a href="{{url('sponsor/home/')}}" class="btn primary_btn">Cancel</a>                                
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@stop

@section('script')

<script type="text/javascript">
  $("#cp_validfrom").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        defaultDate: null,
        minDate: mdate('cp_validfrom'),
        onSelect: function (selected) {
            //var dt = new Date(selected);
            //dt.setDate(dt.getDate());
            $("#cp_validto").datepicker("option", "minDate", selected);
        }
    }).on('change', function () {
        $(this).valid();
    });

    function mdate(str)
    {
        var checkForDate = $("#"+str).val();
        if(checkForDate)
        {
            return checkForDate;

        }
        else
        {
            checkForDate = 'today';
            return checkForDate;
        }
    }

 $("#cp_validto").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        defaultDate: null,
        minDate: mdate('cp_validto')
    }).on('change', function () {
        $(this).valid();
        var myDate = new Date($(this).val());
        var currentYear = (new Date).getFullYear();
        var mydate_fullyear = myDate.getFullYear();
        if(mydate_fullyear < currentYear)
        {
            $('#submit').attr('disabled', true);
        }
        else
        {
            $('#submit').attr('disabled', false);
        }
    });





    jQuery(document).ready(function () {
        $('#cp_credit_used').keypress(function () {
            return false;
        });

        $(".profilePhoto").change(function (e) {
            var ext = this.value.match(/\.(.+)$/)[1];
            switch (ext)
            {
                case 'jpg':
                case 'bmp':
                case 'png':
                case 'jpeg':
                    break;
                default:
                    alert('Image type not allowed');
                    this.value = '';
            }
        });

            var validationRules = {
                cp_code: {
                    required: true
                },
                cp_description: {
                    required: true
                },
                cp_validfrom: {
                    required: true
                },
                cp_validto: {
                    required: true
                },
                cp_limit: {
                    required: true
                },
                cp_credit_used: {
                    required: true
                },
                deleted: {
                    required: true
                }
            }

        $("#editCoupon").validate({
            rules: validationRules,
            messages: {
                editCoupon: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                cp_description: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                },
                cp_validfrom: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                cp_validto: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                cp_limit: {
                    required: "<?php echo trans('validation.requiredfield') ?>"
                },
                cp_credit_used: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                },
                deleted: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });
   
</script>
@stop