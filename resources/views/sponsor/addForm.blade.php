@extends('layouts.sponsor-master')

@section('content')


<div class="centerlize">
    <div class="container">
        <div class="detail_container container_padd clearfix">

            <div class="col-xs-12">
                @if (count($errors) > 0)
                <div class="alert alert-danger danger">
                    <strong>{{trans('validation.whoops')}}</strong> {{trans('validation.someproblems')}}<br><br>
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
                <form name="addActivity" id="addActivity" method="post" class="sponsor_account_form" action="{{ url('/sponsor/save') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <?php if (isset($activityDetail) && !empty($activityDetail)) {
                        $days = $saDurationDays;
                    } else {
                        $days = '';
                    } ?>
                    <input type="hidden" id="endDateTypeWise" name="endDateTypeWise" value="{{ $days }}" >
                    <h1><span class="title_border" style="margin-bottom: 30px;">Activities</span></h1>                    
                    <input type="hidden" name="id" value="<?php echo (isset($activityDetail) && !empty($activityDetail)) ? $activityDetail->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($activityDetail) && !empty($activityDetail)) ? $activityDetail->sa_image : '' ?>">
                    <div class="clearfix">

                        <div class="col-md-1 col-sm-2 input_title"><span>Type</span></div>
                        <div class="col-md-5 col-sm-4">
                            <div class="mandatory">*</div>
                            <?php
                            if (old('sa_type'))
                                $sa_type = old('sa_type');
                            elseif ($activityDetail)
                                $sa_type = $activityDetail->sa_type;
                            else
                                $sa_type = '';
                            
                            ?>
                            
                            @if(empty($activityDetail))
                            <div class="select-style">
                                <?php $type = Helpers::type(); $sizeType = "hide"; ?>
                                <select id="type" name="type" onchange="getCredit(this.value)" >
                                    <option value="">{{trans('labels.selecttype')}}</option>
                                    <?php foreach ($type as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if ($sa_type == $key) echo 'selected'; ?> >{{$value}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <em for="type" class="invalid"></em>
                            @else
                                <input type="hidden" name="type" value="{{$sa_type}}">
                                <?php $value = ''; ?>
                                @if($sa_type == 1)
                                    <?php 
                                        $value = "Ad";
                                        $sizeType = ""; ?>
                                @elseif($sa_type == 2)
                                    <?php 
                                        $value = "Event"; 
                                        $sizeType = "hide"; ?>
                                @elseif($sa_type == 3)
                                    <?php 
                                        $value = "Scholarship"; 
                                        $sizeType = "hide";    ?>
                                @endif
                               <input type="text" id="type" name="type_name" value="{{$value}}" class="cst_input_primary" readonly="readonly">
                            @endif
                            <div class="size-type select-style {{$sizeType}}">
                                <select class="" id="sa_size_type" name="sa_size_type">
                                    <?php $sizeList = Helpers::adsSizeType(); ?>
                                    <option value="">Select Image Size</option>
                                    <?php foreach ($sizeList as $key => $val) { ?>
                                        <option value="{{$key}}" <?php if (!empty($activityDetail) && $key == $activityDetail->sa_size_type) { ?> selected <?php } ?> >{{$val}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-2 input_title"><span></span></div>
                        <div class="col-md-5 col-sm-4 u_image">
                            <div class="sponsor_detail">
                                <div class="">
                                    
                                    <!-- <span>(The size of the image must be 730 x 50 pixels.)</span> -->
                                    <div class="sponsor_image">
                                        <div class="upload_image">
                                            <?php
                                            $saImage = isset($activityDetail->sa_image) ? $activityDetail->sa_image : '';
                                            if (isset($saImage) && $saImage != '') {
                                                $image = Storage::url($uploadSAOrigionalPath . $activityDetail->sa_image);
                                            } else {
                                                $image = Storage::url($uploadSAOrigionalPath . 'proteen-logo.png');
                                            }
                                            ?>    
                                            <input type='file' name="sa_image" onchange="readImageURL(this);" class="profilePhoto" accept=".png, .jpg, .jpeg, .bmp"/>                                            
                                            <div class="placeholder_image sponsor update_profile full_width">
                                                <span>
                                                    <?php if (!empty($image)) { ?>
                                                        <img src="{{$image}}"/>
                                                    <?php } else { ?>
                                                        <span> <p>Upload Your Photo</p> </span>
                                                    <?php } ?>
                                                </span>
                                            </div>                                                                                                                                    
                                        </div>
                                    </div>
                                    <div class="photo-error"></div>
                                </div>                                                               
                            </div>
                        </div>
                    </div>
                    <br/>
                    <?php
                    if (old('sa_name'))
                        $sa_name = old('sa_name');
                    elseif ($activityDetail)
                        $sa_name = $activityDetail->sa_name;
                    else
                        $sa_name = '';
                    ?>
                    <div class="clearfix">

                        <div class="col-md-1 col-sm-2 input_title"><span>Name</span></div>
                        <div class="col-md-5 col-sm-4">
                            <div class="mandatory">*</div>
                            <input type="text" name="sa_name" id="sa_name" class="cst_input_primary" placeholder="Name" value="{{$sa_name}}">
                        </div>
                        <div class="col-md-1 col-sm-2 input_title"></div>
                        
                    </div>

                    <?php
                    if (old('sa_apply_level'))
                        $sa_apply_level = old('sa_apply_level');
                    elseif ($activityDetail)
                        $sa_apply_level = $activityDetail->sa_apply_level;
                    else
                        $sa_apply_level = '';
                    ?>
                    
                    <?php
                        if (old('sa_credit_used'))
                            $sa_credit = old('sa_credit_used');
                        elseif ($activityDetail)
                            $sa_credit = $activityDetail->sa_credit_used;
                        else
                            $sa_credit = '';
                        ?>
                    <div class="clearfix">
                        

                        <?php
                        if (old('sa_location'))
                            $sa_location = old('sa_location');
                        elseif ($activityDetail)
                            $sa_location = $activityDetail->sa_location;
                        else
                            $sa_location = '';
                        
                        if (old('sa_image_href'))
                            $sa_image_href = old('sa_image_href');
                        elseif ($activityDetail)
                            $sa_image_href = $activityDetail->sa_image_href;
                        else
                            $sa_image_href = '';
                        ?>
                        <div class="clearfix start_end_date left">

                            <div class="col-md-2 col-sm-4 input_title"><span>Location</span></div>
                            <div class="col-md-10 col-sm-8">
                                <div class="mandatory"></div>
                                <input type="text" name="location" class="cst_input_primary" placeholder="Please enter location" value="{{$sa_location}}">
                            </div>
                        </div>
                        <div class="clearfix start_end_date right" style="margin-top: 10px;">
                            <div class="col-md-2 col-sm-4 input_title"><span>Enter URL</span></div>
                            <div class="col-md-10 col-sm-8">      
                                <input type="text" name="image_href" id="image_href" class="cst_input_primary" placeholder="Your HTML link for users to connect" value="{{$sa_image_href}}">
                            </div>
                        </div>
                    </div>
                    <div class="clearfix">                        
                        <input type="hidden" name="level" id="level" value="0" />
                        <?php
                        if (old('sa_start_date'))
                            $sa_start_date = old('sa_start_date');
                        elseif ($activityDetail)
                            $sa_start_date = date('d/m/Y', strtotime($activityDetail->sa_start_date));
                        else
                            $sa_start_date = '';
                        ?>
                         <?php
                    if (old('sa_end_date'))
                        $sa_end_date = old('sa_end_date');
                    elseif ($activityDetail)
                        $sa_end_date = date('d/m/Y', strtotime($activityDetail->sa_end_date));
                    else
                        $sa_end_date = '';
                    ?>
                        <div class="col-md-1 col-sm-2 input_title"><span>Start Date</span></div>
                        <div class="col-md-5 col-sm-4 input_icon date_picker">
                            <div class="mandatory">*</div>
                            <input type="text" id="startdate" name="startdate" readonly="readonly" class="cst_input_primary" placeholder="Please enter start Date" value="{{$sa_start_date}}">
                        </div>
                        <!--<div class="clearfix start_end_date right">-->
                            <div class="col-md-1 col-sm-2 input_title"><span>End Date</span></div>
                            <div class="col-md-5 col-sm-4 input_icon date_picker">
                                <div class="mandatory">*</div>
                                <input type="text" id="enddate" name="enddate" readonly="readonly" class="cst_input_primary datepicker" placeholder="Please enter end Date" value="{{$sa_end_date}}">
                            </div>
                        <!--</div>-->
                    </div>

                    <?php
                    if (old('sa_end_date'))
                        $sa_end_date = old('sa_end_date');
                    elseif ($activityDetail)
                        $sa_end_date = date('d/m/Y', strtotime($activityDetail->sa_end_date));
                    else
                        $sa_end_date = '';
                    ?>
                    <div class="clearfix">
                        <div class="col-md-1 col-sm-2 input_title"><span>Status</span></div>
                        <div class="col-md-5 col-sm-4">
                            <div class="mandatory">*</div>
                            <div class="select-style">
                                <?php $status = Helpers::status();  $deleted = (isset($activityDetail) && !empty($activityDetail)) ? $activityDetail->deleted:''; ?>
                                <select name="status" id="status">
                                    <?php foreach ($status as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if($deleted == $key) {echo "selected";} ?>>{{$value}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="clearfix start_end_date right">
                            <div class="col-md-2 col-sm-4 input_title"><span class="special">Credit Deducted</span></div>
                            <div class="col-md-10 col-sm-8">                                
                            <div class="mandatory">*</div>
                                <input type="text" name="creditdeducted" id="creditdeducted" class="cst_input_primary" placeholder="" readonly value="{{ $sa_credit }}">
                            </div>
                        </div>

                        <?php
                        if (old('sa_description'))
                            $sa_description = old('sa_description');
                        elseif ($activityDetail)
                            $sa_description = $activityDetail->sa_description;
                        else
                            $sa_description = '';
                        ?>
                        <div class="clearfix start_end_date left">
                            <div class="col-md-2 col-sm-4 input_title"><span class="special">Description</span></div>
                            <div class="col-md-10 col-sm-8">                                
                            <div class="mandatory">*</div>
                                <textarea id="sa_description" name="sa_description" class="cst_input_primary" rows="4">{{$sa_description}}</textarea>            
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
$("#enddate").datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: 'dd/mm/yy',
    defaultDate: null,
    minDate: mdate('enddate')
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
<?php 
    if(isset($activityDetail) && !empty($activityDetail)) { 
        if ($datePickerDisabled == 1) { ?>
            $('#startdate').datepicker('destroy');
            $('#enddate').datepicker('destroy');
        <?php } else { ?>
            //$("#enddate").datepicker("option", "minDate", <?php echo $sa_start_date; ?>);
            //$("#enddate").datepicker("option", "maxDate", <?php echo $sa_end_date; ?>);
            $("#startdate").datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd/mm/yy',
                defaultDate: null,
                minDate: <?php echo $sa_start_date; ?>,
                onSelect: function (selected) {
                    var selectedDate = new Date(selected.split('/')[2],(selected.split('/')[1]),selected.split('/')[0]);
                    var getDays = $("#endDateTypeWise").val();
                    if (getDays != "") {
                        selectedDate.setDate(selectedDate.getDate() + parseInt(getDays));
                    } else {
                        selectedDate.setDate(selectedDate.getDate() + 30);
                    }
                    $("#enddate").datepicker("option", "minDate", selected);
                    var finalEndDate = selectedDate.getDate()+'/'+selectedDate.getMonth()+'/'+selectedDate.getFullYear();
                    $("#enddate").datepicker("option", "maxDate", finalEndDate);
                }
            }).on('change', function () {
                $(this).valid();
            });
        <?php } 
    } else { ?>
        $("#startdate").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        defaultDate: null,
        minDate: mdate('startdate'),
        onSelect: function (selected) {
            var selectedDate = new Date(selected.split('/')[2],(selected.split('/')[1]),selected.split('/')[0]);
            var getDays = $("#endDateTypeWise").val();
            if (getDays != "") {
                selectedDate.setDate(selectedDate.getDate() + parseInt(getDays));
            } else {
                selectedDate.setDate(selectedDate.getDate() + 30);
            }
            $("#enddate").datepicker("option", "minDate", selected);
            var finalEndDate = selectedDate.getDate()+'/'+selectedDate.getMonth()+'/'+selectedDate.getFullYear();
            $("#enddate").datepicker("option", "maxDate", finalEndDate);
        }
    }).on('change', function () {
        $(this).valid();
    });
<?php }?>

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
<?php //if($sa_end_date != '') { ?>
    //$('#enddate').datepicker('destroy');
<?php //} else { ?>

 <?php //} ?>
    //var imageWidth = 730;
    //var imageHeight = 50;
jQuery(document).ready(function () {
        $('#creditdeducted').keypress(function () {
            return false;
        });

        // $(".profilePhoto").change(function (e) {
        //     var ext = this.value.match(/\.(.+)$/)[1];
        //     switch (ext)
        //     {
        //         case 'jpg':
        //         case 'bmp':
        //         case 'png':
        //         case 'jpeg':
        //             break;
        //         default:
        //             alert('Image type not allowed');
        //             this.value = '';
        //     }
        // });

<?php if (isset($activityDetail->id) && $activityDetail->id != '0') { ?>
            var validationRules = {
                type: {
                    required: true
                },
                sa_name: {
                    required: true
                },
                creditdeducted: {
                    required: true
                },
                status: {
                    required: true
                },
                startdate: {
                    required: true
                },
                enddate: {
                    required: true
                },
                deleted: {
                    required: true
                }
            }
            //$('.size-type').show();
            $('#sa_size_type option:not(:selected)').attr("disabled", true); 
            var sa_size_type = $('#sa_size_type').val();
            switch (sa_size_type) {
                case '1':
                    imageWidth = 343;
                    imageHeight = 400;
                    break;

                case '2':
                    imageWidth = 343;
                    imageHeight = 800;
                    break;

                case '3':
                    imageWidth = 850;
                    imageHeight = 90;
                    break;

                case '4':
                    imageWidth = 1200;
                    imageHeight = 90;
                    break;

                default:
                    imageWidth = 730;
                    imageHeight = 50;
                    break;
            }
<?php } else { ?>
            var validationRules = {
                type: {
                    required: true
                },
                sa_name: {
                    required: true
                },
                creditdeducted: {
                    required: true
                },
                status: {
                    required: true
                },
                startdate: {
                    required: true
                },
                enddate: {
                    required: true,
                },
                deleted: {
                    required: true
                },
                image_href: {
                    url: true
                }
            }
            //$('.size-type').hide(); 
<?php } ?>
        $("#addActivity").validate({
            rules: validationRules,
            messages: {
                type: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                sa_name: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                    email: "<?php echo trans('validation.validemail'); ?>"
                },
                level: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                startdate: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                enddate: {
                    required: "<?php echo trans('validation.requiredfield') ?>"
                },
                status: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                },
                deleted: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                image_href: {
                    required: "Please enter valid URL"
                }
            }
        })
    });


    function getCredit(type) {
        $(".size-type").addClass('hide');
        $(".photo-error").text("");
        $(".profilePhoto").val("");
        $(".upload_image").css("background-image", "none");
        if (type == 1) {
            var configKey = 'Ads ProCoins';
            $('#ads_image_dimension').show();
            $(".size-type").removeClass('hide');
        } else if (type == 2) {
            var configKey = 'Event ProCoins';
            $('#ads_image_dimension').hide();
        } else if (type == 3) {
            var configKey = 'Contest ProCoins';
        } else {
            var configKey = '';
        }



        $.ajax({
            url: "{{ url('/sponsor/get-credit') }}",
            type: 'post',
            data: {
                "_token": '{{ csrf_token() }}',
                "configKey": configKey
            },
            success: function (response) {
                if (response.requiredCredit != "") {
                    $("#creditdeducted").val(response.requiredCredit);
                }
                
                if (response.allowedDays && response.allowedDays != "") {
                    var startDate = $("#startdate").val();
                    if (startDate != "") {
                        var setEndDate = new Date(startDate.split('/')[2],(startDate.split('/')[1]-1),startDate.split('/')[0]);
                        setEndDate.setDate(setEndDate.getDate() + response.allowedDays);
                        var finalEndDate = setEndDate.getDate()+'/'+ (setEndDate.getMonth()+1) +'/'+setEndDate.getFullYear();
                        $("#enddate").datepicker("option", "minDate", startDate);
                        $("#enddate").datepicker("option", "maxDate", finalEndDate);
                    } else {
                        var date = new Date();
                        var currentDate = date.getDate()+'/'+date.getMonth()+'/'+date.getFullYear();
                        date.setDate(date.getDate() + response.allowedDays);
                        var setEnddate = date.getDate()+'/'+date.getMonth()+'/'+date.getFullYear();
                        $("#enddate").datepicker("option", "minDate", currentDate);
                        $("#enddate").datepicker("option", "maxDate", setEnddate);
                    }
                    $("#endDateTypeWise").val(response.allowedDays);
                } else {
                    $("#endDateTypeWise").val(30);
                }

            }
        });

    }

    $("#sa_size_type").change(function() {
        var sizeType = $(this).val();
        $(".upload_image").css("background-image", "none");
        switch (sizeType) {
            case '1':
                imageWidth = 343;
                imageHeight = 400;
                break;

            case '2':
                imageWidth = 343;
                imageHeight = 800;
                break;

            case '3':
                imageWidth = 850;
                imageHeight = 90;
                break;

            case '4':
                imageWidth = 1200;
                imageHeight = 90;
                break;

            default:
                //imageWidth = 730;
                //imageHeight = 50;
                break;
        }
    });
        

    function readImageURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var a = document.querySelector(".upload_image");
                if (input.files[0].type == 'image/jpeg' || input.files[0].type == 'image/jpg' || input.files[0].type == 'image/png' || input.files[0].type == 'image/bmp') {
                    if (input.files[0].size > 3000000) {
                        $(".photo-error").text("File size is too large. Maximum 3MB allowed");
                        $(this).val('');
                    } else {
                        var activityType = $("#type").val();
                        var image = new Image();
                        image.src = e.target.result;
                        image.onload = function() {
                            if ((this.height !== imageHeight || this.width !== imageWidth) && (activityType == 'Ad' || activityType == 1)) {
                                $(".photo-error").text("Image width must be " + imageWidth + "px and Height " + imageHeight + "px");
                                $(this).val('');
                                a.style.backgroundImage = "";
                            } else if ((this.height < 200 || this.width < 200) && (activityType == 'Scholarship' || activityType == 3)) {
                                $(".photo-error").text("Image width and height must be 200 X 200");
                                $(this).val('');
                                
                            } else {
                                a.style.backgroundImage = "url('" + e.target.result + "')";
                                a.className = "upload_image activated";
                                $(".photo-error").text("");
                            }
                        };
                    }
                } else {
                    $(".photo-error").text("File type not allowed");
                    $(this).val('');
                }
            };

            reader.readAsDataURL(input.files[0]);
        }
    }


</script>
@stop