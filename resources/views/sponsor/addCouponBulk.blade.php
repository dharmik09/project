@extends('layouts.sponsor-master')

@section('content')

<div class="centerlize">
    <div class="col-xs-12">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
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
    <div class="container">        
        <div class="container_padd">                        
            <h1><span class="title_border">Bulk Coupon Import</span></h1>
            <div style="text-align: center;margin-top: 10px;">&nbsp;&nbsp;(Upload excel file for bulk coupon import).
                <a href="{{asset($couponOriginalImageUploadPath.'coupon_import_sample_file.xls')}}" id="" class="rlink">Download sample file</a>
            </div>
            <form id="addCouponBulk" method="post" action="{{ url('/sponsor/coupon-bulk-save') }}" enctype="multipart/form-data">
                <div class="my_box" style="margin-top: 50px;">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div>
                        <div>
                            <div class="file_type_button with_label clearfix">
                                <label id="file_name">No file selected</label>
                                <input type="file" value="" id="coupon_bulk" name="coupon_bulk" accept=".xlsx, .xls" onChange="readU(this);" />                            
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
                <a href="{{ url('sponsor/home') }}" class="rlink"><i class="fa fa-chevron-left" aria-hidden="true"></i> Back to dashboard</a>
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
                        Code : e.g GET50
                    </p>
                    <p>
                        Limit : e.g 100
                    </p>                                       
                    <p>
                        Description : Short description
                    </p>
                    <p>
                        Valid From Date : e.g 6/27/2016
                    </p>
                    <p>
                        Valid To Date : e.g 6/30/2016
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('script')
<script type="text/javascript">
                                    jQuery(document).ready(function() {
//                                        $("#coupon_bulk").change(function(e) {
//                                            var $this = $(this);
//                                            var filename = $this.val().split('\\').pop();
//                                            $('#file_name').text(filename);
//                                        });

                                        $('#view_coupon_bulk_sample').click(function(event) {
                                            $('#coupon_info_popup').modal('show');
                                        });

                                        var validationRules = {
                                            coupon_bulk: {
                                                required: true
                                            }
                                        };
                                        $("#addCouponBulk").validate({
                                            rules: validationRules,
                                            messages: {
                                                coupon_bulk: {
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
                                    function readU(input_file) {
                                        if (input_file.files && input_file.files[0]) {
                                            var fileType = input_file.files[0];
                                            $('#file_name').text(input_file.files[0].name);
                                            if (fileType.type == 'application/vnd.ms-excel' || fileType.type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                                                $("#bulk_error_msg").text('');
                                            } else {
                                                $("#bulk_error_msg").text('File format is not excel. ').css('color', 'red');
                                                $("#coupon_bulk").val('');
                                            }
                                        }
                                    }
                                    $("#addCouponBulk").on('submit', (function(e) {
                                        if ($('#addCouponBulk').validate().form()) {
                                            $(".ajax-loader").show();
                                            $("#submit").attr('type', 'text');
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    }));
</script>
@stop