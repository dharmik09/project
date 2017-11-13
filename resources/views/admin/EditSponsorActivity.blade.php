@extends('layouts.admin-master')

@section('content')

<!-- Content Wrapper. Contains page content -->

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.viewsponsoractivityform')}}
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
                    <h3 class="box-title"><?php echo (isset($sponsorsActivities) && !empty($sponsorsActivities)) ? trans('labels.edit') : trans('labels.add') ?> {{trans('labels.viewsponsoractivityform')}}</h3>
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

                <form id="editSponsorActivity" class="form-horizontal" method="post" action="{{ url('/admin/save-sponsor-activity') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="<?php echo (isset($sponsorsActivities) && !empty($sponsorsActivities)) ? $sponsorsActivities->id : '0' ?>">
                    <input type="hidden" name="hidden_logo" value="<?php echo (isset($sponsorsActivities) && !empty($sponsorsActivities)) ? $sponsorsActivities->sa_image : '' ?>">
                    <input type="hidden" name="hidden_sponsor_id" value="<?php echo (isset($sponsorsActivities) && !empty($sponsorsActivities)) ? $sponsorsActivities->sa_sponsor_id : '' ?>">

                    <div class="box-body">

                        <?php
                        if (old('sa_type'))
                            $sa_type = old('sa_type');
                        elseif ($sponsorsActivities)
                            $sa_type = $sponsorsActivities->sa_type;
                        else
                            $sa_type = '';
                        ?>
                        <div class="form-group">
                            <?php $types = Helpers::type(); ?>
                            <label for="sa_type" class="col-sm-2 control-label">{{trans('labels.viewsponsoractivitytype')}}</label>
                            <div class="col-sm-6">
                                <select class="form-control" id="sa_type" name="sa_type">
                                    <option value="">{{trans('labels.viewsatype')}}</option>
                                    <?php foreach ($types as $key => $value) { ?>
                                        <option value="{{$key}}" <?php if ($sa_type == $key) echo 'selected'; ?> >{{$value}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>


                        <?php
                        if (old('sa_name'))
                            $sa_name = old('sa_name');
                        elseif ($sponsorsActivities)
                            $sa_name = $sponsorsActivities->sa_name;
                        else
                            $sa_name = '';
                        ?>
                        <div class="form-group">
                            <label for="sa_name" class="col-sm-2 control-label">{{trans('labels.viewsponsoractivityname')}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="sa_name" name="sa_name" placeholder="Please Enter Activity Name.." value="{{$sa_name}}" minlength="5" maxlength="50"/>
                            </div>
                        </div>

                        <?php
                        if (old('sa_apply_level'))
                            $sa_apply_level = old('sa_apply_level');
                        elseif ($sponsorsActivities)
                            $sa_apply_level = $sponsorsActivities->sa_apply_level;
                        else
                            $sa_apply_level = '';
                        ?>
                        
                        <div class="form-group">
                            <label for="sa_apply_level" class="col-sm-2 control-label">{{trans('labels.viewsponsoractivitylevel')}}</label>
                            <div class="col-sm-6">
                            <?php $levels = Helpers::getActiveLevels(); ?>
                                <select class="form-control" id="sa_apply_level" name="sa_apply_level">
                                    <option value="">{{trans('labels.viewsalevel')}}</option>
                                <?php foreach ($levels as $key => $value) { ?>
                                        <option value="{{$value->id}}" <?php if ($sa_type == $value->id) echo 'selected'; ?> >{{$value->sl_name}}</option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <?php
                        if (old('sa_name'))
                            $sa_name = old('sa_name');
                        elseif ($sponsorsActivities)
                            $sa_name = $sponsorsActivities->sa_name;
                        else
                            $sa_name = '';
                        ?>

                        <div class="form-group">
                            <label for="sa_image" class="col-sm-2 control-label">{{trans('labels.viewsponsoractivityimage')}}</label>
                            <div class="col-sm-6">
                                <?php
                                    $imageData = (isset($sponsorsActivities->sa_image) && $sponsorsActivities->sa_image != "" && Storage::disk('s3')->exists($uploadSAThumbPath . $sponsorsActivities->sa_image) ) ? Config::get('constant.DEFAULT_AWS') . $uploadSAThumbPath . $sponsorsActivities->sa_image : asset($uploadSAThumbPath.'proteen-logo.png');
                                ?>                                             
                                <input type='file' name="sa_image" onchange="readURL(this);" class="profilePhoto" accept=".png, .jpg, .jpeg, .bmp"/>
                                <img src="{{asset($imageData)}}" height="70" width="70"/>
                            </div>
                        </div>
                        <?php
                        if (old('sa_credit_used'))
                            $sa_credit_used = old('sa_credit_used');
                        elseif ($sponsorsActivities)
                            $sa_credit_used = $sponsorsActivities->sa_credit_used;
                        else
                            $sa_credit_used = '';
                        ?>
                        <div class="form-group">
                            <label for="sa_credit_used" class="col-sm-2 control-label">{{trans('labels.viewsponsoractivitycredit')}}</label>
                            <div class="col-sm-6">
                                 <input type="text" class="form-control" id="sa_credit_used" name="sa_credit_used" value="{{$sa_credit_used}}" readonly/>
                            </div>
                        </div>
                   

                        

<?php
if (old('sa_location'))
    $sa_location = old('sa_location');
elseif ($sponsorsActivities)
    $sa_location = $sponsorsActivities->sa_location;
else
    $sa_location = '';
?>
                        <div class="form-group">
                            <label for="sa_location" class="col-sm-2 control-label">{{trans('labels.viewsponsoractivitylocation')}}</label>
                            <div class="col-sm-6">
                                <input type="text" name="sa_location" class="form-control" maxlength="100" placeholder="Enter Location" value="{{ $sa_location or ''}}">
                            </div>
                        </div>

<?php
if (old('sa_start_date'))
    $sa_start_date = old('sa_start_date');
elseif ($sponsorsActivities)
    $sa_start_date = date('d/m/Y', strtotime($sponsorsActivities->sa_start_date));
else
    $sa_start_date = '';
?>
                        <div class="form-group">
                            <label for="sa_start_date" class="col-sm-2 control-label">{{trans('labels.viewactivitystartdate')}}</label>
                            <div class="col-sm-6">
                                <input type="text" id="startdate" name="sa_start_date" class="form-control" placeholder="Please Enter {{trans('labels.viewactivitystartdate')}}"  value="{{ $sa_start_date }}">
                            </div>
                        </div>

                        <!--- city start-->

<?php
if (old('sa_end_date'))
    $sa_end_date = old('sa_end_date');
elseif ($sponsorsActivities)
    $sa_end_date = date('d/m/Y', strtotime($sponsorsActivities->sa_end_date));
else
    $sa_end_date = '';
?>
                        <div class="form-group">
                            <label for="sa_end_date" class="col-sm-2 control-label">{{trans('labels.viewactivityenddate')}}</label>
                            <div class="col-sm-6">
                                <input type="text" id="enddate" name="sa_end_date" class="form-control" placeholder="Please Enter {{trans('labels.viewactivityenddate')}}"  value="{{ $sa_end_date }}">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="status" class="col-sm-2 control-label">{{trans('labels.viewactivitystatus')}}</label>
                            <div class="col-sm-6">
                                    <?php $status = Helpers::status(); ?>
                                    <select class="form-control" name="status" id="status">
                                        <?php foreach ($status as $key => $value) { ?>
                                            <option value="{{$key}}">{{$value}}</option>
                                        <?php } ?>
                                    </select>
                            </div>
                        </div>

                        <div class="box-footer">
                            <input type="submit" id="submit" class="btn btn-primary btn-flat" value="{{trans('labels.savebtn')}}" />
                            <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/sponsor-activity') }}/{{$sponsorsActivities->sa_sponsor_id}}">{{trans('labels.cancelbtn')}}</a>
                        </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section><!-- /.content -->

@stop

@section('script')
<script type="text/javascript" src="{{ asset('backend/js/jquery.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('backend/js/jquery-ui.js') }}"></script>
<script type="text/javascript">

    $("#startdate").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        defaultDate: null,
        minDate: mdate('startdate'), 
        onSelect: function (selected) {
            $("#enddate").datepicker("option", "minDate", selected);
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
    

    $("#enddate").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy',
        defaultDate: null,
        minDate: mdate('enddate'),
    }).on('change', function () {
        $(this).valid();
        var myDate = new Date($(this).val());
        var currentYear = (new Date).getFullYear();
        var mydate_fullyear = myDate.getFullYear();
        if (mydate_fullyear < currentYear)
        {
            $('#submit').attr('disabled', true);
        } else
        {
            $('#submit').attr('disabled', false);
        }
    });

$(".profilePhoto").change(function(e) {
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
    
    jQuery(document).ready(function () {
       
<?php if (isset($sponsorsActivities->id) && $sponsorsActivities->id != '0') { ?>
            var validationRules = {
                sa_type: {
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
                sa_start_date: {
                    required: true
                },
                sa_end_date: {
                    required: true
                },
                deleted: {
                    required: true
                }
            }
<?php } else { ?>
            var validationRules = {
                sa_type: {
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
                sa_start_date: {
                    required: true
                },
                sa_end_date: {
                    required: true,
                },
                deleted: {
                    required: true
                }
            }
<?php } ?>
        $("#editSponsorActivity").validate({
            rules: validationRules,
            messages: {
                sa_type: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                sa_name: {
                    required: "<?php echo trans('validation.requiredfield'); ?>",
                    email: "<?php echo trans('validation.validemail'); ?>"
                },
                sa_apply_level: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                sa_start_date: {
                    required: "<?php echo trans('validation.requiredfield'); ?>"
                },
                sa_end_date: {
                    required: "<?php echo trans('validation.requiredfield') ?>"
                },
                status: {
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

