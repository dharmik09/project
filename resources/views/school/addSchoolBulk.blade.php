@extends('layouts.school-master')

@section('content')

@if(Session::has('invalidemails'))
<?php $invalidEmails = Session::get('invalidemails'); ?>
@if(!empty($invalidEmails))
<div class="col-md-12">
    <div class="box-body">
        <div class="alert alert-error alert-dismissable danger">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
            <h4><i class="icon fa fa-check"></i> {{trans('validation.whoops')}}</h4>Below are the invalid emails so not imported into database
            <ul>
                @foreach($invalidEmails as $key=>$email)
                <li>{{ $email }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
@endif
<?php Session::forget('invalidemails'); ?>
<div class="col-xs-12">
    @if (count($errors) > 0)
    <div class="alert alert-error alert-dismissable danger">
        <strong>{{trans('validation.whoops')}}</strong> {{trans('validation.someproblems')}}<br><br>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>

@if($message = Session::get('error'))
<div class="col-md-12">
    <div class="box-body">
        <div class="alert alert-error alert-dismissable danger">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
            {{ $message }}
        </div>
    </div>
</div>
@endif

<div class="centerlize">
    <div class="container">
        <div class="container_padd">
            <h1><span class="title_border">{{Auth::guard('school')->user()->sc_name}}</span><br/>Students</h1>
            <div style="text-align: center;margin-top: 10px;">&nbsp;&nbsp;(Upload excel file for bulk student import)
                <a href="{{asset($schoolOriginalImagePath.'school_student_import_sample_file.xls')}}" id="" class="rlink">Download sample file</a>
            </div>

            <form id="addSchoolBulk" method="post" action="{{ url('/school/save-school-bulk-import') }}" enctype="multipart/form-data">
                <div class="my_box" style="margin-top: 50px;">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div>
                        <div>
                            <div class="file_type_button with_label clearfix">
                                <label id="file_name">No file selected</label>
                                <input type="file" value="" id="school_bulk" name="school_bulk" accept=".xlsx, .xls" onchange="readU(this);">                            
                                <span class="btn primary_btn">Browse</span>
                            </div>
                            <div id="bulk_error_msg"></div>                              
                        </div>
                        <div>
                            <button type="submit" id="submit" class="next btn primary_btn">Import</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="back_to back_next_container clearfix">
                <a href="{{ url('school/home') }}" class="rlink"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back to dashboard</a>
            </div>

        </div>            
    </div>
</div>
<div id="coupon_info_popup" class="modal fade login_info_popup_cst" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                <h4 class="modal-title">Sample</h4>
            </div>
            <div class="modal-body">
                <div class="para_holder">
                    <p>
                        Below fields are must be present in excel sheet
                    </p>
                    <p>
                        Student Name : Harsh
                    </p>
                    <p>
                        Student Email : e.g harsh099@gmail.com
                    </p>                                       
                    <p>
                        Gender : Male/Female
                    </p>
                    <p>
                        BirthDate : e.g 12/11/1994
                    </p>
                    <p>
                        roll_no : e.g 33
                    </p>
                    <p>
                        class : e.g 11
                    </p>
                    <p>
                        devision : e.g A
                    </p>
                    <p>
                        Medium : e.g English
                    </p>
                    <p>
                        academic year : e.g 2015
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="loader ajax-loader" style="display:none;">
    <div class="cont_loader">
        <div class="img1"></div>
        <div class="img2"></div>
    </div>
</div>

@stop

@section('script')
<script type="text/javascript">
    function readU(input_file){
        if (input_file.files && input_file.files[0]) {
            //var reader2 = new FileReader();
            //reader2.onload = function(e) {
              var fileType = input_file.files[0];
              $('#file_name').text(input_file.files[0].name);
              if (fileType.type == 'application/vnd.ms-excel' || fileType.type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                  $("#bulk_error_msg").text('');
              }else{
                  $("#bulk_error_msg").text('File format is not excel. ').css('color','red');
                  $("#school_bulk").val('');
              }
            //};
            //reader2.readAsDataURL(input_file.files[0]);
        }
    }
    jQuery(document).ready(function() {
        $('#view_coupon_bulk_sample').click(function(event) {
            $('#coupon_info_popup').modal('show');
        });
        var validationRules = {
            school_bulk: {
                required: true
            }
        };
        $("#addSchoolBulk").validate({
            rules: validationRules,
            messages: {
                school_bulk: {
                    required: "Please select proper excel file",
                }
            },
            errorPlacement: function(error, element) {
                $("#bulk_error_msg").text('');
                $('#file_name').text('');
                error.appendTo("#bulk_error_msg");
            }
        });
    });
    $("#addSchoolBulk").on('submit', (function(e) {
        if($('#addSchoolBulk').validate().form()){
            $(".ajax-loader").show();
            $("#submit").attr('type','text');
            return true;
        }else{
            return false;
        }
    }));
</script>
@stop

