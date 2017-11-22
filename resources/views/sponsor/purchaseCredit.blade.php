@extends('layouts.sponsor-master')

@section('content')
<div class="col-xs-12">
    @if ($message = Session::get('error'))
    <div class="row">
        <div class="col-md-12">
            <div class="box-body">
                <div class="alert alert-error alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">X</button>
                    <h4><i class="icon fa fa-check"></i> {{trans('validation.errorlbl')}}</h4>
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @endif
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
</div>  

<!-- Content Wrapper. Contains page content -->

<!-- Main content -->
<div class="centerlize">
    <div class="container">
        <div class="clearfix col-md-offset-2 col-sm-offset-1 col-md-8 col-sm-10 detail_container">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <form id="change_password_form" role="form" method="POST" class="login_form" action="" autocomplete="off">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <h1><span class="title_border">Purchase Credit</span></h1>
                    <div class="col-md-offset-1 col-sm-offset-1 col-md-10 col-sm-10">
                        <div class="clearfix">
                            <div class="col-md-12 col-sm-12 col-xs-12 input_icon">
                                <input type="text" class="cst_input_primary" id="credit" maxlength="20" minlength="6" name="credit" placeholder="Credit">
                            </div>
                        </div>
                        
                        <div class="button_container social_btn">
                            <div class="submit_register">
                                <input type="submit" value="Save" class="btn primary_btn">
                                <a href="{{ url('sponsor/home') }}" class="btn primary_btn"><em>Cancel</em><span></span></a>
                            </div>
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
    jQuery(document).ready(function() {
        $('#view_coupon_bulk_sample').click(function(event) {
            $('#coupon_info_popup').modal('show');
        });
        
            var validationRules = {
                 coupon_bulk : {
                    required : true
                }
            };
        $("#addCouponBulk").validate({
            rules : validationRules,
            messages : {
                  coupon_bulk : {
                    required : "Please select proper excel file",                    
                }
            }
        });
        });
</script>
@stop

