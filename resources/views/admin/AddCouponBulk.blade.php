@extends('layouts.admin-master')

@section('content')
<!-- Content Wrapper. Contains page content -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{trans('labels.coupons')}}
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
                    <h3 class="box-title"> {{trans('labels.addcouponbulk')}}</h3>
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
                <form id="addCouponBulk" class="form-horizontal" method="post" action="{{ url('/admin/save-coupon-bulk') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="cp_bulk" class="col-sm-2 control-label">{{trans('labels.formcouponbulkupload')}}</label>
                            <div class="col-sm-6">
                                <input type="file" id="cp_bulk" name="cp_bulk" />
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" id="submit" class="btn btn-primary btn-flat" >{{trans('labels.savebtn')}}</button>
                        <a class="btn btn-danger btn-flat pull-right" href="{{ url('admin/coupons') }}">{{trans('labels.cancelbtn')}}</a>
                    </div><!-- /.box-footer -->
                </form>
            </div>   <!-- /.row -->
        </div>
    </div>
</section>
@stop
@section('script')
<script type="text/javascript">
    jQuery(document).ready(function() {
        var validationRules = {
             cp_bulk : {
                required : true
            }
        }
        $("#addCouponBulk").validate({
            rules : validationRules,
            messages : {
                  cp_bulk : {
                    required : "<?php echo trans('validation.requiredfield'); ?>"
                }
            }
        })
    });
</script>
@stop

