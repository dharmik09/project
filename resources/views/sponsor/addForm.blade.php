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
                    <h1><span class="title_border" style="margin-bottom: 30px;">Advertisements</span></h1>                    
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
                                <?php $type = Helpers::type(); ?>
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
                                    <?php $value = "Ad"; ?>
                                @elseif($sa_type == 2)
                                    <?php $value = "Event"; ?>
                                @elseif($sa_type == 3)
                                    <?php $value = "Contest"; ?>
                                @endif
                               <input type="text" id="type" name="type_name" value="{{$value}}" class="cst_input_primary" readonly="readonly">
                            @endif
                        </div>
                        <div class="col-md-1 col-sm-2 input_title"><span></span></div>
                        <div class="col-md-5 col-sm-4 u_image">
                            <div class="sponsor_detail">
                                <div class="">
                                    <span>(The size of the image must be 730 x 50 pixels.)</span>
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
                                            <input type='file' name="sa_image" onchange="readURL(this);" class="profilePhoto" accept=".png, .jpg, .jpeg, .bmp"/>                                            
                                            <div class="placeholder_image sponsor update_profile full_width">
                                                <span>
                                                    <?php if (!empty($image)) { ?>
                                                        <img src="{{asset($image)}}"/>
                                                    <?php } else { ?>
                                                        <span> <p>Upload Your Photo</p> </span>
                                                    <?php } ?>
                                                </span>
                                            </div>                                                                                                                                    
                                        </div>
                                    </div>
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
                        <div class="clearfix start_end_date right">
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
                                <input type="text" name="creditdeducted" id="creditdeducted" class="cst_input_primary" placeholder="" readonly value="{{$sa_credit}}">            
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
<?php if($sa_start_date != '') {?>
    $('#startdate').datepicker('destroy');
<?php } else {?>
$("#startdate").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        defaultDate: null,
        minDate: mdate('startdate'),
        onSelect: function (selected) {
            //var dt = new Date(selected);
            //dt.setDate(dt.getDate());
            var date=new Date(selected.split('/')[2],(selected.split('/')[1]-0+1),selected.split('/')[0]);
            var dt = date.getDate()+'/'+date.getMonth()+'/'+date.getFullYear();
            $("#enddate").datepicker("option", "minDate" ,selected);
            $("#enddate").datepicker("option", "maxDate" ,dt);
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
<?php if($sa_end_date != '') {?>
    $('#enddate').datepicker('destroy');
<?php } else {?>
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
 <?php }?>

jQuery(document).ready(function () {
        $('#creditdeducted').keypress(function () {
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
        if (type == 1) {
            var configKey = 'Ads ProCoins';
            $('#ads_image_dimension').show();
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
            success: function (type) {
                $("#creditdeducted").val(type);
            }
        });
    }
</script>
@stop